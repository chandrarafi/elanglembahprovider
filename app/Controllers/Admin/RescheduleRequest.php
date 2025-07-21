<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemesananModel;
use App\Models\RescheduleRequestModel;
use App\Models\UserModel;
use Config\Services;

class RescheduleRequest extends BaseController
{
    protected $pemesananModel;
    protected $rescheduleModel;
    protected $userModel;

    public function __construct()
    {
        $this->pemesananModel = new PemesananModel();
        $this->rescheduleModel = new RescheduleRequestModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display list of reschedule requests
     */
    public function index()
    {
        // Check if admin is logged in
        // if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
        //     return redirect()->to('/admin/login');
        // }

        $data = [
            'title' => 'Daftar Permintaan Perubahan Jadwal',
            'requests' => $this->rescheduleModel->getRequestWithDetails()
        ];

        return view('admin/reschedule/index', $data);
    }

    /**
     * View details of a reschedule request
     */
    public function view($id)
    {
        // Check if admin is logged in
        // if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
        //     return redirect()->to('/admin/login');
        // }

        // Get request data with details
        $request = $this->rescheduleModel->getRequestWithDetails($id);

        if (!$request) {
            return redirect()->to('/admin/reschedule')->with('error', 'Permintaan perubahan jadwal tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Permintaan Perubahan Jadwal',
            'request' => $request
        ];

        return view('admin/reschedule/view', $data);
    }

    /**
     * Process a reschedule request (approve/reject)
     */
    public function process()
    {
        // Check if admin is logged in
        // if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
        //     return redirect()->to('/admin/login');
        // }

        // Get post data
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $admin_notes = $this->request->getPost('admin_notes');

        // Validate input
        $validation = $this->validate([
            'id' => 'required|numeric',
            'status' => 'required|in_list[approved,rejected]',
            'admin_notes' => 'required|min_length[5]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get request data
        $request = $this->rescheduleModel->find($id);

        if (!$request) {
            return redirect()->to('/admin/reschedule')->with('error', 'Permintaan perubahan jadwal tidak ditemukan');
        }

        // Check if request is still pending
        if ($request['status'] !== 'pending') {
            return redirect()->to('/admin/reschedule')->with('error', 'Permintaan ini sudah diproses sebelumnya');
        }

        // Begin transaction
        $this->db->transBegin();

        try {
            // Update request status
            $this->rescheduleModel->update($id, [
                'status' => $status,
                'admin_notes' => $admin_notes
            ]);

            // If approved, update booking dates
            if ($status === 'approved') {
                $this->pemesananModel->update($request['idpesan'], [
                    'tgl_berangkat' => $request['requested_tgl_berangkat'],
                    'tgl_selesai' => $request['requested_tgl_selesai']
                ]);

                // Get booking and user details for notification
                $pemesanan = $this->pemesananModel->find($request['idpesan']);
                $user = $this->userModel->find($pemesanan['iduser']);

                // Send email notification to user
                $this->sendRescheduleNotification($request, $pemesanan, $user, $status);
            } else {
                // Send rejection notification
                $pemesanan = $this->pemesananModel->find($request['idpesan']);
                $user = $this->userModel->find($pemesanan['iduser']);
                $this->sendRescheduleNotification($request, $pemesanan, $user, $status);
            }

            $this->db->transCommit();

            return redirect()->to('/admin/reschedule')
                ->with('success', 'Permintaan perubahan jadwal telah berhasil ' .
                    ($status === 'approved' ? 'disetujui' : 'ditolak'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error processing reschedule request: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memproses permintaan. Silakan coba lagi.');
        }
    }

    /**
     * Display form to create reschedule request by admin
     */
    public function createRequest($id_pemesanan)
    {
        // Get booking data
        $pemesanan = $this->pemesananModel->find($id_pemesanan);

        if (!$pemesanan) {
            return redirect()->to('/admin/pemesanan')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Check if this is admin-created booking
        if ($pemesanan['catatan'] !== "pemesanan dilakukan oleh admin") {
            return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
                ->with('error', 'Hanya pemesanan yang dibuat oleh admin yang dapat diubah jadwalnya oleh admin');
        }

        // Check if booking is not completed or cancelled
        if (in_array($pemesanan['status'], ['completed', 'cancelled'])) {
            return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
                ->with('error', 'Tidak dapat mengubah jadwal pemesanan yang telah selesai atau dibatalkan');
        }

        // Check if there's already pending reschedule request
        $pendingRequest = $this->rescheduleModel->where('idpesan', $id_pemesanan)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
                ->with('error', 'Sudah ada permintaan perubahan jadwal yang sedang menunggu persetujuan');
        }

        // Get additional booking details
        $bookingDetails = $this->pemesananModel->getPemesananWithPaket($id_pemesanan);

        $data = [
            'title' => 'Ajukan Perubahan Jadwal',
            'pemesanan' => $bookingDetails,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/reschedule/create', $data);
    }

    /**
     * Process reschedule request creation by admin
     */
    public function submitRequest()
    {
        // Validate input
        $rules = [
            'idpesan' => 'required|numeric',
            'current_tgl_berangkat' => 'required|valid_date',
            'requested_tgl_berangkat' => 'required|valid_date',
            'alasan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $idpesan = $this->request->getPost('idpesan');
        $current_tgl_berangkat = $this->request->getPost('current_tgl_berangkat');
        $requested_tgl_berangkat = $this->request->getPost('requested_tgl_berangkat');
        $alasan = $this->request->getPost('alasan');

        // Get booking data
        $pemesanan = $this->pemesananModel->find($idpesan);

        if (!$pemesanan) {
            return redirect()->to('/admin/pemesanan')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Check if this is admin-created booking
        if ($pemesanan['catatan'] !== "pemesanan dilakukan oleh admin") {
            return redirect()->to('/admin/pemesanan/detail/' . $idpesan)
                ->with('error', 'Hanya pemesanan yang dibuat oleh admin yang dapat diubah jadwalnya oleh admin');
        }

        // Get paket details
        $paket = $this->db->table('paket_wisata')->where('idpaket', $pemesanan['idpaket'])->get()->getRowArray();

        if (!$paket) {
            return redirect()->back()->withInput()->with('error', 'Paket wisata tidak ditemukan');
        }

        // Calculate new end date based on package duration
        $durasi = isset($paket['durasi']) && !empty($paket['durasi']) ? (int)$paket['durasi'] : 1;
        $requested_tgl_selesai = date('Y-m-d', strtotime("$requested_tgl_berangkat + $durasi days"));

        // Check if requested date is available
        $isAvailable = $this->pemesananModel->cekKetersediaan(
            $pemesanan['idpaket'],
            $requested_tgl_berangkat,
            $requested_tgl_selesai,
            $idpesan
        );

        if (!$isAvailable) {
            return redirect()->back()->withInput()
                ->with('error', 'Paket tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.');
        }

        try {
            // Begin transaction
            $this->db->transBegin();

            // Create new reschedule request
            $requestData = [
                'idpesan' => $idpesan,
                'current_tgl_berangkat' => $current_tgl_berangkat,
                'current_tgl_selesai' => $pemesanan['tgl_selesai'],
                'requested_tgl_berangkat' => $requested_tgl_berangkat,
                'requested_tgl_selesai' => $requested_tgl_selesai,
                'alasan' => $alasan,
                'status' => 'approved', // Auto approve because it's admin
                'admin_notes' => 'Perubahan jadwal dilakukan oleh admin'
            ];

            $this->rescheduleModel->insert($requestData);

            // Auto-update booking with new dates since it's admin request
            $this->pemesananModel->update($idpesan, [
                'tgl_berangkat' => $requested_tgl_berangkat,
                'tgl_selesai' => $requested_tgl_selesai
            ]);

            // Get user details for notification
            $user = $this->userModel->find($pemesanan['iduser']);

            // Send notification to user
            if ($user) {
                $this->sendAdminRescheduleNotification($pemesanan, $user, $requestData);
            }

            $this->db->transCommit();

            return redirect()->to('/admin/pemesanan/detail/' . $idpesan)
                ->with('success', 'Jadwal pemesanan berhasil diubah');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating reschedule request: ' . $e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan saat mengubah jadwal. Silakan coba lagi.');
        }
    }

    /**
     * Send email notification about admin-initiated reschedule
     */
    private function sendAdminRescheduleNotification($pemesanan, $user, $requestData)
    {
        $email = Services::email();

        $email->setFrom('noreply@elanglembah.com', 'Elang Lembah Tourism');
        $email->setTo($user['email']);
        $email->setSubject('Perubahan Jadwal Pemesanan - ' . $pemesanan['kode_booking']);

        $message = "
            <h2>Perubahan Jadwal Pemesanan</h2>
            <p>Halo {$user['name']},</p>
            <p>Kami informasikan bahwa jadwal pemesanan Anda dengan kode booking <strong>{$pemesanan['kode_booking']}</strong> telah diubah oleh admin kami.</p>
            
            <h3>Detail Perubahan:</h3>
            <p>
                <strong>Jadwal Lama:</strong> " . date('d M Y', strtotime($requestData['current_tgl_berangkat'])) . " s/d " . date('d M Y', strtotime($requestData['current_tgl_selesai'])) . "<br>
                <strong>Jadwal Baru:</strong> " . date('d M Y', strtotime($requestData['requested_tgl_berangkat'])) . " s/d " . date('d M Y', strtotime($requestData['requested_tgl_selesai'])) . "
            </p>
            
            <p><strong>Alasan perubahan:</strong> {$requestData['alasan']}</p>
            
            <p>Anda dapat melihat detail pemesanan Anda di halaman detail pesanan. Jika memiliki pertanyaan, silakan hubungi customer service kami.</p>
            
            <p>Terima kasih telah memilih Elang Lembah Tourism.</p>
        ";

        $email->setMessage($message);

        try {
            $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send admin reschedule notification email: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification about reschedule request status
     */
    private function sendRescheduleNotification($request, $pemesanan, $user, $status)
    {
        $email = Services::email();

        $email->setFrom('noreply@elanglembah.com', 'Elang Lembah Tourism');
        $email->setTo($user['email']);

        if ($status === 'approved') {
            $email->setSubject('Perubahan Jadwal Disetujui - ' . $pemesanan['kode_booking']);

            $message = "
                <h2>Perubahan Jadwal Pemesanan Disetujui</h2>
                <p>Halo {$user['name']},</p>
                <p>Permintaan perubahan jadwal untuk pemesanan Anda dengan kode booking <strong>{$pemesanan['kode_booking']}</strong> telah disetujui.</p>
                
                <h3>Detail Perubahan:</h3>
                <p>
                    <strong>Jadwal Lama:</strong> " . date('d M Y', strtotime($request['current_tgl_berangkat'])) . " s/d " . date('d M Y', strtotime($request['current_tgl_selesai'])) . "<br>
                    <strong>Jadwal Baru:</strong> " . date('d M Y', strtotime($request['requested_tgl_berangkat'])) . " s/d " . date('d M Y', strtotime($request['requested_tgl_selesai'])) . "
                </p>
                
                <p><strong>Catatan Admin:</strong> {$request['admin_notes']}</p>
                
                <p>Anda dapat melihat detail pemesanan Anda di halaman detail pesanan. Jika memiliki pertanyaan, silakan hubungi customer service kami.</p>
                
                <p>Terima kasih telah memilih Elang Lembah Tourism.</p>
            ";
        } else {
            $email->setSubject('Perubahan Jadwal Ditolak - ' . $pemesanan['kode_booking']);

            $message = "
                <h2>Perubahan Jadwal Pemesanan Ditolak</h2>
                <p>Halo {$user['name']},</p>
                <p>Mohon maaf, permintaan perubahan jadwal untuk pemesanan dengan kode booking <strong>{$pemesanan['kode_booking']}</strong> tidak dapat disetujui.</p>
                
                <p><strong>Alasan:</strong> {$request['admin_notes']}</p>
                
                <p>Jika Anda memiliki pertanyaan atau ingin mengajukan permintaan perubahan jadwal lagi, silakan hubungi customer service kami.</p>
                
                <p>Terima kasih atas pengertian Anda.</p>
            ";
        }

        $email->setMessage($message);

        try {
            $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send reschedule notification email: ' . $e->getMessage());
        }
    }
}

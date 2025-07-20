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

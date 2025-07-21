<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemesananModel;
use App\Models\PembayaranModel;
use App\Models\PaketWisataModel;
use App\Models\UserModel;
use CodeIgniter\Email\Email;

class Pemesanan extends BaseController
{
    protected $pemesananModel;
    protected $pembayaranModel;
    protected $paketModel;
    protected $userModel;
    protected $email;

    public function __construct()
    {
        $this->pemesananModel = new PemesananModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->paketModel = new PaketWisataModel();
        $this->userModel = new UserModel();
        $this->email = \Config\Services::email();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Pemesanan',
            'status_options' => [
                'pending' => 'Pending',
                'down_payment' => 'DP Terbayar',
                'waiting_confirmation' => 'Menunggu Konfirmasi',
                'confirmed' => 'Dikonfirmasi',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ]
        ];
        return view('admin/pemesanan/index', $data);
    }

    public function getPemesanan()
    {
        $request = $this->request;
        $db = \Config\Database::connect();

        // Query utama
        $builder = $db->table('pemesanan p');
        $builder->select('p.*, pw.namapaket, u.name as nama_pelanggan');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->join('users u', 'p.iduser = u.id');
        // Removed pembayaran join

        // Subquery untuk mendapatkan id pemesanan terbaru untuk setiap kode_booking
        $subBuilder = $db->table('pemesanan ps')
            ->select('MAX(ps.idpesan) as max_id')
            ->groupBy('ps.kode_booking');

        // Terapkan filter ke subquery jika diperlukan
        $status = $request->getGet('status');
        if ($status) {
            // Kita hanya perlu filter status di query utama
            $builder->where('p.status', $status);
        }

        // Filter by date range di query utama
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        if ($startDate && $endDate) {
            $builder->where('p.tanggal >=', $startDate)
                ->where('p.tanggal <=', $endDate);
        }

        // Removed payment type filter

        // Filter pencarian
        $search = $request->getPost('search')['value'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->like('p.kode_booking', $search)
                ->orLike('pw.namapaket', $search)
                ->orLike('u.name', $search)
                ->groupEnd();
        }

        // Gunakan WHERE idpesan IN (subquery)
        $latestIds = $subBuilder->get()->getResultArray();
        if (!empty($latestIds)) {
            $ids = array_column($latestIds, 'max_id');
            $builder->whereIn('p.idpesan', $ids);
        } else {
            // Tidak ada pemesanan
            return $this->response->setJSON([
                'draw' => $request->getPost('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // Hitung total sebelum filter pencarian dan pagination
        $countBuilder = clone $builder;
        $totalRecords = $countBuilder->countAllResults(false);
        $totalFiltered = $totalRecords;

        if (!empty($search)) {
            $countBuilder = clone $builder;
            $totalFiltered = $countBuilder->countAllResults();
        }

        // Pengurutan
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 3; // Default column index (tanggal)
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

        // Definisikan kolom yang bisa diurutkan
        $columns = [
            0 => 'p.kode_booking',
            1 => 'pw.namapaket',
            2 => 'u.name',
            3 => 'p.tanggal',
            4 => 'p.tgl_berangkat',
            5 => 'p.totalbiaya',
            6 => 'p.status'
            // Removed payment status column
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'p.tanggal';
        $builder->orderBy($orderColumn, $orderDir);

        // Pagination
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $builder->limit($length, $start);

        // Eksekusi query
        $result = $builder->get()->getResultArray();

        // Format data untuk DataTables
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'idpesan' => $row['idpesan'],
                'kode_booking' => $row['kode_booking'],
                'namapaket' => $row['namapaket'],
                'nama_pelanggan' => $row['nama_pelanggan'],
                'tanggal' => date('d-m-Y', strtotime($row['tanggal'])),
                'tgl_berangkat' => date('d-m-Y', strtotime($row['tgl_berangkat'])),
                'totalbiaya' => number_format($row['totalbiaya'], 0, ',', '.'),
                'status' => $row['status'],
                'catatan' => $row['catatan'] ?? ''
                // Removed payment-related fields
            ];
        }

        return $this->response->setJSON([
            'draw' => $request->getPost('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function detail($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->getPemesananWithPaket($id_pemesanan);
        if (!$pemesanan) {
            return redirect()->to('/admin/pemesanan')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Ambil semua pembayaran untuk pemesanan ini
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)->orderBy('tanggal_bayar', 'DESC')->findAll();

        // Jika tidak ada pembayaran sama sekali, set sebagai array kosong
        if (empty($pembayaran)) {
            $pembayaran = [];
        }

        $data = [
            'title' => 'Detail Pemesanan',
            'pemesanan' => $pemesanan,
            'pembayaran' => $pembayaran,
            'status_options' => [
                'pending' => 'Pending',
                'down_payment' => 'DP Terbayar',
                'waiting_confirmation' => 'Menunggu Konfirmasi',
                'confirmed' => 'Dikonfirmasi',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ],
            'payment_status_options' => [
                'pending' => 'Pending',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak',
            ]
        ];

        return view('admin/pemesanan/detail', $data);
    }

    public function updateStatus($id_pemesanan)
    {
        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        // Cek apakah status berubah menjadi completed dan pembayaran sebelumnya DP
        if ($status == 'completed') {
            // Ambil semua pembayaran yang terverifikasi
            $verified_payments = $this->pembayaranModel->where('idpesan', $id_pemesanan)
                ->where('status_pembayaran', 'verified')
                ->findAll();

            // Periksa tipe pembayaran
            $has_dp = false;
            $has_full = false;

            foreach ($verified_payments as $payment) {
                if ($payment['tipe_pembayaran'] == 'dp') {
                    $has_dp = true;
                }
                if ($payment['tipe_pembayaran'] == 'lunas') {
                    $has_full = true;
                }
            }

            // Jika ada DP dan tidak ada pembayaran lunas, baru buat pelunasan otomatis
            if ($has_dp && !$has_full) {
                // Cari pembayaran DP terverifikasi terbaru
                $dp_payment = $this->pembayaranModel->where('idpesan', $id_pemesanan)
                    ->where('status_pembayaran', 'verified')
                    ->where('tipe_pembayaran', 'dp')
                    ->orderBy('tanggal_bayar', 'DESC')
                    ->first();

                if ($dp_payment) {
                    // Hitung jumlah sisa pembayaran (50% dari total)
                    $sisa_pembayaran = $pemesanan['totalbiaya'] - $dp_payment['jumlah_bayar'];

                    // Pastikan ada sisa pembayaran
                    if ($sisa_pembayaran > 0) {
                        // Buat pembayaran pelunasan otomatis
                        $data_pelunasan = [
                            'idpesan' => $id_pemesanan,
                            'tanggal_bayar' => date('Y-m-d H:i:s'),
                            'jumlah_bayar' => $sisa_pembayaran,
                            'metode_pembayaran' => 'Pelunasan Admin',
                            'tipe_pembayaran' => 'lunas',
                            'bukti_bayar' => null,
                            'status_pembayaran' => 'verified',
                            'keterangan' => 'Pelunasan otomatis oleh admin'
                        ];

                        // Simpan data pelunasan
                        $this->pembayaranModel->insert($data_pelunasan);

                        // Log pelunasan
                        log_message('info', "Auto settlement created - ID: {$id_pemesanan}, Amount: {$sisa_pembayaran}");
                    }
                }
            } else {
                log_message('info', "No auto settlement needed - ID: {$id_pemesanan}, Has DP: " . ($has_dp ? 'Yes' : 'No') . ", Has Full Payment: " . ($has_full ? 'Yes' : 'No'));
            }
        }

        // Update status pemesanan
        $this->pemesananModel->update($id_pemesanan, [
            'status' => $status
        ]);

        // Ambil data pelanggan
        $user = $this->userModel->find($pemesanan['iduser']);

        // Kirim email notifikasi
        $this->sendStatusUpdateEmail($user, $pemesanan, $status, $keterangan);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Status pemesanan berhasil diperbarui'
        ]);
    }

    public function verifyPayment($id_pemesanan)
    {
        $status_pembayaran = $this->request->getPost('status_pembayaran');
        $keterangan = $this->request->getPost('keterangan');
        $id_bayar = $this->request->getPost('idbayar');

        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        // Cari pembayaran berdasarkan ID bayar jika ada, jika tidak cari berdasarkan ID pemesanan
        if (!empty($id_bayar)) {
            $pembayaran = $this->pembayaranModel->find($id_bayar);
        } else {
            $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)
                ->where('status_pembayaran', 'pending')
                ->first();
        }

        if (!$pembayaran) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data pembayaran tidak ditemukan'
            ]);
        }

        // Log semua data pembayaran untuk debugging
        log_message('debug', "Payment data: " . json_encode($pembayaran));

        // Update status pembayaran
        $this->pembayaranModel->update($pembayaran['idbayar'], [
            'status_pembayaran' => $status_pembayaran,
            'keterangan' => $keterangan
        ]);

        // Jika pembayaran ditolak, maka pemesanan dibatalkan
        if ($status_pembayaran == 'rejected') {
            $this->pemesananModel->update($id_pemesanan, [
                'status' => 'cancelled'
            ]);
            log_message('debug', "Payment rejected - ID: {$id_pemesanan}, Status set to: cancelled");
        }
        // Jika pembayaran diterima, maka status pemesanan berubah menjadi confirmed
        else if ($status_pembayaran == 'verified') {
            // Log semua data pembayaran untuk debugging
            log_message('debug', "Payment data: " . json_encode($pembayaran));

            // Validasi tipe_pembayaran ada dan valid
            if (!isset($pembayaran['tipe_pembayaran']) || empty($pembayaran['tipe_pembayaran'])) {
                log_message('error', "Payment type missing! Setting default to 'lunas'");
                $pembayaran['tipe_pembayaran'] = 'lunas';

                // Update tipe pembayaran jika tidak ada
                $this->pembayaranModel->update($pembayaran['idbayar'], [
                    'tipe_pembayaran' => 'lunas'
                ]);
            }

            // Semua pembayaran yang terverifikasi (baik DP maupun lunas) statusnya menjadi confirmed
            $new_status = 'confirmed';

            // Debug log
            log_message('debug', "Payment verified - ID: {$id_pemesanan}, Payment type: {$pembayaran['tipe_pembayaran']}, Setting status to: {$new_status}");

            // Update status dengan try-catch untuk menangkap error
            try {
                $result = $this->pemesananModel->update($id_pemesanan, [
                    'status' => $new_status
                ]);

                // Log update result
                log_message('debug', "Update pemesanan result: " . ($result ? "Success" : "Failed"));

                // Double-check hasil update
                $updated_pemesanan = $this->pemesananModel->find($id_pemesanan);
                log_message('debug', "Updated status check: " . ($updated_pemesanan['status'] ?? 'NULL'));
            } catch (\Exception $e) {
                log_message('error', "Error updating booking status: " . $e->getMessage());
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui status pemesanan: ' . $e->getMessage()
                ]);
            }
        }

        // Ambil data pelanggan
        $user = $this->userModel->find($pemesanan['iduser']);

        // Kirim email notifikasi
        $this->sendPaymentVerificationEmail($user, $pemesanan, $pembayaran, $status_pembayaran, $keterangan);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Status pembayaran berhasil diperbarui'
        ]);
    }

    protected function sendStatusUpdateEmail($user, $pemesanan, $status, $keterangan)
    {
        // Pastikan email pelanggan tersedia
        if (empty($user['email'])) {
            return false;
        }

        // Ambil data paket wisata
        $paket = $this->paketModel->find($pemesanan['idpaket']);

        // Status dalam bahasa Indonesia
        $status_text = [
            'pending' => 'Menunggu Pembayaran',
            'down_payment' => 'DP Telah Dibayar',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Telah Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        // Siapkan email
        $this->email->setTo($user['email']);
        $this->email->setFrom('noreply@elanglembah.com', 'Elang Lembah Tourism');
        $this->email->setSubject('Update Status Pemesanan #' . $pemesanan['kode_booking']);

        // Buat body email
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a6fdc; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                .details { background-color: #f9f9f9; padding: 15px; margin: 15px 0; }
                .status { font-weight: bold; color: #4a6fdc; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Update Status Pemesanan</h2>
                </div>
                <div class='content'>
                    <p>Halo <b>{$user['name']}</b>,</p>
                    <p>Status pemesanan paket wisata Anda telah diperbarui.</p>
                    
                    <div class='details'>
                        <p><strong>Kode Booking:</strong> {$pemesanan['kode_booking']}</p>
                        <p><strong>Paket Wisata:</strong> {$paket['namapaket']}</p>
                        <p><strong>Tanggal Berangkat:</strong> " . date('d M Y', strtotime($pemesanan['tgl_berangkat'])) . "</p>
                        <p><strong>Status Baru:</strong> <span class='status'>{$status_text[$status]}</span></p>";

        if (!empty($keterangan)) {
            $message .= "<p><strong>Keterangan:</strong> {$keterangan}</p>";
        }

        $message .= "
                    </div>
                    
                    <p>Silakan login ke akun Anda untuk melihat detail pemesanan atau hubungi customer service kami jika Anda memiliki pertanyaan.</p>
                    
                    <p>Terima kasih telah memilih Elang Lembah Tourism.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Elang Lembah Tourism. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $this->email->setMessage($message);

        // Coba kirim email
        if ($this->email->send()) {
            return true;
        } else {
            log_message('error', 'Failed to send status update email: ' . $this->email->printDebugger(['headers']));
            return false;
        }
    }

    protected function sendPaymentVerificationEmail($user, $pemesanan, $pembayaran, $status, $keterangan)
    {
        // Pastikan email pelanggan tersedia
        if (empty($user['email'])) {
            return false;
        }

        // Ambil data paket wisata
        $paket = $this->paketModel->find($pemesanan['idpaket']);

        // Status dalam bahasa Indonesia
        $status_text = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Pembayaran Dikonfirmasi',
            'rejected' => 'Pembayaran Ditolak',
        ];

        // Siapkan email
        $this->email->setTo($user['email']);
        $this->email->setFrom('noreply@elanglembah.com', 'Elang Lembah Tourism');
        $this->email->setSubject('Konfirmasi Pembayaran #' . $pemesanan['kode_booking']);

        // Buat body email
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a6fdc; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                .details { background-color: #f9f9f9; padding: 15px; margin: 15px 0; }
                .status { font-weight: bold; }
                .status-verified { color: #28a745; }
                .status-rejected { color: #dc3545; }
                .status-pending { color: #ffc107; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Konfirmasi Pembayaran</h2>
                </div>
                <div class='content'>
                    <p>Halo <b>{$user['name']}</b>,</p>";

        if ($status == 'verified') {
            $message .= "<p>Pembayaran Anda untuk pemesanan paket wisata telah <b>DIKONFIRMASI</b>.</p>";

            if (isset($pembayaran['tipe_pembayaran']) && $pembayaran['tipe_pembayaran'] == 'dp') {
                $message .= "<p>Anda telah membayar DP sebesar 50%. Jangan lupa untuk melakukan pelunasan sebelum tanggal keberangkatan.</p>";
            } else {
                $message .= "<p>Pembayaran lunas Anda telah diterima. Terima kasih!</p>";
            }
        } else if ($status == 'rejected') {
            $message .= "<p>Maaf, pembayaran Anda untuk pemesanan paket wisata <b>DITOLAK</b>.</p>";
            $message .= "<p>Pemesanan Anda telah dibatalkan secara otomatis. Silakan melakukan pemesanan ulang jika Anda masih berminat.</p>";
        }

        $message .= "
                    <div class='details'>
                        <p><strong>Kode Booking:</strong> {$pemesanan['kode_booking']}</p>
                        <p><strong>Paket Wisata:</strong> {$paket['namapaket']}</p>
                        <p><strong>Tanggal Berangkat:</strong> " . date('d M Y', strtotime($pemesanan['tgl_berangkat'])) . "</p>";

        if (isset($pembayaran['jumlah_bayar'])) {
            $message .= "<p><strong>Jumlah Pembayaran:</strong> Rp " . number_format($pembayaran['jumlah_bayar'], 0, ',', '.') . "</p>";
        }

        if (isset($pembayaran['tipe_pembayaran'])) {
            $message .= "<p><strong>Tipe Pembayaran:</strong> " . ($pembayaran['tipe_pembayaran'] == 'dp' ? 'DP (50%)' : 'Lunas') . "</p>";
        }

        $message .= "<p><strong>Status Pembayaran:</strong> <span class='status status-{$status}'>{$status_text[$status]}</span></p>";

        if (!empty($keterangan)) {
            $message .= "<p><strong>Keterangan:</strong> {$keterangan}</p>";
        }

        $message .= "
                    </div>
                    
                    <p>Silakan login ke akun Anda untuk melihat detail pemesanan atau hubungi customer service kami jika Anda memiliki pertanyaan.</p>
                    
                    <p>Terima kasih telah memilih Elang Lembah Tourism.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Elang Lembah Tourism. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $this->email->setMessage($message);

        // Coba kirim email
        if ($this->email->send()) {
            return true;
        } else {
            log_message('error', 'Failed to send payment verification email: ' . $this->email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Halaman formulir pembuatan pemesanan oleh admin
     */
    public function create()
    {
        // Load model pelanggan
        $pelangganModel = new \App\Models\PelangganModel();

        $data = [
            'title' => 'Buat Pemesanan Baru',
            'paket' => $this->paketModel->where('statuspaket', 'active')->findAll(),
            'users' => $this->userModel->where('role', 'user')->findAll(),
            'pelanggan' => $pelangganModel->getPelangganWithUser() // Get pelanggan with associated user data
        ];

        return view('admin/pemesanan/create', $data);
    }

    /**
     * Proses pembuatan pemesanan oleh admin
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'idpaket' => 'required|',
            'iduser' => 'required|',
            'tgl_berangkat' => 'required|valid_date',
            'jumlah_peserta' => 'required|numeric|greater_than[0]',
            'catatan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Mohon periksa kembali form isian Anda.')
                ->with('validation', $this->validator->getErrors())
                ->withInput();
        }

        // Ambil data input
        $id_paket = $this->request->getPost('idpaket');
        $id_user = $this->request->getPost('iduser');
        $tgl_berangkat = $this->request->getPost('tgl_berangkat');
        $jumlah_peserta = (int)$this->request->getPost('jumlah_peserta');
        $catatan = $this->request->getPost('catatan');
        $status_pemesanan = $this->request->getPost('status') ?? 'confirmed';

        // Validasi paket dan user
        $paket = $this->paketModel->find($id_paket);
        $user = $this->userModel->find($id_user);

        if (!$paket) {
            return redirect()->back()
                ->with('error', 'Paket wisata tidak ditemukan.')
                ->withInput();
        }

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Pelanggan tidak ditemukan.')
                ->withInput();
        }

        // Hitung tanggal selesai berdasarkan durasi paket
        $durasi = isset($paket['durasi']) && !empty($paket['durasi']) ? (int)$paket['durasi'] : 1;
        $tgl_selesai = date('Y-m-d', strtotime("$tgl_berangkat + " . ($durasi - 1) . " days"));

        // Cek ketersediaan paket pada tanggal yang dipilih
        if (!$this->pemesananModel->cekKetersediaan($id_paket, $tgl_berangkat, $tgl_selesai)) {
            return redirect()->back()
                ->with('error', 'Paket tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.')
                ->withInput();
        }

        // Generate kode booking
        $kode_booking = 'ELP' . date('Ymd') . rand(1000, 9999);

        // Hitung total biaya
        $total_biaya = $paket['harga'];

        // Simpan data pemesanan
        $data_pemesanan = [
            'idpaket' => $id_paket,
            'iduser' => $id_user,
            'kode_booking' => $kode_booking,
            'tanggal' => date('Y-m-d H:i:s'),
            'tgl_berangkat' => $tgl_berangkat,
            'tgl_selesai' => $tgl_selesai,
            'harga' => $paket['harga'],
            'jumlah_peserta' => $jumlah_peserta,
            'totalbiaya' => $total_biaya,
            'catatan' => "pemesanan dilakukan oleh admin",
            'status' => $status_pemesanan,
        ];

        // Insert pemesanan ke database
        $this->pemesananModel->insert($data_pemesanan);
        $id_pemesanan = $this->pemesananModel->getInsertID();

        // Jika status confirmed, buat juga pembayaran yang langsung terverifikasi
        if ($status_pemesanan == 'confirmed') {
            $data_pembayaran = [
                'idpesan' => $id_pemesanan,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'jumlah_bayar' => $total_biaya,
                'metode_pembayaran' => 'Offline',
                'bukti_bayar' => 'offline_payment.jpg', // default image for offline payments
                'tipe_pembayaran' => 'lunas',
                'status_pembayaran' => 'verified',
                'keterangan' => 'Pembayaran offline (pemesanan oleh admin)'
            ];

            $this->pembayaranModel->insert($data_pembayaran);

            // Kirim email notifikasi ke pelanggan
            $this->sendBookingConfirmationEmail($user, $data_pemesanan, $paket);
        }

        return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
            ->with('success', 'Pemesanan berhasil dibuat.');
    }

    /**
     * Kirim email konfirmasi pemesanan ke pelanggan
     */
    protected function sendBookingConfirmationEmail($user, $pemesanan, $paket)
    {
        // Pastikan email pelanggan tersedia
        if (empty($user['email'])) {
            return false;
        }

        // Siapkan email
        $this->email->setTo($user['email']);
        $this->email->setFrom('noreply@elanglembah.com', 'Elang Lembah Tourism');
        $this->email->setSubject('Konfirmasi Pemesanan #' . $pemesanan['kode_booking']);

        // Buat body email
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a6fdc; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                .details { background-color: #f9f9f9; padding: 15px; margin: 15px 0; }
                .status { font-weight: bold; color: #28a745; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Konfirmasi Pemesanan</h2>
                </div>
                <div class='content'>
                    <p>Halo <b>{$user['name']}</b>,</p>
                    <p>Pemesanan paket wisata Anda telah <b>DIKONFIRMASI</b>.</p>
                    
                    <div class='details'>
                        <p><strong>Kode Booking:</strong> {$pemesanan['kode_booking']}</p>
                        <p><strong>Paket Wisata:</strong> {$paket['namapaket']}</p>
                        <p><strong>Tanggal Berangkat:</strong> " . date('d M Y', strtotime($pemesanan['tgl_berangkat'])) . "</p>
                        <p><strong>Tanggal Selesai:</strong> " . date('d M Y', strtotime($pemesanan['tgl_selesai'])) . "</p>
                        <p><strong>Jumlah Peserta:</strong> {$pemesanan['jumlah_peserta']} orang</p>
                        <p><strong>Total Biaya:</strong> Rp " . number_format($pemesanan['totalbiaya'], 0, ',', '.') . "</p>
                        <p><strong>Status:</strong> <span class='status'>Dikonfirmasi</span></p>
                    </div>
                    
                    <p>Pembayaran Anda telah diterima. Terima kasih!</p>
                    
                    <p>Silakan login ke akun Anda untuk melihat detail pemesanan atau hubungi customer service kami jika Anda memiliki pertanyaan.</p>
                    
                    <p>Terima kasih telah memilih Elang Lembah Tourism.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Elang Lembah Tourism. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $this->email->setMessage($message);

        // Coba kirim email
        if ($this->email->send()) {
            return true;
        } else {
            log_message('error', 'Failed to send booking confirmation email: ' . $this->email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Menampilkan form edit pemesanan
     */
    public function edit($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            return redirect()->to('/admin/pemesanan')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Cek apakah pemesanan sudah completed, jika ya redirect ke detail
        if ($pemesanan['status'] === 'completed') {
            return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
                ->with('error', 'Pemesanan yang sudah selesai tidak dapat diedit');
        }

        // Load model pelanggan
        $pelangganModel = new \App\Models\PelangganModel();

        // Get paket and user data
        $paket = $this->paketModel->find($pemesanan['idpaket']);
        $user = $this->userModel->find($pemesanan['iduser']);

        $data = [
            'title' => 'Edit Pemesanan',
            'pemesanan' => $pemesanan,
            'paket' => $this->paketModel->where('statuspaket', 'active')->findAll(),
            'users' => $this->userModel->where('role', 'user')->findAll(),
            'pelanggan' => $pelangganModel->getPelangganWithUser(),
            'selected_paket' => $paket,
            'selected_user' => $user,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/pemesanan/edit', $data);
    }

    /**
     * Memperbarui data pemesanan
     */
    public function update($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            return redirect()->to('/admin/pemesanan')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Cek apakah pemesanan sudah completed, jika ya redirect ke detail
        if ($pemesanan['status'] === 'completed') {
            return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
                ->with('error', 'Pemesanan yang sudah selesai tidak dapat diedit');
        }

        // Validasi input
        $rules = [
            'idpaket' => 'required',
            'iduser' => 'required',
            'tgl_berangkat' => 'required|valid_date',
            'jumlah_peserta' => 'required|numeric|greater_than[0]',
            'catatan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Mohon periksa kembali form isian Anda.')
                ->with('validation', $this->validator->getErrors())
                ->withInput();
        }

        // Ambil data input
        $id_paket = $this->request->getPost('idpaket');
        $id_user = $this->request->getPost('iduser');
        $tgl_berangkat = $this->request->getPost('tgl_berangkat');
        $jumlah_peserta = (int)$this->request->getPost('jumlah_peserta');
        $catatan = $this->request->getPost('catatan');
        $status_pemesanan = $this->request->getPost('status') ?? $pemesanan['status'];

        // Validasi paket dan user
        $paket = $this->paketModel->find($id_paket);
        $user = $this->userModel->find($id_user);

        if (!$paket) {
            return redirect()->back()
                ->with('error', 'Paket wisata tidak ditemukan.')
                ->withInput();
        }

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Pelanggan tidak ditemukan.')
                ->withInput();
        }

        // Hitung tanggal selesai berdasarkan durasi paket
        $durasi = isset($paket['durasi']) && !empty($paket['durasi']) ? (int)$paket['durasi'] : 1;
        $tgl_selesai = date('Y-m-d', strtotime("$tgl_berangkat + " . ($durasi - 1) . " days"));

        // Cek ketersediaan paket pada tanggal yang dipilih (kecuali pemesanan ini sendiri)
        if (!$this->pemesananModel->cekKetersediaan($id_paket, $tgl_berangkat, $tgl_selesai, $id_pemesanan)) {
            return redirect()->back()
                ->with('error', 'Paket tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.')
                ->withInput();
        }

        // Hitung total biaya
        $total_biaya = $paket['harga'];

        // Siapkan data untuk diupdate
        $data_pemesanan = [
            'idpaket' => $id_paket,
            'iduser' => $id_user,
            'tgl_berangkat' => $tgl_berangkat,
            'tgl_selesai' => $tgl_selesai,
            'harga' => $paket['harga'],
            'jumlah_peserta' => $jumlah_peserta,
            'totalbiaya' => $total_biaya,
            // 'catatan' => $catatan,
            'status' => $status_pemesanan,
        ];

        // Update data pemesanan
        $this->pemesananModel->update($id_pemesanan, $data_pemesanan);

        // Kirim email notifikasi ke pelanggan tentang perubahan
        $this->sendBookingUpdateEmail($user, $data_pemesanan, $paket);

        return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
            ->with('success', 'Pemesanan berhasil diperbarui.');
    }

    /**
     * Menghapus pemesanan
     */
    public function destroy($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            return redirect()->to('/admin/pemesanan')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Cek apakah pemesanan sudah completed, jika ya redirect ke detail
        if ($pemesanan['status'] === 'completed') {
            return redirect()->to('/admin/pemesanan/detail/' . $id_pemesanan)
                ->with('error', 'Pemesanan yang sudah selesai tidak dapat dihapus');
        }

        // Hapus semua pembayaran terkait terlebih dahulu
        $this->pembayaranModel->where('idpesan', $id_pemesanan)->delete();

        // Hapus pemesanan
        $this->pemesananModel->delete($id_pemesanan);

        return redirect()->to('/admin/pemesanan')
            ->with('success', 'Pemesanan berhasil dihapus.');
    }

    /**
     * Mengirim email notifikasi perubahan pemesanan
     */
    private function sendBookingUpdateEmail($user, $pemesanan, $paket)
    {
        $this->email->setFrom('no-reply@elanglembahtourism.com', 'Elang Lembah Tourism');
        $this->email->setTo($user['email']);
        $this->email->setSubject('Perubahan Informasi Pemesanan - Elang Lembah Tourism');

        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Perubahan Informasi Pemesanan</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                }
                .header {
                    background: #4e73df;
                    color: #fff;
                    padding: 20px;
                    text-align: center;
                }
                .content {
                    padding: 20px;
                    background: #fff;
                    border: 1px solid #ddd;
                }
                .footer {
                    text-align: center;
                    padding: 10px;
                    font-size: 12px;
                    color: #777;
                }
                .booking-details {
                    background: #f9f9f9;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 15px 0;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 15px;
                    background: #4e73df;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Perubahan Informasi Pemesanan</h2>
                </div>
                <div class='content'>
                    <p>Halo, <strong>{$user['name']}</strong>,</p>
                    
                    <p>Informasi pemesanan Anda telah diperbarui oleh admin kami. Detail pemesanan yang diperbarui adalah sebagai berikut:</p>
                    
                    <div class='booking-details'>
                        <p><strong>Paket Wisata:</strong> {$paket['namapaket']}</p>
                        <p><strong>Tanggal Keberangkatan:</strong> " . date('d-m-Y', strtotime($pemesanan['tgl_berangkat'])) . "</p>
                        <p><strong>Jumlah Peserta:</strong> {$pemesanan['jumlah_peserta']} orang</p>
                        <p><strong>Total Biaya:</strong> Rp " . number_format($pemesanan['totalbiaya'], 0, ',', '.') . "</p>
                    </div>
                    
                    <p>Silakan login ke akun Anda untuk melihat detail pemesanan atau hubungi customer service kami jika Anda memiliki pertanyaan.</p>
                    
                    <p>Terima kasih telah memilih Elang Lembah Tourism.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " Elang Lembah Tourism. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $this->email->setMessage($message);

        // Coba kirim email
        if ($this->email->send()) {
            return true;
        } else {
            log_message('error', 'Failed to send booking update email: ' . $this->email->printDebugger(['headers']));
            return false;
        }
    }
}

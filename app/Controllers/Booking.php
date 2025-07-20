<?php

namespace App\Controllers;

use App\Models\PaketWisataModel;
use App\Models\PemesananModel;
use App\Models\PembayaranModel;
use App\Models\KategoriModel;
use App\Models\UserModel;

class Booking extends BaseController
{
    protected $paketModel;
    protected $pemesananModel;
    protected $pembayaranModel;
    protected $kategoriModel;
    protected $userModel;

    public function __construct()
    {
        $this->paketModel = new PaketWisataModel();
        $this->pemesananModel = new PemesananModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->kategoriModel = new KategoriModel();
        $this->userModel = new UserModel();
    }

    public function create($id_paket)
    {
        $paket = $this->paketModel->find($id_paket);
        if (!$paket) {
            return redirect()->to('/paket')->with('error', 'Paket tidak ditemukan');
        }

        $data = [
            'title' => 'Pemesanan Paket',
            'paket' => $paket,
            'kategori' => $this->kategoriModel->findAll(),
        ];

        return view('booking/create', $data);
    }

    public function store()
    {
        // Set proper header for JSON response
        if ($this->request->isAJAX()) {
            $this->response->setHeader('Content-Type', 'application/json; charset=UTF-8');

            // Disable any debug output that could interfere with JSON response
            if (function_exists('ob_end_clean')) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
            }
        }

        try {
            // Sanitize input parameters
            $id_paket = $this->request->getPost('id_paket');

            // Server-side validation with sanitized id_paket
            $rules = [
                'tgl_berangkat' => 'required|valid_date',
                'jumlah_peserta' => 'required|numeric',
                'nama_pemesan' => 'required|min_length[3]',
                'email_pemesan' => 'required|valid_email',
                'telp_pemesan' => 'required|min_length[10]'
            ];

            // Check if request is AJAX
            $isAjax = $this->request->isAJAX();

            if (!$this->validate($rules)) {
                if ($isAjax) {
                    log_message('debug', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Terdapat kesalahan pada form. Silakan periksa kembali.',
                        'errors' => $this->validator->getErrors()
                    ]);
                } else {
                    // If not Ajax, redirect back with errors and input data
                    return redirect()->back()
                        ->with('error', 'Mohon periksa kembali form isian Anda.')
                        ->with('validation', $this->validator->getErrors())
                        ->withInput();
                }
            }

            // Validate that id_paket is a valid number and exists in database
            if ($id_paket <= 0) {
                log_message('debug', 'Invalid ID paket: ' . $id_paket);
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'ID paket tidak valid',
                        'errors' => ['id_paket' => 'ID paket tidak valid']
                    ]);
                } else {
                    return redirect()->to('/paket')->with('error', 'ID paket tidak valid');
                }
            }

            $paket = $this->paketModel->find($id_paket);
            if (!$paket) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Paket tidak ditemukan',
                        'errors' => ['id_paket' => 'Paket tidak ditemukan dengan ID tersebut']
                    ]);
                } else {
                    return redirect()->to('/paket')->with('error', 'Paket tidak ditemukan');
                }
            }

            $tgl_berangkat = $this->request->getPost('tgl_berangkat');
            $jumlah_peserta = (int)$this->request->getPost('jumlah_peserta');

            // Calculate end date based on package duration
            // Handle case when durasi is not set by using a default value of 1 day
            $durasi = isset($paket['durasi']) && !empty($paket['durasi']) ? (int)$paket['durasi'] : 1;
            $tgl_selesai = date('Y-m-d', strtotime("$tgl_berangkat + " . ($durasi - 1) . " days"));

            // Check if package is available on the requested dates
            if (!$this->pemesananModel->cekKetersediaan($id_paket, $tgl_berangkat, $tgl_selesai)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Paket tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.'
                    ]);
                } else {
                    return redirect()->to('/booking/create/' . $id_paket)->with('error', 'Paket tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.');
                }
            }

            // Generate kode pemesanan
            $kode_booking = 'ELP' . date('Ymd') . rand(1000, 9999);

            // Harga total sama dengan harga paket (tidak dikalikan jumlah peserta)
            $total_harga = $paket['harga'];

            // Set expiration time for booking and payment (10 minutes)
            $expiration_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Simpan data pemesanan
            $data_pemesanan = [
                'idpaket' => $id_paket,
                'iduser' => session()->get('user_id'),
                'kode_booking' => $kode_booking,
                'tanggal' => date('Y-m-d H:i:s'),
                'tgl_berangkat' => $tgl_berangkat,
                'tgl_selesai' => $tgl_selesai,
                'harga' => $paket['harga'],
                'jumlah_peserta' => $jumlah_peserta,
                'totalbiaya' => $total_harga,
                'catatan' => $this->request->getPost('catatan'),
                'status' => 'pending', // This status will be used for payment
                'expired_at' => $expiration_time // Also set expiration in pemesanan table
            ];

            // Insert the booking record
            $this->pemesananModel->insert($data_pemesanan);
            $id_pemesanan = $this->pemesananModel->getInsertID();

            // Create payment record immediately
            $data_pembayaran = [
                'idpesan' => $id_pemesanan,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'jumlah_bayar' => $total_harga,
                'metode_pembayaran' => '',
                'status_pembayaran' => 'pending',
                'expired_at' => $expiration_time,
                'keterangan' => 'Menunggu pembayaran'
            ];

            $this->pembayaranModel->insert($data_pembayaran);
            log_message('debug', 'Payment record created during booking for ID: ' . $id_pemesanan);

            // Return response based on request type
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pemesanan berhasil dibuat',
                    'redirect_url' => base_url('booking/payment/' . $id_pemesanan)
                ]);
            } else {
                // Redirect directly to payment page instead of detail page
                return redirect()->to('/booking/payment/' . $id_pemesanan)->with('success', 'Pemesanan berhasil dibuat. Silakan lakukan pembayaran.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in store method: ' . $e->getMessage());

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.'
                ]);
            } else {
                return redirect()->to('/paket')->with('error', 'Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.');
            }
        }
    }

    public function detail($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        $paket = $this->paketModel->find($pemesanan['idpaket']);
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)->first();

        // Gabungkan data paket dan pemesanan untuk view
        $pemesananData = $pemesanan;
        $pemesananData['namapaket'] = $paket['namapaket'];
        $pemesananData['foto'] = $paket['foto'];

        $data = [
            'title' => 'Detail Pemesanan',
            'pemesanan' => $pemesananData,
            'paket' => $paket,
            'pembayaran' => $pembayaran,
            'kategori' => $this->kategoriModel->findAll(),
        ];

        return view('booking/detail', $data);
    }

    public function history()
    {
        // Load the RescheduleRequestModel
        $rescheduleModel = new \App\Models\RescheduleRequestModel();

        // Gunakan model dengan join yang sudah disediakan
        $data_pemesanan = $this->pemesananModel->getUserPemesanan(session()->get('user_id'));

        log_message('debug', 'Fetched user bookings: ' . count($data_pemesanan));

        // Format data untuk debugging
        if (!empty($data_pemesanan)) {
            log_message('debug', 'First booking data: ' . json_encode([
                'idpesan' => $data_pemesanan[0]['idpesan'] ?? 'N/A',
                'kode_booking' => $data_pemesanan[0]['kode_booking'] ?? 'N/A',
                'status' => $data_pemesanan[0]['status'] ?? 'N/A',
                'namapaket' => $data_pemesanan[0]['namapaket'] ?? 'N/A'
            ]));
        }

        // Get reschedule requests for each booking
        $reschedule_requests = [];
        foreach ($data_pemesanan as $pemesanan) {
            $requests = $rescheduleModel->getByBookingId($pemesanan['idpesan']);
            if (!empty($requests)) {
                $reschedule_requests[$pemesanan['idpesan']] = $requests;
            }
        }

        $data = [
            'title' => 'Riwayat Pemesanan',
            'pemesanan' => $data_pemesanan,
            'kategori' => $this->kategoriModel->findAll(),
            'reschedule_requests' => $reschedule_requests
        ];

        return view('booking/history', $data);
    }

    public function cancel($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        $this->pemesananModel->update($id_pemesanan, ['status' => 'cancelled']);
        return redirect()->to('/booking/history')->with('success', 'Pemesanan berhasil dibatalkan');
    }

    public function payment($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id') || $pemesanan['status'] != 'pending') {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak valid untuk pembayaran');
        }

        $paket = $this->paketModel->find($pemesanan['idpaket']);

        // Log the beginning of the payment process
        log_message('debug', '===== PAYMENT PAGE LOAD =====');
        log_message('debug', 'Booking ID: ' . $id_pemesanan);
        log_message('debug', 'User ID: ' . session()->get('user_id'));
        log_message('debug', 'Booking Status: ' . $pemesanan['status']);
        log_message('debug', 'Booking Expiration: ' . ($pemesanan['expired_at'] ?? 'Not set'));

        // Check if there's an existing payment record
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)->first();
        log_message('debug', 'Payment record found: ' . ($pembayaran ? 'Yes' : 'No'));

        if ($pembayaran) {
            log_message('debug', 'Payment ID: ' . ($pembayaran['idbayar'] ?? 'N/A'));
            log_message('debug', 'Payment Status: ' . ($pembayaran['status_pembayaran'] ?? 'N/A'));
            log_message('debug', 'Payment Expiration: ' . ($pembayaran['expired_at'] ?? 'N/A'));
        }

        // Determine expiration time in this priority:
        // 1. Use payment record's expired_at if it exists
        // 2. Use booking record's expired_at if it exists
        // 3. Create a new expiration time (10 minutes from now)
        $expiration_time = null;

        if ($pembayaran && isset($pembayaran['expired_at']) && !empty($pembayaran['expired_at'])) {
            $expiration_time = $pembayaran['expired_at'];
            log_message('debug', 'Using payment record expiration time: ' . $expiration_time);
        } else if (isset($pemesanan['expired_at']) && !empty($pemesanan['expired_at'])) {
            $expiration_time = $pemesanan['expired_at'];
            log_message('debug', 'Using booking record expiration time: ' . $expiration_time);
        } else {
            $expiration_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            log_message('debug', 'Created new expiration time: ' . $expiration_time);

            // Update the expiration time in the booking record if it's not set
            if (!isset($pemesanan['expired_at']) || empty($pemesanan['expired_at'])) {
                $this->pemesananModel->update($id_pemesanan, ['expired_at' => $expiration_time]);
                log_message('debug', 'Updated booking record with expiration time');
            }
        }

        // Create or update the payment record
        if (!$pembayaran) {
            // Create a new payment record if none exists
            $data_pembayaran = [
                'idpesan' => $id_pemesanan,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'jumlah_bayar' => $pemesanan['totalbiaya'],
                'metode_pembayaran' => '',  // Will be updated when user selects payment method
                'status_pembayaran' => 'pending',
                'expired_at' => $expiration_time,
                'keterangan' => 'Menunggu pembayaran'
            ];

            try {
                $result = $this->pembayaranModel->insert($data_pembayaran);
                log_message('debug', 'Payment record created with ID: ' . $this->pembayaranModel->getInsertID());
            } catch (\Exception $e) {
                log_message('error', 'Error creating payment record: ' . $e->getMessage());
            }
        } else if (empty($pembayaran['expired_at'])) {
            // Update the existing payment record with the expiration time if it doesn't have one
            try {
                $this->pembayaranModel->update($pembayaran['idbayar'], ['expired_at' => $expiration_time]);
                log_message('debug', 'Updated existing payment record with expiration time');
            } catch (\Exception $e) {
                log_message('error', 'Error updating payment record: ' . $e->getMessage());
            }
        }

        // Re-fetch the payment record to ensure we have the latest data
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)->first();

        // Double-check the final expiration time we're sending to the view
        log_message('debug', 'FINAL expiration_time for view: ' . $expiration_time);

        // Calculate remaining time to verify it's correct
        $now = new \DateTime();
        $exp = new \DateTime($expiration_time);
        $remaining_seconds = max(0, $exp->getTimestamp() - $now->getTimestamp());
        $remaining_minutes = floor($remaining_seconds / 60);
        $remaining_secs = $remaining_seconds % 60;
        log_message('debug', 'Calculated remaining time: ' . $remaining_minutes . ':' . $remaining_secs . ' (' . $remaining_seconds . ' seconds)');

        // Gabungkan data paket dan pemesanan untuk view
        $pemesananData = $pemesanan;
        $pemesananData['namapaket'] = $paket['namapaket'];
        $pemesananData['foto'] = $paket['foto'];

        $data = [
            'title' => 'Pembayaran',
            'pemesanan' => $pemesananData,
            'paket' => $paket,
            'kategori' => $this->kategoriModel->findAll(),
            'pembayaran' => $pembayaran,
            'expiration_time' => $expiration_time,
            'remaining_seconds' => $remaining_seconds
        ];

        return view('booking/payment', $data);
    }

    public function savePayment()
    {
        // Server-side validation
        $rules = [
            'idpesan' => 'required|numeric',
            'metode_pembayaran' => 'required',
            'tipe_pembayaran' => 'required|in_list[dp,lunas]',
            'bukti_pembayaran' => 'uploaded[bukti_pembayaran]|mime_in[bukti_pembayaran,image/jpg,image/jpeg,image/png,image/gif]|max_size[bukti_pembayaran,2048]'
        ];

        if (!$this->validate($rules)) {
            // If validation fails, redirect back with errors
            return redirect()->back()
                ->with('error', 'Upload bukti pembayaran gagal: ' . implode(', ', $this->validator->getErrors()))
                ->withInput();
        }

        $id_pemesanan = $this->request->getPost('idpesan');
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        $bukti_pembayaran = $this->request->getFile('bukti_pembayaran');

        // Ambil tipe pembayaran
        $tipe_pembayaran = $this->request->getPost('tipe_pembayaran');
        if (!in_array($tipe_pembayaran, ['dp', 'lunas'])) {
            $tipe_pembayaran = 'lunas'; // Default ke pembayaran penuh jika tidak valid
        }

        // Hitung jumlah yang harus dibayar berdasarkan tipe pembayaran
        $totalbiaya = $pemesanan['totalbiaya'];
        $jumlah_bayar = $tipe_pembayaran === 'dp' ? ($totalbiaya * 0.5) : $totalbiaya;

        // Upload the payment proof file
        $newName = $pemesanan['kode_booking'] . '_' . time() . '.' . $bukti_pembayaran->getExtension();
        $bukti_pembayaran->move(ROOTPATH . 'public/uploads/payments', $newName);

        // Check if payment record exists and get its ID
        $existingPayment = $this->pembayaranModel->where('idpesan', $id_pemesanan)->first();

        // Data to update/insert
        $payment_data = [
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'tipe_pembayaran' => $tipe_pembayaran,
            'jumlah_bayar' => $jumlah_bayar,
            'bukti_bayar' => $newName,
            'keterangan' => $this->request->getPost('keterangan') ?? ''
        ];

        if ($existingPayment) {
            // Update existing payment record
            log_message('debug', 'Updating existing payment record ID: ' . $existingPayment['idbayar']);
            $this->pembayaranModel->update($existingPayment['idbayar'], $payment_data);
        } else {
            // Create new payment record if one doesn't exist (shouldn't normally happen)
            log_message('debug', 'Creating new payment record for booking: ' . $id_pemesanan);
            $payment_data = array_merge($payment_data, [
                'idpesan' => $id_pemesanan,
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'status_pembayaran' => 'pending',
                'expired_at' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
            ]);
            $this->pembayaranModel->insert($payment_data);
        }


        $status = 'waiting_confirmation';
        $this->pemesananModel->update($id_pemesanan, ['status' => $status]);

        // Log payment details
        log_message('info', "Payment saved - ID: {$id_pemesanan}, Type: {$tipe_pembayaran}, Amount: {$jumlah_bayar}, Status: {$status}");

        // Set appropriate success message
        $successMessage = $tipe_pembayaran === 'dp'
            ? 'Pembayaran DP berhasil diupload. Silakan lakukan pelunasan sebelum tanggal keberangkatan.'
            : 'Pembayaran berhasil diupload dan sedang menunggu konfirmasi admin.';

        // Redirect to history with success message
        return redirect()->to('/booking/history')->with('success', $successMessage);
    }

    /**
     * Download E-Ticket for confirmed bookings
     */
    public function downloadTicket($id_pemesanan)
    {
        // Increase execution time limit for PDF generation
        set_time_limit(120); // 2 minutes
        ini_set('memory_limit', '256M');

        // Verify booking exists and belongs to user
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Check if booking status allows ticket download
        if (!in_array($pemesanan['status'], ['confirmed', 'completed'])) {
            return redirect()->to('/booking/detail/' . $id_pemesanan)->with('error', 'E-Ticket hanya tersedia untuk pemesanan yang sudah dikonfirmasi');
        }

        // Get related data
        $paket = $this->paketModel->find($pemesanan['idpaket']);
        $user = $this->userModel->find($pemesanan['iduser']);

        try {
            // Generate QR code
            $qr_code = $this->generateQRCode($pemesanan['kode_booking']);

            // Check if DOMPDF class exists
            if (!class_exists('Dompdf\Dompdf')) {
                return redirect()->to('/booking/detail/' . $id_pemesanan)->with('error', 'PDF library not found. Please install dompdf/dompdf package.');
            }

            // Load the PDF library
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);
            $options->set('debugLayout', false);
            $options->set('chroot', FCPATH);
            $dompdf->setOptions($options);

            // Prepare the data for the view
            $data = [
                'pemesanan' => $pemesanan,
                'paket' => $paket,
                'user' => $user,
                'qr_code' => $qr_code,
            ];

            // Load the view containing the ticket template
            $html = view('booking/ticket_template', $data);

            // Load HTML into Dompdf
            $dompdf->loadHtml($html);

            // Set paper size and orientation (A4 Portrait)
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to browser with a filename
            return $dompdf->stream("ETicket_" . $pemesanan['kode_booking'] . ".pdf", ["Attachment" => true]);
        } catch (\Exception $e) {
            log_message('error', 'Error generating ticket: ' . $e->getMessage());
            return redirect()->to('/booking/detail/' . $id_pemesanan)->with('error', 'Terjadi kesalahan saat membuat E-Ticket: ' . $e->getMessage());
        }
    }

    /**
     * Download payment invoice
     */
    public function downloadInvoice($id_pemesanan)
    {
        // Increase execution time limit for PDF generation
        set_time_limit(120); // 2 minutes
        ini_set('memory_limit', '256M');

        // Verify booking exists and belongs to user
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Get payment data
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)
            ->where('status_pembayaran', 'verified')
            ->orderBy('tanggal_bayar', 'DESC')
            ->first();

        if (!$pembayaran) {
            return redirect()->to('/booking/detail/' . $id_pemesanan)->with('error', 'Data pembayaran tidak ditemukan atau belum diverifikasi');
        }

        // Get related data
        $paket = $this->paketModel->find($pemesanan['idpaket']);
        $user = $this->userModel->find($pemesanan['iduser']);

        try {
            // Check if DOMPDF class exists
            if (!class_exists('Dompdf\Dompdf')) {
                return redirect()->to('/booking/detail/' . $id_pemesanan)->with('error', 'PDF library not found. Please install dompdf/dompdf package.');
            }

            // Load the PDF library
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);
            $options->set('debugLayout', false);
            $options->set('chroot', FCPATH);
            $dompdf->setOptions($options);

            // Prepare the data for the view
            $data = [
                'pemesanan' => $pemesanan,
                'pembayaran' => $pembayaran,
                'paket' => $paket,
                'user' => $user,
                'tanggal_invoice' => date('Y-m-d'),
                'nomor_invoice' => 'INV/' . date('Ymd') . '/' . $pemesanan['kode_booking'],
            ];

            // Load the view containing the invoice template
            $html = view('booking/invoice_template', $data);

            // Load HTML into Dompdf
            $dompdf->loadHtml($html);

            // Set paper size and orientation (A4 Portrait)
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to browser with a filename
            return $dompdf->stream("Invoice_" . $pemesanan['kode_booking'] . ".pdf", ["Attachment" => true]);
        } catch (\Exception $e) {
            log_message('error', 'Error generating invoice: ' . $e->getMessage());
            return redirect()->to('/booking/detail/' . $id_pemesanan)->with('error', 'Terjadi kesalahan saat membuat Invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR Code for ticket
     */
    private function generateQRCode($kode_booking)
    {
        try {
            // Check if the QR code library exists
            if (!class_exists('chillerlan\QRCode\QRCode')) {
                log_message('error', 'QR code library not found. Please install chillerlan/php-qrcode package.');
                return base_url('assets/images/qr-placeholder.png');
            }

            // Create directory if it doesn't exist
            $directory = ROOTPATH . 'public/uploads/qrcodes';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            // Generate QR code file path
            $qrCodePath = $directory . '/' . $kode_booking . '.png';
            $qrCodeWebPath = base_url('uploads/qrcodes/' . $kode_booking . '.png');

            // Check if QR code already exists
            if (file_exists($qrCodePath)) {
                return $qrCodeWebPath;
            }

            // Generate QR code content (ticket verification URL)
            $qrContent = site_url('verify/ticket/' . $kode_booking);

            // Create QR code settings
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => false,
            ]);

            // Generate and save QR code
            $qrCode = new \chillerlan\QRCode\QRCode($options);
            $qrCodeImage = $qrCode->render($qrContent);
            file_put_contents($qrCodePath, $qrCodeImage);

            return $qrCodeWebPath;
        } catch (\Exception $e) {
            log_message('error', 'Error generating QR code: ' . $e->getMessage());
            return base_url('assets/images/qr-placeholder.png');
        }
    }

    // Check payment expiration - method to be called via AJAX
    public function checkPaymentExpiration($id_pemesanan)
    {
        // Log the request
        log_message('debug', '=== CHECKING PAYMENT EXPIRATION ===');
        log_message('debug', 'Booking ID: ' . $id_pemesanan);

        // First check if the booking exists and is still pending
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            log_message('debug', 'Booking not found: ' . $id_pemesanan);
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        log_message('debug', 'Booking status: ' . $pemesanan['status']);

        // Check if booking already cancelled
        if ($pemesanan['status'] === 'cancelled') {
            log_message('debug', 'Booking already cancelled');
            return $this->response->setJSON([
                'status' => true,
                'expired' => true,
                'message' => 'Pemesanan sudah dibatalkan'
            ]);
        }

        // Check for booking expiration first
        $now = new \DateTime();
        $booking_expiry = null;

        if (isset($pemesanan['expired_at']) && !empty($pemesanan['expired_at'])) {
            $booking_expiry = new \DateTime($pemesanan['expired_at']);
            $time_diff = $booking_expiry->getTimestamp() - $now->getTimestamp();
            log_message('debug', 'Booking expiration time: ' . $pemesanan['expired_at'] . ' (diff: ' . $time_diff . ' seconds)');

            // If booking has expired, update status and return expired response
            if ($now > $booking_expiry) {
                log_message('debug', 'Booking has expired');

                // Update order status if it's still pending or waiting_payment
                if (in_array($pemesanan['status'], ['pending', 'waiting_payment'])) {
                    log_message('debug', 'Updating booking status to cancelled');

                    try {
                        $update_result = $this->pemesananModel->update($id_pemesanan, [
                            'status' => 'cancelled'
                        ]);

                        log_message('debug', 'Update result: ' . ($update_result ? 'Success' : 'Failed'));
                    } catch (\Exception $e) {
                        log_message('error', 'Error updating booking status: ' . $e->getMessage());
                    }
                }

                return $this->response->setJSON([
                    'status' => true,
                    'expired' => true,
                    'message' => 'Waktu pemesanan telah habis',
                    'booking_status' => 'cancelled'
                ]);
            }
        } else {
            log_message('debug', 'No expiration time set for booking');
        }

        // Check if there's an active payment record
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)
            ->where('status_pembayaran', 'pending')
            ->first();

        log_message('debug', 'Payment record found: ' . ($pembayaran ? 'Yes' : 'No'));

        // If no payment record yet, use booking expiration time
        if (!$pembayaran) {
            // Calculate time left based on booking expiration or default to 10 minutes
            $remaining = 600; // 10 minutes in seconds
            $expiration_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            if ($booking_expiry) {
                $remaining = max(0, $booking_expiry->getTimestamp() - $now->getTimestamp());
                $expiration_time = $pemesanan['expired_at'];
            }

            log_message('debug', 'No payment record found. Using booking timer or default: ' . $remaining . ' seconds');

            // If time has expired but status not updated, update it now
            if ($remaining <= 0 && in_array($pemesanan['status'], ['pending', 'waiting_payment'])) {
                log_message('debug', 'Remaining time is zero. Updating booking status to cancelled');
                $this->pemesananModel->update($id_pemesanan, [
                    'status' => 'cancelled'
                ]);

                return $this->response->setJSON([
                    'status' => true,
                    'expired' => true,
                    'message' => 'Waktu pemesanan telah habis',
                    'booking_status' => 'cancelled'
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'expired' => false,
                'remaining' => $remaining,
                'expiration' => $expiration_time,
                'booking_status' => $pemesanan['status']
            ]);
        }

        log_message('debug', 'Payment expiration time: ' . $pembayaran['expired_at']);

        // If payment record exists, check if it's expired
        $payment_expiry = new \DateTime($pembayaran['expired_at']);
        $payment_time_diff = $payment_expiry->getTimestamp() - $now->getTimestamp();
        log_message('debug', 'Payment time difference: ' . $payment_time_diff . ' seconds');

        if ($now > $payment_expiry) {
            log_message('debug', 'Payment has expired');

            // Payment expired, update status
            $this->pembayaranModel->update($pembayaran['idbayar'], [
                'status_pembayaran' => 'rejected'
            ]);
            log_message('debug', 'Updated payment status to rejected');

            // Update order status if it's still pending or waiting_payment
            if (in_array($pemesanan['status'], ['pending', 'waiting_payment'])) {
                $this->pemesananModel->update($id_pemesanan, [
                    'status' => 'cancelled'
                ]);
                log_message('debug', 'Updated booking status to cancelled');
            }

            return $this->response->setJSON([
                'status' => true,
                'expired' => true,
                'message' => 'Waktu pembayaran telah habis',
                'booking_status' => 'cancelled'
            ]);
        }

        // Calculate remaining time in seconds
        $remaining = $payment_expiry->getTimestamp() - $now->getTimestamp();
        log_message('debug', 'Remaining time: ' . $remaining . ' seconds');

        return $this->response->setJSON([
            'status' => true,
            'expired' => false,
            'remaining' => $remaining,
            'expiration' => $pembayaran['expired_at'],
            'booking_status' => $pemesanan['status']
        ]);
    }

    // Test method to create payment record - remove in production
    public function testCreatePayment($id_pemesanan)
    {
        // Check if pemesanan exists
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        // Create a new payment record
        $expiration_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $data_pembayaran = [
            'idpesan' => $id_pemesanan,
            'tanggal_bayar' => date('Y-m-d H:i:s'),
            'jumlah_bayar' => $pemesanan['totalbiaya'],
            'metode_pembayaran' => 'Test',
            'status_pembayaran' => 'pending',
            'expired_at' => $expiration_time,
            'keterangan' => 'Test payment record'
        ];

        try {
            $result = $this->pembayaranModel->insert($data_pembayaran);
            $insert_id = $this->pembayaranModel->getInsertID();

            // Return success result
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Payment record created successfully',
                'payment_id' => $insert_id,
                'expiration_time' => $expiration_time,
                'payment_data' => $data_pembayaran,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            // Return error result
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error creating payment record: ' . $e->getMessage(),
                'error_details' => [
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }

    /**
     * Check availability of a package for a date range via AJAX
     */
    public function checkAvailability()
    {
        // Return 403 if not an AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $id_paket = $this->request->getPost('id_paket');
        $tgl_berangkat = $this->request->getPost('tgl_berangkat');
        $tgl_selesai = $this->request->getPost('tgl_selesai');

        if (!$id_paket || !$tgl_berangkat || !$tgl_selesai) {
            return $this->response->setJSON([
                'available' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        // Check if package exists
        $paket = $this->paketModel->find($id_paket);
        if (!$paket) {
            return $this->response->setJSON([
                'available' => false,
                'message' => 'Package not found'
            ]);
        }

        // Check availability
        $available = $this->pemesananModel->cekKetersediaan($id_paket, $tgl_berangkat, $tgl_selesai);

        return $this->response->setJSON([
            'available' => $available,
            'message' => $available ? 'Available' : 'Not available'
        ]);
    }

    /**
     * Memeriksa semua pembayaran yang kedaluwarsa secara massal dan membatalkan
     * pemesanan yang kedaluwarsa. Metode ini digunakan oleh sistem background check.
     *
     * @return \CodeIgniter\HTTP\Response|array
     */
    public function checkAllExpiredPayments()
    {
        log_message('info', '=== CHECKING ALL EXPIRED PAYMENTS ===');
        $now = new \DateTime();
        $cancelledBookings = [];
        $cancelledCount = 0;

        // 1. Periksa pemesanan dengan status pending yang expired_at-nya sudah lewat
        $expiredBookings = $this->pemesananModel->where('status', 'pending')
            ->where('expired_at <', $now->format('Y-m-d H:i:s'))
            ->findAll();

        log_message('info', 'Found ' . count($expiredBookings) . ' expired bookings');

        foreach ($expiredBookings as $booking) {
            // Update status pemesanan menjadi cancelled
            $this->pemesananModel->update($booking['idpesan'], ['status' => 'cancelled']);
            log_message('info', 'Cancelled expired booking: ID=' . $booking['idpesan'] . ', Kode=' . $booking['kode_booking']);

            // Cari dan update status pembayaran terkait jika ada
            $payment = $this->pembayaranModel->where('idpesan', $booking['idpesan'])->first();
            if ($payment) {
                $this->pembayaranModel->update($payment['idbayar'], [
                    'status_pembayaran' => 'rejected',
                    'keterangan' => 'Pembayaran kedaluwarsa secara otomatis'
                ]);
                log_message('info', 'Updated related payment status: ID=' . $payment['idbayar']);
            }

            $cancelledBookings[] = [
                'booking_id' => $booking['idpesan'],
                'kode_booking' => $booking['kode_booking'],
                'action' => 'cancelled'
            ];
            $cancelledCount++;
        }

        // 2. Periksa pembayaran dengan status pending yang expired_at-nya sudah lewat
        $expiredPayments = $this->pembayaranModel->select('pembayaran.*, pemesanan.kode_booking, pemesanan.status as booking_status')
            ->join('pemesanan', 'pembayaran.idpesan = pemesanan.idpesan')
            ->where('pemesanan.status', 'pending')
            ->where('pembayaran.status_pembayaran', 'pending')
            ->where('pembayaran.expired_at <', $now->format('Y-m-d H:i:s'))
            ->findAll();

        log_message('info', 'Found ' . count($expiredPayments) . ' expired payments');

        foreach ($expiredPayments as $payment) {
            // Cek apakah pemesanan sudah dibatalkan sebelumnya
            if (!in_array($payment['idpesan'], array_column($cancelledBookings, 'booking_id'))) {
                // Update status pemesanan menjadi cancelled
                $this->pemesananModel->update($payment['idpesan'], ['status' => 'cancelled']);
                log_message('info', 'Cancelled booking due to expired payment: ID=' . $payment['idpesan']);

                // Update status pembayaran
                $this->pembayaranModel->update($payment['idbayar'], [
                    'status_pembayaran' => 'rejected',
                    'keterangan' => 'Pembayaran kedaluwarsa secara otomatis'
                ]);
                log_message('info', 'Updated payment status: ID=' . $payment['idbayar']);

                $cancelledBookings[] = [
                    'booking_id' => $payment['idpesan'],
                    'kode_booking' => $payment['kode_booking'],
                    'payment_id' => $payment['idbayar'],
                    'action' => 'cancelled'
                ];
                $cancelledCount++;
            }
        }

        // Kembalikan hasil dalam format yang sesuai
        $result = [
            'success' => true,
            'cancelled_count' => $cancelledCount,
            'details' => $cancelledBookings,
            'timestamp' => $now->format('Y-m-d H:i:s')
        ];

        // Jika dipanggil melalui API, kembalikan JSON response
        if (isset($this->response) && method_exists($this->response, 'setJSON')) {
            return $this->response->setJSON($result);
        }

        // Jika dipanggil secara langsung, kembalikan array
        return $result;
    }
}

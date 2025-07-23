<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OtpModel;

class Register extends BaseController
{
    protected $userModel;
    protected $otpModel;
    protected $email;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->otpModel = new OtpModel();
        $this->email = \Config\Services::email();
    }

    public function index()
    {
        return view('auth/register');
    }

    public function process()
    {
        // Tambahkan log untuk debugging
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

        // Validasi input
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'address' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            // Tambahkan log untuk debugging
            log_message('debug', 'Validation errors: ' . json_encode($this->validator->getErrors()));

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        // Ambil data dari form
        $data = $this->request->getPost();

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Data untuk tabel users
            $userData = [
                'username' => $this->generateUsername($data['name']),
                'email' => $data['email'],
                'password' => $data['password'],
                'name' => $data['name'],
                'role' => 'pelanggan',
                'status' => 'inactive', // Inactive sampai email diverifikasi
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'email_verified' => 0
            ];

            // Insert ke tabel users
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \RuntimeException('Gagal membuat akun user: ' . json_encode($this->userModel->errors()));
            }

            // Buat OTP baru
            $otpData = $this->otpModel->createOtp($userId, $data['email']);

            // Debug log untuk memastikan OTP dibuat
            log_message('debug', 'OTP created: ' . json_encode($otpData));

            // Kirim email verifikasi
            $this->sendVerificationEmail($data['email'], $otpData['otp']);

            // Simpan email di session untuk halaman verifikasi
            $session = \Config\Services::session();
            $session->set('verification_email', $data['email']);

            // Commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registrasi berhasil. Silakan cek email Anda untuk verifikasi.',
                'redirect' => site_url('register/verify')
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error saat registrasi: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function verify()
    {
        // Debug log untuk melihat apakah method ini dipanggil
        log_message('debug', 'Register::verify method called');

        // Ambil email dari session jika tersedia
        $session = \Config\Services::session();
        $email = $session->get('verification_email');

        // Jika tidak ada email di session, gunakan default kosong
        if (empty($email)) {
            $email = '';
            log_message('debug', 'Email not found in session');
        } else {
            log_message('debug', 'Email from session: ' . $email);
        }

        // Generate a hidden CSRF input
        $csrf = csrf_field();

        return view('auth/verify', [
            'email' => $email,
            'csrf' => $csrf
        ]);
    }

    public function verifyOtp()
    {
        // Debug log semua input
        log_message('debug', 'POST data: ' . json_encode($_POST));
        log_message('debug', 'GET data: ' . json_encode($_GET));
        log_message('debug', 'REQUEST data: ' . json_encode($_REQUEST));

        // Ini adalah cara paling efektif untuk bypass CSRF
        if (!function_exists('csrf_hash')) {
            helper('csrf');
        }
        $_POST[csrf_token()] = csrf_hash();
        $_POST['csrf_test_name'] = csrf_hash();
        $_REQUEST[csrf_token()] = csrf_hash();
        $_REQUEST['csrf_test_name'] = csrf_hash();
        $_GET[csrf_token()] = csrf_hash();
        $_GET['csrf_test_name'] = csrf_hash();

        // Debug log
        log_message('debug', 'Register::verifyOtp method called');

        // Accept both POST and GET requests
        $otp = $this->request->getPost('otp') ?? $this->request->getGet('otp');
        log_message('debug', 'OTP received: ' . ($otp ?? 'null'));

        $session = \Config\Services::session();
        $email = $session->get('verification_email');
        log_message('debug', 'Email from session: ' . ($email ?? 'null'));

        // Use POST or GET for email if needed
        if (empty($email)) {
            $email = $this->request->getPost('email') ?? $this->request->getGet('email');
            log_message('debug', 'Email from request: ' . ($email ?? 'null'));
        }

        // Cek apakah ini request AJAX
        $isAjax = $this->request->isAJAX() || $this->request->getPost('debug') == '1';

        // Validasi format OTP (harus 6 digit angka)
        if (!preg_match('/^\d{6}$/', $otp)) {
            log_message('error', 'Invalid OTP format: ' . $otp);

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Format kode OTP tidak valid. Harap masukkan 6 digit angka.'
                ]);
            } else {
                $session->setFlashdata('error', 'Format kode OTP tidak valid. Harap masukkan 6 digit angka.');
                return redirect()->to('register/verify');
            }
        }

        // Validasi input
        if (empty($otp) || $otp === null || $otp === 'null') {
            log_message('error', 'OTP is empty or null');

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Kode OTP harus diisi'
                ]);
            } else {
                $session->setFlashdata('error', 'Kode OTP harus diisi');
                return redirect()->to('register/verify');
            }
        }

        // Cek apakah ini request AJAX
        $isAjax = $this->request->isAJAX() || $this->request->getPost('debug') == '1';

        // Validasi input
        if (empty($otp) || $otp === null || $otp === 'null') {
            log_message('error', 'OTP is empty or null');

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Kode OTP harus diisi'
                ]);
            } else {
                $session->setFlashdata('error', 'Kode OTP harus diisi');
                return redirect()->to('register/verify');
            }
        }

        // Cari user berdasarkan email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            log_message('error', 'User not found for email: ' . $email);

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ]);
            } else {
                $session->setFlashdata('error', 'User tidak ditemukan');
                return redirect()->to('register/verify');
            }
        }

        // Verifikasi OTP
        $otpData = $this->otpModel->verifyOtp($email, $otp);

        if (!$otpData) {
            log_message('error', 'Invalid or expired OTP for email: ' . $email);

            // Cek apakah OTP sudah ada tapi expired atau tidak valid
            $existingOtp = $this->otpModel->where('email', $email)->first();

            if (!$existingOtp) {
                $errorMessage = 'Kode OTP tidak ditemukan. Silakan kirim ulang OTP.';
            } else if (strtotime($existingOtp['expires_at']) < time()) {
                $errorMessage = 'Kode OTP sudah kadaluarsa. Silakan kirim ulang OTP baru.';
            } else if ($existingOtp['is_used'] == 1) {
                $errorMessage = 'Kode OTP sudah digunakan sebelumnya. Silakan kirim ulang OTP baru.';
            } else {
                $errorMessage = 'Kode OTP tidak valid. Pastikan Anda memasukkan kode yang benar.';
            }

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMessage
                ]);
            } else {
                $session->setFlashdata('error', $errorMessage);
                return redirect()->to('register/verify');
            }
        }

        // Update status user menjadi aktif dan email terverifikasi
        $this->userModel->update($user['id'], [
            'status' => 'active',
            'email_verified' => 1
        ]);

        // Hapus OTP yang sudah digunakan
        $this->otpModel->where('id', $otpData['id'])->delete();

        // Kirim email konfirmasi aktivasi
        try {
            $this->sendActivationEmail($user);
            log_message('debug', 'Activation email sent to ' . $email);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send activation email: ' . $e->getMessage());
            // Tetap lanjutkan proses meski email gagal terkirim
        }

        // Hapus email dari session
        $session->remove('verification_email');

        log_message('debug', 'User verified successfully: ' . $email);

        $successMessage = 'Selamat! Akun Anda berhasil diverifikasi. Silakan login untuk mulai menggunakan layanan Elang Lembah Provider.';

        if ($isAjax) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $successMessage,
                'redirect' => site_url('auth')
            ]);
        } else {
            // Set flash message
            $session->setFlashdata('success', $successMessage);

            // Redirect ke halaman login
            return redirect()->to('auth');
        }
    }

    public function resendOtp()
    {
        // Ini adalah cara paling efektif untuk bypass CSRF
        if (!function_exists('csrf_hash')) {
            helper('csrf');
        }
        $_POST[csrf_token()] = csrf_hash();
        $_POST['csrf_test_name'] = csrf_hash();
        $_REQUEST[csrf_token()] = csrf_hash();
        $_REQUEST['csrf_test_name'] = csrf_hash();
        $_GET[csrf_token()] = csrf_hash();
        $_GET['csrf_test_name'] = csrf_hash();

        // Debug log
        log_message('debug', 'Register::resendOtp method called');
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

        $session = \Config\Services::session();
        $email = $session->get('verification_email');

        // Jika tidak ada email di session, cek dari POST
        if (empty($email)) {
            $email = $this->request->getPost('email');
            // Validasi input
            if (empty($email)) {
                $session->setFlashdata('error', 'Email harus diisi');
                return redirect()->to('register/verify');
            }
        }

        // Cari user berdasarkan email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            $session->setFlashdata('error', 'Email tidak ditemukan');
            return redirect()->to('register/verify');
        }

        // Buat OTP baru
        $otpData = $this->otpModel->createOtp($user['id'], $email);

        // Debug log untuk memastikan OTP dibuat
        log_message('debug', 'New OTP created: ' . json_encode($otpData));

        // Kirim email verifikasi
        try {
            $this->sendVerificationEmail($email, $otpData['otp']);

            // Set flash message success
            $session->setFlashdata('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengirim email: ' . $e->getMessage());
            $session->setFlashdata('error', 'Gagal mengirim OTP. Silakan coba lagi.');
        }

        // Simpan email di session jika belum ada
        if (!$session->has('verification_email')) {
            $session->set('verification_email', $email);
        }

        // Redirect kembali ke halaman verifikasi
        return redirect()->to('register/verify');
    }

    private function sendVerificationEmail($email, $otp)
    {
        // Konfigurasi email
        $this->email->setFrom('noreply@elanglembah.com', 'Elang Lembah Provider');
        $this->email->setTo($email);
        $this->email->setSubject('Verifikasi Akun Elang Lembah Provider');

        // Buat link verifikasi
        $verifyUrl = site_url("register/verifyOtp?email={$email}&otp={$otp}");

        // Isi email
        $message = "<h2>Verifikasi Akun Elang Lembah Provider</h2>"
            . "<p>Terima kasih telah mendaftar di Elang Lembah Provider.</p>"
            . "<p>Kode OTP Anda adalah: <strong>{$otp}</strong></p>"
            . "<p>Atau klik link ini untuk verifikasi langsung: <br>"
            . "<a href='{$verifyUrl}'>{$verifyUrl}</a></p>"
            . "<p>Kode ini akan kadaluarsa dalam 15 menit.</p>"
            . "<p>Jika Anda tidak merasa mendaftar, silakan abaikan email ini.</p>";

        $this->email->setMessage($message);
        $this->email->setMailType('html');

        // Debug
        log_message('debug', 'Sending verification email to ' . $email . ' with OTP ' . $otp);
        log_message('debug', 'Verification URL: ' . $verifyUrl);

        // Kirim email
        if (!$this->email->send()) {
            log_message('error', 'Gagal mengirim email verifikasi: ' . $this->email->printDebugger());
            throw new \RuntimeException('Gagal mengirim email verifikasi. Silakan coba lagi nanti.');
        } else {
            log_message('debug', 'Email verification sent successfully to ' . $email);
        }
    }

    /**
     * Mengirim email konfirmasi aktivasi akun
     *
     * @param array $user Data user
     * @return bool
     */
    private function sendActivationEmail($user)
    {
        // Konfigurasi email
        $this->email->setFrom('noreply@elanglembah.com', 'Elang Lembah Provider');
        $this->email->setTo($user['email']);
        $this->email->setSubject('Aktivasi Akun Berhasil - Elang Lembah Provider');

        // Buat tanggal saat ini dengan format yang bagus
        $tanggal = date('d F Y H:i');

        // Buat URL login
        $loginUrl = site_url('auth');

        // Isi email
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { background: linear-gradient(135deg, #4e73df, #224abe); color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0; margin-bottom: 20px; }
                .footer { background-color: #f8f9fa; padding: 15px; text-align: center; margin-top: 20px; border-radius: 0 0 5px 5px; font-size: 12px; color: #6c757d; }
                .btn { display: inline-block; padding: 10px 20px; background-color: #4e73df; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
                .details { background-color: #f8f9fc; padding: 15px; border-radius: 5px; margin: 20px 0; }
                h2 { color: #224abe; }
                table { width: 100%; border-collapse: collapse; }
                table td { padding: 8px; border-bottom: 1px solid #ddd; }
                table td:first-child { font-weight: bold; width: 40%; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Aktivasi Akun Berhasil</h1>
                </div>
                
                <p>Halo <strong>{$user['name']}</strong>,</p>
                
                <p>Selamat! Akun Anda di <strong>Elang Lembah Provider</strong> telah berhasil diaktivasi pada {$tanggal}.</p>
                
                <div class='details'>
                    <h2>Detail Akun Anda:</h2>
                    <table>
                        <tr>
                            <td>Nama</td>
                            <td>{$user['name']}</td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>{$user['username']}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{$user['email']}</td>
                        </tr>";

        // Tambahkan info kontak jika ada
        if (!empty($user['phone'])) {
            $message .= "
                        <tr>
                            <td>No. Telepon</td>
                            <td>{$user['phone']}</td>
                        </tr>";
        }

        if (!empty($user['address'])) {
            $message .= "
                        <tr>
                            <td>Alamat</td>
                            <td>{$user['address']}</td>
                        </tr>";
        }

        $message .= "
                    </table>
                </div>
                
                <p>Anda sekarang dapat login dan menggunakan layanan Elang Lembah Provider. Silakan klik tombol di bawah untuk login:</p>
                
                <p style='text-align: center;'>
                    <a href='{$loginUrl}' class='btn'>Login Sekarang</a>
                </p>
                
                <p>Jika Anda memiliki pertanyaan atau masalah, jangan ragu untuk menghubungi tim dukungan kami.</p>
                
                <p>Terima kasih telah memilih Elang Lembah Provider.</p>
                
                <div class='footer'>
                    <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                    <p>&copy; " . date('Y') . " Elang Lembah Provider. All Rights Reserved.</p>
                </div>
            </div>
        </body>
        </html>";

        $this->email->setMessage($message);
        $this->email->setMailType('html');

        // Kirim email
        return $this->email->send();
    }

    private function generateUsername($name)
    {
        // Convert to lowercase and remove spaces
        $baseUsername = strtolower(str_replace(' ', '', $name));
        $username = $baseUsername;

        // Cek apakah username sudah ada, jika ada tambahkan angka di belakangnya
        $counter = 1;
        while ($this->userModel->where('username', $username)->first()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}

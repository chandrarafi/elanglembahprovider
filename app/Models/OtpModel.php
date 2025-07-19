<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table = 'otp';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'email',
        'otp',
        'expires_at',
        'is_used'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id'    => 'required|integer',
        'email'      => 'required|valid_email',
        'otp'        => 'required|min_length[6]|max_length[6]',
        'expires_at' => 'required'
    ];

    /**
     * Membuat OTP baru untuk user
     *
     * @param int $userId ID user
     * @param string $email Email user
     * @return array Data OTP yang dibuat
     */
    public function createOtp($userId, $email)
    {
        // Generate OTP 6 digit
        $otp = random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Hapus OTP lama jika ada
        $this->where('user_id', $userId)->delete();

        // Data OTP baru
        $data = [
            'user_id'    => $userId,
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => $expiresAt,
            'is_used'    => 0
        ];

        // Insert ke database
        $insertId = $this->insert($data);

        // Debug log untuk memastikan data ter-insert
        log_message('debug', 'OTP created with ID: ' . $insertId . ', Data: ' . json_encode($data));

        if (!$insertId) {
            log_message('error', 'Failed to insert OTP: ' . json_encode($this->errors()));
        }

        return $data;
    }

    /**
     * Verifikasi OTP
     *
     * @param string $email Email user
     * @param string $otp Kode OTP
     * @return bool|array False jika gagal, data user jika berhasil
     */
    public function verifyOtp($email, $otp)
    {
        log_message('debug', 'Verifying OTP for email: ' . $email . ', OTP: ' . $otp);

        // Cari OTP yang sesuai dengan email
        $otpData = $this->where('email', $email)->first();

        log_message('debug', 'OTP data found: ' . ($otpData ? json_encode($otpData) : 'null'));

        // Jika tidak ada OTP untuk email ini
        if (!$otpData) {
            log_message('debug', 'OTP data not found for email: ' . $email);
            return false;
        }

        // Cek apakah OTP sudah digunakan
        if ($otpData['is_used'] == 1) {
            log_message('debug', 'OTP already used for email: ' . $email);
            return false;
        }

        // Cek apakah OTP sudah expired
        if (strtotime($otpData['expires_at']) < time()) {
            log_message('debug', 'OTP expired: ' . $otpData['expires_at'] . ' < ' . date('Y-m-d H:i:s'));
            return false;
        }

        // Cek apakah OTP cocok
        if ($otpData['otp'] !== $otp) {
            log_message('debug', 'OTP mismatch: ' . $otp . ' != ' . $otpData['otp']);
            return false;
        }

        // Set OTP as used
        $this->update($otpData['id'], ['is_used' => 1]);
        log_message('debug', 'OTP verified and marked as used');

        return $otpData;
    }
}

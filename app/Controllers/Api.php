<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
    /**
     * Endpoint untuk memeriksa pembayaran kedaluwarsa dan membatalkan pemesanan
     * Dapat dipanggil oleh WebSocket server atau cronjob
     */
    public function checkExpiredPayments()
    {
        // Buat token sederhana untuk keamanan - versi lebih sederhana
        $requestToken = $this->request->getGet('token');
        $validToken = 'elanglembahsecret123'; // Hardcoded untuk keamanan sederhana

        // Verifikasi token (tambahan keamanan sederhana)
        if (empty($requestToken) || $requestToken !== $validToken) {
            return $this->fail([
                'status' => 401,
                'error' => 401,
                'messages' => [
                    'error' => 'Unauthorized'
                ]
            ], 401);
        }

        // Gunakan metode dari Booking controller untuk memeriksa pembayaran kedaluwarsa
        try {
            $bookingController = new Booking();
            $result = $bookingController->checkAllExpiredPayments();

            // Jika hasil berbentuk array, kembalikan sebagai JSON
            if (is_array($result)) {
                return $this->respond($result);
            }

            // Jika hasil sudah berbentuk Response, kembalikan langsung
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error checking expired payments: ' . $e->getMessage());
            return $this->fail([
                'status' => 500,
                'error' => 500,
                'messages' => [
                    'error' => 'Internal Server Error: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}

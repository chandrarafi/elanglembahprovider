<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PaketWisataModel;
use CodeIgniter\HTTP\ResponseInterface;

class Booking extends BaseController
{
    protected $userModel;
    protected $paketModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->paketModel = new PaketWisataModel();
    }

    public function history()
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/')->with('error', 'User tidak ditemukan');
        }

        // Untuk sementara, kita gunakan array kosong karena belum ada model BookingModel
        $bookings = [];

        $data = [
            'title' => 'Riwayat Pemesanan - Elang Lembah Travel',
            'user' => $user,
            'bookings' => $bookings,
            'is_logged_in' => true,
            'user_data' => [
                'name' => session()->get('name'),
                'role' => session()->get('role')
            ]
        ];

        return view('booking/history', $data);
    }

    public function detail($id)
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = session()->get('user_id');

        // Untuk sementara, kita gunakan array kosong karena belum ada model BookingModel
        $booking = [
            'id' => $id,
            'booking_code' => 'BK' . str_pad($id, 6, '0', STR_PAD_LEFT),
            'user_id' => $userId,
            'paket_id' => 'PKT001',
            'paket_name' => 'Paket Wisata Bali 3 Hari 2 Malam',
            'booking_date' => date('Y-m-d'),
            'travel_date' => date('Y-m-d', strtotime('+1 month')),
            'jumlah_peserta' => 2,
            'total_harga' => 5000000,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $data = [
            'title' => 'Detail Pemesanan - Elang Lembah Travel',
            'booking' => $booking,
            'is_logged_in' => true,
            'user_data' => [
                'name' => session()->get('name'),
                'role' => session()->get('role')
            ]
        ];

        return view('booking/detail', $data);
    }

    public function create($paketId)
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $paket = $this->paketModel->find($paketId);

        if (!$paket) {
            return redirect()->to('/paket')->with('error', 'Paket wisata tidak ditemukan');
        }

        $data = [
            'title' => 'Pesan Paket Wisata - Elang Lembah Travel',
            'paket' => $paket,
            'is_logged_in' => true,
            'user_data' => [
                'name' => session()->get('name'),
                'role' => session()->get('role')
            ]
        ];

        return view('booking/create', $data);
    }

    public function store()
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');
        $data = $this->request->getPost();

        // Untuk sementara, kita hanya mengembalikan respons sukses
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Pemesanan berhasil dibuat',
            'redirect' => base_url('booking/history')
        ]);
    }

    public function cancel($id)
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        // Untuk sementara, kita hanya mengembalikan respons sukses
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Pemesanan berhasil dibatalkan',
            'redirect' => base_url('booking/history')
        ]);
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\BookingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Profile extends BaseController
{
    protected $userModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // Asumsi BookingModel sudah ada atau akan dibuat
        // $this->bookingModel = new BookingModel();
    }

    public function index()
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

        // Ambil data booking jika BookingModel sudah ada
        $bookings = []; // Ganti dengan $this->bookingModel->where('user_id', $userId)->findAll();

        $data = [
            'title' => 'Profil Saya - Elang Lembah Travel',
            'user' => $user,
            'bookings' => $bookings,
            'is_logged_in' => true,
            'user_data' => [
                'name' => session()->get('name'),
                'role' => session()->get('role')
            ]
        ];

        return view('profile/index', $data);
    }

    public function update()
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

        try {
            // Jika password diisi, hash password
            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                // Jika password kosong, hapus dari data
                unset($data['password']);
            }

            // Update data user
            if (!$this->userModel->update($userId, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->userModel->errors()
                ])->setStatusCode(400);
            }

            // Update session data jika ada perubahan pada nama atau email
            if (isset($data['name'])) {
                session()->set('name', $data['name']);
            }
            if (isset($data['email'])) {
                session()->set('email', $data['email']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Profil berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate profil: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

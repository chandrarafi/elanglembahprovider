<?php

namespace App\Controllers;

use App\Models\PemesananModel;
use App\Models\PaketWisataModel;
use App\Models\UserModel;

class Verify extends BaseController
{
    protected $pemesananModel;
    protected $paketModel;
    protected $userModel;

    public function __construct()
    {
        $this->pemesananModel = new PemesananModel();
        $this->paketModel = new PaketWisataModel();
        $this->userModel = new UserModel();
    }

    /**
     * Verify a ticket based on the QR code
     */
    public function ticket($kode_booking = null)
    {
        if (!$kode_booking) {
            return $this->response->setJSON(['success' => false, 'message' => 'No booking code provided']);
        }

        // Find booking by code
        $pemesanan = $this->pemesananModel->where('kode_booking', $kode_booking)->first();

        if (!$pemesanan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid booking code']);
        }

        // Get related data
        $paket = $this->paketModel->find($pemesanan['idpaket']);
        $user = $this->userModel->find($pemesanan['iduser']);

        // Prepare response data
        $data = [
            'success' => true,
            'pemesanan' => [
                'kode_booking' => $pemesanan['kode_booking'],
                'tanggal' => date('d M Y', strtotime($pemesanan['tanggal'])),
                'tgl_berangkat' => date('d M Y', strtotime($pemesanan['tgl_berangkat'])),
                'tgl_selesai' => date('d M Y', strtotime($pemesanan['tgl_selesai'])),
                'jumlah_peserta' => $pemesanan['jumlah_peserta'],
                'status' => $pemesanan['status']
            ],
            'paket' => [
                'nama' => $paket['namapaket'],
                'durasi' => $paket['durasi']
            ],
            'user' => [
                'nama' => $user['name']
            ]
        ];

        return $this->response->setJSON($data);
    }

    /**
     * Display ticket verification page
     */
    public function index()
    {
        return view('verify/scan');
    }
}

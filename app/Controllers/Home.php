<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\PaketWisataModel;
use App\Models\PemesananModel;

class Home extends BaseController
{
    protected $kategoriModel;
    protected $paketModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->paketModel = new PaketWisataModel();
    }

    public function index()
    {
        // Load models
        $paketModel = new PaketWisataModel();
        $kategoriModel = new KategoriModel();
        $pemesananModel = new PemesananModel();
        $rescheduleModel = new \App\Models\RescheduleRequestModel();

        // Get active categories and packages
        $kategori = $kategoriModel->findAll();
        $paket = $paketModel->findAll();

        // Ambil beberapa kategori untuk featured destinations
        $featured_destinations = array_slice($kategori, 0, 3);

        // Ambil paket populer dan terbaru
        $paket_populer = array_slice($paket, 0, 3);
        $paket_terbaru = array_slice($paket, -3);

        // Data for view
        $data = [
            'title' => 'Beranda',
            'kategori' => $kategori,
            'paket' => $paket,
            'featured_destinations' => $featured_destinations,
            'paket_populer' => $paket_populer,
            'paket_terbaru' => $paket_terbaru,
            'is_logged_in' => session()->get('user_id') ? true : false,
            'user' => session()->get('user_id') ? [
                'name' => session()->get('name'),
                'email' => session()->get('email')
            ] : null,
        ];

        // If user is logged in, get booking data
        if (session()->get('user_id')) {
            $userId = session()->get('user_id');

            // Get upcoming bookings
            $pemesanan = $pemesananModel->where('iduser', $userId)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('tgl_berangkat >=', date('Y-m-d'))
                ->orderBy('tgl_berangkat', 'ASC')
                ->findAll(3);

            // Get pending reschedule requests
            $rescheduleRequests = $rescheduleModel->where('reschedule_requests.status', 'pending')
                ->join('pemesanan', 'pemesanan.idpesan = reschedule_requests.idpesan')
                ->where('pemesanan.iduser', $userId)
                ->select('reschedule_requests.*, pemesanan.kode_booking')
                ->findAll();

            $data['upcomingBookings'] = $pemesanan;
            $data['rescheduleRequests'] = $rescheduleRequests;
        }

        return view('home/index', $data);
    }
}

<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\PaketWisataModel;

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
        // Ambil data kategori dari database (hanya yang aktif)
        $kategori = $this->kategoriModel->where('status', 'active')->findAll();

        // Ambil 3 kategori teratas untuk featured destinations
        $featured_destinations = array_slice($kategori, 0, 3);

        // Ambil data paket populer - Kita gunakan 6 paket dengan cara yang lebih baik
        // Asumsi: paket populer bisa diambil berdasarkan field tertentu atau filter tertentu
        $paket_populer = $this->paketModel
            ->where('statuspaket', 'active')
            ->orderBy('harga', 'DESC') // Pakai orderBy harga sebagai contoh untuk paket premium/populer
            ->limit(6)
            ->find();

        // Ambil data paket terbaru berdasarkan created_at
        $paket_terbaru = $this->paketModel
            ->where('statuspaket', 'active')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->find();

        // Get kategori details for each paket
        foreach ($paket_populer as $key => $paket) {
            $kategoriDetail = $this->kategoriModel->find($paket['idkategori']);
            $paket_populer[$key]['kategori_nama'] = $kategoriDetail['namakategori'] ?? '';
        }

        foreach ($paket_terbaru as $key => $paket) {
            $kategoriDetail = $this->kategoriModel->find($paket['idkategori']);
            $paket_terbaru[$key]['kategori_nama'] = $kategoriDetail['namakategori'] ?? '';
        }

        // Data untuk view
        $data = [
            'title' => 'Elang Lembah Travel - Wisata Terbaik untuk Anda',
            'kategori' => $kategori,
            'featured_destinations' => $featured_destinations,
            'paket_populer' => $paket_populer,
            'paket_terbaru' => $paket_terbaru,
            'is_logged_in' => session()->get('logged_in') ?? false,
            'user' => [
                'name' => session()->get('name') ?? '',
                'role' => session()->get('role') ?? ''
            ]
        ];

        return view('home/index', $data);
    }
}

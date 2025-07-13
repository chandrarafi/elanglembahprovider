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
        // Ambil data untuk halaman home
        $data = [
            'title' => 'Elang Lembah Travel - Wisata Terbaik untuk Anda',
            'kategori' => $this->kategoriModel->where('status', 'active')->findAll(),
            'paket_populer' => $this->paketModel->where('statuspaket', 'active')
                ->orderBy('RAND()')
                ->limit(6)
                ->find(),
            'paket_terbaru' => $this->paketModel->where('statuspaket', 'active')
                ->orderBy('created_at', 'DESC')
                ->limit(3)
                ->find(),
            'is_logged_in' => session()->get('logged_in') ?? false,
            'user' => [
                'name' => session()->get('name') ?? '',
                'role' => session()->get('role') ?? ''
            ]
        ];

        return view('home/index', $data);
    }
}

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
        $id_paket = $this->request->getPost('id_paket');
        $paket = $this->paketModel->find($id_paket);
        if (!$paket) {
            return redirect()->to('/paket')->with('error', 'Paket tidak ditemukan');
        }

        // Generate kode pemesanan
        $kode_booking = 'ELP' . date('Ymd') . rand(1000, 9999);

        // Hitung total harga
        $total_harga = $paket['harga'];

        // Simpan data pemesanan
        $data_pemesanan = [
            'idpaket' => $id_paket,
            'iduser' => session()->get('user_id'),
            'kode_booking' => $kode_booking,
            'tanggal' => date('Y-m-d H:i:s'),
            'tgl_berangkat' => $this->request->getPost('tgl_berangkat'),
            'harga' => $paket['harga'],
            'totalbiaya' => $total_harga,
            'status' => 'pending'
        ];

        $this->pemesananModel->insert($data_pemesanan);
        $id_pemesanan = $this->pemesananModel->getInsertID();

        return redirect()->to('/booking/detail/' . $id_pemesanan)->with('success', 'Pemesanan berhasil dibuat');
    }

    public function detail($id_pemesanan)
    {
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        $paket = $this->paketModel->find($pemesanan['idpaket']);
        $pembayaran = $this->pembayaranModel->where('idpesan', $id_pemesanan)->first();

        $data = [
            'title' => 'Detail Pemesanan',
            'pemesanan' => $pemesanan,
            'paket' => $paket,
            'pembayaran' => $pembayaran,
            'kategori' => $this->kategoriModel->findAll(),
        ];

        return view('booking/detail', $data);
    }

    public function history()
    {
        $pemesanan = $this->pemesananModel->where('iduser', session()->get('user_id'))->findAll();

        $data_pemesanan = [];
        foreach ($pemesanan as $p) {
            $paket = $this->paketModel->find($p['idpaket']);
            $p['paket'] = $paket;
            $data_pemesanan[] = $p;
        }

        $data = [
            'title' => 'Riwayat Pemesanan',
            'pemesanan' => $data_pemesanan,
            'kategori' => $this->kategoriModel->findAll(),
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

        $data = [
            'title' => 'Pembayaran',
            'pemesanan' => $pemesanan,
            'paket' => $paket,
            'kategori' => $this->kategoriModel->findAll(),
        ];

        return view('booking/payment', $data);
    }

    public function savePayment()
    {
        $id_pemesanan = $this->request->getPost('idpesan');
        $pemesanan = $this->pemesananModel->find($id_pemesanan);
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        $bukti_pembayaran = $this->request->getFile('bukti_pembayaran');
        if (!$bukti_pembayaran->isValid()) {
            return redirect()->to('/booking/payment/' . $id_pemesanan)->with('error', 'Bukti pembayaran tidak valid');
        }

        $newName = $pemesanan['kode_booking'] . '.' . $bukti_pembayaran->getExtension();
        $bukti_pembayaran->move(ROOTPATH . 'public/uploads/payments', $newName);

        $data_pembayaran = [
            'idpesan' => $id_pemesanan,
            'tanggal_bayar' => date('Y-m-d H:i:s'),
            'jumlah_bayar' => $pemesanan['totalbiaya'],
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'bukti_bayar' => $newName,
            'status_pembayaran' => 'pending'
        ];

        $this->pembayaranModel->insert($data_pembayaran);
        $this->pemesananModel->update($id_pemesanan, ['status' => 'waiting_confirmation']);

        return redirect()->to('/booking/detail/' . $id_pemesanan)->with('success', 'Pembayaran berhasil diupload');
    }
}

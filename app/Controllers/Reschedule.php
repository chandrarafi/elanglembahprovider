<?php

namespace App\Controllers;

use App\Models\PemesananModel;
use App\Models\PaketWisataModel;
use App\Models\RescheduleRequestModel;
use CodeIgniter\I18n\Time;

class Reschedule extends BaseController
{
    protected $pemesananModel;
    protected $paketModel;
    protected $rescheduleModel;

    public function __construct()
    {
        $this->pemesananModel = new PemesananModel();
        $this->paketModel = new PaketWisataModel();
        $this->rescheduleModel = new RescheduleRequestModel();
    }

    /**
     * Display reschedule request form
     */
    public function request($id_pemesanan)
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Get booking data
        $pemesanan = $this->pemesananModel->find($id_pemesanan);

        // Check if booking exists and belongs to this user
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Check if booking can be rescheduled (must be confirmed/completed and not yet departed)
        if (!in_array($pemesanan['status'], ['confirmed', 'completed']) || strtotime($pemesanan['tgl_berangkat']) <= time()) {
            return redirect()->to('/booking/detail/' . $id_pemesanan)
                ->with('error', 'Pemesanan ini tidak dapat dijadwalkan ulang. Hanya pemesanan yang sudah dikonfirmasi dan belum berangkat yang dapat diubah jadwalnya.');
        }

        // Check if there's already a pending reschedule request
        $existingRequest = $this->rescheduleModel->where('idpesan', $id_pemesanan)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->to('/booking/detail/' . $id_pemesanan)
                ->with('error', 'Sudah ada permintaan perubahan jadwal yang sedang diproses untuk pemesanan ini.');
        }

        // Get package data
        $paket = $this->paketModel->find($pemesanan['idpaket']);

        // Load the view
        return view('booking/reschedule_request', [
            'pemesanan' => $pemesanan,
            'paket' => $paket,
            'min_date' => date('Y-m-d', strtotime('+3 days')) // Minimum 3 days from today
        ]);
    }

    /**
     * Submit reschedule request
     */
    public function submit()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Get post data
        $idpesan = $this->request->getPost('id_pemesanan');
        $requested_tgl_berangkat = $this->request->getPost('requested_tgl_berangkat');
        $alasan = $this->request->getPost('alasan');

        // Validate input
        $validation = $this->validate([
            'id_pemesanan' => 'required|numeric',
            'requested_tgl_berangkat' => 'required|valid_date',
            'alasan' => 'required|min_length[10]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get booking data
        $pemesanan = $this->pemesananModel->find($idpesan);

        // Check if booking exists and belongs to this user
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Check if booking can be rescheduled
        if (!in_array($pemesanan['status'], ['confirmed', 'completed']) || strtotime($pemesanan['tgl_berangkat']) <= time()) {
            return redirect()->to('/booking/detail/' . $idpesan)
                ->with('error', 'Pemesanan ini tidak dapat dijadwalkan ulang');
        }

        // Get package data
        $paket = $this->paketModel->find($pemesanan['idpaket']);

        // Calculate new end date based on package duration
        $requested_tgl_selesai = date('Y-m-d', strtotime($requested_tgl_berangkat . ' + ' . $paket['durasi'] . ' days'));

        // Create reschedule request
        $data = [
            'idpesan' => $idpesan,
            'current_tgl_berangkat' => $pemesanan['tgl_berangkat'],
            'requested_tgl_berangkat' => $requested_tgl_berangkat,
            'current_tgl_selesai' => $pemesanan['tgl_selesai'],
            'requested_tgl_selesai' => $requested_tgl_selesai,
            'alasan' => $alasan,
            'status' => 'pending'
        ];

        try {
            $this->rescheduleModel->insert($data);
            return redirect()->to('/booking/detail/' . $idpesan)
                ->with('success', 'Permintaan perubahan jadwal telah dikirimkan. Admin akan memprosesnya segera.');
        } catch (\Exception $e) {
            log_message('error', 'Error creating reschedule request: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal membuat permintaan. Silakan coba lagi.');
        }
    }

    /**
     * View reschedule request history
     */
    public function history($id_pemesanan)
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Get booking data
        $pemesanan = $this->pemesananModel->find($id_pemesanan);

        // Check if booking exists and belongs to this user
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Get reschedule requests
        $requests = $this->rescheduleModel->getByBookingId($id_pemesanan);

        // Load the view
        return view('booking/reschedule_history', [
            'pemesanan' => $pemesanan,
            'requests' => $requests
        ]);
    }

    /**
     * Cancel a pending reschedule request
     */
    public function cancel($id_request)
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Get request data
        $request = $this->rescheduleModel->find($id_request);

        if (!$request) {
            return redirect()->to('/booking/history')->with('error', 'Permintaan tidak ditemukan');
        }

        // Get booking data to verify ownership
        $pemesanan = $this->pemesananModel->find($request['idpesan']);

        // Check if booking belongs to this user
        if (!$pemesanan || $pemesanan['iduser'] != session()->get('user_id')) {
            return redirect()->to('/booking/history')->with('error', 'Anda tidak memiliki akses ke permintaan ini');
        }

        // Check if request is still pending
        if ($request['status'] !== 'pending') {
            return redirect()->to('/booking/detail/' . $pemesanan['idpesan'])
                ->with('error', 'Hanya permintaan dengan status pending yang dapat dibatalkan');
        }

        // Update request status to cancelled
        $this->rescheduleModel->update($id_request, ['status' => 'rejected', 'admin_notes' => 'Dibatalkan oleh pengguna']);

        return redirect()->to('/booking/detail/' . $pemesanan['idpesan'])
            ->with('success', 'Permintaan perubahan jadwal berhasil dibatalkan');
    }
}

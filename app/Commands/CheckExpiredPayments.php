<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PemesananModel;
use App\Models\PembayaranModel;

class CheckExpiredPayments extends BaseCommand
{
    protected $group = 'Booking';
    protected $name = 'booking:check-expired';
    protected $description = 'Check for expired payments and update booking status to cancelled';

    public function run(array $params)
    {
        $pemesananModel = new PemesananModel();
        $pembayaranModel = new PembayaranModel();

        CLI::write('Memeriksa pembayaran yang kedaluwarsa...', 'yellow');

        // Cari pemesanan dengan status pending yang memiliki expired_at yang sudah lewat
        $expiredBookings = $pemesananModel->where('status', 'pending')
            ->where('expired_at <', date('Y-m-d H:i:s'))
            ->findAll();

        CLI::write('Ditemukan ' . count($expiredBookings) . ' pemesanan kedaluwarsa.', 'yellow');

        $cancelledCount = 0;

        // Proses setiap pemesanan kedaluwarsa
        foreach ($expiredBookings as $booking) {
            // Update status pemesanan menjadi cancelled
            $pemesananModel->update($booking['idpesan'], ['status' => 'cancelled']);

            // Log untuk debugging
            log_message('info', 'Cancelled expired booking: ID=' . $booking['idpesan'] . ', Kode=' . $booking['kode_booking']);

            // Cari dan update status pembayaran terkait jika ada
            $payment = $pembayaranModel->where('idpesan', $booking['idpesan'])->first();
            if ($payment) {
                $pembayaranModel->update($payment['idbayar'], [
                    'status_pembayaran' => 'rejected',
                    'keterangan' => 'Pembayaran kedaluwarsa secara otomatis'
                ]);
                log_message('info', 'Updated related payment status: ID=' . $payment['idbayar']);
            }

            $cancelledCount++;
            CLI::write('Membatalkan pemesanan #' . $booking['kode_booking'], 'green');
        }

        // Cari juga pemesanan yang pembayarannya kedaluwarsa
        $paymentExpired = $pembayaranModel->select('pembayaran.*, pemesanan.kode_booking, pemesanan.status as booking_status')
            ->join('pemesanan', 'pembayaran.idpesan = pemesanan.idpesan')
            ->where('pemesanan.status', 'pending')
            ->where('pembayaran.status_pembayaran', 'pending')
            ->where('pembayaran.expired_at <', date('Y-m-d H:i:s'))
            ->findAll();

        CLI::write('Ditemukan ' . count($paymentExpired) . ' pembayaran kedaluwarsa.', 'yellow');

        // Proses setiap pembayaran kedaluwarsa
        foreach ($paymentExpired as $payment) {
            // Update status pemesanan menjadi cancelled jika belum
            if ($payment['booking_status'] !== 'cancelled') {
                $pemesananModel->update($payment['idpesan'], ['status' => 'cancelled']);
                log_message('info', 'Cancelled booking due to expired payment: ID=' . $payment['idpesan']);
                $cancelledCount++;
            }

            // Update status pembayaran
            $pembayaranModel->update($payment['idbayar'], [
                'status_pembayaran' => 'rejected',
                'keterangan' => 'Pembayaran kedaluwarsa secara otomatis'
            ]);
            log_message('info', 'Updated payment status: ID=' . $payment['idbayar']);

            CLI::write('Membatalkan pemesanan dengan kode booking #' . $payment['kode_booking'], 'green');
        }

        CLI::write('Total ' . $cancelledCount . ' pemesanan dibatalkan karena kedaluwarsa.', 'green');
    }
}

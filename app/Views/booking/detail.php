<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary animate-heading">Detail Pemesanan</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <?php
            // Show payment timer if payment is pending and there's an active payment record
            if ($pemesanan['status'] == 'pending' && isset($pembayaran) && $pembayaran && $pembayaran['status_pembayaran'] == 'pending'):
            ?>
                <!-- Payment Timer -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 animate-card">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Batas Waktu Pembayaran</h2>
                        <div id="payment-timer" class="text-xl font-bold text-red-600 timer-pulse">--:--</div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Silakan selesaikan pembayaran sebelum batas waktu berakhir. Jika waktu habis, pemesanan akan otomatis dibatalkan.</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                        <div id="timer-progress" class="bg-red-600 h-2.5 rounded-full animate__animated animate__pulse animate__infinite" style="width: 100%"></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 animate-card">
                <div class="bg-primary text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold m-0 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Detail Pemesanan
                    </h2>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center
                    <?= ($pemesanan['status'] == 'pending') ? 'bg-yellow-200 text-yellow-800' : (($pemesanan['status'] == 'confirmed' || $pemesanan['status'] == 'waiting_confirmation') ? 'bg-blue-200 text-blue-800' : (($pemesanan['status'] == 'completed') ? 'bg-green-200 text-green-800' : (($pemesanan['status'] == 'down_payment') ? 'bg-purple-200 text-purple-800' : (($pemesanan['status'] == 'cancelled') ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-800')))) ?>">
                        <?php if ($pemesanan['status'] == 'pending'): ?>
                            <i class="fas fa-clock mr-1"></i>
                        <?php elseif ($pemesanan['status'] == 'confirmed'): ?>
                            <i class="fas fa-check-circle mr-1"></i>
                        <?php elseif ($pemesanan['status'] == 'waiting_confirmation'): ?>
                            <i class="fas fa-hourglass-half mr-1"></i>
                        <?php elseif ($pemesanan['status'] == 'completed'): ?>
                            <i class="fas fa-check-double mr-1"></i>
                        <?php elseif ($pemesanan['status'] == 'cancelled'): ?>
                            <i class="fas fa-times-circle mr-1"></i>
                        <?php elseif ($pemesanan['status'] == 'down_payment'): ?>
                            <i class="fas fa-hand-holding-usd mr-1"></i>
                        <?php endif; ?>
                        <?= ucfirst(str_replace('_', ' ', $pemesanan['status'])) ?>
                    </span>
                </div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row mb-6">
                        <div class="w-full md:w-1/3 mb-4 md:mb-0">
                            <img src="<?= base_url('uploads/paket/' . $paket['foto']) ?>" class="w-full rounded-lg" alt="<?= $paket['namapaket'] ?>">
                        </div>
                        <div class="w-full md:w-2/3 md:pl-6 fade-in">
                            <h3 class="text-xl font-bold mb-3 flex items-center">
                                <i class="fas fa-suitcase text-primary mr-2"></i>
                                <?= $paket['namapaket'] ?>
                            </h3>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-tag text-primary mr-2"></i>Harga Paket:
                                </span>
                                <span class="text-lg font-semibold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-clock text-primary mr-2"></i>Durasi:
                                </span>
                                <span><?= $paket['durasi'] ?? '1' ?> Hari</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i>Informasi Pemesanan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 slide-up">
                        <div>
                            <table class="w-full">
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-ticket-alt text-primary mr-2"></i>Kode Booking
                                    </td>
                                    <td class="py-2 font-semibold"><?= $pemesanan['kode_booking'] ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-calendar text-primary mr-2"></i>Tanggal Pemesanan
                                    </td>
                                    <td class="py-2"><?= date('d M Y', strtotime($pemesanan['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-plane-departure text-primary mr-2"></i>Tanggal Berangkat
                                    </td>
                                    <td class="py-2"><?= date('d M Y', strtotime($pemesanan['tgl_berangkat'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-plane-arrival text-primary mr-2"></i>Tanggal Selesai
                                    </td>
                                    <td class="py-2"><?= date('d M Y', strtotime($pemesanan['tgl_selesai'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table class="w-full">
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-user text-primary mr-2"></i>Nama Pemesan
                                    </td>
                                    <td class="py-2"><?= session()->get('name') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-envelope text-primary mr-2"></i>Email
                                    </td>
                                    <td class="py-2"><?= session()->get('email') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 flex items-center">
                                        <i class="fas fa-phone text-primary mr-2"></i>No. Telepon
                                    </td>
                                    <td class="py-2"><?= session()->get('phone') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-primary mr-2"></i>Informasi Pembayaran
                    </h3>
                    <div class="mb-6">
                        <table class="w-full">
                            <tr>
                                <td class="py-2 text-gray-600 flex items-center">
                                    <i class="fas fa-money-bill-wave text-primary mr-2"></i>
                                    Total Pembayaran
                                </td>
                                <td class="py-2 font-semibold">Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600 flex items-center">
                                    <i class="fas fa-tag text-primary mr-2"></i>
                                    Status Pembayaran
                                </td>
                                <td class="py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center w-fit
                                    <?= ($pemesanan['status'] == 'pending') ? 'bg-yellow-200 text-yellow-800' : (($pemesanan['status'] == 'confirmed' || $pemesanan['status'] == 'waiting_confirmation') ? 'bg-blue-200 text-blue-800' : (($pemesanan['status'] == 'completed') ? 'bg-green-200 text-green-800' : (($pemesanan['status'] == 'cancelled') ? 'bg-red-200 text-red-800' : (($pemesanan['status'] == 'down_payment') ? 'bg-purple-200 text-purple-800' : 'bg-gray-200 text-gray-800')))) ?>">
                                        <?php if ($pemesanan['status'] == 'pending'): ?>
                                            <i class="fas fa-clock mr-1"></i>
                                        <?php elseif ($pemesanan['status'] == 'confirmed'): ?>
                                            <i class="fas fa-check-circle mr-1"></i>
                                        <?php elseif ($pemesanan['status'] == 'waiting_confirmation'): ?>
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                        <?php elseif ($pemesanan['status'] == 'completed'): ?>
                                            <i class="fas fa-check-double mr-1"></i>
                                        <?php elseif ($pemesanan['status'] == 'cancelled'): ?>
                                            <i class="fas fa-times-circle mr-1"></i>
                                        <?php elseif ($pemesanan['status'] == 'down_payment'): ?>
                                            <i class="fas fa-hand-holding-usd mr-1"></i>
                                        <?php endif; ?>
                                        <?= ucfirst(str_replace('_', ' ', $pemesanan['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php if ($pembayaran): ?>
                        <div class="bg-blue-50 p-4 rounded-lg mb-6 animate__animated animate__fadeIn">
                            <h4 class="text-lg font-semibold mb-3 text-blue-800 flex items-center">
                                <i class="fas fa-credit-card mr-2"></i>
                                Detail Pembayaran:
                            </h4>
                            <div class="space-y-2">
                                <p class="mb-1 text-gray-700 flex items-center">
                                    <i class="fas fa-money-check text-blue-600 mr-2 w-5"></i>
                                    <span class="font-medium w-48">Metode Pembayaran:</span>
                                    <?= $pembayaran['metode_pembayaran'] ?>
                                </p>
                                <p class="mb-1 text-gray-700 flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-600 mr-2 w-5"></i>
                                    <span class="font-medium w-48">Tanggal Pembayaran:</span>
                                    <?= date('d M Y', strtotime($pembayaran['tanggal_bayar'])) ?>
                                </p>
                                <p class="mb-1 text-gray-700 flex items-center">
                                    <i class="fas fa-percentage text-blue-600 mr-2 w-5"></i>
                                    <span class="font-medium w-48">Tipe Pembayaran:</span>
                                    <span class="px-2 py-0.5 rounded text-sm font-semibold flex items-center <?= ($pembayaran['tipe_pembayaran'] ?? 'lunas') == 'dp' ? 'bg-purple-200 text-purple-800' : 'bg-green-200 text-green-800' ?>">
                                        <?php if (isset($pembayaran['tipe_pembayaran']) && $pembayaran['tipe_pembayaran'] == 'dp'): ?>
                                            <i class="fas fa-hand-holding-usd mr-1"></i> DP (50%)
                                        <?php else: ?>
                                            <i class="fas fa-check-circle mr-1"></i> Lunas
                                        <?php endif; ?>
                                    </span>
                                </p>
                                <p class="mb-1 text-gray-700 flex items-center">
                                    <i class="fas fa-coins text-blue-600 mr-2 w-5"></i>
                                    <span class="font-medium w-48">Jumlah Dibayar:</span>
                                    Rp <?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.') ?>
                                </p>
                                <p class="mb-3 text-gray-700 flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-2 w-5"></i>
                                    <span class="font-medium w-48">Status:</span>
                                    <span class="px-2 py-0.5 rounded text-sm font-semibold flex items-center
                                    <?= ($pembayaran['status_pembayaran'] == 'pending') ? 'bg-yellow-200 text-yellow-800' : (($pembayaran['status_pembayaran'] == 'verified') ? 'bg-green-200 text-green-800' : (($pembayaran['status_pembayaran'] == 'rejected') ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-800')) ?>">
                                        <?php if ($pembayaran['status_pembayaran'] == 'pending'): ?>
                                            <i class="fas fa-clock mr-1"></i>
                                        <?php elseif ($pembayaran['status_pembayaran'] == 'verified'): ?>
                                            <i class="fas fa-check-circle mr-1"></i>
                                        <?php elseif ($pembayaran['status_pembayaran'] == 'rejected'): ?>
                                            <i class="fas fa-times-circle mr-1"></i>
                                        <?php endif; ?>
                                        <?= ucfirst($pembayaran['status_pembayaran']) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="mt-3">
                                <p class="mb-2 text-gray-700 flex items-center">
                                    <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
                                    <span class="font-medium">Bukti Pembayaran:</span>
                                </p>
                                <div class="mt-2 border border-gray-200 rounded-lg overflow-hidden">
                                    <img src="<?= base_url('uploads/payments/' . $pembayaran['bukti_bayar']) ?>" class="max-h-48 w-auto mx-auto cursor-pointer hover:opacity-90 transition-opacity" alt="Bukti Pembayaran" onclick="showImagePreview(this.src)">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
                        <div>
                            <a href="<?= base_url('booking/history') ?>" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2.5 px-6 rounded transition duration-300 flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Riwayat
                            </a>
                        </div>

                        <?php if ($pemesanan['status'] == 'pending'): ?>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="<?= base_url('booking/payment/' . $pemesanan['idpesan']) ?>" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 px-6 rounded transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Lakukan Pembayaran
                                </a>
                                <button id="cancel-booking-btn" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-6 rounded transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Batalkan Pemesanan
                                </button>
                            </div>
                        <?php elseif ($pemesanan['status'] == 'down_payment'): ?>
                            <div>
                                <a href="<?= base_url('booking/payment/' . $pemesanan['idpesan']) ?>" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-6 rounded transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-hand-holding-usd mr-2"></i>
                                    Lakukan Pelunasan
                                </a>
                            </div>
                        <?php elseif (in_array($pemesanan['status'], ['confirmed', 'completed']) && strtotime($pemesanan['tgl_berangkat']) > time()): ?>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="<?= base_url('reschedule/request/' . $pemesanan['idpesan']) ?>" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 px-6 rounded transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Perubahan Jadwal
                                </a>
                                <a href="<?= base_url('reschedule/history/' . $pemesanan['idpesan']) ?>" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2.5 px-6 rounded transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-history mr-2"></i>
                                    Riwayat Perubahan
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-1 mb-6">
                <div class="bg-primary text-white px-6 py-4">
                    <h2 class="text-xl font-semibold m-0 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Status
                    </h2>
                </div>
                <div class="p-6">
                    <ul class="divide-y divide-gray-200">
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-clock text-yellow-500 mr-2"></i>Pending
                            </span>
                            <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded text-xs font-semibold flex items-center">
                                <i class="fas fa-clock mr-1"></i>Menunggu Pembayaran
                            </span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-hand-holding-usd text-purple-500 mr-2"></i>Down Payment
                            </span>
                            <span class="px-2 py-1 bg-purple-200 text-purple-800 rounded text-xs font-semibold flex items-center">
                                <i class="fas fa-hand-holding-usd mr-1"></i>DP 50% Terbayar
                            </span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-hourglass-half text-blue-500 mr-2"></i>Waiting Confirmation
                            </span>
                            <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded text-xs font-semibold flex items-center">
                                <i class="fas fa-hourglass-half mr-1"></i>Menunggu Konfirmasi
                            </span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-check-circle text-indigo-500 mr-2"></i>Confirmed
                            </span>
                            <span class="px-2 py-1 bg-indigo-200 text-indigo-800 rounded text-xs font-semibold flex items-center">
                                <i class="fas fa-check-circle mr-1"></i>Pembayaran Dikonfirmasi
                            </span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-check-double text-green-500 mr-2"></i>Completed
                            </span>
                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded text-xs font-semibold flex items-center">
                                <i class="fas fa-check-double mr-1"></i>Selesai
                            </span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>Cancelled
                            </span>
                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded text-xs font-semibold flex items-center">
                                <i class="fas fa-times-circle mr-1"></i>Dibatalkan
                            </span>
                        </li>
                    </ul>

                    <div class="mt-4 p-4 bg-blue-50 rounded-md animate__animated animate__fadeIn animate__delay-1s">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 text-blue-500">
                                <i class="fas fa-info-circle text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-blue-800">Informasi</h6>
                                <p class="text-sm text-blue-700 mt-1">
                                    Jika ada pertanyaan, silakan hubungi customer service kami.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($pemesanan['status'] == 'confirmed' || $pemesanan['status'] == 'completed'): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 animate__animated animate__fadeInUp lg:col-span-1">
                    <div class="bg-green-600 text-white px-6 py-4">
                        <h2 class="text-xl font-semibold m-0 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Booking Berhasil
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-green-500 mr-2"></i>
                            Pemesanan Anda telah dikonfirmasi. Silakan cetak e-ticket atau simpan sebagai PDF.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="<?= base_url('booking/downloadTicket/' . $pemesanan['idpesan']) ?>" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-md transition duration-300 flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i>
                                Download E-Ticket
                            </a>
                            <?php if (isset($pembayaran) && $pembayaran['status_pembayaran'] == 'verified'): ?>
                                <a href="<?= base_url('booking/downloadInvoice/' . $pemesanan['idpesan']) ?>" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-md transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-file-invoice mr-2"></i>
                                    Download Invoice
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle cancel booking button
        const cancelButton = document.getElementById('cancel-booking-btn');
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                Swal.fire({
                    title: 'Batalkan Pemesanan?',
                    text: 'Apakah Anda yakin ingin membatalkan pemesanan ini? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('booking/cancel/' . $pemesanan['idpesan']) ?>';
                    }
                });
            });
        }

        // Function to show image preview in modal
        window.showImagePreview = function(src) {
            Swal.fire({
                imageUrl: src,
                imageAlt: 'Bukti Pembayaran',
                width: 'auto',
                padding: '1em',
                showConfirmButton: false,
                showCloseButton: true,
                background: '#fff',
                backdrop: `rgba(0,0,0,0.8)`
            });
        };

        <?php if ($pemesanan['status'] == 'pending' && isset($pembayaran) && $pembayaran && $pembayaran['status_pembayaran'] == 'pending'): ?>
            // Initialize payment timer with WebSocket or AJAX fallback
            const paymentTimer = new PaymentTimer(
                null, // Payment ID will be set by the server
                '<?= $pemesanan['idpesan'] ?>',
                null, // Expiration time will be retrieved from server
                'payment-timer',
                'timer-progress'
            );
            paymentTimer.connect();
        <?php endif; ?>

        // Handle download ticket button
        const downloadTicketBtn = document.getElementById('download-ticket-btn');
        if (downloadTicketBtn) {
            downloadTicketBtn.addEventListener('click', function() {
                // Show loading state
                Swal.fire({
                    title: 'Generating E-Ticket',
                    html: 'Please wait...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Simulate download (replace with actual download logic)
                setTimeout(() => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'E-Ticket has been downloaded',
                        icon: 'success',
                        confirmButtonColor: '#1E40AF'
                    });
                }, 1500);
            });
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
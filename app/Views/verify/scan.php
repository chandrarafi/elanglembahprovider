<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary">Verifikasi Tiket</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-primary text-white px-6 py-4">
                <h2 class="text-xl font-semibold">Scan QR Code</h2>
            </div>
            <div class="p-6">
                <div class="mb-4 text-center">
                    <p class="text-gray-600 mb-4">Arahkan kamera ke QR Code pada e-tiket untuk memverifikasi.</p>
                    <div id="qr-reader" class="mx-auto" style="width: 100%; max-width: 500px;"></div>
                    <div id="qr-reader-results" class="mt-4"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-primary text-white px-6 py-4">
                <h2 class="text-xl font-semibold">Hasil Verifikasi</h2>
            </div>
            <div class="p-6">
                <div id="verification-result" class="hidden">
                    <div id="verification-success" class="hidden">
                        <div class="bg-green-100 border-l-4 border-green-500 p-4 mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 text-green-500">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h6 class="text-sm font-medium text-green-800">Tiket Valid</h6>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold mb-4 text-primary">Detail Pemesanan</h3>
                        <div class="mb-6">
                            <table class="w-full">
                                <tr>
                                    <td class="py-2 text-gray-600">Kode Booking</td>
                                    <td class="py-2 font-semibold" id="kode-booking"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Nama Paket</td>
                                    <td class="py-2" id="nama-paket"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Nama Pemesan</td>
                                    <td class="py-2" id="nama-pemesan"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Tanggal Berangkat</td>
                                    <td class="py-2" id="tgl-berangkat"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Tanggal Selesai</td>
                                    <td class="py-2" id="tgl-selesai"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Jumlah Peserta</td>
                                    <td class="py-2" id="jumlah-peserta"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Status</td>
                                    <td class="py-2" id="status"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div id="verification-error" class="hidden">
                        <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 text-red-500">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h6 class="text-sm font-medium text-red-800" id="error-message">Tiket Tidak Valid</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="scan-instruction" class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <p class="text-gray-600">Hasil verifikasi akan ditampilkan disini setelah QR Code dipindai.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrReader = document.getElementById('qr-reader');
        const verificationResult = document.getElementById('verification-result');
        const verificationSuccess = document.getElementById('verification-success');
        const verificationError = document.getElementById('verification-error');
        const scanInstruction = document.getElementById('scan-instruction');
        const errorMessage = document.getElementById('error-message');

        function onScanSuccess(decodedText, decodedResult) {
            // Check if the URL is a valid ticket verification URL
            if (decodedText.includes('verify/ticket/')) {
                const kodeBooking = decodedText.split('/').pop();

                // Show loading state
                scanInstruction.classList.add('hidden');
                verificationResult.classList.remove('hidden');
                verificationSuccess.classList.add('hidden');
                verificationError.classList.add('hidden');

                // Fetch ticket data
                fetch(`<?= base_url('verify/ticket') ?>/${kodeBooking}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI with ticket details
                            document.getElementById('kode-booking').textContent = data.pemesanan.kode_booking;
                            document.getElementById('nama-paket').textContent = data.paket.nama;
                            document.getElementById('nama-pemesan').textContent = data.user.nama;
                            document.getElementById('tgl-berangkat').textContent = data.pemesanan.tgl_berangkat;
                            document.getElementById('tgl-selesai').textContent = data.pemesanan.tgl_selesai;
                            document.getElementById('jumlah-peserta').textContent = data.pemesanan.jumlah_peserta + ' orang';

                            // Format status display
                            let statusText = '';
                            switch (data.pemesanan.status) {
                                case 'confirmed':
                                    statusText = '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">Dikonfirmasi</span>';
                                    break;
                                case 'completed':
                                    statusText = '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs">Selesai</span>';
                                    break;
                                default:
                                    statusText = '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs">' + data.pemesanan.status + '</span>';
                            }
                            document.getElementById('status').innerHTML = statusText;

                            // Show success section
                            verificationSuccess.classList.remove('hidden');
                        } else {
                            // Show error section
                            verificationError.classList.remove('hidden');
                            errorMessage.textContent = data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        verificationError.classList.remove('hidden');
                        errorMessage.textContent = 'Terjadi kesalahan saat memverifikasi tiket.';
                    });
            } else {
                // Show invalid QR code error
                scanInstruction.classList.add('hidden');
                verificationResult.classList.remove('hidden');
                verificationError.classList.remove('hidden');
                errorMessage.textContent = 'QR Code tidak valid untuk verifikasi tiket.';
            }

            // Stop scanning
            html5QrcodeScanner.clear();
        }

        function onScanFailure(error) {
            // Handle scan failure, usually due to no QR code found in the image
            console.warn(`QR code scanning failure: ${error}`);
        }

        // Initialize QR scanner
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            }
        );

        // Add error handling
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        // Handle scanner errors
        html5QrcodeScanner.getState()
            .then(state => {
                console.log("Scanner initialized successfully", state);
            })
            .catch(error => {
                console.error("Error initializing QR scanner:", error);
                alertContainer.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin kamera dan menggunakan browser yang mendukung.
                    </div>
                `;
            });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
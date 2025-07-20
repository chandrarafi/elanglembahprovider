<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary animate__animated animate__fadeIn">Pembayaran</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <ul class="list-disc pl-5">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Payment Timer -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 animate__animated animate__fadeIn">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Batas Waktu Pembayaran</h2>
                    <?php
                    // Calculate remaining time
                    $now = new DateTime();
                    $expTime = isset($expiration_time) ? new DateTime($expiration_time) : new DateTime('+10 minutes');
                    $timeLeft = max(0, $expTime->getTimestamp() - $now->getTimestamp());
                    $minutes = floor($timeLeft / 60);
                    $seconds = $timeLeft % 60;
                    $timerDisplay = sprintf('%02d:%02d', $minutes, $seconds);
                    $percentage = min(100, ($timeLeft / 600) * 100); // 10 minutes = 600 seconds
                    ?>
                    <div id="payment-timer" class="text-xl font-bold text-red-600"><?= $timerDisplay ?></div>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                    <p>Silakan selesaikan pembayaran sebelum batas waktu berakhir. Jika waktu habis, pemesanan akan otomatis dibatalkan.</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                    <div id="timer-progress" class="bg-red-600 h-2.5 rounded-full" style="width: <?= $percentage ?>%"></div>
                </div>

                <!-- Add hidden input to store expiration time and remaining seconds -->
                <input type="hidden" id="expiration-time" value="<?= isset($expiration_time) ? $expiration_time : date('Y-m-d H:i:s', strtotime('+10 minutes')) ?>">
                <input type="hidden" id="remaining-seconds" value="<?= isset($remaining_seconds) ? $remaining_seconds : $timeLeft ?>">

                <!-- Debug information - Remove in production -->
                <div class="mt-3 text-xs text-gray-500">
                    <p class="hidden">Exp: <?= isset($expiration_time) ? $expiration_time : 'Not set' ?> | Remaining: <?= isset($remaining_seconds) ? $remaining_seconds : $timeLeft ?></p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6 animate__animated animate__fadeIn">
                <h2 class="text-xl font-semibold mb-4">Informasi Pembayaran</h2>

                <form action="<?= base_url('booking/savePayment') ?>" method="post" enctype="multipart/form-data" id="payment-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="idpesan" value="<?= $pemesanan['idpesan'] ?>">
                    <input type="hidden" name="totalbiaya" value="<?= $pemesanan['totalbiaya'] ?>">

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Tipe Pembayaran</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="tipe_pembayaran" value="dp" class="h-5 w-5 text-primary" id="payment-dp">
                                <div class="ml-2 p-3 border border-gray-300 rounded-md hover:border-primary hover:bg-blue-50 cursor-pointer flex-grow transition-all duration-300">
                                    <span class="font-medium">DP (50%)</span>
                                    <p class="text-sm text-gray-600">Rp <?= number_format($pemesanan['totalbiaya'] * 0.5, 0, ',', '.') ?></p>
                                </div>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipe_pembayaran" value="lunas" class="h-5 w-5 text-primary" checked id="payment-full">
                                <div class="ml-2 p-3 border border-gray-300 rounded-md hover:border-primary hover:bg-blue-50 cursor-pointer flex-grow transition-all duration-300">
                                    <span class="font-medium">Lunas (100%)</span>
                                    <p class="text-sm text-gray-600">Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="jumlah_bayar" class="block text-gray-700 font-medium mb-2">Jumlah Pembayaran</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">Rp</span>
                            </div>
                            <input type="text" id="jumlah_bayar" name="jumlah_bayar" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" value="<?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?>" readonly>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Total yang harus dibayar: <span class="font-semibold text-primary">Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></span></p>
                    </div>

                    <div class="mb-6">
                        <label for="metode_pembayaran" class="block text-gray-700 font-medium mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="payment-method-label">
                                <input type="radio" name="metode_pembayaran" value="Transfer Bank BCA" class="sr-only payment-method-input" data-bank="bca">
                                <div class="p-3 border rounded-lg hover:bg-blue-50 cursor-pointer text-center transition-all duration-300">
                                    <img src="<?= base_url('assets/images/logobank/bca.jpg') ?>" alt="BCA" class="h-8 mx-auto mb-2" onerror="this.src='<?= base_url('assets/images/bank.jpg') ?>'; this.onerror=null;">
                                    <span class="text-sm font-medium">BCA</span>
                                </div>
                            </label>
                            <label class="payment-method-label">
                                <input type="radio" name="metode_pembayaran" value="Transfer Bank Mandiri" class="sr-only payment-method-input" data-bank="mandiri">
                                <div class="p-3 border rounded-lg hover:bg-blue-50 cursor-pointer text-center transition-all duration-300">
                                    <img src="<?= base_url('assets/images/logobank/mandiri.jpg') ?>" alt="Mandiri" class="h-8 mx-auto mb-2" onerror="this.src='<?= base_url('assets/images/bank.jpg') ?>'; this.onerror=null;">
                                    <span class="text-sm font-medium">Mandiri</span>
                                </div>
                            </label>
                            <label class="payment-method-label">
                                <input type="radio" name="metode_pembayaran" value="Transfer Bank BNI" class="sr-only payment-method-input" data-bank="bni">
                                <div class="p-3 border rounded-lg hover:bg-blue-50 cursor-pointer text-center transition-all duration-300">
                                    <img src="<?= base_url('assets/images/logobank/bni.jpg') ?>" alt="BNI" class="h-8 mx-auto mb-2" onerror="this.src='<?= base_url('assets/images/bank.jpg') ?>'; this.onerror=null;">
                                    <span class="text-sm font-medium">BNI</span>
                                </div>
                            </label>
                            <label class="payment-method-label">
                                <input type="radio" name="metode_pembayaran" value="Transfer Bank BRI" class="sr-only payment-method-input" data-bank="bri">
                                <div class="p-3 border rounded-lg hover:bg-blue-50 cursor-pointer text-center transition-all duration-300">
                                    <img src="<?= base_url('assets/images/logobank/bri.jpg') ?>" alt="BRI" class="h-8 mx-auto mb-2" onerror="this.src='<?= base_url('assets/images/bank.jpg') ?>'; this.onerror=null;">
                                    <span class="text-sm font-medium">BRI</span>
                                </div>
                            </label>
                        </div>

                        <!-- Informasi Rekening Bank -->
                        <div id="bank-details" class="mt-4 hidden animate__animated animate__fadeIn">
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md">
                                <div class="flex items-center mb-2">
                                    <div id="bank-logo" class="w-12 h-12 mr-3 flex-shrink-0">
                                        <img src="" alt="Bank" class="w-full">
                                    </div>
                                    <div>
                                        <h4 id="bank-name" class="font-semibold text-lg text-gray-800">Bank</h4>
                                        <p class="text-sm text-gray-600">PT Elang Lembah Provider</p>
                                    </div>
                                </div>
                                <div class="flex flex-col space-y-1 mt-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">No. Rekening:</span>
                                        <span id="bank-account" class="font-medium text-gray-800">1234567890</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Atas Nama:</span>
                                        <span id="bank-account-name" class="font-medium text-gray-800">PT Elang Lembah Provider</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="bg-white p-3 rounded-md border border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span id="account-number-display" class="font-mono text-lg">1234567890</span>
                                            <button type="button" id="copy-account-number" class="text-blue-600 hover:text-blue-800 flex items-center text-sm font-medium">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                                </svg>
                                                Salin
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-gray-700">
                                    <span class="font-semibold">Petunjuk:</span> Transfer sesuai jumlah yang tertera, lalu upload bukti transfer pada form di bawah.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="bukti_pembayaran" class="block text-gray-700 font-medium mb-2">Bukti Pembayaran</label>
                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:bg-gray-50 transition-all duration-300" id="dropzone-container">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="bukti_pembayaran" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-800 focus-within:outline-none">
                                        <span>Unggah bukti pembayaran</span>
                                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" class="sr-only" accept="image/*" required>
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">Format: JPG, PNG, JPEG. Ukuran maksimal: 2MB</p>
                            </div>
                        </div>
                        <div id="preview-container" class="mt-3 hidden">
                            <p class="text-sm text-gray-700 mb-2">Preview:</p>
                            <div class="relative">
                                <img id="preview-image" class="max-h-64 rounded-lg border border-gray-300 shadow-sm mx-auto" alt="Preview bukti pembayaran">
                                <button type="button" id="remove-image" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="keterangan" class="block text-gray-700 font-medium mb-2">Keterangan (Opsional)</label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Tambahkan catatan atau keterangan jika diperlukan"></textarea>
                    </div>

                    <div class="flex justify-between">
                        <a href="<?= base_url('booking/history') ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Kembali
                        </a>
                        <button type="button" id="submit-payment-btn" class="bg-primary hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-md transition duration-300 animate__animated animate__pulse">
                            Kirim Bukti Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 animate__animated animate__fadeIn">
                <h2 class="text-xl font-semibold mb-4">Detail Pemesanan</h2>
                <div class="mb-4">
                    <img src="<?= base_url('uploads/paket/' . $pemesanan['foto']) ?>" alt="<?= esc($pemesanan['namapaket']) ?>" class="w-full h-40 object-cover rounded-lg mb-4">
                    <h3 class="text-lg font-bold text-gray-800"><?= esc($pemesanan['namapaket']) ?></h3>
                </div>

                <div class="border-t pt-4">
                    <table class="w-full text-sm">
                        <tr>
                            <td class="py-2 text-gray-600">Kode Booking</td>
                            <td class="py-2 font-semibold text-right"><?= $pemesanan['kode_booking'] ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Tanggal Berangkat</td>
                            <td class="py-2 text-right"><?= date('d M Y', strtotime($pemesanan['tgl_berangkat'])) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Tanggal Selesai</td>
                            <td class="py-2 text-right"><?= date('d M Y', strtotime($pemesanan['tgl_selesai'])) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Tanggal Pemesanan</td>
                            <td class="py-2 text-right"><?= date('d M Y', strtotime($pemesanan['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Status</td>
                            <td class="py-2 text-right"><?= ucfirst($pemesanan['status']) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Harga Paket</td>
                            <td class="py-2 text-right">Rp <?= number_format($pemesanan['harga'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="border-t">
                            <td class="py-2 font-bold">Total Biaya</td>
                            <td class="py-2 font-bold text-primary text-right">Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 animate__animated animate__fadeIn">
                <h2 class="text-xl font-semibold mb-4">Petunjuk Pembayaran</h2>
                <div class="text-sm text-gray-700">
                    <p class="mb-2">1. Pilih metode pembayaran yang tersedia.</p>
                    <p class="mb-2">2. Lakukan pembayaran sesuai dengan total biaya.</p>
                    <p class="mb-2">3. Upload bukti pembayaran pada form yang disediakan.</p>
                    <p class="mb-2">4. Admin akan memverifikasi pembayaran Anda dalam waktu 1x24 jam.</p>
                    <p class="mb-2">5. Status pemesanan akan diperbarui setelah pembayaran terverifikasi.</p>
                </div>

                <div class="mt-4 p-4 bg-blue-50 rounded-md animate__animated animate__fadeIn">
                    <h3 class="font-medium text-blue-800 mb-2">Rekening Pembayaran</h3>
                    <p class="text-sm text-blue-700 mb-1">BCA: 1234567890 a.n. PT Elang Lembah Provider</p>
                    <p class="text-sm text-blue-700 mb-1">Mandiri: 1234567890 a.n. PT Elang Lembah Provider</p>
                    <p class="text-sm text-blue-700 mb-1">BNI: 1234567890 a.n. PT Elang Lembah Provider</p>
                    <p class="text-sm text-blue-700">BRI: 1234567890 a.n. PT Elang Lembah Provider</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data rekening bank
        const bankAccounts = {
            bca: {
                name: 'Bank BCA',
                logo: '<?= base_url('assets/images/logobank/bca.jpg') ?>',
                account: '1234567890',
                accountName: 'PT Elang Lembah Provider'
            },
            mandiri: {
                name: 'Bank Mandiri',
                logo: '<?= base_url('assets/images/logobank/mandiri.jpg') ?>',
                account: '2345678901',
                accountName: 'PT Elang Lembah Provider'
            },
            bni: {
                name: 'Bank BNI',
                logo: '<?= base_url('assets/images/logobank/bni.jpg') ?>',
                account: '3456789012',
                accountName: 'PT Elang Lembah Provider'
            },
            bri: {
                name: 'Bank BRI',
                logo: '<?= base_url('assets/images/logobank/bri.jpg') ?>',
                account: '4567890123',
                accountName: 'PT Elang Lembah Provider'
            }
        };

        // Elemen UI
        const bankDetailsContainer = document.getElementById('bank-details');
        const bankLogo = document.getElementById('bank-logo').querySelector('img');
        const bankName = document.getElementById('bank-name');
        const bankAccount = document.getElementById('bank-account');
        const bankAccountName = document.getElementById('bank-account-name');
        const accountNumberDisplay = document.getElementById('account-number-display');
        const copyButton = document.getElementById('copy-account-number');

        // Handle pemilihan metode pembayaran
        const paymentMethods = document.querySelectorAll('.payment-method-input');
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                const bankCode = this.dataset.bank;
                console.log('Bank dipilih:', bankCode);

                // Sembunyikan semua container informasi pembayaran
                bankDetailsContainer.classList.add('hidden');

                if (bankAccounts[bankCode]) {
                    // Tampilkan informasi bank
                    const bankData = bankAccounts[bankCode];
                    console.log('Bank data:', bankData);

                    // Update informasi bank
                    bankLogo.src = bankData.logo;
                    bankName.textContent = bankData.name;
                    bankAccount.textContent = bankData.account;
                    bankAccountName.textContent = bankData.accountName;
                    accountNumberDisplay.textContent = bankData.account;

                    // Tampilkan container bank details
                    bankDetailsContainer.classList.remove('hidden');
                    console.log('Bank details container ditampilkan');
                }
            });
        });

        // Tambahkan event click untuk label bank juga (selain input radio)
        document.querySelectorAll('.payment-method-label').forEach(label => {
            label.addEventListener('click', function() {
                console.log('Bank label diklik');
                const input = this.querySelector('input[type="radio"]');
                if (input) {
                    // Trigger event change pada input radio
                    input.checked = true;
                    const event = new Event('change');
                    input.dispatchEvent(event);
                }
            });
        });

        // Handle salin nomor rekening
        copyButton.addEventListener('click', function() {
            const accountNumber = accountNumberDisplay.textContent;
            navigator.clipboard.writeText(accountNumber).then(() => {
                // Tampilkan notifikasi sukses disalin
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tersalin!
                `;
                copyButton.classList.add('text-green-600');

                // Kembalikan teks asli setelah 2 detik
                setTimeout(() => {
                    copyButton.innerHTML = originalText;
                    copyButton.classList.remove('text-green-600');
                }, 2000);
            }).catch(err => {
                console.error('Gagal menyalin: ', err);
            });
        });

        // Check booking status first
        const idPemesanan = '<?= $pemesanan['idpesan'] ?>';

        // Check if booking is still valid
        if ('<?= $pemesanan['status'] ?>' === 'cancelled') {
            Swal.fire({
                title: 'Pemesanan Dibatalkan',
                text: 'Pemesanan ini telah dibatalkan.',
                icon: 'warning',
                confirmButtonText: 'Kembali ke Daftar Pemesanan',
            }).then(() => {
                window.location.href = '<?= base_url('booking/history') ?>';
            });
            return;
        }

        // Verify booking status with server
        fetch('<?= base_url('booking/checkPaymentExpiration/') ?>' + idPemesanan)
            .then(response => response.json())
            .then(data => {
                console.log('Initial booking status check:', data);

                // If expired, show expired message and redirect
                if (data.expired) {
                    Swal.fire({
                        title: 'Waktu Pembayaran Habis',
                        text: 'Pemesanan Anda telah dibatalkan karena waktu pembayaran telah habis.',
                        icon: 'error',
                        confirmButtonText: 'Kembali ke Daftar Pemesanan',
                    }).then(() => {
                        window.location.href = '<?= base_url('booking/history') ?>';
                    });
                    return;
                }

                // Otherwise, continue with normal initialization
                initializePayment();
            })
            .catch(error => {
                console.error('Error checking booking status:', error);
                // Continue with normal initialization anyway
                initializePayment();
            });

        function initializePayment() {
            // Pilih BCA sebagai default
            const bcaInput = document.querySelector('input[data-bank="bca"]');
            if (bcaInput) {
                bcaInput.checked = true;
                const event = new Event('change');
                bcaInput.dispatchEvent(event);

                // Tambahkan styling
                const bcaLabel = bcaInput.closest('.payment-method-label');
                if (bcaLabel) {
                    const bcaCard = bcaLabel.querySelector('div');
                    bcaCard.classList.remove('border-gray-300');
                    bcaCard.classList.add('border-primary', 'bg-blue-50');
                }
            }

            // Handle payment type change (DP or Full)
            const totalBiaya = <?= $pemesanan['totalbiaya'] ?>;
            const paymentDpRadio = document.getElementById('payment-dp');
            const paymentFullRadio = document.getElementById('payment-full');
            const jumlahBayarInput = document.getElementById('jumlah_bayar');

            paymentDpRadio.addEventListener('change', function() {
                if (this.checked) {
                    // DP is 50% of the package price
                    const dpAmount = totalBiaya * 0.5;
                    jumlahBayarInput.value = formatRupiah(dpAmount);
                }
            });

            paymentFullRadio.addEventListener('change', function() {
                if (this.checked) {
                    // Full payment is the package price
                    jumlahBayarInput.value = formatRupiah(totalBiaya);
                }
            });

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            // Payment method selection styling
            const paymentMethodInputs = document.querySelectorAll('.payment-method-input');
            const paymentMethodLabels = document.querySelectorAll('.payment-method-label');

            paymentMethodLabels.forEach(label => {
                label.addEventListener('click', function() {
                    // Remove selected class from all labels
                    paymentMethodLabels.forEach(l => {
                        l.querySelector('div').classList.remove('border-primary', 'bg-blue-50');
                        l.querySelector('div').classList.add('border-gray-300');
                    });

                    // Add selected class to clicked label
                    this.querySelector('div').classList.remove('border-gray-300');
                    this.querySelector('div').classList.add('border-primary', 'bg-blue-50');

                    // Check the radio input
                    this.querySelector('input').checked = true;
                });
            });

            // Drag and drop for file upload
            const dropzone = document.getElementById('dropzone-container');
            const fileInput = document.getElementById('bukti_pembayaran');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropzone.classList.add('border-primary', 'bg-blue-50');
            }

            function unhighlight() {
                dropzone.classList.remove('border-primary', 'bg-blue-50');
            }

            dropzone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    handleFiles(files);
                }
            }

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                if (files && files[0]) {
                    const file = files[0];

                    // Check file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            title: 'Format File Tidak Valid',
                            text: 'Hanya file gambar (JPG, PNG, JPEG) yang diperbolehkan.',
                            icon: 'error',
                            confirmButtonColor: '#1E40AF'
                        });
                        fileInput.value = '';
                        return;
                    }

                    // Check file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            title: 'Ukuran File Terlalu Besar',
                            text: 'Ukuran maksimal file adalah 2MB.',
                            icon: 'error',
                            confirmButtonColor: '#1E40AF'
                        });
                        fileInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview-image').src = e.target.result;
                        document.getElementById('preview-container').classList.remove('hidden');
                        document.getElementById('preview-container').classList.add('animate__animated', 'animate__fadeIn');
                    }
                    reader.readAsDataURL(file);
                }
            }

            // Remove image button
            document.getElementById('remove-image').addEventListener('click', function() {
                fileInput.value = '';
                document.getElementById('preview-container').classList.add('hidden');
            });

            // Preview uploaded image
            const buktiPembayaran = document.getElementById('bukti_pembayaran');
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');

            buktiPembayaran.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        previewContainer.classList.add('animate__animated', 'animate__fadeIn');
                    }

                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewContainer.classList.add('hidden');
                }
            });

            // Payment form submission with validation
            const paymentForm = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-payment-btn');

            submitButton.addEventListener('click', function() {
                let metodeSelected = false;
                document.querySelectorAll('input[name="metode_pembayaran"]').forEach(input => {
                    if (input.checked) metodeSelected = true;
                });

                const bukti = document.getElementById('bukti_pembayaran').files.length;

                if (!metodeSelected) {
                    Swal.fire({
                        title: 'Metode Pembayaran Belum Dipilih',
                        text: 'Silakan pilih metode pembayaran terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonColor: '#1E40AF'
                    });
                    return;
                }

                if (!bukti) {
                    Swal.fire({
                        title: 'Bukti Pembayaran Belum Diupload',
                        text: 'Silakan upload bukti pembayaran terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonColor: '#1E40AF'
                    });
                    return;
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi Pembayaran',
                    html: 'Apakah bukti pembayaran yang Anda upload sudah benar?<br>Pastikan bukti pembayaran jelas dan sesuai dengan total biaya.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1E40AF',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Kirim Bukti Pembayaran',
                    cancelButtonText: 'Batal',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Mengirim Bukti Pembayaran',
                            html: 'Mohon tunggu sebentar...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        paymentForm.submit();
                    }
                });
            });

            // Initialize payment timer using the PaymentTimer class from scripts.php
            console.log('Starting payment timer initialization');

            // Make sure we have access to the PaymentTimer class
            if (typeof PaymentTimer === 'undefined') {
                console.error('PaymentTimer class not found. Check if scripts.php is loaded.');
                // Fallback to basic timer
                startBasicTimer();
            } else {
                try {
                    initializePaymentTimer();
                } catch (error) {
                    console.error('Error initializing payment timer:', error);
                    // Fallback to basic timer if there's an error
                    startBasicTimer();
                }
            }

            // Initialize payment timer
            function initializePaymentTimer() {
                const expirationTime = document.getElementById('expiration-time').value;
                const remainingSeconds = parseInt(document.getElementById('remaining-seconds').value);

                console.log('Payment init - ID:', idPemesanan);
                console.log('Payment init - Expiration Time:', expirationTime);
                console.log('Payment init - Remaining seconds:', remainingSeconds);

                // Store this data in sessionStorage to persist across page refreshes
                sessionStorage.setItem('payment_remaining_' + idPemesanan, remainingSeconds);
                sessionStorage.setItem('payment_expiration_' + idPemesanan, expirationTime);

                // Create payment timer instance using the class from scripts.php
                const paymentTimer = new PaymentTimer(
                    null, // Payment ID (optional)
                    idPemesanan, // Booking ID
                    expirationTime, // Expiration time
                    'payment-timer', // Timer element ID
                    'timer-progress' // Progress bar element ID
                );

                // Connect to WebSocket or fall back to AJAX
                paymentTimer.connect();

                // Store timer in window object to prevent garbage collection
                window.paymentTimer = paymentTimer;
            }

            // Basic timer fallback in case PaymentTimer isn't available
            function startBasicTimer() {
                console.log('Starting basic timer fallback');

                const timerDisplay = document.getElementById('payment-timer');
                const timerProgress = document.getElementById('timer-progress');
                const expirationTime = document.getElementById('expiration-time').value;
                let remainingSeconds = parseInt(document.getElementById('remaining-seconds').value);

                // Update timer immediately
                updateTimer();

                // Start interval
                const interval = setInterval(() => {
                    remainingSeconds--;

                    if (remainingSeconds <= 0) {
                        clearInterval(interval);
                        timerDisplay.textContent = '00:00';
                        timerProgress.style.width = '0%';

                        // Notify server
                        fetch('<?= base_url('booking/checkPaymentExpiration/') ?>' + idPemesanan)
                            .then(() => {
                                Swal.fire({
                                    title: 'Waktu Pembayaran Habis',
                                    text: 'Pemesanan Anda telah dibatalkan karena waktu pembayaran telah habis.',
                                    icon: 'error',
                                    confirmButtonText: 'Kembali ke Daftar Pemesanan',
                                }).then(() => {
                                    window.location.href = '<?= base_url('booking/history') ?>';
                                });
                            });
                    } else {
                        updateTimer();
                    }
                }, 1000);

                function updateTimer() {
                    const minutes = Math.floor(remainingSeconds / 60);
                    const seconds = remainingSeconds % 60;
                    timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                    // Update progress bar (assume 10 minute total)
                    const percentage = (remainingSeconds / 600) * 100;
                    timerProgress.style.width = `${percentage}%`;

                    // Update appearance
                    if (remainingSeconds <= 60) {
                        timerDisplay.classList.add('text-red-700', 'font-extrabold', 'animate__animated', 'animate__flash', 'animate__infinite');
                        timerProgress.classList.add('bg-red-700');
                    } else if (remainingSeconds <= 180) {
                        timerDisplay.classList.add('text-red-700');
                        timerProgress.classList.add('bg-red-700');
                    }
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>
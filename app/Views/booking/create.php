<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-yellow-600">Form Pemesanan Paket Wisata</h1>

    <div id="alert-container"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-yellow-500 text-white px-6 py-4">
                    <h5 class="font-semibold text-lg">Form Pemesanan Paket Wisata</h5>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row items-center">
                            <div class="w-full md:w-1/3 mb-4 md:mb-0 text-center">
                                <img src="<?= base_url('uploads/paket/' . $paket['foto']) ?>" class="w-full rounded-lg" alt="<?= $paket['namapaket'] ?>">
                            </div>
                            <div class="w-full md:w-2/3 md:pl-6">
                                <h4 class="text-xl font-bold mb-3"><?= $paket['namapaket'] ?></h4>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-gray-500">Harga Paket:</span>
                                    <span class="text-xl text-yellow-600 font-semibold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                </div>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-gray-500">Durasi:</span>
                                    <span class="text-gray-700"><?= $paket['durasi'] ?? 'Tidak ditentukan' ?> Hari</span>
                                </div>
                                <!-- <div class="mb-3">
                                    <span class="text-gray-500 block mb-1">Fasilitas:</span>
                                    <p class="text-gray-700"><?= $paket['fasilitas'] ?? 'Tidak ada informasi fasilitas' ?></p>
                                </div> -->
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-gray-500">Min-Max Peserta:</span>
                                    <span class="text-gray-700"><?= ($paket['minimalpeserta'] ?? '1') ?> - <?= ($paket['maximalpeserta'] ?? 'Tidak dibatasi') ?> orang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <form id="booking-form" class="slide-up">
                        <?= csrf_field() ?>
                        <!-- Ensure id_paket is properly formatted as a number -->
                        <input type="hidden" name="id_paket" value="<?= $paket['idpaket'] ?>">
                        <input type="hidden" name="durasi_paket" id="durasi_paket" value="<?= isset($paket['durasi']) && !empty($paket['durasi']) ? intval($paket['durasi']) : 1 ?>">

                        <h5 class="text-lg font-semibold mb-4">Informasi Pemesan</h5>

                        <div class="mb-4">
                            <label for="nama_pemesan" class="block text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" id="nama_pemesan" name="nama_pemesan" value="<?= session()->get('name') ?>">
                            <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-nama_pemesan"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="email_pemesan" class="block text-gray-700 mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" id="email_pemesan" name="email_pemesan" value="<?= session()->get('email') ?>">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-email_pemesan"></div>
                            </div>
                            <div class="mb-4">
                                <label for="telp_pemesan" class="block text-gray-700 mb-2">No. Telepon</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" id="telp_pemesan" name="telp_pemesan" value="<?= session()->get('phone') ?>">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-telp_pemesan"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="tgl_berangkat" class="block text-gray-700 mb-2">Tanggal Berangkat</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" id="tgl_berangkat" name="tgl_berangkat" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-tgl_berangkat"></div>
                            </div>
                            <div class="mb-4">
                                <label for="tgl_selesai" class="block text-gray-700 mb-2">Tanggal Selesai</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" id="tgl_selesai" name="tgl_selesai" min="<?= date('Y-m-d', strtotime('+2 day')) ?>" readonly>
                                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-tgl_selesai"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="jumlah_peserta" class="block text-gray-700 mb-2">Jumlah Peserta</label>
                                <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                    id="jumlah_peserta"
                                    name="jumlah_peserta"
                                    min="<?= $paket['minimalpeserta'] ?? 1 ?>"
                                    max="<?= $paket['maximalpeserta'] ?? '' ?>"
                                    value="<?= $paket['minimalpeserta'] ?? 1 ?>">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-jumlah_peserta"></div>
                            </div>
                            <div class="mb-4">
                                <label for="catatan" class="block text-gray-700 mb-2">Catatan Khusus</label>
                                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" id="catatan" name="catatan" rows="3" placeholder="Permintaan khusus, kebutuhan spesial, dll."></textarea>
                                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-catatan"></div>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <!-- <div class="mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total Harga</span>
                                <span class="text-2xl text-yellow-600 font-bold total-price">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                        </div> -->

                        <div class="flex flex-col space-y-3">
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-4 rounded-md transition duration-300" id="submitBtn">
                                <span id="submitBtnText">Buat Pemesanan</span>
                                <span id="loadingIndicator" class="hidden">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                            <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="w-full bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 font-semibold py-3 px-4 rounded-md text-center transition duration-300">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-yellow-500 text-white px-6 py-4">
                    <h5 class="font-semibold text-lg">Informasi Pemesanan</h5>
                </div>
                <div class="p-6">
                    <ul class="divide-y divide-gray-200 mb-6">
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700">Harga Paket</span>
                            <span>Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700">Total Pembayaran</span>
                            <span class="font-bold total-price">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                        </li>
                    </ul>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 text-yellow-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-yellow-800">Informasi</h6>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Setelah melakukan pemesanan, Anda akan diarahkan ke halaman pembayaran untuk menyelesaikan transaksi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 text-yellow-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-yellow-800">Penting</h6>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Pastikan data yang Anda masukkan sudah benar sebelum melanjutkan ke proses pembayaran.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menghitung tanggal selesai otomatis berdasarkan durasi
        const tglBerangkat = document.getElementById('tgl_berangkat');
        const tglSelesai = document.getElementById('tgl_selesai');
        const durasiPaketInput = document.getElementById('durasi_paket');
        const durasiPaket = parseInt(durasiPaketInput.value);

        tglBerangkat.addEventListener('change', function() {
            if (durasiPaket && tglBerangkat.value) {
                let tanggalSelesai = new Date(tglBerangkat.value);
                tanggalSelesai.setDate(tanggalSelesai.getDate() + durasiPaket - 1);

                // Format tanggal untuk input date (YYYY-MM-DD)
                const year = tanggalSelesai.getFullYear();
                const month = String(tanggalSelesai.getMonth() + 1).padStart(2, '0');
                const day = String(tanggalSelesai.getDate()).padStart(2, '0');

                tglSelesai.value = `${year}-${month}-${day}`;

                // Clear any error for tgl_selesai
                hideError('tgl_selesai');
            }
        });

        // Fungsi untuk menghitung total biaya
        const jumlahPeserta = document.getElementById('jumlah_peserta');
        const hargaSatuan = <?= $paket['harga'] ?? 0 ?>;

        jumlahPeserta.addEventListener('change', function() {
            // Total is just the package price, not multiplied by number of participants
            const total = hargaSatuan;
            document.querySelectorAll('.total-price').forEach(el => {
                el.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            });

            // Clear any error for jumlah_peserta
            hideError('jumlah_peserta');
        });

        // Form validation and submission via Ajax
        const form = document.getElementById('booking-form');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const alertContainer = document.getElementById('alert-container');

        // Validation functions
        function showError(field, message) {
            const errorElement = document.getElementById(`error-${field}`);
            const inputElement = document.getElementById(field);

            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove('hidden');
                inputElement.classList.add('border-red-500');
            }
        }

        function hideError(field) {
            const errorElement = document.getElementById(`error-${field}`);
            const inputElement = document.getElementById(field);

            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
                inputElement.classList.remove('border-red-500');
            }
        }

        function validateForm() {
            let isValid = true;

            // Reset all errors
            document.querySelectorAll('.error-message').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });

            document.querySelectorAll('input, textarea').forEach(el => {
                el.classList.remove('border-red-500');
            });

            // Validate nama_pemesan
            const nama_pemesan = document.getElementById('nama_pemesan').value.trim();
            if (nama_pemesan === '') {
                showError('nama_pemesan', 'Nama pemesan harus diisi');
                isValid = false;
            } else if (nama_pemesan.length < 3) {
                showError('nama_pemesan', 'Nama pemesan minimal 3 karakter');
                isValid = false;
            }

            // Validate email_pemesan
            const email_pemesan = document.getElementById('email_pemesan').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email_pemesan === '') {
                showError('email_pemesan', 'Email harus diisi');
                isValid = false;
            } else if (!emailRegex.test(email_pemesan)) {
                showError('email_pemesan', 'Format email tidak valid');
                isValid = false;
            }

            // Validate telp_pemesan
            const telp_pemesan = document.getElementById('telp_pemesan').value.trim();
            const phoneRegex = /^[0-9]{10,15}$/;
            if (telp_pemesan === '') {
                showError('telp_pemesan', 'No. Telepon harus diisi');
                isValid = false;
            } else if (!phoneRegex.test(telp_pemesan)) {
                showError('telp_pemesan', 'No. Telepon harus berisi 10-15 angka');
                isValid = false;
            }

            // Validate tgl_berangkat
            const tgl_berangkat = document.getElementById('tgl_berangkat').value;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(tgl_berangkat);
            selectedDate.setHours(0, 0, 0, 0);
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            if (tgl_berangkat === '') {
                showError('tgl_berangkat', 'Tanggal keberangkatan harus diisi');
                isValid = false;
            } else if (selectedDate < tomorrow) {
                showError('tgl_berangkat', 'Tanggal keberangkatan minimal H+1 dari sekarang');
                isValid = false;
            }

            // Validate jumlah_peserta
            const jumlah_peserta = document.getElementById('jumlah_peserta').value;
            const minPeserta = <?= $paket['minimalpeserta'] ?? 1 ?>;
            const maxPeserta = <?= $paket['maximalpeserta'] ?? 999 ?>;

            if (jumlah_peserta === '') {
                showError('jumlah_peserta', 'Jumlah peserta harus diisi');
                isValid = false;
            } else if (parseInt(jumlah_peserta) < minPeserta) {
                showError('jumlah_peserta', `Jumlah peserta minimal ${minPeserta} orang`);
                isValid = false;
            } else if (parseInt(jumlah_peserta) > maxPeserta) {
                showError('jumlah_peserta', `Jumlah peserta maksimal ${maxPeserta} orang`);
                isValid = false;
            }

            return isValid;
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                // Scroll to first error
                const firstError = document.querySelector('.error-message:not(.hidden)');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return;
            }

            // Show loading state
            submitBtnText.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');
            submitBtn.disabled = true;

            // Check paket availability
            const tgl_berangkat = document.getElementById('tgl_berangkat').value;
            const tgl_selesai = document.getElementById('tgl_selesai').value;
            const id_paket = '<?= $paket['idpaket'] ?>';

            // First check if the dates are available
            fetch('<?= base_url('booking/checkAvailability') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `id_paket=${id_paket}&tgl_berangkat=${tgl_berangkat}&tgl_selesai=${tgl_selesai}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        // Show error if dates are not available
                        alertContainer.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            Paket tidak tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.
                        </div>
                    `;

                        // Reset button state
                        submitBtnText.classList.remove('hidden');
                        loadingIndicator.classList.add('hidden');
                        submitBtn.disabled = false;

                        // Scroll to error message
                        alertContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        return;
                    }

                    // If dates are available, proceed with form submission
                    const formData = new FormData(form);

                    // Debug output for the form data being sent
                    console.log('Form data to be submitted:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    // Submit the form via Ajax
                    fetch('<?= base_url('booking/store') ?>', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            // Debug the content type
                            const contentType = response.headers.get('content-type');
                            console.log('Response content type:', contentType);

                            // Check if the response is JSON
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().catch(error => {
                                    console.error('Error parsing JSON:', error);
                                    throw new Error('Invalid JSON response');
                                });
                            } else {
                                // If not JSON, get the text and log it for debugging
                                return response.text().then(text => {
                                    console.error('Expected JSON response but got:', text);
                                    throw new Error('Expected JSON response but got text');
                                });
                            }
                        })
                        .then(data => {
                            if (data.success) {
                                // Redirect to payment page
                                window.location.href = data.redirect_url;
                            } else {
                                // Show validation errors
                                alertContainer.innerHTML = `
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                ${data.message}
                            </div>
                        `;

                                // Display field errors
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        showError(field, data.errors[field]);
                                    });

                                    // Scroll to first error
                                    const firstError = document.querySelector('.error-message:not(.hidden)');
                                    if (firstError) {
                                        firstError.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });
                                    }
                                } else {
                                    // Scroll to error message
                                    alertContainer.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }

                                // Reset button state
                                submitBtnText.classList.remove('hidden');
                                loadingIndicator.classList.add('hidden');
                                submitBtn.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alertContainer.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            Terjadi kesalahan saat memproses pemesanan. Silakan coba lagi.
                        </div>
                    `;

                            // Reset button state
                            submitBtnText.classList.remove('hidden');
                            loadingIndicator.classList.add('hidden');
                            submitBtn.disabled = false;

                            // Scroll to error message
                            alertContainer.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alertContainer.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        Terjadi kesalahan saat memeriksa ketersediaan paket. Silakan coba lagi.
                    </div>
                `;

                    // Reset button state
                    submitBtnText.classList.remove('hidden');
                    loadingIndicator.classList.add('hidden');
                    submitBtn.disabled = false;

                    // Scroll to error message
                    alertContainer.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });
        });

        // Input event listeners for real-time validation
        document.getElementById('nama_pemesan').addEventListener('input', function() {
            const value = this.value.trim();
            if (value === '') {
                showError('nama_pemesan', 'Nama pemesan harus diisi');
            } else if (value.length < 3) {
                showError('nama_pemesan', 'Nama pemesan minimal 3 karakter');
            } else {
                hideError('nama_pemesan');
            }
        });

        document.getElementById('email_pemesan').addEventListener('input', function() {
            const value = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (value === '') {
                showError('email_pemesan', 'Email harus diisi');
            } else if (!emailRegex.test(value)) {
                showError('email_pemesan', 'Format email tidak valid');
            } else {
                hideError('email_pemesan');
            }
        });

        document.getElementById('telp_pemesan').addEventListener('input', function() {
            const value = this.value.trim();
            const phoneRegex = /^[0-9]{10,15}$/;

            if (value === '') {
                showError('telp_pemesan', 'No. Telepon harus diisi');
            } else if (!phoneRegex.test(value)) {
                showError('telp_pemesan', 'No. Telepon harus berisi 10-15 angka');
            } else {
                hideError('telp_pemesan');
            }
        });

        document.getElementById('tgl_berangkat').addEventListener('change', function() {
            const value = this.value;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(value);
            selectedDate.setHours(0, 0, 0, 0);
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            if (value === '') {
                showError('tgl_berangkat', 'Tanggal keberangkatan harus diisi');
            } else if (selectedDate < tomorrow) {
                showError('tgl_berangkat', 'Tanggal keberangkatan minimal H+1 dari sekarang');
            } else {
                hideError('tgl_berangkat');
            }
        });

        document.getElementById('jumlah_peserta').addEventListener('input', function() {
            const value = this.value;
            const minPeserta = <?= $paket['minimalpeserta'] ?? 1 ?>;
            const maxPeserta = <?= $paket['maximalpeserta'] ?? 999 ?>;

            if (value === '') {
                showError('jumlah_peserta', 'Jumlah peserta harus diisi');
            } else if (parseInt(value) < minPeserta) {
                showError('jumlah_peserta', `Jumlah peserta minimal ${minPeserta} orang`);
            } else if (parseInt(value) > maxPeserta) {
                showError('jumlah_peserta', `Jumlah peserta maksimal ${maxPeserta} orang`);
            } else {
                hideError('jumlah_peserta');
            }
        });
    });
</script>
<?= $this->endSection() ?>
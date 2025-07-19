<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary">Form Pemesanan Paket Wisata</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-primary text-white px-6 py-4">
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
                                    <span class="text-xl text-primary font-semibold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <form action="<?= base_url('booking/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_paket" value="<?= $paket['idpaket'] ?>">

                        <h5 class="text-lg font-semibold mb-4">Informasi Pemesan</h5>

                        <div class="mb-4">
                            <label for="nama_pemesan" class="block text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="nama_pemesan" name="nama_pemesan" value="<?= session()->get('name') ?>" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="email_pemesan" class="block text-gray-700 mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="email_pemesan" name="email_pemesan" value="<?= session()->get('email') ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="telp_pemesan" class="block text-gray-700 mb-2">No. Telepon</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="telp_pemesan" name="telp_pemesan" value="<?= session()->get('phone') ?>" required>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="tgl_berangkat" class="block text-gray-700 mb-2">Tanggal Berangkat</label>
                            <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="tgl_berangkat" name="tgl_berangkat" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>

                        <hr class="my-6 border-gray-200">

                        <div class="mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total Harga</span>
                                <span class="text-2xl text-primary font-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-3">
                            <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-md transition duration-300">Buat Pemesanan</button>
                            <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="w-full bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 font-semibold py-3 px-4 rounded-md text-center transition duration-300">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-primary text-white px-6 py-4">
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
                            <span class="font-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                        </li>
                    </ul>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 text-blue-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h6 class="text-sm font-medium text-blue-800">Informasi</h6>
                                <p class="text-sm text-blue-700 mt-1">
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
<?= $this->endSection() ?>
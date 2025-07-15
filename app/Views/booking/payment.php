<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary">Pembayaran</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Pembayaran</h2>

                <form action="<?= base_url('booking/savePayment') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="idpesan" value="<?= $pemesanan['idpesan'] ?>">

                    <div class="mb-6">
                        <label for="jumlah_bayar" class="block text-gray-700 font-medium mb-2">Jumlah Pembayaran</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" value="<?= $pemesanan['totalbiaya'] ?>" required>
                        <p class="text-sm text-gray-500 mt-1">Total yang harus dibayar: Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></p>
                    </div>

                    <div class="mb-6">
                        <label for="metode_pembayaran" class="block text-gray-700 font-medium mb-2">Metode Pembayaran</label>
                        <select id="metode_pembayaran" name="metode_pembayaran" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="Transfer Bank BNI">Transfer Bank BNI</option>
                            <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="bukti_bayar" class="block text-gray-700 font-medium mb-2">Bukti Pembayaran</label>
                        <input type="file" id="bukti_bayar" name="bukti_bayar" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" accept="image/*" required>
                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, JPEG. Ukuran maksimal: 2MB</p>
                    </div>

                    <div class="mb-6">
                        <label for="keterangan" class="block text-gray-700 font-medium mb-2">Keterangan (Opsional)</label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>

                    <div class="flex justify-between">
                        <a href="<?= base_url('booking/detail/' . $pemesanan['idpesan']) ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Kembali
                        </a>
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-md transition duration-300">
                            Kirim Bukti Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
                            <td class="py-2 text-gray-600">Tanggal Kembali</td>
                            <td class="py-2 text-right"><?= date('d M Y', strtotime($pemesanan['tgl_kembali'])) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Jumlah Peserta</td>
                            <td class="py-2 text-right"><?= $pemesanan['jumlah_peserta'] ?> orang</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-600">Harga per Orang</td>
                            <td class="py-2 text-right">Rp <?= number_format($pemesanan['harga'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="border-t">
                            <td class="py-2 font-bold">Total Biaya</td>
                            <td class="py-2 font-bold text-primary text-right">Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Petunjuk Pembayaran</h2>
                <div class="text-sm text-gray-700">
                    <p class="mb-2">1. Pilih metode pembayaran yang tersedia.</p>
                    <p class="mb-2">2. Lakukan pembayaran sesuai dengan total biaya.</p>
                    <p class="mb-2">3. Upload bukti pembayaran pada form yang disediakan.</p>
                    <p class="mb-2">4. Admin akan memverifikasi pembayaran Anda dalam waktu 1x24 jam.</p>
                    <p class="mb-2">5. Status pemesanan akan diperbarui setelah pembayaran terverifikasi.</p>
                </div>

                <div class="mt-4 p-4 bg-blue-50 rounded-md">
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
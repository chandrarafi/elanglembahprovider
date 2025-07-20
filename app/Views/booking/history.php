<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-primary">Riwayat Pemesanan</h1>

    <?php if (empty($pemesanan)): ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
            <p>Anda belum memiliki riwayat pemesanan. <a href="<?= base_url('paket') ?>" class="text-blue-600 hover:text-blue-800 underline">Jelajahi paket wisata</a> untuk melakukan pemesanan.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pemesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berangkat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pemesanan as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $p['kode_booking'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $p['namapaket'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('d M Y', strtotime($p['tanggal'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= date('d M Y', strtotime($p['tgl_berangkat'])) ?>
                                <?php if (isset($reschedule_requests[$p['idpesan']])): ?>
                                    <?php foreach ($reschedule_requests[$p['idpesan']] as $request): ?>
                                        <?php if ($request['status'] == 'approved'): ?>
                                            <div class="text-xs text-green-600 mt-1">
                                                <span class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    Jadwal diubah
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp <?= number_format($p['totalbiaya'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?= ($p['status'] == 'pending') ? 'bg-yellow-100 text-yellow-800' : (($p['status'] == 'confirmed' || $p['status'] == 'waiting_confirmation') ? 'bg-blue-100 text-blue-800' : (($p['status'] == 'completed') ? 'bg-green-100 text-green-800' : (($p['status'] == 'cancelled') ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex flex-col space-y-2">
                                <div class="flex space-x-2">
                                    <a href="<?= base_url('booking/detail/' . $p['idpesan']) ?>" class="text-blue-600 hover:text-blue-900 px-3 py-1 bg-blue-100 rounded-md">Detail</a>
                                    <?php if ($p['status'] == 'pending'): ?>
                                        <a href="<?= base_url('booking/payment/' . $p['idpesan']) ?>" class="text-green-600 hover:text-green-900 px-3 py-1 bg-green-100 rounded-md">Bayar</a>
                                        <button onclick="confirmCancel(<?= $p['idpesan'] ?>)" class="text-red-600 hover:text-red-900 px-3 py-1 bg-red-100 rounded-md">Batal</button>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($reschedule_requests[$p['idpesan']])): ?>
                                    <div class="flex space-x-2">
                                        <a href="<?= base_url('reschedule/history/' . $p['idpesan']) ?>"
                                            class="text-indigo-600 hover:text-indigo-900 px-3 py-1 bg-indigo-100 rounded-md text-xs flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Riwayat Perubahan Jadwal
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (in_array($p['status'], ['confirmed', 'completed']) && strtotime($p['tgl_berangkat']) > time()): ?>
                                    <div class="flex space-x-2">
                                        <a href="<?= base_url('reschedule/request/' . $p['idpesan']) ?>"
                                            class="text-blue-600 hover:text-blue-900 px-3 py-1 bg-blue-100 rounded-md text-xs flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Ajukan Perubahan Jadwal
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->section('scripts') ?>
<script>
    function confirmCancel(idPesan) {
        Swal.fire({
            title: 'Konfirmasi Pembatalan',
            text: "Apakah Anda yakin ingin membatalkan pemesanan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('booking/cancel/') ?>' + idPesan;
            }
        });
    }
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
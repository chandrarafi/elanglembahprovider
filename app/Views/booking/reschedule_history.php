<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary">
        <i class="fas fa-history mr-2"></i>Riwayat Perubahan Jadwal
    </h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-primary text-white px-6 py-4">
            <h2 class="text-xl font-semibold m-0">
                <i class="fas fa-info-circle mr-2"></i>Informasi Pemesanan
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="mb-3"><strong><i class="fas fa-ticket-alt text-primary mr-1"></i> Kode Booking:</strong> <?= $pemesanan['kode_booking'] ?></p>
                    <p class="mb-3"><strong><i class="fas fa-tag text-primary mr-1"></i> Status:</strong>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                        <?= ($pemesanan['status'] == 'pending') ? 'bg-yellow-200 text-yellow-800' : (($pemesanan['status'] == 'confirmed') ? 'bg-blue-200 text-blue-800' : (($pemesanan['status'] == 'completed') ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800')) ?>">
                            <?php if ($pemesanan['status'] == 'pending'): ?>
                                <i class="fas fa-clock mr-1"></i>
                            <?php elseif ($pemesanan['status'] == 'confirmed'): ?>
                                <i class="fas fa-check-circle mr-1"></i>
                            <?php elseif ($pemesanan['status'] == 'completed'): ?>
                                <i class="fas fa-check-double mr-1"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle mr-1"></i>
                            <?php endif; ?>
                            <?= ucfirst($pemesanan['status']) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <p class="mb-3"><strong><i class="fas fa-calendar-check text-primary mr-1"></i> Tanggal Keberangkatan:</strong> <?= date('d M Y', strtotime($pemesanan['tgl_berangkat'])) ?></p>
                    <p class="mb-3"><strong><i class="fas fa-calendar-times text-primary mr-1"></i> Tanggal Selesai:</strong> <?= date('d M Y', strtotime($pemesanan['tgl_selesai'])) ?></p>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= base_url('booking/detail/' . $pemesanan['idpesan']) ?>" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-200 inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Detail Pemesanan
                </a>
            </div>
        </div>
    </div>

    <?php if (empty($requests)): ?>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h.01a1 1 0 000-2H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Belum ada riwayat pengajuan perubahan jadwal untuk pemesanan ini.
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4">
                <h2 class="text-xl font-semibold m-0">
                    <i class="fas fa-history mr-2"></i>Riwayat Pengajuan
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-6 py-3 text-left"><i class="fas fa-clock text-blue-600 mr-1"></i> Tanggal Pengajuan</th>
                            <th class="px-6 py-3 text-left"><i class="fas fa-calendar text-blue-600 mr-1"></i> Jadwal Lama</th>
                            <th class="px-6 py-3 text-left"><i class="fas fa-calendar-plus text-blue-600 mr-1"></i> Jadwal Baru</th>
                            <th class="px-6 py-3 text-left"><i class="fas fa-comment-alt text-blue-600 mr-1"></i> Alasan</th>
                            <th class="px-6 py-3 text-left"><i class="fas fa-tag text-blue-600 mr-1"></i> Status</th>
                            <th class="px-6 py-3 text-left"><i class="fas fa-sticky-note text-blue-600 mr-1"></i> Catatan Admin</th>
                            <th class="px-6 py-3 text-left"><i class="fas fa-cog text-blue-600 mr-1"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($request['created_at'])) ?></td>
                                <td class="px-6 py-4"><?= date('d M Y', strtotime($request['current_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['current_tgl_selesai'])) ?></td>
                                <td class="px-6 py-4"><?= date('d M Y', strtotime($request['requested_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['requested_tgl_selesai'])) ?></td>
                                <td class="px-6 py-4">
                                    <button class="text-blue-500 hover:underline flex items-center" onclick="showReason('<?= htmlspecialchars(addslashes($request['alasan'])) ?>')">
                                        <i class="fas fa-eye mr-1"></i> Lihat Alasan
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center w-fit
                                    <?= ($request['status'] == 'pending') ? 'bg-yellow-200 text-yellow-800' : (($request['status'] == 'approved') ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800') ?>">
                                        <?php if ($request['status'] == 'pending'): ?>
                                            <i class="fas fa-clock mr-1"></i>
                                        <?php elseif ($request['status'] == 'approved'): ?>
                                            <i class="fas fa-check-circle mr-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle mr-1"></i>
                                        <?php endif; ?>
                                        <?= ucfirst($request['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (!empty($request['admin_notes'])): ?>
                                        <button class="text-blue-500 hover:underline flex items-center" onclick="showNotes('<?= htmlspecialchars(addslashes($request['admin_notes'])) ?>')">
                                            <i class="fas fa-eye mr-1"></i> Lihat Catatan
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($request['status'] == 'pending'): ?>
                                        <a href="<?= base_url('reschedule/cancel/' . $request['id']) ?>" class="text-red-500 hover:underline flex items-center cancel-request" data-id="<?= $request['id'] ?>">
                                            <i class="fas fa-times-circle mr-1"></i> Batalkan
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->section('scripts') ?>
<script>
    // Show reason in a modal
    function showReason(reason) {
        Swal.fire({
            title: 'Alasan Perubahan Jadwal',
            text: reason,
            icon: 'info',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#3b82f6',
        });
    }

    // Show admin notes in a modal
    function showNotes(notes) {
        Swal.fire({
            title: 'Catatan Admin',
            text: notes,
            icon: 'info',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#3b82f6',
        });
    }

    // Handle cancel request confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const cancelLinks = document.querySelectorAll('.cancel-request');

        cancelLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');

                Swal.fire({
                    title: 'Batalkan Permintaan?',
                    text: "Anda yakin ingin membatalkan permintaan perubahan jadwal ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
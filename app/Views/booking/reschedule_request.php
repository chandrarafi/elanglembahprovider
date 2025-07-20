<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-primary">
        <i class="fas fa-calendar-alt mr-2"></i>Ajukan Perubahan Jadwal
    </h1>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__fadeIn">
            <ul class="list-disc pl-5">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><i class="fas fa-exclamation-triangle mr-1"></i> <?= $error ?></li>
                <?php endforeach; ?>
            </ul>
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
                    <p class="mb-3"><i class="fas fa-ticket-alt text-primary mr-2"></i> <strong>Kode Booking:</strong> <?= $pemesanan['kode_booking'] ?></p>
                    <p class="mb-3"><i class="fas fa-suitcase text-primary mr-2"></i> <strong>Nama Paket:</strong> <?= $paket['namapaket'] ?></p>
                    <p class="mb-3"><i class="fas fa-clock text-primary mr-2"></i> <strong>Durasi:</strong> <?= $paket['durasi'] ?> hari</p>
                </div>
                <div>
                    <p class="mb-3"><i class="fas fa-calendar text-primary mr-2"></i> <strong>Tanggal Pemesanan:</strong> <?= date('d M Y', strtotime($pemesanan['tanggal'])) ?></p>
                    <p class="mb-3"><i class="fas fa-calendar-day text-primary mr-2"></i> <strong>Jadwal Saat Ini:</strong> <?= date('d M Y', strtotime($pemesanan['tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($pemesanan['tgl_selesai'])) ?></p>
                    <p class="mb-3"><i class="fas fa-users text-primary mr-2"></i> <strong>Jumlah Peserta:</strong> <?= $pemesanan['jumlah_peserta'] ?> orang</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h2 class="text-xl font-semibold m-0">
                <i class="fas fa-edit mr-2"></i>Form Perubahan Jadwal
            </h2>
        </div>
        <div class="p-6">
            <form action="<?= base_url('reschedule/submit') ?>" method="post" id="rescheduleForm">
                <input type="hidden" name="id_pemesanan" value="<?= $pemesanan['idpesan'] ?>">

                <div class="mb-6">
                    <label for="requested_tgl_berangkat" class="block mb-2 text-sm font-medium text-gray-700">
                        <i class="fas fa-calendar-plus text-blue-600 mr-1"></i> Tanggal Keberangkatan Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="requested_tgl_berangkat" id="requested_tgl_berangkat"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        min="<?= $min_date ?>" required>
                    <p class="mt-1 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i> Pilih tanggal keberangkatan baru (minimal 3 hari dari sekarang)</p>
                </div>

                <div class="mb-6">
                    <label for="requested_tgl_selesai" class="block mb-2 text-sm font-medium text-gray-700">
                        <i class="fas fa-calendar-check text-blue-600 mr-1"></i> Tanggal Selesai Baru
                    </label>
                    <input type="text" id="requested_tgl_selesai"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100"
                        readonly>
                    <p class="mt-1 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i> Tanggal selesai akan dihitung otomatis berdasarkan durasi paket (<?= $paket['durasi'] ?> hari)</p>
                </div>

                <div class="mb-6">
                    <label for="alasan" class="block mb-2 text-sm font-medium text-gray-700">
                        <i class="fas fa-comment-alt text-blue-600 mr-1"></i> Alasan Perubahan Jadwal <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan" id="alasan" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Berikan alasan mengapa Anda perlu mengubah jadwal..." required></textarea>
                    <p class="mt-1 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i> Minimal 10 karakter. Jelaskan alasan Anda dengan detail.</p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong><i class="fas fa-info-circle mr-1"></i> Perhatian:</strong> Perubahan jadwal dikenakan biaya administrasi sesuai dengan kebijakan perusahaan. Permintaan perubahan jadwal akan ditinjau dan disetujui berdasarkan ketersediaan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <a href="<?= base_url('booking/detail/' . $pemesanan['idpesan']) ?>" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i> Ajukan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tglBerangkatInput = document.getElementById('requested_tgl_berangkat');
        const tglSelesaiInput = document.getElementById('requested_tgl_selesai');
        const durasiPaket = <?= $paket['durasi'] ?>;

        // Calculate end date based on start date and package duration
        function calculateEndDate() {
            if (tglBerangkatInput.value) {
                const startDate = new Date(tglBerangkatInput.value);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + durasiPaket);

                // Format date as YYYY-MM-DD
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');

                // Format for display
                const displayDate = `${day}/${month}/${year}`;
                tglSelesaiInput.value = displayDate;
            } else {
                tglSelesaiInput.value = '';
            }
        }

        // Add event listener for date input
        tglBerangkatInput.addEventListener('change', calculateEndDate);

        // Form validation
        const rescheduleForm = document.getElementById('rescheduleForm');
        rescheduleForm.addEventListener('submit', function(e) {
            let isValid = true;
            const alasan = document.getElementById('alasan').value.trim();

            if (alasan.length < 10) {
                e.preventDefault();
                alert('Alasan harus minimal 10 karakter.');
                isValid = false;
            }

            if (!tglBerangkatInput.value) {
                e.preventDefault();
                alert('Silakan pilih tanggal keberangkatan baru.');
                isValid = false;
            }

            return isValid;
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pemesanan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="/admin/pemesanan">Manajemen Pemesanan</a></li>
                <li class="breadcrumb-item"><a href="/admin/pemesanan/detail/<?= $pemesanan['idpesan'] ?>">Detail Pemesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Jadwal</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="/admin/pemesanan/detail/<?= $pemesanan['idpesan'] ?>" class="btn btn-secondary px-3">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-title d-flex align-items-center">
            <div><i class="bx bx-calendar-edit me-1 font-22 text-primary"></i></div>
            <h5 class="mb-0 text-primary">Form Perubahan Jadwal Pemesanan</h5>
        </div>
        <hr>

        <?php if (session()->has('error')): ?>
            <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-danger"><i class="bx bxs-message-square-x"></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-danger">Error</h6>
                        <div><?= session('error') ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('errors')): ?>
            <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-danger"><i class="bx bxs-message-square-x"></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-danger">Validasi Error</h6>
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12">
                <div class="alert border-0 border-start border-5 border-info alert-dismissible fade show py-2 bg-info-subtle">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-info"><i class="bx bx-info-circle"></i></div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-info">Informasi Pemesanan</h6>
                            <div>
                                <strong>Kode Booking:</strong> <?= $pemesanan['kode_booking'] ?? '-' ?><br>
                                <strong>Paket:</strong> <?= $pemesanan['namapaket'] ?? '-' ?><br>
                                <strong>Pelanggan:</strong> <?= $pemesanan['name'] ?? '-' ?><br>
                                <strong>Status:</strong> <?= formatStatus($pemesanan['status'] ?? 'pending') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= base_url('admin/reschedule/submitRequest') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="idpesan" value="<?= $pemesanan['idpesan'] ?>">
            <input type="hidden" name="current_tgl_berangkat" value="<?= $pemesanan['tgl_berangkat'] ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="current_dates" class="form-label">Jadwal Saat Ini</label>
                        <input type="text" class="form-control bg-light" readonly
                            value="<?= date('d-m-Y', strtotime($pemesanan['tgl_berangkat'])) ?> s/d <?= date('d-m-Y', strtotime($pemesanan['tgl_selesai'])) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="requested_tgl_berangkat" class="form-label">Tanggal Berangkat Baru <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="requested_tgl_berangkat" name="requested_tgl_berangkat"
                            value="<?= old('requested_tgl_berangkat') ?>"
                            min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="preview_tgl_selesai" class="form-label">Tanggal Selesai (Otomatis)</label>
                        <input type="date" class="form-control bg-light" id="preview_tgl_selesai" readonly>
                        <small class="text-muted">Tanggal selesai akan dihitung otomatis berdasarkan durasi paket (<?= $pemesanan['durasi'] ?? '1' ?> hari)</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan Perubahan Jadwal <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan" name="alasan" rows="9" required><?= old('alasan') ?></textarea>
                        <div class="form-text">Jelaskan alasan perubahan jadwal secara detail (minimal 10 karakter).</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="/admin/pemesanan/detail/<?= $pemesanan['idpesan'] ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan Jadwal</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Calculate end date based on departure date change
        $('#requested_tgl_berangkat').on('change', function() {
            calculateEndDate();
        });

        // Calculate end date on page load if departure date is set
        if ($('#requested_tgl_berangkat').val()) {
            calculateEndDate();
        }

        // Function to calculate end date based on package duration
        function calculateEndDate() {
            const durasi = <?= $pemesanan['durasi'] ?? 1 ?>;
            const startDate = $('#requested_tgl_berangkat').val();

            if (startDate) {
                // Calculate end date (start date + duration days)
                const start = new Date(startDate);
                const end = new Date(start);
                end.setDate(start.getDate() + durasi);

                // Format date as YYYY-MM-DD
                const endFormatted = end.toISOString().split('T')[0];
                $('#preview_tgl_selesai').val(endFormatted);
            }
        }
    });
</script>
<?= $this->endSection() ?>

<?php
// Helper function to format status text
function formatStatus($status)
{
    switch ($status) {
        case 'pending':
            return 'Pending';
        case 'down_payment':
            return 'DP Terbayar';
        case 'waiting_confirmation':
            return 'Menunggu Konfirmasi';
        case 'confirmed':
            return 'Dikonfirmasi';
        case 'completed':
            return 'Selesai';
        case 'cancelled':
            return 'Dibatalkan';
        default:
            return ucfirst($status);
    }
}
?>
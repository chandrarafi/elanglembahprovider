<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pemesanan</h5>
                    <span class="badge <?= ($pemesanan['status'] == 'pending') ? 'bg-warning' : (($pemesanan['status'] == 'confirmed' || $pemesanan['status'] == 'waiting_confirmation') ? 'bg-info' : (($pemesanan['status'] == 'completed') ? 'bg-success' : (($pemesanan['status'] == 'cancelled') ? 'bg-danger' : 'bg-secondary'))) ?>">
                        <?= ucfirst(str_replace('_', ' ', $pemesanan['status'])) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="<?= base_url('uploads/paket/' . $paket['foto']) ?>" class="img-fluid rounded" alt="<?= $paket['namapaket'] ?>">
                        </div>
                        <div class="col-md-8">
                            <h5><?= $paket['namapaket'] ?></h5>
                            <div class="d-flex justify-content-between">
                                <span>Harga Paket:</span>
                                <span class="fw-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">Informasi Pemesanan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Kode Pemesanan</td>
                                    <td>: <strong><?= $pemesanan['kode_pemesanan'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Pemesanan</td>
                                    <td>: <?= date('d M Y', strtotime($pemesanan['tgl_pemesanan'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal Berangkat</td>
                                    <td>: <?= date('d M Y', strtotime($pemesanan['tgl_berangkat'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Nama Pemesan</td>
                                    <td>: <?= $pemesanan['nama_pemesan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>: <?= $pemesanan['email_pemesan'] ?></td>
                                </tr>
                                <tr>
                                    <td>No. Telepon</td>
                                    <td>: <?= $pemesanan['telp_pemesan'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">Informasi Pembayaran</h6>
                    <div class="row mb-3">
                        <div class="col-12">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200">Total Pembayaran</td>
                                    <td>: <strong>Rp <?= number_format($pemesanan['total_harga'], 0, ',', '.') ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Status Pembayaran</td>
                                    <td>:
                                        <span class="badge <?= ($pemesanan['status'] == 'pending') ? 'bg-warning' : (($pemesanan['status'] == 'confirmed' || $pemesanan['status'] == 'waiting_confirmation') ? 'bg-info' : (($pemesanan['status'] == 'completed') ? 'bg-success' : (($pemesanan['status'] == 'cancelled') ? 'bg-danger' : 'bg-secondary'))) ?>">
                                            <?= ucfirst(str_replace('_', ' ', $pemesanan['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($pembayaran): ?>
                        <div class="alert alert-info">
                            <h6 class="mb-2">Detail Pembayaran:</h6>
                            <p class="mb-1">Metode Pembayaran: <?= $pembayaran['metode_pembayaran'] ?></p>
                            <p class="mb-1">Tanggal Pembayaran: <?= date('d M Y', strtotime($pembayaran['tanggal_pembayaran'])) ?></p>
                            <p class="mb-1">Status: <?= ucfirst($pembayaran['status']) ?></p>
                            <div class="mt-2">
                                <p class="mb-1">Bukti Pembayaran:</p>
                                <img src="<?= base_url('uploads/payments/' . $pembayaran['bukti_pembayaran']) ?>" class="img-thumbnail" style="max-height: 200px;" alt="Bukti Pembayaran">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('booking/history') ?>" class="btn btn-outline-secondary">Kembali ke Riwayat</a>

                        <?php if ($pemesanan['status'] == 'pending'): ?>
                            <div>
                                <a href="<?= base_url('booking/payment/' . $pemesanan['id_pemesanan']) ?>" class="btn btn-primary">Lakukan Pembayaran</a>
                                <a href="<?= base_url('booking/cancel/' . $pemesanan['id_pemesanan']) ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')">Batalkan Pemesanan</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Status</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Pending</span>
                            <span class="badge bg-warning">Menunggu Pembayaran</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Waiting Confirmation</span>
                            <span class="badge bg-info">Menunggu Konfirmasi</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Confirmed</span>
                            <span class="badge bg-primary">Pembayaran Dikonfirmasi</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Completed</span>
                            <span class="badge bg-success">Selesai</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Cancelled</span>
                            <span class="badge bg-danger">Dibatalkan</span>
                        </li>
                    </ul>
                    <div class="mt-3">
                        <div class="alert alert-info" role="alert">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                Jika ada pertanyaan, silakan hubungi customer service kami.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
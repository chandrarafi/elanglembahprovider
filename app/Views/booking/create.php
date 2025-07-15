<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Pemesanan Paket Wisata</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-3 mb-md-0 text-center">
                                <img src="<?= base_url('uploads/paket/' . $paket['foto']) ?>" class="img-fluid rounded" alt="<?= $paket['namapaket'] ?>">
                            </div>
                            <div class="col-md-8">
                                <h4 class="mb-3"><?= $paket['namapaket'] ?></h4>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Harga Paket:</span>
                                    <span class="h5 text-primary mb-0">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <form action="<?= base_url('booking/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_paket" value="<?= $paket['idpaket'] ?>">

                        <h5 class="mb-3">Informasi Pemesan</h5>

                        <div class="mb-3">
                            <label for="nama_pemesan" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_pemesan" name="nama_pemesan" value="<?= session()->get('name') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_pemesan" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_pemesan" name="email_pemesan" value="<?= session()->get('email') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telp_pemesan" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="telp_pemesan" name="telp_pemesan" value="<?= session()->get('phone') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="tgl_berangkat" class="form-label">Tanggal Berangkat</label>
                            <input type="date" class="form-control" id="tgl_berangkat" name="tgl_berangkat" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">Total Harga</span>
                                <span class="h4 text-primary mb-0">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Buat Pemesanan</button>
                            <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pemesanan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Harga Paket</span>
                            <span>Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Pembayaran</span>
                            <span class="fw-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                        </li>
                    </ul>

                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading mb-2"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                        <p class="small mb-0">
                            Setelah melakukan pemesanan, Anda akan diarahkan ke halaman pembayaran untuk menyelesaikan transaksi.
                        </p>
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Penting</h6>
                        <p class="small mb-0">
                            Pastikan data yang Anda masukkan sudah benar sebelum melanjutkan ke proses pembayaran.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Riwayat Pemesanan</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pemesanan)): ?>
                        <div class="alert alert-info">
                            Anda belum memiliki riwayat pemesanan. <a href="<?= base_url('paket') ?>" class="alert-link">Jelajahi paket wisata</a> untuk melakukan pemesanan.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Paket</th>
                                        <th>Tanggal Pemesanan</th>
                                        <th>Tanggal Berangkat</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pemesanan as $p): ?>
                                        <tr>
                                            <td><?= $p['kode_pemesanan'] ?></td>
                                            <td><?= $p['paket']['namapaket'] ?></td>
                                            <td><?= date('d M Y', strtotime($p['tgl_pemesanan'])) ?></td>
                                            <td><?= date('d M Y', strtotime($p['tgl_berangkat'])) ?></td>
                                            <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge <?= ($p['status'] == 'pending') ? 'bg-warning' : (($p['status'] == 'confirmed' || $p['status'] == 'waiting_confirmation') ? 'bg-info' : (($p['status'] == 'completed') ? 'bg-success' : (($p['status'] == 'cancelled') ? 'bg-danger' : 'bg-secondary'))) ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('booking/detail/' . $p['id_pemesanan']) ?>" class="btn btn-sm btn-info">Detail</a>
                                                <?php if ($p['status'] == 'pending'): ?>
                                                    <a href="<?= base_url('booking/payment/' . $p['id_pemesanan']) ?>" class="btn btn-sm btn-primary">Bayar</a>
                                                    <a href="<?= base_url('booking/cancel/' . $p['id_pemesanan']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')">Batal</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
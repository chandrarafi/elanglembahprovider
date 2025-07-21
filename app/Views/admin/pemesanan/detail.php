<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pemesanan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="/admin/pemesanan">Manajemen Pemesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Pemesanan</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="/admin/pemesanan" class="btn btn-secondary px-3">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>

        <?php if (($pemesanan['status'] ?? '') != 'completed'): ?>
            <a href="/admin/pemesanan/edit/<?= $pemesanan['idpesan'] ?? '' ?>" class="btn btn-warning px-3 ms-1">
                <i class="bx bx-edit"></i> Edit
            </a>
            <a href="#" onclick="confirmDelete(<?= $pemesanan['idpesan'] ?? '' ?>)" class="btn btn-danger px-3 ms-1">
                <i class="bx bx-trash"></i> Hapus
            </a>
            <?php if (isset($pemesanan['catatan']) && $pemesanan['catatan'] === "pemesanan dilakukan oleh admin"): ?>
                <a href="/admin/reschedule/createRequest/<?= $pemesanan['idpesan'] ?? '' ?>" class="btn btn-primary px-3 ms-1">
                    <i class="bx bx-calendar-edit"></i> Ubah Jadwal
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Detail Pemesanan -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0">Detail Pemesanan</h5>
                    <span class="ms-auto badge bg-<?= getStatusBadgeClass($pemesanan['status'] ?? 'pending') ?>"><?= formatStatus($pemesanan['status'] ?? 'pending') ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Kode Booking</h6>
                        <p class="mb-0 font-weight-bold"><?= $pemesanan['kode_booking'] ?? '-' ?></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Tanggal Pemesanan</h6>
                        <p class="mb-0"><?= isset($pemesanan['tanggal']) ? date('d M Y H:i', strtotime($pemesanan['tanggal'])) : '-' ?></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Status</h6>
                        <div>
                            <span class="badge bg-<?= getStatusBadgeClass($pemesanan['status'] ?? 'pending') ?> px-3 py-2">
                                <?= formatStatus($pemesanan['status'] ?? 'pending') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">Informasi Paket Wisata</h6>
                        <div class="d-flex">
                            <?php if (!empty($pemesanan['foto'])): ?>
                                <img src="<?= base_url('uploads/paket/' . $pemesanan['foto']) ?>" alt="<?= $pemesanan['namapaket'] ?? 'Paket' ?>" class="me-3 rounded" style="width: 100px; height: 70px; object-fit: cover;">
                            <?php else: ?>
                                <div class="me-3 rounded bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 70px;">
                                    <i class="bx bx-image text-secondary" style="font-size: 2rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-fill">
                                <h5 class="mb-1"><?= $pemesanan['namapaket'] ?? 'Paket Tidak Tersedia' ?></h5>
                                <div class="d-flex mb-1">
                                    <span class="me-3"><i class="bx bx-calendar me-1"></i> <?= isset($pemesanan['tgl_berangkat']) ? date('d M Y', strtotime($pemesanan['tgl_berangkat'])) : '-' ?> - <?= isset($pemesanan['tgl_selesai']) ? date('d M Y', strtotime($pemesanan['tgl_selesai'])) : '-' ?></span>
                                    <span><i class="bx bx-user me-1"></i> <?= $pemesanan['jumlah_peserta'] ?? 0 ?> orang</span>
                                </div>
                                <div>
                                    <span class="badge bg-info"><?= $pemesanan['durasi'] ?? '-' ?> Hari</span>
                                    <span class="badge bg-primary">Rp <?= isset($pemesanan['harga']) ? number_format($pemesanan['harga'], 0, ',', '.') : '0' ?>/orang</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Informasi Pelanggan</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Nama</td>
                                <td width="60%">: <?= $pemesanan['name'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>: <?= $pemesanan['email'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td>No. Telepon</td>
                                <td>: <?= $pemesanan['phone'] ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <!-- <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Informasi Pembayaran</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%">Total Biaya</td>
                                <td width="60%">: Rp <?= number_format($pemesanan['totalbiaya'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Jumlah Dibayar</td>
                                <td>: <?= isset($pembayaran['jumlah_bayar']) ? 'Rp ' . number_format($pembayaran['jumlah_bayar'], 0, ',', '.') : '-' ?></td>
                            </tr>
                            <tr>
                                <td>Tipe Pembayaran</td>
                                <td>: <?= isset($pembayaran['tipe_pembayaran']) ? ($pembayaran['tipe_pembayaran'] == 'dp' ? 'DP (50%)' : 'Lunas') : '-' ?></td>
                            </tr>
                            <tr>
                                <td>Status Pembayaran</td>
                                <td>:
                                    <?php if (isset($pembayaran['status_pembayaran'])): ?>
                                        <span class="badge bg-<?= getPaymentStatusBadgeClass($pembayaran['status_pembayaran']) ?>">
                                            <?= formatPaymentStatus($pembayaran['status_pembayaran']) ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div> -->
                </div>

                <!-- Catatan Pemesanan -->
                <?php if (!empty($pemesanan['catatan'])): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Catatan Pemesanan</h6>
                            <div class="bg-light p-3 rounded">
                                <?= $pemesanan['catatan'] ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Riwayat Pembayaran -->
                <?php if (isset($pembayaran)): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Riwayat Pembayaran</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Metode</th>
                                            <th>Tipe</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Jika ada riwayat pembayaran (bisa lebih dari satu jika ada pelunasan)
                                        $pembayaran_list = is_array($pembayaran) && isset($pembayaran[0]) ? $pembayaran : [$pembayaran];

                                        foreach ($pembayaran_list as $bayar):
                                            if (!is_array($bayar)) continue;
                                        ?>
                                            <tr>
                                                <td><?= isset($bayar['tanggal_bayar']) ? date('d M Y H:i', strtotime($bayar['tanggal_bayar'])) : '-' ?></td>
                                                <td>Rp <?= isset($bayar['jumlah_bayar']) ? number_format($bayar['jumlah_bayar'], 0, ',', '.') : '0' ?></td>
                                                <td><?= $bayar['metode_pembayaran'] ?? '-' ?></td>
                                                <td>
                                                    <?php if (isset($bayar['tipe_pembayaran'])): ?>
                                                        <span class="badge <?= $bayar['tipe_pembayaran'] == 'dp' ? 'bg-primary' : 'bg-success' ?>">
                                                            <?= $bayar['tipe_pembayaran'] == 'dp' ? 'DP (50%)' : 'Lunas' ?>
                                                        </span>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($bayar['status_pembayaran'])): ?>
                                                        <span class="badge bg-<?= getPaymentStatusBadgeClass($bayar['status_pembayaran']) ?>">
                                                            <?= formatPaymentStatus($bayar['status_pembayaran']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $bayar['keterangan'] ?? '-' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Update Status Pemesanan -->
        <?php if (($pemesanan['status'] ?? '') != 'completed'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Update Status Pemesanan</h5>
                </div>
                <div class="card-body">
                    <form id="updateStatusForm">
                        <div class="mb-3">
                            <label class="form-label">Status Pemesanan</label>
                            <select class="form-select" name="status" id="status">
                                <?php foreach ($status_options as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (($pemesanan['status'] ?? '') == $value) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3" placeholder="Masukkan keterangan untuk pelanggan..."></textarea>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" id="updateStatusBtn">
                                <span id="updateStatusSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status Pemesanan</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-0">
                        <i class="bx bx-check-circle me-2"></i>
                        Pemesanan ini telah <strong>selesai</strong> dan tidak dapat diubah lagi.
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Konfirmasi Pembayaran -->
        <?php
        // Cek apakah ada pembayaran pending yang perlu dikonfirmasi
        $pending_payment = null;
        foreach ($pembayaran as $pay) {
            if ($pay['status_pembayaran'] == 'pending') {
                $pending_payment = $pay;
                break;
            }
        }

        if ($pending_payment):
        ?>
            <div class="card mb-4 border-primary border-top border-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Konfirmasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Detail Pembayaran</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td>Tanggal Bayar</td>
                                <td>: <?= isset($pending_payment['tanggal_bayar']) ? date('d M Y H:i', strtotime($pending_payment['tanggal_bayar'])) : '-' ?></td>
                            </tr>
                            <tr>
                                <td>Metode Pembayaran</td>
                                <td>: <?= $pending_payment['metode_pembayaran'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td>Tipe Pembayaran</td>
                                <td>: <?= isset($pending_payment['tipe_pembayaran']) ? ($pending_payment['tipe_pembayaran'] == 'dp' ? 'DP (50%)' : 'Lunas') : '-' ?></td>
                            </tr>
                            <tr>
                                <td>Jumlah Bayar</td>
                                <td>: <strong>Rp <?= isset($pending_payment['jumlah_bayar']) ? number_format($pending_payment['jumlah_bayar'], 0, ',', '.') : '0' ?></strong></td>
                            </tr>
                        </table>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Bukti Pembayaran</h6>
                        <?php if (!empty($pending_payment['bukti_bayar'])): ?>
                            <img src="<?= base_url('uploads/payments/' . $pending_payment['bukti_bayar']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded mb-2 img-thumbnail">
                            <div class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="showImagePreview('<?= base_url('uploads/payments/' . $pending_payment['bukti_bayar']) ?>')">
                                    <i class="bx bx-search"></i> Lihat Full
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Bukti pembayaran tidak tersedia</div>
                        <?php endif; ?>
                    </div>

                    <form id="verifyPaymentForm">
                        <input type="hidden" name="idbayar" value="<?= $pending_payment['idbayar'] ?? '' ?>">
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Pembayaran</label>
                            <select class="form-select" name="status_pembayaran" id="status_pembayaran">
                                <option value="verified">Terima Pembayaran</option>
                                <option value="rejected">Tolak Pembayaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" name="keterangan" id="keterangan_pembayaran" rows="3" placeholder="Masukkan keterangan untuk pelanggan..."></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary" id="verifyPaymentBtn">
                                <span id="verifyPaymentSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                                <i class="bx bx-check-circle me-1"></i> Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php elseif (!empty($pembayaran)):
            // Ambil pembayaran terverifikasi terbaru untuk ditampilkan
            $latest_verified = null;
            $dp_payment = null;
            $full_payment = null;

            // Kategorikan pembayaran
            foreach ($pembayaran as $pay) {
                if ($pay['status_pembayaran'] == 'verified') {
                    $latest_verified = $pay; // Simpan pembayaran terverifikasi terbaru

                    // Kategorikan berdasarkan tipe
                    if ($pay['tipe_pembayaran'] == 'dp') {
                        $dp_payment = $pay;
                    } else if ($pay['tipe_pembayaran'] == 'lunas') {
                        $full_payment = $pay;
                    }
                }
            }

            // Jika ada pelunasan, tampilkan pelunasan sebagai pembayaran utama
            if ($full_payment) {
                $latest_verified = $full_payment;
            }

            if ($latest_verified):
            ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="text-muted mb-0">Status Pembayaran</h6>
                                <span class="badge bg-<?= getPaymentStatusBadgeClass($latest_verified['status_pembayaran']) ?> px-3 py-2">
                                    <?= formatPaymentStatus($latest_verified['status_pembayaran']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Detail Pembayaran</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Tanggal Bayar</td>
                                    <td>: <?= date('d M Y H:i', strtotime($latest_verified['tanggal_bayar'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td>: <?= $latest_verified['metode_pembayaran'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tipe Pembayaran</td>
                                    <td>: <?= ($latest_verified['tipe_pembayaran'] == 'dp') ? 'DP (50%)' : 'Lunas' ?></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Bayar</td>
                                    <td>: <strong>Rp <?= number_format($latest_verified['jumlah_bayar'], 0, ',', '.') ?></strong></td>
                                </tr>
                            </table>
                        </div>

                        <?php if (!empty($latest_verified['keterangan'])): ?>
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Keterangan</h6>
                                <div class="bg-light p-3 rounded">
                                    <?= $latest_verified['keterangan'] ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Bukti Pembayaran</h6>
                            <?php if (!empty($latest_verified['bukti_bayar'])): ?>
                                <img src="<?= base_url('uploads/payments/' . $latest_verified['bukti_bayar']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded mb-2">
                                <div class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showImagePreview('<?= base_url('uploads/payments/' . $latest_verified['bukti_bayar']) ?>')">
                                        <i class="bx bx-search"></i> Lihat Full
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">Bukti pembayaran tidak tersedia</div>
                            <?php endif; ?>
                        </div>

                        <?php if ($dp_payment && $full_payment && $dp_payment['idbayar'] != $full_payment['idbayar']): ?>
                            <!-- Tampilkan bukti pembayaran DP jika ada pelunasan -->
                            <hr class="my-4">
                            <div>
                                <h6 class="border-bottom pb-2 mb-3">Bukti Pembayaran DP Sebelumnya</h6>
                                <div class="mb-3">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td>Tanggal DP</td>
                                            <td>: <?= date('d M Y H:i', strtotime($dp_payment['tanggal_bayar'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah DP</td>
                                            <td>: <strong>Rp <?= number_format($dp_payment['jumlah_bayar'], 0, ',', '.') ?></strong></td>
                                        </tr>
                                    </table>
                                </div>

                                <?php if (!empty($dp_payment['bukti_bayar'])): ?>
                                    <div class="text-center mb-2">
                                        <img src="<?= base_url('uploads/payments/' . $dp_payment['bukti_bayar']) ?>" alt="Bukti DP" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showImagePreview('<?= base_url('uploads/payments/' . $dp_payment['bukti_bayar']) ?>')">
                                                <i class="bx bx-search"></i> Lihat Bukti DP
                                            </button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">Bukti pembayaran DP tidak tersedia</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bx bx-info-circle me-1"></i> Pelanggan belum melakukan pembayaran
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Timeline Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline-item <?= in_array($pemesanan['status'] ?? '', ['pending', 'down_payment', 'waiting_confirmation', 'confirmed', 'completed']) ? 'done' : 'cancelled' ?>">
                    <div class="timeline-marker <?= in_array($pemesanan['status'] ?? '', ['pending', 'down_payment', 'waiting_confirmation', 'confirmed', 'completed']) ? 'bg-success' : 'bg-danger' ?>"></div>
                    <div class="timeline-content">
                        <h6 class="mb-0">Pemesanan Dibuat</h6>
                        <p class="text-muted small mb-0"><?= isset($pemesanan['tanggal']) ? date('d M Y H:i', strtotime($pemesanan['tanggal'])) : '-' ?></p>
                    </div>
                </div>

                <div class="timeline-item <?= in_array($pemesanan['status'] ?? '', ['down_payment', 'waiting_confirmation', 'confirmed', 'completed']) ? 'done' : '' ?> <?= ($pemesanan['status'] ?? '') == 'cancelled' ? 'cancelled' : '' ?>">
                    <div class="timeline-marker <?= in_array($pemesanan['status'] ?? '', ['down_payment', 'waiting_confirmation', 'confirmed', 'completed']) ? 'bg-success' : (($pemesanan['status'] ?? '') == 'cancelled' ? 'bg-danger' : 'bg-secondary') ?>"></div>
                    <div class="timeline-content">
                        <h6 class="mb-0">Pembayaran</h6>
                        <p class="text-muted small mb-0">
                            <?php if (in_array($pemesanan['status'] ?? '', ['down_payment', 'waiting_confirmation', 'confirmed', 'completed']) && isset($pembayaran['tanggal_bayar'])): ?>
                                <?= date('d M Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?>
                            <?php elseif (($pemesanan['status'] ?? '') == 'cancelled'): ?>
                                Dibatalkan
                            <?php else: ?>
                                Menunggu
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="timeline-item <?= in_array($pemesanan['status'] ?? '', ['confirmed', 'completed']) ? 'done' : '' ?> <?= ($pemesanan['status'] ?? '') == 'cancelled' ? 'cancelled' : '' ?>">
                    <div class="timeline-marker <?= in_array($pemesanan['status'] ?? '', ['confirmed', 'completed']) ? 'bg-success' : (($pemesanan['status'] ?? '') == 'cancelled' ? 'bg-danger' : 'bg-secondary') ?>"></div>
                    <div class="timeline-content">
                        <h6 class="mb-0">Konfirmasi</h6>
                        <p class="text-muted small mb-0">
                            <?php if (in_array($pemesanan['status'] ?? '', ['confirmed', 'completed'])): ?>
                                Dikonfirmasi
                            <?php elseif (($pemesanan['status'] ?? '') == 'cancelled'): ?>
                                Dibatalkan
                            <?php else: ?>
                                Menunggu
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="timeline-item <?= ($pemesanan['status'] ?? '') == 'completed' ? 'done' : '' ?> <?= ($pemesanan['status'] ?? '') == 'cancelled' ? 'cancelled' : '' ?>">
                    <div class="timeline-marker <?= ($pemesanan['status'] ?? '') == 'completed' ? 'bg-success' : (($pemesanan['status'] ?? '') == 'cancelled' ? 'bg-danger' : 'bg-secondary') ?>"></div>
                    <div class="timeline-content">
                        <h6 class="mb-0">Perjalanan Wisata</h6>
                        <p class="text-muted small mb-0">
                            <?php if (($pemesanan['status'] ?? '') == 'completed'): ?>
                                Selesai
                            <?php elseif (($pemesanan['status'] ?? '') == 'cancelled'): ?>
                                Dibatalkan
                            <?php else: ?>
                                <?= isset($pemesanan['tgl_berangkat']) ? date('d M Y', strtotime($pemesanan['tgl_berangkat'])) : '-' ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Bukti Pembayaran" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus pemesanan ini? Semua data pembayaran terkait juga akan dihapus.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btn-delete" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        let imagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));

        // Update Status Pemesanan
        $('#updateStatusBtn').click(function() {
            const status = $('#status').val();
            const keterangan = $('#keterangan').val();
            const updateStatusBtn = $(this);
            const updateStatusSpinner = $('#updateStatusSpinner');

            // Konfirmasi jika status cancelled
            if (status === 'cancelled') {
                Swal.fire({
                    title: 'Konfirmasi Pembatalan',
                    text: 'Apakah Anda yakin ingin membatalkan pemesanan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateStatus();
                    }
                });
            } else {
                updateStatus();
            }

            function updateStatus() {
                // Tampilkan spinner dan nonaktifkan tombol
                updateStatusSpinner.removeClass('d-none');
                updateStatusBtn.prop('disabled', true);

                $.ajax({
                    url: '/admin/pemesanan/updateStatus/<?= $pemesanan['idpesan'] ?? 0 ?>',
                    type: 'POST',
                    data: {
                        status: status,
                        keterangan: keterangan
                    },
                    success: function(response) {
                        // Sembunyikan spinner
                        updateStatusSpinner.addClass('d-none');
                        updateStatusBtn.prop('disabled', false);

                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil',
                                text: 'Status pemesanan berhasil diperbarui',
                                icon: 'success',
                                confirmButtonColor: '#4a6fdc'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan',
                                icon: 'error',
                                confirmButtonColor: '#4a6fdc'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Sembunyikan spinner
                        updateStatusSpinner.addClass('d-none');
                        updateStatusBtn.prop('disabled', false);

                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                            icon: 'error',
                            confirmButtonColor: '#4a6fdc'
                        });
                    }
                });
            }
        });

        // Verify Payment
        $('#verifyPaymentBtn').click(function() {
            const status = $('#status_pembayaran').val();
            const keterangan = $('#keterangan_pembayaran').val();
            const idbayar = $('input[name="idbayar"]').val();
            const verifyPaymentBtn = $(this);
            const verifyPaymentSpinner = $('#verifyPaymentSpinner');

            // Konfirmasi jika pembayaran ditolak
            if (status === 'rejected') {
                Swal.fire({
                    title: 'Konfirmasi Penolakan',
                    text: 'Pembayaran akan ditolak dan pemesanan akan dibatalkan. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak Pembayaran',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        verifyPayment();
                    }
                });
            } else {
                verifyPayment();
            }

            function verifyPayment() {
                // Tampilkan spinner dan nonaktifkan tombol
                verifyPaymentSpinner.removeClass('d-none');
                verifyPaymentBtn.prop('disabled', true);

                $.ajax({
                    url: '/admin/pemesanan/verifyPayment/<?= $pemesanan['idpesan'] ?? 0 ?>',
                    type: 'POST',
                    data: {
                        status_pembayaran: status,
                        keterangan: keterangan,
                        idbayar: idbayar
                    },
                    success: function(response) {
                        // Sembunyikan spinner
                        verifyPaymentSpinner.addClass('d-none');
                        verifyPaymentBtn.prop('disabled', false);

                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil',
                                text: 'Status pembayaran berhasil diperbarui',
                                icon: 'success',
                                confirmButtonColor: '#4a6fdc'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan',
                                icon: 'error',
                                confirmButtonColor: '#4a6fdc'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Sembunyikan spinner
                        verifyPaymentSpinner.addClass('d-none');
                        verifyPaymentBtn.prop('disabled', false);

                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                            icon: 'error',
                            confirmButtonColor: '#4a6fdc'
                        });
                    }
                });
            }
        });
    });

    // Function to show image preview
    function showImagePreview(src) {
        document.getElementById('previewImage').src = src;
        const imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        imageModal.show();
    }
</script>

<script>
    // Function for delete confirmation
    function confirmDelete(id) {
        // Set URL untuk tombol hapus
        document.getElementById('btn-delete').href = `/admin/pemesanan/destroy/${id}`;
        // Tampilkan modal konfirmasi
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<style>
    /* Timeline Styles */
    .timeline-item {
        position: relative;
        padding-left: 40px;
        margin-bottom: 25px;
    }

    .timeline-marker {
        position: absolute;
        left: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px #ccc;
        top: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 10px;
        height: 100%;
        width: 2px;
        background-color: #ccc;
        top: 20px;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-item.done .timeline-marker {
        box-shadow: 0 0 0 3px #28a745;
    }

    .timeline-item.cancelled .timeline-marker {
        box-shadow: 0 0 0 3px #dc3545;
    }

    /* Spinner Styles */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>
<?= $this->endSection() ?>

<?php
// Helper functions for badges
function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'down_payment':
            return 'primary';
        case 'waiting_confirmation':
            return 'info';
        case 'confirmed':
            return 'success';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

function getPaymentStatusBadgeClass($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'verified':
            return 'success';
        case 'rejected':
            return 'danger';
        default:
            return 'secondary';
    }
}

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
            return $status;
    }
}

function formatPaymentStatus($status)
{
    switch ($status) {
        case 'pending':
            return 'Menunggu';
        case 'verified':
            return 'Diverifikasi';
        case 'rejected':
            return 'Ditolak';
        default:
            return $status;
    }
}
?>
<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Permintaan Perubahan Jadwal</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="/admin/reschedule">Daftar Permintaan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Permintaan</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="/admin/reschedule" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
        <div class="text-white"><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
        <div class="text-white"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<h6 class="mb-0 text-uppercase d-flex align-items-center gap-2">
    Status Permintaan:
    <span class="badge rounded-pill <?= $request['status'] == 'pending' ? 'bg-warning text-dark' : ($request['status'] == 'approved' ? 'bg-success' : 'bg-danger') ?>">
        <?php if ($request['status'] == 'pending'): ?>
            <i class="bx bx-time-five me-1"></i>
        <?php elseif ($request['status'] == 'approved'): ?>
            <i class="bx bx-check-circle me-1"></i>
        <?php else: ?>
            <i class="bx bx-x-circle me-1"></i>
        <?php endif; ?>
        <?= ucfirst($request['status']) ?>
    </span>
</h6>
<hr />

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card radius-10">
            <div class="card-header bg-primary bg-gradient">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0 text-white"><i class="bx bx-info-circle me-2"></i>Informasi Pemesanan</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 200px">Kode Booking</th>
                                <td><?= $request['kode_booking'] ?></td>
                            </tr>
                            <tr>
                                <th>Status Pemesanan</th>
                                <td>
                                    <span class="badge <?= $request['booking_status'] == 'pending' ? 'bg-warning text-dark' : ($request['booking_status'] == 'confirmed' ? 'bg-info' : ($request['booking_status'] == 'completed' ? 'bg-success' : 'bg-danger')) ?>">
                                        <i class="bx bx-check me-1"></i><?= ucfirst($request['booking_status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Nama Paket</th>
                                <td><?= $request['namapaket'] ?></td>
                            </tr>
                            <tr>
                                <th>Durasi</th>
                                <td><?= $request['durasi'] ?> hari</td>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <td>Rp <?= number_format($request['harga'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah Peserta</th>
                                <td><?= $request['jumlah_peserta'] ?> orang</td>
                            </tr>
                            <tr>
                                <th>Total Biaya</th>
                                <td>Rp <?= number_format($request['totalbiaya'], 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card radius-10">
            <div class="card-header bg-info bg-gradient">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0 text-white"><i class="bx bx-user me-2"></i>Informasi Pelanggan</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 200px"><i class="bx bx-user-circle text-info me-1"></i> Nama</th>
                                <td><?= $request['user_name'] ?></td>
                            </tr>
                            <tr>
                                <th><i class="bx bx-envelope text-info me-1"></i> Email</th>
                                <td><?= $request['email'] ?></td>
                            </tr>
                            <tr>
                                <th><i class="bx bx-phone text-info me-1"></i> No. Telepon</th>
                                <td><?= $request['phone'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card radius-10 mt-3">
            <div class="card-header bg-warning bg-gradient">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0 text-dark"><i class="bx bx-calendar-alt me-2"></i>Detail Permintaan Perubahan</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 200px"><i class="bx bx-time text-warning me-1"></i> Tanggal Pengajuan</th>
                                <td><?= date('d M Y H:i', strtotime($request['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <th><i class="bx bx-calendar text-warning me-1"></i> Jadwal Lama</th>
                                <td><?= date('d M Y', strtotime($request['current_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['current_tgl_selesai'])) ?></td>
                            </tr>
                            <tr>
                                <th><i class="bx bx-calendar-plus text-warning me-1"></i> Jadwal Baru</th>
                                <td><?= date('d M Y', strtotime($request['requested_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['requested_tgl_selesai'])) ?></td>
                            </tr>
                            <tr>
                                <th><i class="bx bx-comment-detail text-warning me-1"></i> Alasan</th>
                                <td><?= nl2br($request['alasan']) ?></td>
                            </tr>
                            <?php if (!empty($request['admin_notes'])): ?>
                                <tr>
                                    <th><i class="bx bx-notepad text-warning me-1"></i> Catatan Admin</th>
                                    <td><?= nl2br($request['admin_notes']) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($request['status'] == 'pending'): ?>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header bg-gradient-cosmic">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0 text-white"><i class="bx bx-cog me-2"></i>Proses Permintaan</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success btn-block" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bx bx-check-circle me-2"></i> Setujui Permintaan
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bx bx-x-circle me-2"></i> Tolak Permintaan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Permintaan Perubahan Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/reschedule/process') ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                        <input type="hidden" name="status" value="approved">

                        <p>Anda akan menyetujui permintaan perubahan jadwal untuk booking: <strong><?= $request['kode_booking'] ?></strong></p>
                        <p>Jadwal akan berubah dari <strong><?= date('d M Y', strtotime($request['current_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['current_tgl_selesai'])) ?></strong> menjadi <strong><?= date('d M Y', strtotime($request['requested_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['requested_tgl_selesai'])) ?></strong>.</p>

                        <div class="alert border-0 border-start border-5 border-warning alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="font-35 text-warning"><i class="bx bx-info-circle"></i></div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-warning">Perhatian</h6>
                                    <p>Jika disetujui, jadwal pemesanan akan langsung diubah sesuai permintaan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="approve_notes" class="form-label">Catatan (akan ditampilkan kepada pelanggan)</label>
                            <textarea class="form-control" name="admin_notes" id="approve_notes" rows="3" required></textarea>
                            <div class="form-text">Berikan informasi tambahan atau instruksi jika diperlukan.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Permintaan Perubahan Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('admin/reschedule/process') ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                        <input type="hidden" name="status" value="rejected">

                        <p>Anda akan menolak permintaan perubahan jadwal untuk booking: <strong><?= $request['kode_booking'] ?></strong></p>

                        <div class="mb-3">
                            <label for="reject_notes" class="form-label">Alasan Penolakan (akan ditampilkan kepada pelanggan)</label>
                            <textarea class="form-control" name="admin_notes" id="reject_notes" rows="3" required></textarea>
                            <div class="form-text">Jelaskan alasan penolakan dengan jelas.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
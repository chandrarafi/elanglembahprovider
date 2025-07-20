<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Permintaan Perubahan Jadwal</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Daftar Permintaan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
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

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Filter Status</label>
                <div class="btn-group" role="group" aria-label="Filter Status">
                    <button type="button" class="btn btn-outline-primary active filter-btn" data-filter="all">Semua</button>
                    <button type="button" class="btn btn-outline-warning filter-btn" data-filter="pending">Pending</button>
                    <button type="button" class="btn btn-outline-success filter-btn" data-filter="approved">Disetujui</button>
                    <button type="button" class="btn btn-outline-danger filter-btn" data-filter="rejected">Ditolak</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="rescheduleTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Booking</th>
                        <th>Pelanggan</th>
                        <th>Paket</th>
                        <th>Jadwal Lama</th>
                        <th>Jadwal Baru</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr data-status="<?= $request['status'] ?>">
                            <td><?= $request['id'] ?></td>
                            <td><?= $request['kode_booking'] ?></td>
                            <td><?= $request['user_name'] ?></td>
                            <td><?= $request['namapaket'] ?></td>
                            <td><?= date('d M Y', strtotime($request['current_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['current_tgl_selesai'])) ?></td>
                            <td><?= date('d M Y', strtotime($request['requested_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['requested_tgl_selesai'])) ?></td>
                            <td><?= date('d M Y H:i', strtotime($request['created_at'])) ?></td>
                            <td>
                                <span class="badge <?= $request['status'] == 'pending' ? 'bg-warning text-dark' : ($request['status'] == 'approved' ? 'bg-success' : 'bg-danger') ?>">
                                    <?php if ($request['status'] == 'pending'): ?>
                                        <i class="bx bx-time-five me-1"></i>
                                    <?php elseif ($request['status'] == 'approved'): ?>
                                        <i class="bx bx-check-circle me-1"></i>
                                    <?php else: ?>
                                        <i class="bx bx-x-circle me-1"></i>
                                    <?php endif; ?>
                                    <?= ucfirst($request['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= base_url('admin/reschedule/view/' . $request['id']) ?>" class="btn btn-sm btn-primary" title="Lihat Detail">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <?php if ($request['status'] == 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-success approve-btn" data-id="<?= $request['id'] ?>" data-bs-toggle="modal" data-bs-target="#approveModal" data-booking="<?= $request['kode_booking'] ?>" title="Setujui">
                                            <i class="bx bx-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="<?= $request['id'] ?>" data-bs-toggle="modal" data-bs-target="#rejectModal" data-booking="<?= $request['kode_booking'] ?>" title="Tolak">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                    <input type="hidden" name="id" id="approve_id">
                    <input type="hidden" name="status" value="approved">

                    <p>Anda akan menyetujui permintaan perubahan jadwal untuk booking: <strong id="approve_booking"></strong></p>
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
                    <input type="hidden" name="id" id="reject_id">
                    <input type="hidden" name="status" value="rejected">

                    <p>Anda akan menolak permintaan perubahan jadwal untuk booking: <strong id="reject_booking"></strong></p>

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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#rescheduleTable').DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang sesuai",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Filter by status
        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');

            const status = $(this).data('filter');
            if (status === 'all') {
                table.column(7).search('').draw();
            } else {
                table.column(7).search(status, true, false).draw();
            }
        });

        // Handle approve modal
        $('.approve-btn').click(function() {
            const id = $(this).data('id');
            const booking = $(this).data('booking');
            $('#approve_id').val(id);
            $('#approve_booking').text(booking);
        });

        // Handle reject modal
        $('.reject-btn').click(function() {
            const id = $(this).data('id');
            const booking = $(this).data('booking');
            $('#reject_id').val(id);
            $('#reject_booking').text(booking);
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pemesanan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Pemesanan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <h5 class="mb-0">Daftar Pemesanan</h5>
            <div class="ms-auto">
                <a href="<?= base_url('admin/pemesanan/create') ?>" class="btn btn-primary px-3">
                    <i class="bx bx-plus me-1"></i>Buat Pemesanan Baru
                </a>
            </div>
        </div>
        <hr>

        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Filter Status Pemesanan</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Semua Status</option>
                    <?php foreach ($status_options as $value => $label): ?>
                        <option value="<?= $value ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Filter Tanggal Pemesanan</label>
                <div class="input-group">
                    <input type="date" class="form-control" id="startDate">
                    <span class="input-group-text">sampai</span>
                    <input type="date" class="form-control" id="endDate">
                    <button class="btn btn-outline-secondary" type="button" id="applyDateFilter">
                        <span id="filterSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bx bx-filter-alt"></i> Terapkan
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Aksi</label>
                <div>
                    <button class="btn btn-outline-primary" id="refreshBtn">
                        <span id="refreshSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bx bx-refresh"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="pemesananTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Paket</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Pesan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Total Biaya</th>
                        <th>Status Pemesanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let pemesananTable;

    $(document).ready(function() {
        // Initialize DataTable
        pemesananTable = $('#pemesananTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/pemesanan/getPemesanan',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                    return d;
                },
                beforeSend: function() {
                    // Tampilkan spinner pada tombol saat memuat data baru
                    if ($('#statusFilter').data('changed')) {
                        // Reset flag
                        $('#statusFilter').data('changed', false);
                    }

                    if ($('#applyDateFilter').data('clicked')) {
                        $('#filterSpinner').removeClass('d-none');
                        $('#applyDateFilter').prop('disabled', true);
                        $('#applyDateFilter').data('clicked', false);
                    }

                    if ($('#refreshBtn').data('clicked')) {
                        $('#refreshSpinner').removeClass('d-none');
                        $('#refreshBtn').prop('disabled', true);
                        $('#refreshBtn').data('clicked', false);
                    }
                },
                complete: function() {
                    // Sembunyikan spinner setelah data dimuat
                    $('#filterSpinner').addClass('d-none');
                    $('#applyDateFilter').prop('disabled', false);
                    $('#refreshSpinner').addClass('d-none');
                    $('#refreshBtn').prop('disabled', false);
                }
            },
            columns: [{
                    data: 'kode_booking'
                },
                {
                    data: 'namapaket'
                },
                {
                    data: 'nama_pelanggan',
                    render: function(data, type, row) {
                        let result = data;
                        if (row.catatan === "pemesanan dilakukan oleh admin") {
                            result += ' <span class="badge bg-info text-white" title="Dibuat oleh Admin"><i class="bx bx-user-check"></i> Admin</span>';
                        }
                        return result;
                    }
                },
                {
                    data: 'tanggal'
                },
                {
                    data: 'tgl_berangkat'
                },
                {
                    data: 'totalbiaya',
                    render: function(data) {
                        return 'Rp ' + data;
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        let badge;
                        switch (data) {
                            case 'pending':
                                badge = 'warning';
                                break;
                            case 'down_payment':
                                badge = 'primary';
                                break;
                            case 'waiting_confirmation':
                                badge = 'info';
                                break;
                            case 'confirmed':
                                badge = 'success';
                                break;
                            case 'completed':
                                badge = 'success';
                                break;
                            case 'cancelled':
                                badge = 'danger';
                                break;
                            default:
                                badge = 'secondary';
                        }
                        return `<span class="badge bg-${badge}">${formatStatus(data)}</span>`;
                    }
                },
                {
                    data: 'idpesan',
                    orderable: false,
                    render: function(data, type, row) {
                        let buttons = `
                        <a href="/admin/pemesanan/detail/${data}" class="btn btn-sm btn-info">
                            <i class="bx bx-search"></i> Detail
                        </a>
                        `;

                        // Tambahkan tombol edit dan hapus jika status bukan completed
                        if (row.status !== 'completed') {
                            buttons += `
                            <a href="/admin/pemesanan/edit/${data}" class="btn btn-sm btn-warning ms-1">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                            <a href="#" onclick="confirmDelete(${data})" class="btn btn-sm btn-danger ms-1">
                                <i class="bx bx-trash"></i> Hapus
                            </a>
                            `;

                            // Tambahkan tombol ubah jadwal untuk pemesanan oleh admin
                            if (row.catatan === "pemesanan dilakukan oleh admin") {
                                buttons += `
                                <a href="/admin/reschedule/createRequest/${data}" class="btn btn-sm btn-primary ms-1">
                                    <i class="bx bx-calendar-edit"></i> Ubah Jadwal
                                </a>
                                `;
                            }
                        }

                        return buttons;
                    }
                }
            ],
            order: [
                [3, 'desc']
            ], // Order by tanggal pemesanan desc
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            }
        });

        // Filter event handlers
        $('#statusFilter').change(function() {
            // Set flag untuk menandai bahwa filter status diubah
            $(this).data('changed', true);
            pemesananTable.ajax.reload();
        });

        $('#applyDateFilter').click(function() {
            // Set flag untuk menandai bahwa tombol filter diklik
            $(this).data('clicked', true);
            pemesananTable.ajax.reload();
        });

        $('#refreshBtn').click(function() {
            // Set flag untuk menandai bahwa tombol refresh diklik
            $(this).data('clicked', true);

            // Clear filters
            $('#statusFilter').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            pemesananTable.ajax.reload();
        });
    });

    // Helper function to format status
    function formatStatus(status) {
        switch (status) {
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
                return status;
        }
    }
</script>

<style>
    /* Spinner Styles */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }

    /* DataTable Processing Indicator */
    div.dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 60px;
        margin-left: -50%;
        margin-top: -25px;
        padding-top: 20px;
        text-align: center;
        background: rgba(255, 255, 255, 0.9);
    }
</style>

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

<script>
    function confirmDelete(id) {
        // Set URL untuk tombol hapus
        document.getElementById('btn-delete').href = `/admin/pemesanan/destroy/${id}`;
        // Tampilkan modal konfirmasi
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
<?= $this->endSection() ?>
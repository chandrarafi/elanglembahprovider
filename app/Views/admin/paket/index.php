<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Paket Wisata</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Paket Wisata</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="/admin/paket/create" class="btn btn-primary px-3">
            <i class="bx bx-plus"></i> Tambah Paket Wisata
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Filter Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Filter Kategori</label>
                <select class="form-select" id="kategoriFilter">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori as $k) : ?>
                        <option value="<?= $k['idkategori'] ?>"><?= $k['namakategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="paketTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID Paket</th>
                        <th>Nama Paket</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Paket Wisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <img id="detailFoto" src="" alt="Foto Paket" class="img-fluid rounded">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>ID Paket</th>
                                <td id="detailId"></td>
                            </tr>
                            <tr>
                                <th>Nama Paket</th>
                                <td id="detailNama"></td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td id="detailKategori"></td>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <td id="detailHarga"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="detailStatus"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Deskripsi:</h6>
                        <div id="detailDeskripsi" class="border p-3 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let paketTable;
    let detailModal;

    $(document).ready(function() {
        detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

        // Initialize DataTable
        paketTable = $('#paketTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/getPaket',
                data: function(d) {
                    d.statuspaket = $('#statusFilter').val();
                    d.idkategori = $('#kategoriFilter').val();
                    return d;
                }
            },
            columns: [{
                    data: 'idpaket'
                },
                {
                    data: 'namapaket'
                },
                {
                    data: 'namakategori'
                },
                {
                    data: 'harga_formatted'
                },
                {
                    data: 'statuspaket',
                    render: function(data) {
                        return `<span class="badge ${data === 'active' ? 'bg-success' : 'bg-danger'}">${data === 'active' ? 'Aktif' : 'Tidak Aktif'}</span>`;
                    }
                },
                {
                    data: 'foto',
                    render: function(data) {
                        if (data) {
                            return `<img src="/uploads/paket/${data}" alt="Foto Paket" class="img-thumbnail" style="max-height: 50px;">`;
                        }
                        return '<span class="badge bg-secondary">Tidak ada foto</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-sm btn-info" onclick="viewDetail('${data.idpaket}')">
                            <i class="bx bx-search"></i>
                        </button>
                        <a href="/admin/paket/edit/${data.idpaket}" class="btn btn-sm btn-warning">
                            <i class="bx bx-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deletePaket('${data.idpaket}')">
                            <i class="bx bx-trash"></i>
                        </button>
                    `;
                    }
                }
            ],
            order: [
                [0, 'asc']
            ]
        });

        // Filter event handlers
        $('#statusFilter, #kategoriFilter').change(function() {
            paketTable.ajax.reload();
        });
    });

    function viewDetail(id) {
        $.get(`/admin/paket/show/${id}`, function(response) {
            if (response.status === 'success') {
                const paket = response.data;

                // Set detail data
                $('#detailId').text(paket.idpaket);
                $('#detailNama').text(paket.namapaket);
                $('#detailKategori').text(paket.namakategori);
                $('#detailHarga').text('Rp ' + new Intl.NumberFormat('id-ID').format(paket.harga));
                $('#detailStatus').html(`<span class="badge ${paket.statuspaket === 'active' ? 'bg-success' : 'bg-danger'}">${paket.statuspaket === 'active' ? 'Aktif' : 'Tidak Aktif'}</span>`);
                $('#detailDeskripsi').html(paket.deskripsi ? paket.deskripsi : '<em>Tidak ada deskripsi</em>');

                // Set foto
                if (paket.foto) {
                    $('#detailFoto').attr('src', `/uploads/paket/${paket.foto}`).show();
                } else {
                    $('#detailFoto').attr('src', '/assets/images/icons/package.png').show();
                }

                // Show modal
                detailModal.show();
            } else {
                Swal.fire('Error', response.message || 'Gagal mengambil data paket', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengambil data paket', 'error');
        });
    }

    function deletePaket(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus paket wisata ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/paket/delete/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status === 'success') {
                            paketTable.ajax.reload();
                            Swal.fire('Sukses', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus paket', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghapus paket', 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
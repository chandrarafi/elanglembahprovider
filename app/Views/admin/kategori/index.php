<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Kategori</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Kategori</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <button type="button" class="btn btn-primary px-3" onclick="showAddModal()">
            <i class="bx bx-plus"></i> Tambah Kategori
        </button>
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
        </div>

        <div class="table-responsive">
            <table id="kategoriTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Kategori -->
<div class="modal fade" id="kategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="kategoriForm" onsubmit="saveKategori(event)" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Hidden ID field -->
                    <input type="hidden" id="idkategori" name="idkategori">

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="namakategori" name="namakategori" required>
                        <div class="invalid-feedback" id="namakategori-error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                        <div class="invalid-feedback" id="status-error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg" onchange="validateAndPreviewImage(this)">
                        <div class="invalid-feedback" id="foto-error"></div>
                        <small class="text-muted">Format: JPG, JPEG, PNG. Ukuran maksimal: 2MB</small>
                        <div class="mt-2">
                            <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 150px; display: none;">
                        </div>
                        <div id="currentImage" class="mt-2" style="display: none;">
                            <p>Foto saat ini:</p>
                            <img id="currentImagePreview" src="" alt="Current" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let kategoriTable;
    let kategoriModal;

    $(document).ready(function() {
        kategoriModal = new bootstrap.Modal(document.getElementById('kategoriModal'));

        // Initialize DataTable
        kategoriTable = $('#kategoriTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/getKategori',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    return d;
                }
            },
            columns: [{
                    data: 'idkategori'
                },
                {
                    data: 'namakategori'
                },
                {
                    data: 'status',
                    render: function(data) {
                        return `<span class="badge ${data === 'active' ? 'bg-success' : 'bg-danger'}">${data === 'active' ? 'Aktif' : 'Tidak Aktif'}</span>`;
                    }
                },
                {
                    data: 'foto',
                    render: function(data) {
                        if (data) {
                            return `<img src="/uploads/kategori/${data}" alt="Foto Kategori" class="img-thumbnail" style="max-height: 50px;">`;
                        }
                        return '<span class="badge bg-secondary">Tidak ada foto</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-sm btn-info" onclick="editKategori('${data.idkategori}')">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteKategori('${data.idkategori}')">
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
        $('#statusFilter').change(function() {
            kategoriTable.ajax.reload();
        });
    });

    function showAddModal() {
        // Reset form
        $('#kategoriForm')[0].reset();

        // Hapus ID
        $('#idkategori').val('');

        // Hapus semua error
        $('.invalid-feedback').html('');
        $('.is-invalid').removeClass('is-invalid');

        // Sembunyikan preview
        $('#imagePreview').hide();
        $('#currentImage').hide();

        // Tampilkan modal
        kategoriModal.show();
    }

    function editKategori(id) {
        // Reset form
        $('#kategoriForm')[0].reset();
        $('.invalid-feedback').html('');
        $('.is-invalid').removeClass('is-invalid');

        // Sembunyikan preview
        $('#imagePreview').hide();

        // Pastikan ID valid
        if (!id) {
            Swal.fire('Error', 'ID kategori tidak valid', 'error');
            return;
        }

        $.get(`/admin/getKategori/${id}`, function(response) {
            if (response.status === 'success') {
                const kategori = response.data;

                // Set nilai form
                $('#idkategori').val(kategori.idkategori);
                $('#namakategori').val(kategori.namakategori);
                $('#status').val(kategori.status);

                // Tampilkan foto saat ini jika ada
                if (kategori.foto) {
                    $('#currentImagePreview').attr('src', `/uploads/kategori/${kategori.foto}`);
                    $('#currentImage').show();
                } else {
                    $('#currentImage').hide();
                }

                // Tampilkan modal
                kategoriModal.show();
            } else {
                Swal.fire('Error', response.message || 'Gagal mengambil data kategori', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengambil data kategori', 'error');
        });
    }

    function saveKategori(e) {
        e.preventDefault();

        // Reset error messages
        $('.invalid-feedback').html('');
        $('.is-invalid').removeClass('is-invalid');

        const form = $('#kategoriForm')[0];
        const formData = new FormData(form);
        const id = $('#idkategori').val();
        const url = id ? `/admin/updateKategori/${id}` : '/admin/createKategori';

        // Show loading
        const submitBtn = $(form).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    kategoriModal.hide();
                    kategoriTable.ajax.reload();
                    Swal.fire('Sukses', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message || 'Gagal menyimpan data', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                if (response.errors) {
                    // Show validation errors
                    Object.keys(response.errors).forEach(function(key) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}-error`).html(response.errors[key]);
                    });
                    Swal.fire('Error', 'Ada kesalahan pada form. Silakan periksa kembali.', 'error');
                } else {
                    Swal.fire('Error', response.message || 'Gagal menyimpan data', 'error');
                }
            },
            complete: function() {
                // Restore button
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    }

    function deleteKategori(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus kategori ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/deleteKategori/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status === 'success') {
                            kategoriTable.ajax.reload();
                            Swal.fire('Sukses', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus kategori', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghapus kategori', 'error');
                    }
                });
            }
        });
    }

    function validateAndPreviewImage(input) {
        const file = input.files[0];
        const preview = document.getElementById('imagePreview');
        const fotoError = $('#foto-error');
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        // Reset
        preview.src = '';
        preview.style.display = 'none';
        $(input).removeClass('is-invalid');
        fotoError.html('');

        if (file) {
            // Validasi tipe file
            if (!allowedTypes.includes(file.type)) {
                $(input).addClass('is-invalid');
                fotoError.html('Format file tidak valid. Gunakan JPG, JPEG, atau PNG.');
                return;
            }

            // Validasi ukuran file
            if (file.size > maxSize) {
                $(input).addClass('is-invalid');
                fotoError.html('Ukuran file terlalu besar. Maksimal 2MB.');
                return;
            }

            // Tampilkan preview
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }
</script>
<?= $this->endSection() ?>
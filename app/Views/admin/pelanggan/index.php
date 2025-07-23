<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pelanggan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Pelanggan</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <button type="button" class="btn btn-primary px-3" onclick="showAddModal()">
            <i class="bx bx-plus"></i> Tambah Pelanggan
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="pelangganTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pelanggan -->
<div class="modal fade" id="pelangganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pelangganForm">
                    <input type="hidden" id="id" name="id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div class="invalid-feedback" id="phone-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        <div class="invalid-feedback" id="address-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback" id="email-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="savePelanggan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal User Account -->
<div class="modal fade" id="userAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informasi Akun User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Akun Berhasil Dibuat!</h4>
                    <p>Berikut adalah informasi akun untuk login:</p>
                    <hr>
                    <p class="mb-0"><strong>Username:</strong> <span id="accountUsername"></span></p>
                    <p class="mb-0"><strong>Password:</strong> <span id="accountPassword"></span></p>
                </div>
                <p>Mohon catat informasi ini atau beritahu pelanggan untuk segera mengganti password setelah login pertama.</p>
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
    let pelangganTable;
    let pelangganModal;
    let userAccountModal;
    let isEdit = false;

    $(document).ready(function() {
        pelangganModal = new bootstrap.Modal(document.getElementById('pelangganModal'));
        userAccountModal = new bootstrap.Modal(document.getElementById('userAccountModal'));

        // Initialize DataTable
        pelangganTable = $('#pelangganTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/getPelanggan'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email',
                    render: function(data) {
                        return data || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'phone',
                    render: function(data) {
                        return data || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'address',
                    render: function(data) {
                        return data || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'username',
                    render: function(data) {
                        return data || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-sm btn-info" onclick="editPelanggan('${data.id}')">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePelanggan('${data.id}')">
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
    });

    function showAddModal() {
        isEdit = false;

        // Reset form
        $('#pelangganForm')[0].reset();
        $('#id').val('');

        // Reset validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        // Set modal title
        $('.modal-title').text('Tambah Pelanggan');

        // Show modal
        pelangganModal.show();
    }

    function editPelanggan(id) {
        isEdit = true;

        // Reset form
        $('#pelangganForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        // Set modal title
        $('.modal-title').text('Edit Pelanggan');

        // Get pelanggan data
        $.ajax({
            url: `/admin/getPelangganById/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    const pelanggan = response.data;

                    // Fill form fields
                    $('#id').val(pelanggan.id);
                    $('#name').val(pelanggan.name);
                    $('#phone').val(pelanggan.phone);
                    $('#address').val(pelanggan.address);
                    $('#email').val(pelanggan.email);

                    // Show modal
                    pelangganModal.show();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal mengambil data pelanggan', 'error');
            }
        });
    }

    $('#savePelanggan').click(function() {
        // Validate form
        const form = $('#pelangganForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Get form data
        const formData = new FormData(form);

        // Send request
        $.ajax({
            url: isEdit ? `/admin/updatePelanggan/${$('#id').val()}` : '/admin/createPelanggan',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    pelangganModal.hide();

                    // Show success message
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Refresh DataTable
                    pelangganTable.ajax.reload();

                    // Show user account info if available
                    if (response.user_account) {
                        $('#accountUsername').text(response.user_account.username);
                        $('#accountPassword').text(response.user_account.password);
                        userAccountModal.show();
                    }
                } else {
                    // Show validation errors
                    if (response.errors) {
                        Object.keys(response.errors).forEach(function(key) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}-feedback`).html(response.errors[key]);
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                if (response.errors) {
                    Object.keys(response.errors).forEach(function(key) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}-feedback`).html(response.errors[key]);
                    });
                } else {
                    Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                }
            }
        });
    });

    function deletePelanggan(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus pelanggan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/deletePelanggan/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            pelangganTable.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus pelanggan', 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
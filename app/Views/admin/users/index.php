<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Users</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <button type="button" class="btn btn-primary px-3" onclick="showAddModal()">
            <i class="bx bx-plus"></i> Tambah User
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Filter Role</label>
                <select class="form-select" id="roleFilter">
                    <option value="">Semua Role</option>
                </select>
            </div>
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
            <table id="userTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" onsubmit="saveUser(event)">
                <div class="modal-body">
                    <!-- Hidden ID field -->
                    <input type="hidden" id="userId" name="id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback" id="username-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="invalid-feedback" id="password-error"></div>
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                            </select>
                            <div class="invalid-feedback" id="role-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                            <div class="invalid-feedback" id="status-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                            <div class="invalid-feedback" id="address-error"></div>
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
    let userTable;
    let userModal;

    $(document).ready(function() {
        userModal = new bootstrap.Modal(document.getElementById('userModal'));

        // Load roles untuk filter dan form
        $.get('/admin/getRoles', function(response) {
            if (response.status === 'success') {
                let roles = response.data;
                let options = '<option value="">Semua Role</option>';
                roles.forEach(function(role) {
                    options += `<option value="${role}">${role.charAt(0).toUpperCase() + role.slice(1)}</option>`;
                });
                $('#roleFilter, #role').html(options);
            }
        });

        // Initialize DataTable
        userTable = $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/getUsers',
                data: function(d) {
                    d.role = $('#roleFilter').val();
                    d.status = $('#statusFilter').val();
                    return d;
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'username'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'role',
                    render: function(data) {
                        let badge = 'bg-secondary';
                        if (data === 'admin') badge = 'bg-primary';
                        else if (data === 'direktur') badge = 'bg-info';
                        else if (data === 'pelanggan') badge = 'bg-success';
                        return `<span class="badge ${badge}">${data}</span>`;
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        return `<span class="badge ${data === 'active' ? 'bg-success' : 'bg-danger'}">${data}</span>`;
                    }
                },
                {
                    data: 'last_login',
                    render: function(data) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-sm btn-info" onclick="editUser(${data.id})">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${data.id})">
                            <i class="bx bx-trash"></i>
                        </button>
                    `;
                    }
                }
            ],
            order: [
                [0, 'desc']
            ]
        });

        // Filter event handlers
        $('#roleFilter, #statusFilter').change(function() {
            userTable.ajax.reload();
        });
    });

    function showAddModal() {
        // Reset form
        $('#userForm')[0].reset();

        // Hapus ID
        $('#userId').val('');

        // Password wajib diisi untuk user baru
        $('#password').prop('required', true);

        // Hapus semua error
        $('.invalid-feedback').html('');
        $('.is-invalid').removeClass('is-invalid');

        // Tampilkan modal
        userModal.show();
    }

    function editUser(id) {
        // Reset form
        $('#userForm')[0].reset();
        $('.invalid-feedback').html('');
        $('.is-invalid').removeClass('is-invalid');

        // Pastikan ID valid
        if (!id) {
            Swal.fire('Error', 'ID user tidak valid', 'error');
            return;
        }

        $.get(`/admin/getUser/${id}`, function(response) {
            if (response.status === 'success') {
                const user = response.data;

                // Set nilai form
                $('#userId').val(user.id);
                $('#username').val(user.username);
                $('#email').val(user.email);
                $('#name').val(user.name);
                $('#role').val(user.role);
                $('#status').val(user.status);
                $('#phone').val(user.phone || '');
                $('#address').val(user.address || '');

                // Password tidak wajib saat edit
                $('#password').prop('required', false);

                // Tampilkan modal
                userModal.show();
            } else {
                Swal.fire('Error', response.message || 'Gagal mengambil data user', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengambil data user', 'error');
        });
    }

    function saveUser(e) {
        e.preventDefault();

        // Reset error messages
        $('.invalid-feedback').html('');
        $('.is-invalid').removeClass('is-invalid');

        const form = $('#userForm');
        const data = form.serialize();
        const id = $('#userId').val();
        const url = id ? `/admin/updateUser/${id}` : '/admin/createUser';

        // Show loading
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.status === 'success') {
                    userModal.hide();
                    userTable.ajax.reload();
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

    function deleteUser(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus user ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/deleteUser/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status === 'success') {
                            userTable.ajax.reload();
                            Swal.fire('Sukses', response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
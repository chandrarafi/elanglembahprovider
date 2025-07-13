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
                        <th>ID Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Akun User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pelanggan -->
<div class="modal fade" id="pelangganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pelangganForm" onsubmit="savePelanggan(event)">
                <div class="modal-body">
                    <!-- Hidden ID field -->
                    <input type="hidden" id="idpelanggan" name="idpelanggan">

                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="namapelanggan" name="namapelanggan" required>
                        <div class="invalid-feedback" id="namapelanggan-error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="nohp" name="nohp">
                        <div class="invalid-feedback" id="nohp-error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        <div class="invalid-feedback" id="alamat-error"></div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_user" name="create_user" value="1">
                            <label class="form-check-label" for="create_user">
                                Buatkan akun user untuk pelanggan ini
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="emailField" style="display: none;">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="invalid-feedback" id="email-error"></div>
                        <small class="text-muted">Email diperlukan untuk membuat akun user</small>
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

<!-- Modal User Account -->
<div class="modal fade" id="userAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informasi Akun User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <h6>Akun user berhasil dibuat!</h6>
                    <p class="mb-0">Berikut adalah informasi login:</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="userUsername" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('userUsername')">
                            <i class="bx bx-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="userPassword" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('userPassword')">
                            <i class="bx bx-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bx bx-info-circle"></i> Harap catat informasi ini. Password tidak dapat dilihat kembali setelah menutup dialog ini.
                </div>

                <div class="alert alert-info">
                    <small>
                        <i class="bx bx-info-circle"></i> Password default untuk semua akun pelanggan adalah: <strong>123456</strong><br>
                        Pelanggan dapat mengubah password setelah login pertama kali.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
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
                    data: 'idpelanggan'
                },
                {
                    data: 'namapelanggan'
                },
                {
                    data: 'nohp',
                    render: function(data) {
                        return data || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'alamat',
                    render: function(data) {
                        return data || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'username',
                    render: function(data, type, row) {
                        if (data) {
                            return `<span class="badge bg-success">Ada (${data})</span>`;
                        }
                        return '<span class="badge bg-secondary">Tidak Ada</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-sm btn-info" onclick="editPelanggan('${data.idpelanggan}')">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePelanggan('${data.idpelanggan}')">
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

        // Toggle email field based on checkbox
        $('#create_user').change(function() {
            if ($(this).is(':checked')) {
                $('#emailField').show();
                $('#email').attr('required', true);
            } else {
                $('#emailField').hide();
                $('#email').removeAttr('required');
            }
        });
    });

    function showAddModal() {
        isEdit = false;

        // Reset form
        $('#pelangganForm')[0].reset();
        $('#idpelanggan').val('');

        // Reset validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        // Reset user account checkbox
        $('#create_user').prop('checked', false).prop('disabled', false);
        $('#emailField').hide();
        $('#email').removeAttr('required');

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
        $.get(`/admin/pelanggan/${id}`, function(response) {
            if (response.status === 'success') {
                const pelanggan = response.data;

                // Set form values
                $('#idpelanggan').val(pelanggan.idpelanggan);
                $('#namapelanggan').val(pelanggan.namapelanggan);
                $('#nohp').val(pelanggan.nohp);
                $('#alamat').val(pelanggan.alamat);

                // Handle user account
                if (pelanggan.has_account) {
                    $('#create_user').prop('checked', true).prop('disabled', true);
                    $('#emailField').show();
                    $('#email').val(pelanggan.email || '');
                    $('#email').attr('required', true);
                } else {
                    $('#create_user').prop('checked', false).prop('disabled', false);
                    $('#emailField').hide();
                    $('#email').removeAttr('required');
                }

                // Show modal
                pelangganModal.show();
            } else {
                Swal.fire('Error', response.message || 'Gagal mengambil data pelanggan', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengambil data pelanggan', 'error');
        });
    }

    function savePelanggan(e) {
        e.preventDefault();

        // Reset validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        // Get form data
        const formData = new FormData(document.getElementById('pelangganForm'));
        const id = $('#idpelanggan').val();
        const url = id ? `/admin/updatePelanggan/${id}` : '/admin/createPelanggan';

        // Pastikan nilai create_user diatur dengan benar
        if ($('#create_user').is(':checked')) {
            formData.set('create_user', '1');

            // Validasi email jika checkbox dicentang
            const email = $('#email').val().trim();
            if (!email) {
                $('#email').addClass('is-invalid');
                $('#email-error').html('Email harus diisi jika ingin membuat akun user');
                Swal.fire('Error', 'Email harus diisi jika ingin membuat akun user', 'error');
                return;
            }
        } else {
            formData.set('create_user', '0');
        }

        // Debug form data
        console.log('Create user checked:', $('#create_user').is(':checked'));
        console.log('Email value:', $('#email').val());
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        // Show loading
        const submitBtn = $(e.target).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);

        // Submit form
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Response:', response);
                if (response.status === 'success') {
                    pelangganModal.hide();
                    pelangganTable.ajax.reload();

                    // Show user account info if created
                    if (response.user_account) {
                        $('#userUsername').val(response.user_account.username);
                        $('#userPassword').val(response.user_account.password);
                        userAccountModal.show();
                    } else {
                        Swal.fire('Sukses', response.message, 'success');
                    }
                } else {
                    // Tangani error
                    if (response.errors && response.errors.email) {
                        $('#email').addClass('is-invalid');
                        $('#email-error').html(response.errors.email);
                    }
                    Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                const response = xhr.responseJSON || {};
                console.log('Error response:', response);

                if (response.errors) {
                    // Show validation errors
                    Object.keys(response.errors).forEach(function(key) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}-error`).html(response.errors[key]);
                    });
                    Swal.fire('Error', 'Ada kesalahan pada form. Silakan periksa kembali.', 'error');
                } else {
                    Swal.fire('Error', response.message || 'Terjadi kesalahan saat menyimpan data', 'error');
                }
            },
            complete: function() {
                // Restore button
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    }

    function deletePelanggan(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menghapus pelanggan ini? Jika pelanggan memiliki akun user, akun tersebut juga akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/deletePelanggan/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status === 'success') {
                            pelangganTable.ajax.reload();
                            Swal.fire('Sukses', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus pelanggan', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghapus pelanggan', 'error');
                    }
                });
            }
        });
    }

    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        document.execCommand('copy');

        // Show tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'position-absolute top-0 start-50 translate-middle badge bg-success';
        tooltip.innerText = 'Disalin!';
        tooltip.style.zIndex = '1050';

        element.parentElement.appendChild(tooltip);

        // Remove tooltip after 1.5 seconds
        setTimeout(() => {
            tooltip.remove();
        }, 1500);
    }
</script>
<?= $this->endSection() ?>
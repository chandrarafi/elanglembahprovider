<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pelanggan - Elang Lembah Travel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
            --accent-color: #f8f9fc;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 420px;
            margin: 0 auto;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 25px 20px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }

        .login-header h4 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #e1e1e1;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            border-color: var(--primary-color);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            border: 1px solid #e1e1e1;
            background-color: #f8f9fa;
        }

        .btn-login {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            border-radius: 15px;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            margin-left: 5px;
        }
    </style>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card position-relative">
                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="login-header">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="brand-logo" onerror="this.style.display='none'">
                    <h4><i class="bi bi-person-plus me-2"></i>Registrasi Pelanggan</h4>
                </div>

                <div class="login-body">
                    <!-- Alert untuk pesan error/success -->
                    <div id="alert-container"></div>

                    <form id="registerForm" method="post">
                        <!-- Tambahkan CSRF token -->
                        <?= csrf_field() ?>

                        <!-- Form fields lainnya -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan email">
                            </div>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="Konfirmasi password">
                            </div>
                            <div class="invalid-feedback" id="password_confirm-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor HP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Masukkan nomor HP">
                            </div>
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Masukkan alamat"></textarea>
                            </div>
                            <div class="invalid-feedback" id="address-error"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a class="small text-primary" href="<?= site_url('auth') ?>">Sudah punya akun? Login</a>
                            <button type="submit" class="btn btn-primary btn-login" id="btnRegister">
                                <i class="bi bi-person-plus me-2"></i>Daftar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#registerForm').submit(function(e) {
                e.preventDefault();

                // Kumpulkan data form terlebih dahulu
                var formData = $(this).serialize();
                console.log('Form data:', formData);

                // Show loading overlay
                $('#loadingOverlay').css('display', 'flex');

                // Reset validation
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').html('');

                // Disable form setelah data dikumpulkan
                $('#registerForm :input').prop('disabled', true);

                $.ajax({
                    url: '<?= site_url('register/process') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#alert-container').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 2000);
                        } else {
                            $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        if (response && response.errors) {
                            // Tampilkan error validasi
                            $.each(response.errors, function(field, message) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '-error').html(message);
                            });
                            $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                response.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        } else {
                            $('#alert-container').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                (response?.message || 'Terjadi kesalahan. Silakan coba lagi.') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                    },
                    complete: function() {
                        // Hide loading overlay
                        $('#loadingOverlay').hide();
                        // Enable form
                        $('#registerForm :input').prop('disabled', false);
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Elang Lembah Travel</title>

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
                    <h4><i class="bi bi-envelope-check me-2"></i>Verifikasi Email</h4>
                </div>

                <div class="login-body">
                    <!-- Alert untuk pesan error/success -->
                    <div id="alert-container">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>

                            <div class="text-center mb-4">
                                <div class="my-4">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                </div>
                                <h5>Akun berhasil diverifikasi!</h5>
                                <p>Anda akan dialihkan ke halaman login dalam <span id="countdown">3</span> detik...</p>
                                <a href="<?= site_url('auth') ?>" class="btn btn-primary">Login Sekarang</a>
                            </div>

                            <script>
                                // Countdown redirect
                                var seconds = 3;
                                var countdownEl = document.getElementById('countdown');
                                var countdownInterval = setInterval(function() {
                                    seconds--;
                                    countdownEl.innerText = seconds;
                                    if (seconds <= 0) {
                                        clearInterval(countdownInterval);
                                        window.location.href = '<?= site_url('auth') ?>';
                                    }
                                }, 1000);
                            </script>
                        <?php else: ?>

                            <p class="text-center mb-4">Silakan masukkan kode OTP yang telah dikirim ke email Anda.</p>

                            <?php if (isset($email) && !empty($email)): ?>
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <p class="mb-0">Kode OTP telah dikirim ke: <strong><?= $email ?></strong></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form id="verifyForm" method="post" action="<?= site_url('register/verifyOtp') ?>">
                                <input type="hidden" id="email" name="email" value="<?= isset($email) ? $email : '' ?>">

                                <div class="mb-3">
                                    <label for="otp" class="form-label">Kode OTP</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                        <input type="text" class="form-control" id="otp" name="otp" required
                                            maxlength="6" minlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                            placeholder="Masukkan kode OTP (6 digit)"
                                            title="Masukkan 6 digit kode OTP"
                                            onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                                            autocomplete="one-time-code">
                                    </div>
                                    <div class="invalid-feedback" id="otp-error">
                                        Kode OTP harus 6 digit angka
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        Masukkan 6 digit kode OTP yang dikirim ke email Anda
                                    </small>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="<?= site_url('register/resendOtp') . '?email=' . (isset($email) ? $email : '') ?>" class="btn btn-link text-primary p-0">Kirim Ulang OTP</a>
                                    <button type="submit" class="btn btn-primary btn-login" id="btnVerify">
                                        <i class="bi bi-check-circle me-2"></i>Verifikasi
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4 text-center">
                                <small class="text-muted">Atau klik link verifikasi di email Anda</small>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function() {
                // Fokuskan ke field OTP saat halaman dimuat
                $('#otp').focus();

                // Format OTP input (hanya angka)
                $('#otp').on('input', function() {
                    // Hapus semua karakter non-angka
                    var value = $(this).val().replace(/[^0-9]/g, '');

                    // Batasi panjang ke 6 digit
                    if (value.length > 6) {
                        value = value.substring(0, 6);
                    }

                    // Set nilai baru
                    $(this).val(value);

                    // Visual feedback
                    if (value.length === 6) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    } else if (value.length > 0) {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-valid is-invalid');
                    }
                });

                // Validasi form saat submit
                $('#verifyForm').submit(function(e) {
                    var otp = $('#otp').val();

                    // Cek apakah OTP sudah diisi
                    if (!otp || otp.trim().length !== 6 || !/^\d{6}$/.test(otp)) {
                        e.preventDefault();
                        $('#otp').addClass('is-invalid');
                        $('#otp-error').html('Kode OTP harus 6 digit angka');
                        return false;
                    }

                    // Tampilkan loading overlay
                    $('#loadingOverlay').css('display', 'flex');

                    // Lanjutkan dengan submit normal
                    return true;
                });

                // Auto-hide alerts after 8 seconds
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 8000);
            });
        </script>
</body>

</html>
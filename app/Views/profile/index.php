<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Profil Saya - Elang Lembah Travel' ?></title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF',
                        secondary: '#0EA5E9',
                        accent: '#F59E0B',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="<?= base_url() ?>" class="flex items-center">
                        <img src="<?= base_url('assets/images/logo-img.png') ?>" alt="Logo" class="h-10 mr-3">
                        <span class="text-xl font-bold text-primary">Elang Lembah Travel</span>
                    </a>
                </div>

                <div class="hidden md:flex space-x-6">
                    <a href="<?= base_url() ?>" class="text-gray-800 hover:text-primary font-medium">Beranda</a>
                    <a href="<?= base_url('paket') ?>" class="text-gray-800 hover:text-primary font-medium">Paket Wisata</a>
                    <a href="<?= base_url('kategori') ?>" class="text-gray-800 hover:text-primary font-medium">Kategori</a>
                    <a href="<?= base_url('about') ?>" class="text-gray-800 hover:text-primary font-medium">Tentang Kami</a>
                    <a href="<?= base_url('kontak') ?>" class="text-gray-800 hover:text-primary font-medium">Kontak</a>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="userDropdownButton" class="flex items-center space-x-2 text-gray-800 hover:text-primary focus:outline-none">
                            <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-8 h-8 rounded-full">
                            <span class="font-medium"><?= $user_data['name'] ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="userDropdownMenu" class="absolute right-0 w-48 mt-2 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                            <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            <a href="<?= base_url('booking/history') ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-history mr-2"></i> Riwayat Pemesanan
                            </a>
                            <hr class="my-1">
                            <a href="javascript:void(0)" onclick="confirmLogout()" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                    <button class="md:hidden text-gray-800" id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden hidden mt-3 pb-2" id="mobile-menu">
                <a href="<?= base_url() ?>" class="block py-2 text-gray-800 hover:text-primary">Beranda</a>
                <a href="<?= base_url('paket') ?>" class="block py-2 text-gray-800 hover:text-primary">Paket Wisata</a>
                <a href="<?= base_url('kategori') ?>" class="block py-2 text-gray-800 hover:text-primary">Kategori</a>
                <a href="<?= base_url('about') ?>" class="block py-2 text-gray-800 hover:text-primary">Tentang Kami</a>
                <a href="<?= base_url('kontak') ?>" class="block py-2 text-gray-800 hover:text-primary">Kontak</a>
                <hr class="my-2">
                <a href="<?= base_url('profile') ?>" class="block py-2 text-primary font-medium">Profil</a>
                <a href="<?= base_url('booking/history') ?>" class="block py-2 text-gray-800 hover:text-primary">Riwayat Pemesanan</a>
                <a href="javascript:void(0)" onclick="confirmLogout()" class="block py-2 text-red-600 hover:text-red-800">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
                <p class="text-gray-600">Kelola informasi profil dan riwayat pemesanan Anda</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= base_url() ?>" class="text-primary hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->has('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p><?= session()->getFlashdata('error') ?></p>
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-red-500 rounded-lg p-1.5 hover:bg-red-200" data-dismiss-target="#alert-1" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->has('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p><?= session()->getFlashdata('success') ?></p>
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-green-500 rounded-lg p-1.5 hover:bg-green-200" data-dismiss-target="#alert-2" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-primary p-6 text-center">
                        <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-24 h-24 rounded-full mx-auto border-4 border-white">
                        <h2 class="text-xl font-bold text-white mt-4"><?= $user['name'] ?></h2>
                        <p class="text-blue-100"><?= $user['email'] ?></p>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Username</p>
                            <p class="font-medium"><?= $user['username'] ?></p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Status</p>
                            <p>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>
                                        Tidak Aktif
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Terakhir Login</p>
                            <p class="font-medium"><?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?></p>
                        </div>
                        <button id="editProfileBtn" class="w-full bg-primary hover:bg-blue-800 text-white py-2 px-4 rounded-md transition duration-300">
                            Edit Profil
                        </button>
                    </div>
                </div>
            </div>

            <!-- Booking History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Riwayat Pemesanan</h3>

                    <?php if (empty($bookings)): ?>
                        <div class="text-center py-8">
                            <img src="<?= base_url('assets/images/icons/booking.svg') ?>" alt="No Bookings" class="w-24 h-24 mx-auto mb-4 opacity-50">
                            <h4 class="text-lg font-medium text-gray-800 mb-2">Belum Ada Pemesanan</h4>
                            <p class="text-gray-600 mb-4">Anda belum melakukan pemesanan paket wisata.</p>
                            <a href="<?= base_url('paket') ?>" class="inline-block bg-primary hover:bg-blue-800 text-white py-2 px-6 rounded-md transition duration-300">
                                Jelajahi Paket Wisata
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Booking</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $booking['booking_code'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $booking['paket_name'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Menunggu
                                                    </span>
                                                <?php elseif ($booking['status'] === 'confirmed'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Dikonfirmasi
                                                    </span>
                                                <?php elseif ($booking['status'] === 'completed'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Selesai
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Dibatalkan
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="<?= base_url('booking/detail/' . $booking['id']) ?>" class="text-primary hover:text-blue-800">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" id="modalOverlay"></div>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md z-50 relative">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Edit Profil</h3>
                    <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form id="profileForm">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="<?= $user['name'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="<?= $user['email'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" id="username" name="username" value="<?= $user['username'] ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary bg-gray-100" readonly>
                            <p class="mt-1 text-xs text-gray-500">Username tidak dapat diubah</p>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah password</p>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="button" id="cancelEdit" class="mr-2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md transition duration-300">
                                Batal
                            </button>
                            <button type="submit" class="bg-primary hover:bg-blue-800 text-white py-2 px-4 rounded-md transition duration-300">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-10 pb-4 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Elang Lembah Travel</h3>
                    <p class="text-gray-400 mb-4 text-sm">Menyediakan layanan perjalanan wisata terbaik untuk pengalaman liburan yang tak terlupakan.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Tautan</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?= base_url() ?>" class="text-gray-400 hover:text-white">Beranda</a></li>
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Paket Wisata</a></li>
                        <li><a href="<?= base_url('booking') ?>" class="text-gray-400 hover:text-white">Pemesanan</a></li>
                        <li><a href="<?= base_url('about') ?>" class="text-gray-400 hover:text-white">Tentang Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>Jl. Raya Bogor No. 123, Bogor, Indonesia</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            <span>+62 812 3456 7890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@elanglembahtravel.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-4">
                <p class="text-center text-gray-400 text-sm">&copy; <?= date('Y') ?> Elang Lembah Travel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // User dropdown toggle
        const userDropdownButton = document.getElementById('userDropdownButton');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        if (userDropdownButton && userDropdownMenu) {
            userDropdownButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdownMenu.contains(e.target) && !userDropdownButton.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        }

        // Alert auto close
        setTimeout(function() {
            const alerts = document.querySelectorAll('[data-dismiss-target]');
            alerts.forEach(function(alert) {
                alert.parentNode.parentNode.style.display = 'none';
            });
        }, 5000);

        // Modal functions
        const modal = document.getElementById('editProfileModal');
        const modalOverlay = document.getElementById('modalOverlay');
        const editProfileBtn = document.getElementById('editProfileBtn');
        const closeModal = document.getElementById('closeModal');
        const cancelEdit = document.getElementById('cancelEdit');

        editProfileBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });

        function closeModalFunc() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        closeModal.addEventListener('click', closeModalFunc);
        cancelEdit.addEventListener('click', closeModalFunc);
        modalOverlay.addEventListener('click', closeModalFunc);

        // Form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('<?= base_url('profile/update') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#1E40AF'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            confirmButtonColor: '#1E40AF'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal menghubungi server. Silakan coba lagi nanti.',
                        confirmButtonColor: '#1E40AF'
                    });
                });
        });

        // Logout confirmation
        function confirmLogout() {
            Swal.fire({
                title: 'Logout',
                text: "Apakah Anda yakin ingin keluar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1E40AF',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('auth/logout') ?>';
                }
            });
        }
    </script>
</body>

</html>
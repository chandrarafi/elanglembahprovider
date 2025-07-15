<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Elang Lembah Travel' ?></title>

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

    <style>
        .paket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
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
                    <?php if (session()->get('logged_in')): ?>
                        <div class="relative">
                            <button id="userDropdownButton" class="flex items-center space-x-2 text-gray-800 hover:text-primary focus:outline-none">
                                <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-8 h-8 rounded-full">
                                <span class="font-medium"><?= session()->get('name') ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div id="userDropdownMenu" class="absolute right-0 w-48 mt-2 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
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
                    <?php else: ?>
                        <a href="<?= base_url('auth') ?>" class="bg-primary hover:bg-blue-800 text-white py-2 px-4 rounded-md transition duration-300">
                            Login
                        </a>
                    <?php endif; ?>
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
                <?php if (session()->get('logged_in')): ?>
                    <hr class="my-2">
                    <a href="<?= base_url('profile') ?>" class="block py-2 text-gray-800 hover:text-primary">Profil</a>
                    <a href="<?= base_url('booking/history') ?>" class="block py-2 text-gray-800 hover:text-primary">Riwayat Pemesanan</a>
                    <a href="javascript:void(0)" onclick="confirmLogout()" class="block py-2 text-red-600 hover:text-red-800">Logout</a>
                <?php else: ?>
                    <hr class="my-2">
                    <a href="<?= base_url('auth') ?>" class="block py-2 text-primary hover:text-blue-800 font-medium">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <?= $this->renderSection('content') ?>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 pb-6 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Elang Lembah Travel</h3>
                    <p class="text-gray-400 mb-4">Menyediakan layanan perjalanan wisata terbaik untuk pengalaman liburan yang tak terlupakan.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold mb-4">Tautan</h3>
                    <ul class="space-y-2">
                        <li><a href="<?= base_url() ?>" class="text-gray-400 hover:text-white">Beranda</a></li>
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Paket Wisata</a></li>
                        <li><a href="<?= base_url('about') ?>" class="text-gray-400 hover:text-white">Tentang Kami</a></li>
                        <li><a href="<?= base_url('kontak') ?>" class="text-gray-400 hover:text-white">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xl font-semibold mb-4">Kategori</h3>
                    <ul class="space-y-2">
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Semua Paket</a></li>
                        <li><a href="<?= base_url('paket?sort=price_low') ?>" class="text-gray-400 hover:text-white">Harga Terendah</a></li>
                        <li><a href="<?= base_url('paket?sort=popular') ?>" class="text-gray-400 hover:text-white">Paket Populer</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xl font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-400">
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

            <div class="border-t border-gray-700 mt-10 pt-6">
                <p class="text-center text-gray-400">&copy; <?= date('Y') ?> Elang Lembah Travel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
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
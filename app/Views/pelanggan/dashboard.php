<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Pelanggan - Elang Lembah Travel' ?></title>

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

    <!-- Custom styles -->
    <style>
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?= base_url('assets/images/gallery/01.png') ?>');
            background-size: cover;
            background-position: center;
            height: 50vh;
        }

        .kategori-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

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
                    <a href="<?= base_url('booking') ?>" class="text-gray-800 hover:text-primary font-medium">Pemesanan</a>
                    <a href="<?= base_url('about') ?>" class="text-gray-800 hover:text-primary font-medium">Tentang Kami</a>
                </div>

                <div class="flex items-center space-x-4">
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
                    <button class="md:hidden text-gray-800" id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden hidden mt-3 pb-2" id="mobile-menu">
                <a href="<?= base_url() ?>" class="block py-2 text-gray-800 hover:text-primary">Beranda</a>
                <a href="<?= base_url('paket') ?>" class="block py-2 text-gray-800 hover:text-primary">Paket Wisata</a>
                <a href="<?= base_url('booking') ?>" class="block py-2 text-gray-800 hover:text-primary">Pemesanan</a>
                <a href="<?= base_url('about') ?>" class="block py-2 text-gray-800 hover:text-primary">Tentang Kami</a>
                <a href="<?= base_url('profile') ?>" class="block py-2 text-gray-800 hover:text-primary">Profil</a>
                <a href="<?= base_url('booking/history') ?>" class="block py-2 text-gray-800 hover:text-primary">Riwayat Pemesanan</a>
                <a href="javascript:void(0)" onclick="confirmLogout()" class="block py-2 text-red-600 hover:text-red-800">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Welcome Banner -->
    <section class="hero-section flex items-center">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Selamat Datang, <?= session()->get('name') ?>!</h1>
                <p class="text-lg text-white mb-6">Temukan pengalaman wisata terbaik bersama Elang Lembah Travel</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?= base_url('paket') ?>" class="bg-primary hover:bg-blue-800 text-white py-2 px-6 rounded-md font-medium transition duration-300 text-center">
                        Jelajahi Paket Wisata
                    </a>
                    <a href="<?= base_url('booking') ?>" class="bg-white hover:bg-gray-100 text-primary py-2 px-6 rounded-md font-medium transition duration-300 text-center">
                        Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-primary">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <i class="fas fa-suitcase text-primary text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Pemesanan</p>
                        <p class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-accent">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 mr-4">
                        <i class="fas fa-clock text-accent text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Pemesanan Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Pemesanan Selesai</p>
                        <p class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori Section -->
        <section class="mb-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Kategori Wisata</h2>
                <a href="<?= base_url('kategori') ?>" class="text-primary hover:text-blue-700 font-medium flex items-center">
                    Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($kategori as $index => $kat): ?>
                    <?php if ($index < 6): ?>
                        <a href="<?= base_url('paket?kategori=' . $kat['idkategori']) ?>" class="kategori-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300 text-center">
                            <div class="h-32 overflow-hidden">
                                <?php if ($kat['foto']): ?>
                                    <img src="<?= base_url('uploads/kategori/' . $kat['foto']) ?>" alt="<?= $kat['namakategori'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/gallery/0' . (($index % 9) + 1) . '.png') ?>" alt="<?= $kat['namakategori'] ?>" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-800"><?= $kat['namakategori'] ?></h3>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Paket Terbaru Section -->
        <section class="mb-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Paket Wisata Terbaru</h2>
                <a href="<?= base_url('paket') ?>" class="text-primary hover:text-blue-700 font-medium flex items-center">
                    Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($paket_terbaru as $index => $paket): ?>
                    <div class="paket-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300 border border-gray-200">
                        <div class="relative h-48 overflow-hidden">
                            <?php if ($paket['foto']): ?>
                                <img src="<?= base_url('uploads/paket/' . $paket['foto']) ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <img src="<?= base_url('assets/images/gallery/0' . (($index % 9) + 1) . '.png') ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover">
                            <?php endif; ?>
                            <div class="absolute top-0 right-0 bg-accent text-white px-3 py-1 m-2 rounded-full text-xs font-bold">
                                Baru
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-blue-100 text-primary text-xs font-medium px-2.5 py-0.5 rounded">
                                    <?php
                                    $kategoriName = '';
                                    foreach ($kategori as $kat) {
                                        if ($kat['idkategori'] === $paket['idkategori']) {
                                            $kategoriName = $kat['namakategori'];
                                            break;
                                        }
                                    }
                                    echo $kategoriName;
                                    ?>
                                </span>
                                <span class="text-accent font-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2"><?= $paket['namapaket'] ?></h3>
                            <p class="text-gray-600 mb-4 text-sm line-clamp-2"><?= $paket['deskripsi'] ?? 'Nikmati perjalanan wisata dengan paket yang telah kami siapkan untuk pengalaman terbaik Anda.' ?></p>
                            <div class="flex justify-between items-center">
                                <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="text-primary hover:text-blue-700 font-medium text-sm">
                                    Lihat Detail
                                </a>
                                <a href="<?= base_url('booking/create/' . $paket['idpaket']) ?>" class="bg-primary hover:bg-blue-800 text-white py-1 px-3 rounded-md text-sm transition duration-300">
                                    Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Paket Populer Section -->
        <section>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Paket Wisata Populer</h2>
                <a href="<?= base_url('paket?sort=popular') ?>" class="text-primary hover:text-blue-700 font-medium flex items-center">
                    Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($paket_populer as $index => $paket): ?>
                    <?php if ($index < 4): ?>
                        <div class="paket-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300 border border-gray-200">
                            <div class="h-40 overflow-hidden">
                                <?php if ($paket['foto']): ?>
                                    <img src="<?= base_url('uploads/paket/' . $paket['foto']) ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/gallery/0' . (($index % 9) + 1) . '.png') ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="bg-blue-100 text-primary text-xs font-medium px-2 py-0.5 rounded">
                                        <?php
                                        $kategoriName = '';
                                        foreach ($kategori as $kat) {
                                            if ($kat['idkategori'] === $paket['idkategori']) {
                                                $kategoriName = $kat['namakategori'];
                                                break;
                                            }
                                        }
                                        echo $kategoriName;
                                        ?>
                                    </span>
                                    <span class="text-accent font-bold text-sm">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                </div>
                                <h3 class="text-base font-bold text-gray-800 mb-2 line-clamp-1"><?= $paket['namapaket'] ?></h3>
                                <div class="flex justify-between items-center mt-3">
                                    <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="text-primary hover:text-blue-700 font-medium text-xs">
                                        Lihat Detail
                                    </a>
                                    <a href="<?= base_url('booking/create/' . $paket['idpaket']) ?>" class="bg-primary hover:bg-blue-800 text-white py-1 px-2 rounded-md text-xs transition duration-300">
                                        Pesan
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
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
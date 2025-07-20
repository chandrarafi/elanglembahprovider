<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Detail Paket - Elang Lembah Travel' ?></title>

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
                    <a href="<?= base_url('paket') ?>" class="text-primary border-b-2 border-primary font-medium">Paket Wisata</a>
                    <a href="<?= base_url('kategori') ?>" class="text-gray-800 hover:text-primary font-medium">Kategori</a>
                    <a href="<?= base_url('about') ?>" class="text-gray-800 hover:text-primary font-medium">Tentang Kami</a>
                    <a href="<?= base_url('kontak') ?>" class="text-gray-800 hover:text-primary font-medium">Kontak</a>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if ($is_logged_in): ?>
                        <div class="relative">
                            <button id="userDropdownButton" class="flex items-center space-x-2 text-gray-800 hover:text-primary focus:outline-none">
                                <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-8 h-8 rounded-full">
                                <span class="font-medium"><?= $user['name'] ?></span>
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
                <a href="<?= base_url('paket') ?>" class="block py-2 text-primary font-medium">Paket Wisata</a>
                <a href="<?= base_url('kategori') ?>" class="block py-2 text-gray-800 hover:text-primary">Kategori</a>
                <a href="<?= base_url('about') ?>" class="block py-2 text-gray-800 hover:text-primary">Tentang Kami</a>
                <a href="<?= base_url('kontak') ?>" class="block py-2 text-gray-800 hover:text-primary">Kontak</a>
                <?php if ($is_logged_in): ?>
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

    <!-- Breadcrumb -->
    <section class="bg-gray-100 py-4 border-b">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-sm">
                <a href="<?= base_url() ?>" class="text-primary hover:text-blue-800">Beranda</a>
                <span class="text-gray-500">/</span>
                <a href="<?= base_url('paket') ?>" class="text-primary hover:text-blue-800">Paket Wisata</a>
                <span class="text-gray-500">/</span>
                <span class="text-gray-700"><?= $paket['namapaket'] ?></span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Content - Paket Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                        <?php
                        $backgroundImages = [
                            'assets/images/gallery/03.png',
                            'assets/images/gallery/15.png',
                            'assets/images/gallery/25.png',
                            'assets/images/gallery/09.png',
                            'assets/images/gallery/23.png',
                            'assets/images/gallery/05.png',
                        ];
                        $imgIndex = array_rand($backgroundImages);
                        $image = $paket['foto'] ? base_url('uploads/paket/' . $paket['foto']) : base_url($backgroundImages[$imgIndex]);
                        ?>
                        <!-- Paket Image -->
                        <div class="h-80 md:h-96 overflow-hidden">
                            <img src="<?= $image ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover">
                        </div>

                        <!-- Paket Info -->
                        <div class="p-6">
                            <div class="flex flex-wrap items-center justify-between mb-4">
                                <div>
                                    <span class="bg-blue-100 text-primary text-xs font-medium px-2.5 py-0.5 rounded mr-2">
                                        <?= $kategori['namakategori'] ?? 'Umum' ?>
                                    </span>
                                </div>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-800 mb-4"><?= $paket['namapaket'] ?></h1>

                            <!-- Deskripsi Paket -->
                            <div class="border-t border-b border-gray-200 py-6 my-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-4">Deskripsi</h2>
                                <div class="prose text-gray-600">
                                    <?php if (isset($paket['deskripsi']) && !empty($paket['deskripsi'])): ?>
                                        <p><?= nl2br($paket['deskripsi']) ?></p>
                                    <?php else: ?>
                                        <p>Informasi detail paket belum tersedia.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Booking Form -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                            <div class="p-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Harga</h2>
                                <div class="bg-gray-50 p-4 rounded-md mb-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700">Harga per orang</span>
                                        <span class="text-xl font-bold text-accent">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <?php if (isset($paket['durasi']) && !empty($paket['durasi'])): ?>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Durasi</span>
                                            <span class="text-gray-800 font-medium"><?= $paket['durasi'] ?> hari <?= ($paket['durasi'] > 1) ? ($paket['durasi'] - 1) . ' malam' : '' ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($paket['minimalpeserta']) && !empty($paket['minimalpeserta'])): ?>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Minimal Peserta</span>
                                            <span class="text-gray-800 font-medium"><?= $paket['minimalpeserta'] ?> orang</span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($paket['maximalpeserta']) && !empty($paket['maximalpeserta'])): ?>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Maksimal Peserta</span>
                                            <span class="text-gray-800 font-medium"><?= $paket['maximalpeserta'] ?> orang</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($paket['fasilitas']) && !empty($paket['fasilitas'])): ?>
                                    <div class="border-t border-gray-200 my-4 pt-4">
                                        <h3 class="font-bold text-gray-800 mb-2">Fasilitas:</h3>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <?php
                                            $fasilitasList = explode("\n", str_replace(', ', "\n", $paket['fasilitas']));
                                            foreach ($fasilitasList as $fasilitas):
                                                if (trim($fasilitas) !== ''):
                                            ?>
                                                    <li class="flex items-center">
                                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                                        <?= trim($fasilitas) ?>
                                                    </li>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <div class="border-t border-gray-200 my-4 pt-4 text-center">
                                    <?php if ($is_logged_in): ?>
                                        <a href="<?= base_url('booking/create/' . $paket['idpaket']) ?>" class="block bg-accent hover:bg-yellow-600 text-white py-3 px-4 rounded-md font-medium transition duration-300 w-full">
                                            Pesan Sekarang
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('auth?redirect=booking/create/' . $paket['idpaket']) ?>" class="block bg-accent hover:bg-yellow-600 text-white py-3 px-4 rounded-md font-medium transition duration-300 w-full">
                                            Login untuk Memesan
                                        </a>
                                    <?php endif; ?>

                                    <p class="text-sm text-gray-500 mt-3">Atau hubungi kami untuk informasi lebih lanjut</p>
                                    <a href="<?= base_url('kontak') ?>" class="block bg-white hover:bg-gray-100 text-primary border border-primary py-3 px-4 rounded-md font-medium transition duration-300 mt-2 w-full">
                                        Hubungi Kami
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Share -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <h3 class="font-bold text-gray-800 mb-3">Bagikan</h3>
                                <div class="flex space-x-3">
                                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white w-9 h-9 rounded-full flex items-center justify-center">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="bg-sky-500 hover:bg-sky-600 text-white w-9 h-9 rounded-full flex items-center justify-center">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="bg-green-600 hover:bg-green-700 text-white w-9 h-9 rounded-full flex items-center justify-center">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="#" class="bg-red-600 hover:bg-red-700 text-white w-9 h-9 rounded-full flex items-center justify-center">
                                        <i class="fab fa-pinterest"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Packages -->
            <?php if (!empty($related_pakets)): ?>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Paket Wisata Terkait</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($related_pakets as $related): ?>
                            <div class="paket-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300 border border-gray-100">
                                <div class="h-56 overflow-hidden">
                                    <?php
                                    $backgroundImages = [
                                        'assets/images/gallery/03.png',
                                        'assets/images/gallery/15.png',
                                        'assets/images/gallery/25.png',
                                        'assets/images/gallery/09.png',
                                        'assets/images/gallery/23.png',
                                        'assets/images/gallery/05.png',
                                    ];
                                    $imgIndex = array_rand($backgroundImages);
                                    $image = $related['foto'] ? base_url('uploads/paket/' . $related['foto']) : base_url($backgroundImages[$imgIndex]);
                                    ?>
                                    <img src="<?= $image ?>" alt="<?= $related['namapaket'] ?>" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                                </div>
                                <div class="p-5">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="bg-blue-100 text-primary text-xs font-medium px-2.5 py-0.5 rounded">
                                            <?= $kategori['namakategori'] ?? 'Umum' ?>
                                        </span>
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            <span class="text-gray-700 text-sm font-medium"><?= number_format(4 + (mt_rand(0, 10) / 10), 1) ?></span>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2"><?= $related['namapaket'] ?></h3>
                                    <p class="text-gray-600 mb-4 text-sm line-clamp-2"><?= $related['deskripsi'] ?? 'Nikmati perjalanan wisata dengan paket yang telah kami siapkan untuk pengalaman terbaik Anda.' ?></p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-accent text-lg font-bold">Rp <?= number_format($related['harga'], 0, ',', '.') ?></span>
                                        <a href="<?= base_url('paket/detail/' . $related['idpaket']) ?>" class="inline-block bg-primary hover:bg-blue-800 text-white py-1.5 px-4 rounded-md text-sm font-medium transition duration-300">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

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
                        <li><a href="<?= base_url('paket?kategori=' . ($kategori['idkategori'] ?? '')) ?>" class="text-gray-400 hover:text-white"><?= $kategori['namakategori'] ?? 'Kategori Lainnya' ?></a></li>
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
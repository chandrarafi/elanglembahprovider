<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kategori Wisata - Elang Lembah Travel' ?></title>

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
        .kategori-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .banner-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?= base_url('assets/images/gallery/09.png') ?>');
            background-size: cover;
            background-position: center;
            height: 40vh;
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
                    <a href="<?= base_url('kategori') ?>" class="text-primary border-b-2 border-primary font-medium">Kategori</a>
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
                <a href="<?= base_url('paket') ?>" class="block py-2 text-gray-800 hover:text-primary">Paket Wisata</a>
                <a href="<?= base_url('kategori') ?>" class="block py-2 text-primary font-medium">Kategori</a>
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

    <!-- Banner Section -->
    <section class="banner-section flex items-center justify-center text-center">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Kategori Wisata</h1>
            <p class="text-xl text-white mb-8 max-w-2xl mx-auto">Temukan berbagai jenis pengalaman wisata luar negeri sesuai dengan preferensi Anda</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Pilih Kategori Wisata</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Temukan paket perjalanan internasional yang sesuai dengan minat dan preferensi Anda</p>
            </div>

            <?php if (empty($kategori)): ?>
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <img src="<?= base_url('assets/images/icons/empty.svg') ?>" alt="Empty" class="w-24 h-24 mx-auto mb-4 opacity-50">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak Ada Kategori Wisata</h3>
                    <p class="text-gray-600 mb-4">Saat ini belum ada kategori wisata yang tersedia.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8">
                    <?php foreach ($kategori as $index => $kat): ?>
                        <div class="kategori-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300 h-full">
                            <div class="h-64 overflow-hidden relative">
                                <?php
                                $backgroundImages = [
                                    'assets/images/gallery/02.png', // Pegunungan
                                    'assets/images/gallery/15.png', // Pantai
                                    'assets/images/gallery/25.png', // Kota
                                    'assets/images/gallery/05.png', // Budaya
                                    'assets/images/gallery/03.png', // Adventure
                                    'assets/images/gallery/09.png', // Kuliner
                                    'assets/images/gallery/23.png', // Alam Liar
                                    'assets/images/gallery/16.png', // Sejarah
                                ];
                                $imgIndex = $index % count($backgroundImages);
                                $image = $kat['foto'] ? base_url('uploads/kategori/' . $kat['foto']) : base_url($backgroundImages[$imgIndex]);
                                ?>
                                <img src="<?= $image ?>" alt="<?= $kat['namakategori'] ?>" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                    <div class="p-6">
                                        <h3 class="text-2xl font-bold text-white mb-1"><?= $kat['namakategori'] ?></h3>
                                        <span class="inline-block bg-accent/80 text-white text-xs px-2 py-1 rounded-full">
                                            <?= $kat['paket_count'] ?> paket tersedia
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-600 mb-6">
                                    <?php
                                    $descriptions = [
                                        'Nikmati pemandangan alam yang memukau dengan perjalanan yang akan membawa Anda lebih dekat dengan keajaiban dunia.',
                                        'Rasakan sensasi liburan mewah di resort eksklusif dengan layanan bintang lima yang akan memanjakan Anda.',
                                        'Jelajahi budaya unik dan menarik dari berbagai negara dengan panduan yang berpengalaman.',
                                        'Petualangan seru menanti Anda dengan berbagai aktivitas menantang dan memacu adrenalin.',
                                        'Nikmati pengalaman baru yang menakjubkan dengan menjelajahi tempat-tempat yang belum banyak dikunjungi.',
                                        'Temukan destinasi eksotis pilihan yang menawarkan pemandangan menakjubkan dan pengalaman tak terlupakan.',
                                        'Kunjungi tempat-tempat ikonik dunia yang menawarkan sejarah dan arsitektur menakjubkan.',
                                        'Eksplorasi keajaiban alam luar biasa yang akan membuat Anda terpesona dengan keindahannya.'
                                    ];
                                    echo $descriptions[$index % count($descriptions)];
                                    ?>
                                </p>
                                <a href="<?= base_url('paket?kategori=' . $kat['idkategori']) ?>" class="inline-block bg-primary hover:bg-blue-700 text-white py-2.5 px-6 rounded-md font-medium transition duration-300">
                                    Lihat Paket Wisata <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-blue-50">
        <div class="container mx-auto px-4">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Manfaat Memilih Kategori</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Kategori membantu Anda mendapatkan pengalaman wisata yang sesuai dengan minat dan kebutuhan Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-filter text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Lebih Mudah Memilih</h3>
                    <p class="text-gray-600">Kategori memudahkan Anda menemukan paket wisata yang sesuai dengan preferensi Anda</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Pengalaman Terbaik</h3>
                    <p class="text-gray-600">Dapatkan pengalaman wisata yang lebih baik dan sesuai dengan ekspektasi Anda</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hemat Waktu</h3>
                    <p class="text-gray-600">Menghemat waktu dalam mencari dan memilih paket wisata yang sesuai</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hand-holding-usd text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Sesuai Budget</h3>
                    <p class="text-gray-600">Temukan paket wisata yang sesuai dengan budget dan kebutuhan Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image: url('<?= base_url('assets/images/gallery/15.png') ?>'); background-size: cover; background-position: center; background-blend-mode: overlay;"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl font-bold text-white mb-6">Temukan Paket Wisata Impian Anda</h2>
            <p class="text-white text-lg mb-8 max-w-2xl mx-auto">Jelajahi berbagai pilihan paket wisata luar negeri yang telah kami sediakan</p>
            <a href="<?= base_url('paket') ?>" class="bg-white hover:bg-gray-100 text-primary py-3 px-8 rounded-lg text-lg font-medium transition duration-300">
                Lihat Semua Paket
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 pb-6">
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
                        <?php foreach (array_slice($kategori, 0, 5) as $kat): ?>
                            <li><a href="<?= base_url('paket?kategori=' . $kat['idkategori']) ?>" class="text-gray-400 hover:text-white"><?= $kat['namakategori'] ?></a></li>
                        <?php endforeach; ?>
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
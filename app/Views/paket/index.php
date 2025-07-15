<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Paket Wisata - Elang Lembah Travel' ?></title>

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

        .banner-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?= base_url('assets/images/gallery/15.png') ?>');
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

    <!-- Banner Section -->
    <section class="banner-section flex items-center justify-center text-center">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Paket Wisata Luar Negeri</h1>
            <p class="text-xl text-white mb-8 max-w-2xl mx-auto">Temukan perjalanan luar biasa ke destinasi luar negeri yang menakjubkan dengan layanan premium</p>
        </div>
    </section>

    <!-- Filter & Search Section -->
    <section class="py-8 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <form action="<?= base_url('paket') ?>" method="get" class="flex flex-col md:flex-row items-center gap-4">
                <div class="w-full md:w-1/3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Paket</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="<?= $search ?? '' ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Cari paket wisata...">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori" id="kategori" class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['idkategori'] ?>" <?= ($selectedKategori == $kat['idkategori']) ? 'selected' : '' ?>><?= $kat['namakategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
                    <select name="sort" id="sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="" <?= ($sort == '') ? 'selected' : '' ?>>Terbaru</option>
                        <option value="price_low" <?= ($sort == 'price_low') ? 'selected' : '' ?>>Harga: Terendah</option>
                        <option value="price_high" <?= ($sort == 'price_high') ? 'selected' : '' ?>>Harga: Tertinggi</option>
                        <option value="popular" <?= ($sort == 'popular') ? 'selected' : '' ?>>Paling Populer</option>
                    </select>
                </div>
                <div class="w-full md:w-auto self-end">
                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-primary hover:bg-blue-800 text-white rounded-md transition duration-300">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <!-- Results Header -->
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">
                    <?php if ($search): ?>
                        Hasil Pencarian: "<?= $search ?>"
                    <?php elseif ($selectedKategori): ?>
                        <?php
                        $kategoriName = '';
                        foreach ($kategori as $kat) {
                            if ($kat['idkategori'] === $selectedKategori) {
                                $kategoriName = $kat['namakategori'];
                                break;
                            }
                        }
                        ?>
                        Paket Wisata: <?= $kategoriName ?>
                    <?php else: ?>
                        Semua Paket Wisata
                    <?php endif; ?>
                </h2>
                <p class="text-gray-600">Menampilkan <?= count($paketList) ?> paket wisata</p>
            </div>

            <?php if (empty($paketList)): ?>
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <img src="<?= base_url('assets/images/icons/empty.svg') ?>" alt="Empty" class="w-24 h-24 mx-auto mb-4 opacity-50">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak Ada Paket Wisata</h3>
                    <p class="text-gray-600 mb-4">Maaf, tidak ada paket wisata yang sesuai dengan kriteria pencarian Anda.</p>
                    <a href="<?= base_url('paket') ?>" class="inline-block bg-primary hover:bg-blue-800 text-white py-2 px-6 rounded-md transition duration-300">
                        <i class="fas fa-sync-alt mr-2"></i> Reset Filter
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($paketList as $paket): ?>
                        <div class="paket-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300 border border-gray-100">
                            <div class="h-56 overflow-hidden relative">
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
                                <img src="<?= $image ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                            </div>
                            <div class="p-5">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="bg-blue-100 text-primary text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?= $paket['kategori_nama'] ?? 'Umum' ?>
                                    </span>
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                                        <span class="text-gray-700 text-sm font-medium"><?= number_format(4 + (mt_rand(0, 10) / 10), 1) ?></span>
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2"><?= $paket['namapaket'] ?></h3>
                                <p class="text-gray-600 mb-4 text-sm line-clamp-2"><?= $paket['deskripsi'] ?? 'Nikmati perjalanan wisata dengan paket yang telah kami siapkan untuk pengalaman terbaik Anda.' ?></p>
                                <div class="flex justify-between items-center">
                                    <span class="text-accent text-lg font-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                    <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="inline-block bg-primary hover:bg-blue-800 text-white py-1.5 px-4 rounded-md text-sm font-medium transition duration-300">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Mengapa Memilih Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Elang Lembah Travel menyediakan pengalaman wisata terbaik dengan standar layanan premium</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Destinasi Terbaik</h3>
                    <p class="text-gray-600">Kami menawarkan destinasi wisata luar negeri terbaik dengan pemandangan menakjubkan</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Harga Terjangkau</h3>
                    <p class="text-gray-600">Harga yang kami tawarkan sangat kompetitif dengan fasilitas premium yang memuaskan</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Pelayanan Prima</h3>
                    <p class="text-gray-600">Tim kami siap membantu 24/7 untuk memastikan perjalanan Anda nyaman dan menyenangkan</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Terjamin & Aman</h3>
                    <p class="text-gray-600">Keamanan dan kenyamanan Anda selama berwisata adalah prioritas utama kami</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image: url('<?= base_url('assets/images/gallery/15.png') ?>'); background-size: cover; background-position: center; background-blend-mode: overlay;"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl font-bold text-white mb-6">Siap Untuk Menjelajahi Dunia?</h2>
            <p class="text-white text-lg mb-8 max-w-2xl mx-auto">Hubungi kami sekarang untuk informasi lebih lanjut atau pemesanan paket wisata</p>
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href="<?= base_url('kontak') ?>" class="bg-white hover:bg-gray-100 text-primary py-3 px-8 rounded-lg text-lg font-medium transition duration-300">
                    Hubungi Kami
                </a>
                <a href="<?= base_url('auth/register') ?>" class="bg-accent hover:bg-yellow-600 text-white py-3 px-8 rounded-lg text-lg font-medium transition duration-300">
                    Daftar Sekarang
                </a>
            </div>
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

            // Form auto-submit on select change
            document.getElementById('kategori').addEventListener('change', function() {
                this.form.submit();
            });

            document.getElementById('sort').addEventListener('change', function() {
                this.form.submit();
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
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tentang Kami - Elang Lembah Travel' ?></title>

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
        .banner-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?= base_url('assets/images/gallery/03.png') ?>');
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
                    <a href="<?= base_url('kategori') ?>" class="text-gray-800 hover:text-primary font-medium">Kategori</a>
                    <a href="<?= base_url('about') ?>" class="text-primary border-b-2 border-primary font-medium">Tentang Kami</a>
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
                <a href="<?= base_url('kategori') ?>" class="block py-2 text-gray-800 hover:text-primary">Kategori</a>
                <a href="<?= base_url('about') ?>" class="block py-2 text-primary font-medium">Tentang Kami</a>
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
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Tentang Kami</h1>
            <p class="text-xl text-white mb-8 max-w-2xl mx-auto">Kenali lebih dekat Elang Lembah Travel, partner terpercaya untuk perjalanan wisata internasional Anda</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-4">SEJARAH KAMI</span>
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Tentang Elang Lembah Travel</h2>
                    <p class="text-gray-600 mb-4">Elang Lembah Travel didirikan pada tahun 2015 dengan visi menjadi penyedia layanan perjalanan wisata terbaik di Indonesia yang mengutamakan kepuasan dan pengalaman pelanggan. Kami memiliki tim profesional yang berpengalaman dalam industri pariwisata dengan pengetahuan mendalam tentang berbagai destinasi wisata luar negeri.</p>
                    <p class="text-gray-600 mb-4">Selama bertahun-tahun, kami telah membangun reputasi yang kuat sebagai travel agent yang dapat dipercaya dan memberikan layanan berkualitas tinggi. Kami selalu berusaha untuk menawarkan pengalaman wisata yang tak terlupakan dengan memperhatikan setiap detail perjalanan.</p>
                    <p class="text-gray-600">Dengan lebih dari 1000+ pelanggan puas dan 100+ tujuan wisata di seluruh dunia, Elang Lembah Travel siap menjadi partner perjalanan Anda untuk menjelajahi keindahan dunia.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <img src="<?= base_url('assets/images/gallery/15.png') ?>" alt="About Us" class="rounded-lg shadow-md">
                    <img src="<?= base_url('assets/images/gallery/25.png') ?>" alt="About Us" class="rounded-lg shadow-md mt-6">
                    <img src="<?= base_url('assets/images/gallery/09.png') ?>" alt="About Us" class="rounded-lg shadow-md">
                    <img src="<?= base_url('assets/images/gallery/23.png') ?>" alt="About Us" class="rounded-lg shadow-md mt-6">
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Mission -->
    <section class="py-16 bg-blue-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-4">VISI & MISI</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Visi & Misi Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Komitmen kami untuk memberikan layanan terbaik dan pengalaman wisata yang tak terlupakan</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-eye text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Visi</h3>
                    <p class="text-gray-600 mb-4">Menjadi perusahaan travel agent terpercaya dan terdepan di Indonesia yang mengutamakan kepuasan pelanggan dengan memberikan layanan perjalanan wisata berkualitas tinggi.</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Menjadi pilihan utama untuk wisata internasional</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Memberikan pengalaman wisata terbaik</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Membangun kepercayaan pelanggan jangka panjang</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-md">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bullseye text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Misi</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex">
                            <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-primary font-bold mr-3 flex-shrink-0">1</span>
                            <span>Menyediakan paket wisata dengan harga kompetitif dan kualitas layanan terbaik</span>
                        </li>
                        <li class="flex">
                            <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-primary font-bold mr-3 flex-shrink-0">2</span>
                            <span>Memberikan pelayanan yang profesional, ramah, dan informatif kepada pelanggan</span>
                        </li>
                        <li class="flex">
                            <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-primary font-bold mr-3 flex-shrink-0">3</span>
                            <span>Terus berinovasi dalam mengembangkan produk dan layanan untuk memenuhi kebutuhan pelanggan</span>
                        </li>
                        <li class="flex">
                            <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-primary font-bold mr-3 flex-shrink-0">4</span>
                            <span>Membangun hubungan jangka panjang dengan pelanggan dan mitra bisnis</span>
                        </li>
                        <li class="flex">
                            <span class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-primary font-bold mr-3 flex-shrink-0">5</span>
                            <span>Berkontribusi positif terhadap perkembangan industri pariwisata Indonesia</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-4">NILAI PERUSAHAAN</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Nilai-Nilai Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Nilai-nilai yang menjadi pedoman kami dalam memberikan layanan terbaik</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Integritas</h3>
                    <p class="text-gray-600">Kami selalu bekerja dengan jujur dan transparan dalam setiap aspek bisnis</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kualitas</h3>
                    <p class="text-gray-600">Kami berkomitmen untuk memberikan layanan berkualitas tinggi dalam setiap perjalanan</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kepuasan Pelanggan</h3>
                    <p class="text-gray-600">Kepuasan pelanggan adalah prioritas utama dalam setiap layanan yang kami berikan</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Inovasi</h3>
                    <p class="text-gray-600">Kami terus berinovasi untuk mengembangkan layanan yang lebih baik bagi pelanggan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="py-16 bg-blue-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-4">TIM KAMI</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Tim Profesional Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Perkenalkan tim berpengalaman yang akan membantu mewujudkan liburan impian Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-lg overflow-hidden shadow-md">
                    <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="CEO" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Ahmad Faisal</h3>
                        <p class="text-primary font-medium mb-3">Chief Executive Officer</p>
                        <p class="text-gray-600 mb-4">Memiliki pengalaman lebih dari 10 tahun di industri pariwisata.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-blue-600">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-500">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-800">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-md">
                    <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="CFO" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Dewi Anggraini</h3>
                        <p class="text-primary font-medium mb-3">Chief Marketing Officer</p>
                        <p class="text-gray-600 mb-4">Ahli dalam pemasaran digital dan pengembangan strategi bisnis.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-blue-600">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-500">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-800">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-md">
                    <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="Operations Manager" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Budi Santoso</h3>
                        <p class="text-primary font-medium mb-3">Operations Manager</p>
                        <p class="text-gray-600 mb-4">Mengelola operasional perjalanan dan hubungan dengan mitra.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-blue-600">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-500">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-800">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-md">
                    <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="Customer Service" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">Siti Rahmawati</h3>
                        <p class="text-primary font-medium mb-3">Customer Service Manager</p>
                        <p class="text-gray-600 mb-4">Berpengalaman dalam memberikan pelayanan pelanggan terbaik.</p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-blue-600">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-500">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-blue-800">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Achievements -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-4">PENCAPAIAN</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Pencapaian Kami</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Beberapa pencapaian yang telah kami raih selama bertahun-tahun melayani pelanggan</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary mb-2">1,000+</div>
                    <p class="text-gray-600">Pelanggan Puas</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary mb-2">100+</div>
                    <p class="text-gray-600">Destinasi Wisata</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary mb-2">8+</div>
                    <p class="text-gray-600">Tahun Pengalaman</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary mb-2">50+</div>
                    <p class="text-gray-600">Mitra Kerja</p>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="<?= base_url('paket') ?>" class="inline-block bg-primary hover:bg-blue-700 text-white py-3 px-8 rounded-lg font-medium transition duration-300">
                    Lihat Paket Wisata Kami
                </a>
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
                <a href="<?= base_url('paket') ?>" class="bg-white hover:bg-gray-100 text-primary py-3 px-8 rounded-lg text-lg font-medium transition duration-300">
                    Lihat Paket Wisata
                </a>
                <a href="<?= base_url('kontak') ?>" class="bg-accent hover:bg-yellow-600 text-white py-3 px-8 rounded-lg text-lg font-medium transition duration-300">
                    Hubungi Kami
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
                        <li><a href="<?= base_url('kategori') ?>" class="text-gray-400 hover:text-white">Kategori</a></li>
                        <li><a href="<?= base_url('kontak') ?>" class="text-gray-400 hover:text-white">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xl font-semibold mb-4">Layanan</h3>
                    <ul class="space-y-2">
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Paket Tour Internasional</a></li>
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Tiket Pesawat</a></li>
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Hotel Booking</a></li>
                        <li><a href="<?= base_url('paket') ?>" class="text-gray-400 hover:text-white">Rental Kendaraan</a></li>
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
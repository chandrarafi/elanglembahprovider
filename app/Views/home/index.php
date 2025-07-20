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

    <!-- Custom styles -->
    <style>
        .hero-slider {
            position: relative;
            height: 90vh;
            overflow: hidden;
        }

        .hero-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-slide.active {
            opacity: 1;
            z-index: 1;
        }

        .slide-content {
            text-align: center;
            max-width: 800px;
            padding: 0 20px;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kategori-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .paket-card {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .paket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px -3px rgba(0, 0, 0, 0.15);
        }

        .testimonial-card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .destination-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            height: 350px;
        }

        .destination-card img {
            transition: transform 0.5s ease;
        }

        .destination-card:hover img {
            transform: scale(1.05);
        }

        .destination-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
            padding: 30px 20px;
        }

        .slider-nav {
            position: absolute;
            z-index: 10;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .slider-dot.active {
            background-color: white;
            transform: scale(1.2);
        }
    </style>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
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

    <!-- Hero Section with Image Slider -->
    <section class="hero-slider">
        <div class="hero-slide active" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?= base_url('assets/images/1.jpg') ?>')">
            <div class="slide-content">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">Jelajahi Keajaiban Dunia</h1>
                <p class="text-xl text-white mb-8 max-w-3xl mx-auto">Temukan destinasi wisata luar negeri terbaik dengan panorama alam menakjubkan dan petualangan tak terlupakan</p>
                <div class="flex flex-col md:flex-row justify-center gap-4">
                    <a href="<?= base_url('paket') ?>" class="bg-accent hover:bg-yellow-600 text-white py-3 px-8 rounded-md text-lg font-medium transition duration-300">
                        Lihat Paket Wisata
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?= base_url('assets/images/2.jpg') ?>')">
            <div class="slide-content">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">Nikmati Keindahan Alam Luar Negeri</h1>
                <p class="text-xl text-white mb-8 max-w-3xl mx-auto">Paket wisata eksklusif ke destinasi internasional dengan pengalaman premium yang dirancang khusus untuk Anda</p>
                <div class="flex flex-col md:flex-row justify-center gap-4">
                    <a href="<?= base_url('paket?kategori=2') ?>" class="bg-primary hover:bg-blue-800 text-white py-3 px-8 rounded-md text-lg font-medium transition duration-300">
                        Lihat Destinasi
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?= base_url('assets/images/3.jpg') ?>')">
            <div class="slide-content">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">Petualangan Menakjubkan Menanti</h1>
                <p class="text-xl text-white mb-8 max-w-3xl mx-auto">Rasakan sensasi menjelajahi keindahan alam luar negeri dengan layanan bintang lima dan harga terbaik</p>
                <div class="flex flex-col md:flex-row justify-center gap-4">
                    <a href="<?= base_url('kontak') ?>" class="bg-white hover:bg-gray-100 text-primary py-3 px-8 rounded-md text-lg font-medium transition duration-300">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>

        <div class="slider-nav">
            <div class="slider-dot active" onclick="showSlide(0)"></div>
            <div class="slider-dot" onclick="showSlide(1)"></div>
            <div class="slider-dot" onclick="showSlide(2)"></div>
        </div>
    </section>

    <?php if (session()->get('user_id') && isset($rescheduleRequests) && !empty($rescheduleRequests)): ?>
        <!-- Reschedule Requests Notification -->
        <section class="py-6 bg-blue-50">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Permintaan Perubahan Jadwal Menunggu
                    </h3>

                    <div class="mb-4">
                        <p class="text-gray-600">Anda memiliki <?= count($rescheduleRequests) ?> permintaan perubahan jadwal yang sedang diproses:</p>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($rescheduleRequests as $request): ?>
                            <div class="bg-blue-50 p-4 rounded-lg flex items-start">
                                <div class="bg-blue-100 rounded-full p-2 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Booking #<?= $request['kode_booking'] ?></p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Jadwal Baru: <?= date('d M Y', strtotime($request['requested_tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($request['requested_tgl_selesai'])) ?>
                                    </p>
                                    <p class="text-sm text-yellow-600 mt-1">
                                        Status: <span class="font-medium">Menunggu Konfirmasi</span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="<?= base_url('booking/history') ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            Lihat Semua Riwayat
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (session()->get('user_id') && isset($upcomingBookings) && !empty($upcomingBookings)): ?>
        <!-- Upcoming Bookings Notification -->
        <section class="py-6 bg-blue-50">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Perjalanan Mendatang
                    </h3>

                    <div class="mb-4">
                        <p class="text-gray-600">Anda memiliki perjalanan yang akan datang:</p>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($upcomingBookings as $booking): ?>
                            <div class="bg-green-50 p-4 rounded-lg flex items-start">
                                <div class="bg-green-100 rounded-full p-2 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Booking #<?= $booking['kode_booking'] ?></p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Jadwal: <?= date('d M Y', strtotime($booking['tgl_berangkat'])) ?> s/d <?= date('d M Y', strtotime($booking['tgl_selesai'])) ?>
                                    </p>
                                    <div class="mt-2">
                                        <a href="<?= base_url('booking/detail/' . $booking['idpesan']) ?>" class="text-xs bg-green-600 hover:bg-green-700 text-white py-1 px-2 rounded">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Featured Destinations -->
    <section class="py-16 bg-gradient-to-b from-blue-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-3">DESTINASI LUAR NEGERI</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Destinasi Luar Negeri Menakjubkan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Temukan keindahan panorama alam luar negeri yang akan membuat mata Anda terpukau</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $backgroundImages = [
                    'assets/images/gallery/23.png', // Pegunungan
                    'assets/images/gallery/25.png', // Pantai
                    'assets/images/gallery/05.png', // Budaya
                ];
                $descriptions = [
                    'Nikmati pemandangan alam yang memukau dan petualangan luar biasa di destinasi wisata ini',
                    'Kunjungi tempat-tempat eksotis dengan keindahan panorama yang memesona',
                    'Rasakan pesona budaya dan keindahan alam yang menakjubkan'
                ];

                foreach ($featured_destinations as $index => $destination):
                    $imgIndex = $index % count($backgroundImages);
                    $descIndex = $index % count($descriptions);
                    $imageSrc = $destination['foto'] ? base_url('uploads/kategori/' . $destination['foto']) : base_url($backgroundImages[$imgIndex]);
                ?>
                    <div class="destination-card">
                        <img src="<?= $imageSrc ?>" alt="<?= $destination['namakategori'] ?>" class="w-full h-full object-cover">
                        <div class="destination-overlay">
                            <h3 class="text-2xl font-bold text-white"><?= $destination['namakategori'] ?></h3>
                            <p class="text-gray-200 mt-2"><?= $descriptions[$descIndex] ?></p>
                            <a href="<?= base_url('paket?kategori=' . $destination['idkategori']) ?>" class="inline-flex items-center text-white mt-4 hover:text-accent transition duration-300">
                                <span class="border-b border-white hover:border-accent">Jelajahi Sekarang</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Kategori Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-3">JENIS WISATA</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Pilihan Kategori Wisata</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Temukan berbagai jenis wisata luar negeri sesuai dengan preferensi perjalanan Anda</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($kategori as $index => $kat): ?>
                    <div class="kategori-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300 border border-gray-100">
                        <div class="h-52 overflow-hidden">
                            <?php if ($kat['foto']): ?>
                                <img src="<?= base_url('uploads/kategori/' . $kat['foto']) ?>" alt="<?= $kat['namakategori'] ?>" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                            <?php else: ?>
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
                                ?>
                                <img src="<?= base_url($backgroundImages[$imgIndex]) ?>" alt="<?= $kat['namakategori'] ?>" class="w-full h-full object-cover hover:scale-105 transition duration-700">
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-gray-800 mb-3"><?= $kat['namakategori'] ?></h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                <?php
                                // Deskripsi default jika tidak ada deskripsi dari database
                                $descriptions = [
                                    'Nikmati pemandangan alam yang memukau',
                                    'Rasakan sensasi liburan mewah',
                                    'Jelajahi budaya unik dan menarik',
                                    'Petualangan seru menanti Anda',
                                    'Nikmati pengalaman baru yang menakjubkan',
                                    'Temukan destinasi eksotis pilihan',
                                    'Kunjungi tempat-tempat ikonik dunia',
                                    'Eksplorasi keajaiban alam luar biasa'
                                ];

                                // Tambahkan logika untuk mengambil deskripsi dari database jika nanti ada
                                echo $descriptions[$index % count($descriptions)];
                                ?>
                            </p>
                            <a href="<?= base_url('paket?kategori=' . $kat['idkategori']) ?>" class="inline-block bg-primary hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium transition duration-300 flex items-center justify-center">
                                <span>Lihat Paket</span> <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Paket Populer Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-yellow-100 text-accent text-sm font-medium mb-3">PAKET TERPOPULER</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Paket Wisata Internasional Terpopuler</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Jelajahi keindahan alam luar negeri dengan paket terbaik pilihan wisatawan kami</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($paket_populer as $index => $paket): ?>
                    <div class="paket-card rounded-xl overflow-hidden group">
                        <div class="h-64 overflow-hidden relative">
                            <?php
                            $popularImages = [
                                'assets/images/gallery/03.png',
                                'assets/images/gallery/15.png',
                                'assets/images/gallery/25.png',
                                'assets/images/gallery/09.png',
                                'assets/images/gallery/23.png',
                                'assets/images/gallery/05.png',
                            ];
                            $imgIndex = $index % count($popularImages);
                            $image = $paket['foto'] ? base_url('uploads/paket/' . $paket['foto']) : base_url($popularImages[$imgIndex]);
                            ?>
                            <img src="<?= $image ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
                            <div class="absolute top-4 right-4">
                                <span class="bg-accent/90 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                    POPULER
                                </span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-map-marker-alt mr-2 text-accent"></i>
                                    <span class="text-sm font-medium"><?= $paket['kategori_nama'] ?? 'Destinasi Wisata' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-blue-100 text-primary text-xs font-medium px-2.5 py-0.5 rounded">
                                    <?= $paket['kategori_nama'] ?? 'Umum' ?>
                                </span>
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-gray-700 text-sm font-medium"><?= number_format(4 + (mt_rand(0, 10) / 10), 1) ?></span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3"><?= $paket['namapaket'] ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?= $paket['deskripsi'] ?? 'Nikmati keindahan panorama alam luar negeri dengan pengalaman yang tak akan terlupakan.' ?></p>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="block text-sm text-gray-500">Mulai dari</span>
                                    <span class="text-accent text-xl font-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                                </div>
                                <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="inline-block bg-primary hover:bg-blue-700 text-white py-2.5 px-5 rounded-lg transition duration-300">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-12">
                <a href="<?= base_url('paket') ?>" class="inline-flex items-center bg-white border-2 border-primary hover:bg-primary hover:text-white text-primary font-medium py-3 px-8 rounded-lg transition duration-300">
                    Lihat Semua Paket <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimoni Section -->
    <section class="py-16 bg-gradient-to-b from-white to-blue-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-blue-100 text-primary text-sm font-medium mb-3">TESTIMONI</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Apa Kata Mereka</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Pengalaman tak terlupakan dari pelanggan yang telah menikmati paket wisata luar negeri kami</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-12 h-12 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-semibold text-gray-800">Budi Santoso</h4>
                            <div class="flex text-yellow-400 mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"Perjalanan ke Pegunungan Alpen Swiss benar-benar luar biasa! Pemandangan alamnya menakjubkan dan pelayanan tour guide sangat profesional. Saya dan keluarga sangat puas dengan paket wisata ini."</p>
                    <div class="mt-4 text-sm text-primary font-medium">Swiss Alps Tour</div>
                </div>

                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-12 h-12 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-semibold text-gray-800">Dewi Anggraini</h4>
                            <div class="flex text-yellow-400 mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"Santorini memang seindah yang saya bayangkan! Sunset di Oia benar-benar memukau. Elang Lembah Travel menyiapkan pengalaman yang tak terlupakan dengan hotel dan restoran pilihan terbaik."</p>
                    <div class="mt-4 text-sm text-primary font-medium">Santorini Island Getaway</div>
                </div>

                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" alt="User" class="w-12 h-12 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="font-semibold text-gray-800">Ahmad Faisal</h4>
                            <div class="flex text-yellow-400 mt-1">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"Tour Kyoto di musim semi adalah keputusan terbaik! Melihat bunga sakura bermekaran dan mengunjungi kuil-kuil kuno memberi pengalaman budaya yang mendalam. Sangat direkomendasikan!"</p>
                    <div class="mt-4 text-sm text-primary font-medium">Japan Spring Exploration</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Paket Terbaru Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded bg-green-100 text-green-600 text-sm font-medium mb-3">BARU DITAMBAHKAN</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Paket Wisata Terbaru</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Jelajahi destinasi luar negeri dengan paket wisata terbaru yang kami tawarkan</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <?php foreach ($paket_terbaru as $index => $paket): ?>
                    <div class="paket-card bg-white rounded-xl overflow-hidden shadow-lg transition duration-300 border border-gray-100">
                        <div class="h-60 overflow-hidden relative">
                            <?php
                            $newImages = [
                                'assets/images/gallery/35.png',
                                'assets/images/gallery/16.png',
                                'assets/images/gallery/14.png',
                                'assets/images/gallery/36.png',
                                'assets/images/gallery/37.png',
                                'assets/images/gallery/08.png',
                            ];
                            $imgIndex = $index % count($newImages);
                            $image = $paket['foto'] ? base_url('uploads/paket/' . $paket['foto']) : base_url($newImages[$imgIndex]);
                            ?>
                            <img src="<?= $image ?>" alt="<?= $paket['namapaket'] ?>" class="w-full h-full object-cover transform hover:scale-105 transition duration-700">
                            <div class="absolute top-4 left-4">
                                <span class="bg-green-500/90 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                    NEW
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-blue-100 text-primary text-xs font-medium px-2.5 py-0.5 rounded">
                                    <?= $paket['kategori_nama'] ?? 'Umum' ?>
                                </span>
                                <span class="text-accent font-bold">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3"><?= $paket['namapaket'] ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?= $paket['deskripsi'] ?? 'Nikmati perjalanan wisata dengan paket yang telah kami siapkan untuk pengalaman terbaik Anda.' ?></p>
                            <a href="<?= base_url('paket/detail/' . $paket['idpaket']) ?>" class="inline-block bg-primary hover:bg-blue-800 text-white py-2 px-4 rounded-md transition duration-300">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image: url('<?= base_url('assets/images/gallery/15.png') ?>'); background-size: cover; background-position: center; background-blend-mode: overlay;"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl font-bold text-white mb-6">Siap Menjelajahi Keajaiban Dunia?</h2>
            <p class="text-white text-lg mb-8 max-w-3xl mx-auto">Dapatkan pengalaman wisata luar negeri yang menakjubkan dengan panorama alam menawan, pelayanan premium, dan harga terbaik hanya di Elang Lembah Travel</p>
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href="<?= base_url('paket') ?>" class="bg-white hover:bg-gray-100 text-primary py-3 px-8 rounded-lg text-lg font-medium transition duration-300">
                    Lihat Semua Paket
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
        document.addEventListener('DOMContentLoaded', function() {
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

            // Hero Slider
            let currentSlide = 0;
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.slider-dot');
            const totalSlides = slides.length;

            function showSlide(n) {
                // Hide all slides
                slides.forEach(slide => {
                    slide.classList.remove('active');
                });

                // Remove active from all dots
                dots.forEach(dot => {
                    dot.classList.remove('active');
                });

                // Show the selected slide
                currentSlide = (n + totalSlides) % totalSlides;
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
            }

            // Auto slide
            setInterval(() => {
                showSlide(currentSlide + 1);
            }, 5000);

            // Make the showSlide function accessible globally
            window.showSlide = showSlide;
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
/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - elanglembahprovider
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`elanglembahprovider` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `elanglembahprovider`;

/*Table structure for table `kategori` */

DROP TABLE IF EXISTS `kategori`;

CREATE TABLE `kategori` (
  `idkategori` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `namakategori` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idkategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `kategori` */

insert  into `kategori`(`idkategori`,`namakategori`,`status`,`foto`,`created_at`,`updated_at`) values 
('KTGR001','Wisata Alam','active','wisata-alam.jpg','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('KTGR002','Wisata Pantai','active','wisata-pantai.jpg','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('KTGR003','Wisata Gunung','active','wisata-gunung.jpg','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('KTGR004','Wisata Budaya','active',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('KTGR005','Wisata Kuliner','active',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('KTGR006','Wisata Religi','inactive',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `migrations` */

/*Table structure for table `otp` */

DROP TABLE IF EXISTS `otp`;

CREATE TABLE `otp` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_user_id_foreign` (`user_id`),
  CONSTRAINT `otp_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `otp` */

/*Table structure for table `paket_wisata` */

DROP TABLE IF EXISTS `paket_wisata`;

CREATE TABLE `paket_wisata` (
  `idpaket` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `namapaket` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `statuspaket` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `idkategori` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idpaket`),
  KEY `paket_wisata_idkategori_foreign` (`idkategori`),
  CONSTRAINT `paket_wisata_idkategori_foreign` FOREIGN KEY (`idkategori`) REFERENCES `kategori` (`idkategori`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `paket_wisata` */

insert  into `paket_wisata`(`idpaket`,`namapaket`,`deskripsi`,`harga`,`statuspaket`,`foto`,`idkategori`,`created_at`,`updated_at`) values 
('PKT001','Paket Wisata Bali 3 Hari 2 Malam','Nikmati keindahan Pulau Bali dengan paket wisata 3 hari 2 malam. Kunjungi Pantai Kuta, Tanah Lot, dan Ubud. Termasuk akomodasi hotel bintang 4, transportasi, dan makan 3 kali sehari.',2500000.00,'active','bali-package.jpg','KTGR002','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT002','Paket Pendakian Gunung Bromo','Paket pendakian Gunung Bromo untuk menikmati keindahan sunrise. Termasuk transportasi, pemandu wisata, dan penginapan di area Bromo.',1800000.00,'active','bromo-package.jpg','KTGR003','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT003','Wisata Budaya Yogyakarta','Jelajahi kekayaan budaya Yogyakarta dengan mengunjungi Keraton, Candi Prambanan, dan Malioboro. Termasuk transportasi, pemandu wisata, dan akomodasi hotel.',1500000.00,'active',NULL,'KTGR004','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT004','Paket Kuliner Jakarta','Nikmati berbagai kuliner khas Jakarta dari Sate Padang, Kerak Telor, hingga kuliner modern di kawasan Jakarta. Termasuk transportasi dan pemandu wisata kuliner.',1200000.00,'active',NULL,'KTGR005','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT005','Wisata Alam Taman Nasional Ujung Kulon','Jelajahi keindahan alam Taman Nasional Ujung Kulon, habitat badak bercula satu. Termasuk transportasi, pemandu wisata, dan perlengkapan camping.',2800000.00,'active',NULL,'KTGR001','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT006','Wisata Religi Walisongo','Kunjungi makam para Wali Songo di Jawa dengan paket wisata religi lengkap. Termasuk transportasi, pemandu wisata, dan penginapan.',1600000.00,'inactive',NULL,'KTGR006','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT007','Paket Wisata Lombok 4 Hari 3 Malam','Jelajahi keindahan Pulau Lombok dengan mengunjungi Pantai Kuta Lombok, Gili Trawangan, dan Air Terjun Sendang Gile. Termasuk akomodasi, transportasi, dan makan 3 kali sehari.',3200000.00,'active','lombok-package.jpg','KTGR002','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT008','Pendakian Gunung Rinjani','Paket pendakian Gunung Rinjani selama 3 hari 2 malam. Termasuk pemandu, porter, perlengkapan camping, dan makanan selama pendakian.',2200000.00,'active','rinjani-package.jpg','KTGR003','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT009','Paket Wisata Pantai Kuta Premium','Nikmati keindahan Pantai Kuta dengan paket premium yang mencakup akomodasi hotel bintang 5, makan 3 kali sehari, transportasi VIP, dan akses ke berbagai fasilitas pantai eksklusif. Cocok untuk honeymoon atau liburan keluarga.',3500000.00,'active','pantai-kuta.jpg','KTGR002','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT010','Paket Wisata Pantai Pink Lombok','Jelajahi keindahan Pantai Pink di Lombok yang terkenal dengan pasirnya yang berwarna merah muda. Paket termasuk transportasi, penginapan, dan pemandu wisata selama 2 hari 1 malam.',1800000.00,'active','pantai-pink.jpg','KTGR002','2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PKT011','Paket Wisata Gili Trawangan','Nikmati keindahan pulau Gili Trawangan dengan aktivitas snorkeling, diving, dan bersepeda mengelilingi pulau. Paket termasuk penginapan, transportasi, dan beberapa aktivitas air.',2200000.00,'active','pantai-gili.jpg','KTGR002','2025-07-19 09:07:11','2025-07-19 09:07:11');

/*Table structure for table `pelanggan` */

DROP TABLE IF EXISTS `pelanggan`;

CREATE TABLE `pelanggan` (
  `idpelanggan` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `namapelanggan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `nohp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `iduser` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idpelanggan`),
  KEY `pelanggan_iduser_foreign` (`iduser`),
  CONSTRAINT `pelanggan_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `pelanggan` */

insert  into `pelanggan`(`idpelanggan`,`namapelanggan`,`alamat`,`nohp`,`iduser`,`created_at`,`updated_at`) values 
('PLG001','Budi Santoso','Jl. Merdeka No. 123, Jakarta Selatan','081234567890',3,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PLG002','Siti Rahayu','Jl. Pahlawan No. 45, Bandung','082345678901',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PLG003','Ahmad Hidayat','Jl. Diponegoro No. 78, Surabaya','083456789012',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PLG004','Dewi Lestari','Jl. Sudirman No. 56, Semarang','084567890123',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PLG005','Eko Prasetyo','Jl. Gatot Subroto No. 89, Yogyakarta','085678901234',NULL,'2025-07-19 09:07:11','2025-07-19 09:07:11'),
('PLG006','Mimi wulandari','Padang','083182423488',6,'2025-07-19 09:46:00','2025-07-19 09:46:00');

/*Table structure for table `pembayaran` */

DROP TABLE IF EXISTS `pembayaran`;

CREATE TABLE `pembayaran` (
  `idbayar` int unsigned NOT NULL AUTO_INCREMENT,
  `idpesan` int unsigned NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `metode_pembayaran` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `bukti_bayar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_pembayaran` enum('pending','verified','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idbayar`),
  KEY `pembayaran_idpesan_foreign` (`idpesan`),
  CONSTRAINT `pembayaran_idpesan_foreign` FOREIGN KEY (`idpesan`) REFERENCES `pemesanan` (`idpesan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `pembayaran` */

/*Table structure for table `pemesanan` */

DROP TABLE IF EXISTS `pemesanan`;

CREATE TABLE `pemesanan` (
  `idpesan` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_booking` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` datetime NOT NULL,
  `iduser` int unsigned NOT NULL,
  `idpaket` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `tgl_berangkat` date NOT NULL,
  `totalbiaya` decimal(15,2) NOT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','confirmed','paid','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`idpesan`),
  UNIQUE KEY `kode_booking` (`kode_booking`),
  KEY `pemesanan_iduser_foreign` (`iduser`),
  KEY `pemesanan_idpaket_foreign` (`idpaket`),
  CONSTRAINT `pemesanan_idpaket_foreign` FOREIGN KEY (`idpaket`) REFERENCES `paket_wisata` (`idpaket`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `pemesanan_iduser_foreign` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `pemesanan` */

insert  into `pemesanan`(`idpesan`,`kode_booking`,`tanggal`,`iduser`,`idpaket`,`harga`,`tgl_berangkat`,`totalbiaya`,`catatan`,`status`,`created_at`,`updated_at`) values 
(1,'ELP202507197938','2025-07-19 10:34:36',6,'PKT007',3200000.00,'2025-07-21',3200000.00,NULL,'pending','2025-07-19 10:34:36','2025-07-19 10:34:36');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','direktur','pelanggan') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pelanggan' COMMENT 'admin, direktur, pelanggan',
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `last_login` datetime DEFAULT NULL,
  `last_page_visited` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_verified` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`email`,`password`,`name`,`role`,`status`,`phone`,`address`,`last_login`,`last_page_visited`,`remember_token`,`email_verified`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'admin','admin@elanglembah.com','$2y$10$xwMdfGK9yvcVaUzrxfsvXearsAdsci.ErjyeEwNZivIYnUiI01A22','Administrator','admin','active',NULL,NULL,NULL,NULL,NULL,0,'2025-07-19 09:07:11','2025-07-19 09:07:11',NULL),
(2,'direktur','direktur@elanglembah.com','$2y$10$X0FbqYx8PIZx3HCCn8BC1uCk3MCr3Zb8ZhRncEWsppKt8bvjKapl2','Direktur','direktur','active',NULL,NULL,NULL,NULL,NULL,0,'2025-07-19 09:07:11','2025-07-19 09:07:11',NULL),
(3,'pelanggan','pelanggan@example.com','$2y$10$1Y77zZ/RntuWkCdDwW0gv.6wA1ve8lMYB3j7AKtu2XHttovD0Vs..','Pelanggan','pelanggan','active',NULL,NULL,NULL,NULL,NULL,0,'2025-07-19 09:07:11','2025-07-19 09:07:11',NULL),
(6,'mimiwulandari','mimi@pingaja.site','$2y$10$zfLmOwknZqZs1JysEXeFm.kD3l1BEFIYH48Ev8Y.TsiR7hBeR7/66','Mimi wulandari','pelanggan','active','083182423488','Padang','2025-07-19 09:46:50',NULL,NULL,1,'2025-07-19 09:46:00','2025-07-19 09:46:50',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

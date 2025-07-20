-- Add expired_at column to pembayaran table
ALTER TABLE `pembayaran` ADD COLUMN `expired_at` datetime DEFAULT NULL AFTER `keterangan`;

-- Add expired_at column to pemesanan table as well for booking expiration tracking
ALTER TABLE `pemesanan` ADD COLUMN `expired_at` datetime DEFAULT NULL AFTER `status`;

-- Add tgl_selesai column to pemesanan table if it doesn't exist already
-- This column will store the end date calculated from start date + duration
ALTER TABLE `pemesanan` ADD COLUMN IF NOT EXISTS `tgl_selesai` date DEFAULT NULL AFTER `tgl_berangkat`;

-- Add durasi column to paket_wisata table if it doesn't exist already
ALTER TABLE `paket_wisata` ADD COLUMN IF NOT EXISTS `durasi` int DEFAULT NULL COMMENT 'Duration in days' AFTER `harga`;

-- Tambahkan kolom tipe_pembayaran pada tabel pembayaran
ALTER TABLE pembayaran ADD COLUMN tipe_pembayaran ENUM('dp', 'lunas') DEFAULT 'lunas' AFTER metode_pembayaran; 
-- Tambahkan nilai down_payment dan waiting_confirmation ke enum status pada tabel pemesanan
ALTER TABLE `pemesanan` MODIFY COLUMN `status` ENUM('pending', 'down_payment', 'waiting_confirmation', 'confirmed', 'paid', 'completed', 'cancelled') DEFAULT 'pending' NOT NULL;

-- Memastikan nilai tipe_pembayaran pada pembayaran sudah benar
ALTER TABLE `pembayaran` MODIFY COLUMN `tipe_pembayaran` ENUM('dp', 'lunas') DEFAULT 'lunas' NOT NULL; 
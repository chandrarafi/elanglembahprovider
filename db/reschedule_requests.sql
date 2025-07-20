CREATE TABLE `reschedule_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idpesan` int(11) NOT NULL,
  `current_tgl_berangkat` date NOT NULL,
  `requested_tgl_berangkat` date NOT NULL,
  `current_tgl_selesai` date NOT NULL,
  `requested_tgl_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idpesan` (`idpesan`),
  CONSTRAINT `reschedule_requests_ibfk_1` FOREIGN KEY (`idpesan`) REFERENCES `pemesanan` (`idpesan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
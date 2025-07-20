<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>E-Ticket - <?= $pemesanan['kode_booking'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .ticket-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .ticket-header {
            text-align: center;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .ticket-title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            color: #f59e0b;
        }

        .ticket-subtitle {
            font-size: 16px;
            margin-bottom: 15px;
            color: #666;
        }

        .booking-code {
            background-color: #f59e0b;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }

        .ticket-details {
            margin-top: 20px;
        }

        .detail-row {
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }

        .package-details {
            margin: 20px 0;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
        }

        .package-title {
            font-size: 18px;
            font-weight: bold;
            color: #f59e0b;
            margin-bottom: 10px;
        }

        .barcode {
            text-align: center;
            margin-top: 30px;
        }

        .barcode img {
            max-width: 150px;
            height: auto;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .important-note {
            margin-top: 20px;
            padding: 10px;
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1 class="ticket-title">ELANG LEMBAH TOURISM</h1>
            <div class="ticket-subtitle">E-Ticket / Bukti Pemesanan Paket Wisata</div>
            <div class="booking-code"><?= $pemesanan['kode_booking'] ?></div>
        </div>

        <div class="ticket-details">
            <div class="detail-row">
                <span class="detail-label">Nama Pemesan:</span>
                <span><?= $user['name'] ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span><?= $user['email'] ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">No. Telepon:</span>
                <span><?= $user['phone'] ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Pemesanan:</span>
                <span><?= date('d M Y', strtotime($pemesanan['tanggal'])) ?></span>
            </div>
        </div>

        <div class="package-details">
            <div class="package-title">Detail Paket Wisata</div>
            <div class="detail-row">
                <span class="detail-label">Nama Paket:</span>
                <span><?= $paket['namapaket'] ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Keberangkatan:</span>
                <span><?= date('d M Y', strtotime($pemesanan['tgl_berangkat'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Selesai:</span>
                <span><?= date('d M Y', strtotime($pemesanan['tgl_selesai'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Durasi:</span>
                <span><?= $paket['durasi'] ?> hari</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jumlah Peserta:</span>
                <span><?= $pemesanan['jumlah_peserta'] ?> orang</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Biaya:</span>
                <span>Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status Pemesanan:</span>
                <span><?= ucfirst($pemesanan['status']) ?></span>
            </div>
        </div>

        <?php if (!empty($pemesanan['catatan'])): ?>
            <div class="package-details">
                <div class="package-title">Catatan Khusus</div>
                <p><?= $pemesanan['catatan'] ?></p>
            </div>
        <?php endif; ?>

        <div class="important-note">
            <strong>Penting:</strong> E-ticket ini harus ditunjukkan pada saat keberangkatan. Harap datang setidaknya 30 menit sebelum jadwal keberangkatan.
        </div>

        <div class="barcode">
            <img src="<?= $qr_code ?>" alt="QR Code">
            <p>Scan QR Code untuk validasi</p>
        </div>

        <div class="footer">
            <p>Elang Lembah Tourism &copy; <?= date('Y') ?> | info@elanglembah.com | +62 812-3456-7890</p>
        </div>
    </div>
</body>

</html>
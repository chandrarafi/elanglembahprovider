<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemesanan - Elang Lembah Provider</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
            width: 100%;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        .header {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .logo-cell {
            display: table-cell;
            vertical-align: middle;
            width: 100px;
            padding-right: 15px;
        }

        .logo {
            max-width: 90px;
            height: auto;
        }

        .info-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-right: 100px;
            /* Add padding to offset the logo width */
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }

        .company-info {
            font-size: 11px;
            margin: 5px 0;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 10px;
            text-align: center;
            width: 100%;
            display: block;
        }

        .report-meta {
            margin: 10px 0;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
        }

        .report-meta p {
            margin: 3px 0;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-pending {
            background-color: #ffc107;
            color: black;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-waiting_confirmation {
            background-color: #0dcaf0;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-confirmed {
            background-color: #0d6efd;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-paid {
            background-color: #198754;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-completed {
            background-color: #6c757d;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 20px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-cell">
                    <!-- Use embedded image data with correct path -->
                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents(ROOTPATH . 'public/assets/images/logo.png')) ?>" alt="Elang Lembah Provider" class="logo">
                </div>
                <div class="info-cell">
                    <div class="company-name">Elang Lembah Provider</div>
                    <div class="company-info">
                        Jl. Belati Barat IV No. 5, Lolong Belati, Padang Utara, Sumatera Barat<br>
                        Telepon: 0822-1095-5523 | Email: elanglembahprovider@gmail.com
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="report-title">Laporan Pemesanan Per-Tanggal</div>

        <!-- Report Metadata -->
        <div class="report-meta">
            <div>
                <p><strong>Tanggal Laporan:</strong> <?= $reportDate ?></p>
            </div>
            <div>
                <p><strong>Periode:</strong> <?= $reportPeriode ?></p>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Kode Booking</th>
                    <th>Nama Paket</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Tanggal Berangkat</th>
                    <th>Tanggal Selesai</th>
                    <th>Jumlah Peserta</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pemesanan)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">Tidak ada data pemesanan yang ditemukan</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pemesanan as $item): ?>
                        <tr>
                            <td><?= $item['kode_booking'] ?></td>
                            <td><?= $item['namapaket'] ?></td>
                            <td><?= $item['name'] ?></td>
                            <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($item['tgl_berangkat'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($item['tgl_selesai'])) ?></td>
                            <td><?= $item['jumlah_peserta'] ?></td>
                            <td>Rp <?= number_format($item['totalbiaya'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($item['status'] === 'pending'): ?>
                                    <div class="status-pending">Pending</div>
                                <?php elseif ($item['status'] === 'waiting_confirmation'): ?>
                                    <div class="status-waiting_confirmation">Menunggu Konfirmasi</div>
                                <?php elseif ($item['status'] === 'confirmed'): ?>
                                    <div class="status-confirmed">Dikonfirmasi</div>
                                <?php elseif ($item['status'] === 'paid'): ?>
                                    <div class="status-paid">Dibayar</div>
                                <?php elseif ($item['status'] === 'completed'): ?>
                                    <div class="status-completed">Selesai</div>
                                <?php elseif ($item['status'] === 'cancelled'): ?>
                                    <div class="status-cancelled">Dibatalkan</div>
                                <?php else: ?>
                                    <?= $item['status'] ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> | Elang Lembah Provider &copy; <?= date('Y') ?></p>
        </div>
    </div>
</body>

</html>
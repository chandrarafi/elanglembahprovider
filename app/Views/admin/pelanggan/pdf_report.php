<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggan - Elang Lembah Provider</title>
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

        .status-active {
            background-color: #198754;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-inactive {
            background-color: #dc3545;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-admin {
            background-color: #0d6efd;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .status-direktur {
            background-color: #0dcaf0;
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

        <div class="report-title">Laporan Data Pelanggan</div>

        <!-- Report Metadata -->
        <div class="report-meta">
            <p>Tanggal: <?= $tanggal ?></p>
            <p>Total Pelanggan: <?= count($pelanggan) ?></p>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pelanggan) > 0): ?>
                    <?php $no = 1;
                    foreach ($pelanggan as $row): ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['phone'] ?? '-' ?></td>
                            <td><?= $row['address'] ?? '-' ?></td>
                            <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada data pelanggan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            Laporan ini dicetak pada <?= date('d-m-Y H:i:s') ?> | Elang Lembah Provider
        </div>
    </div>
</body>

</html>
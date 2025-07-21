<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran Bulanan - Elang Lembah Provider</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 10px;
            color: #333;
            margin: 0;
            font-size: 12px;
        }

        .header {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .company-info {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            text-align: center;
            text-transform: uppercase;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        .report-meta {
            margin-bottom: 15px;
            width: 100%;
        }

        .meta-item {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">Elang Lembah Provider</div>
            <div class="company-info">
                Jl. Belati Barat IV No. 5, Lolong Belati, Padang Utara, Sumatera Barat<br>
                Telepon: 0822-1095-5523 | Email: elanglembahprovider@gmail.com
            </div>
        </div>

        <div class="divider"></div>

        <div class="report-title">Laporan Pembayaran Bulanan</div>

        <!-- Report Metadata -->
        <div class="report-meta">
            <table style="border: none; width: 100%;">
                <tr style="border: none;">
                    <td style="border: none; width: 50%; text-align: left;">
                        <div class="meta-item"><strong>Tanggal Laporan:</strong> <?= $reportDate ?></div>
                    </td>
                    <td style="border: none; width: 50%; text-align: right;">
                        <div class="meta-item"><strong>Periode:</strong> <?= $reportPeriode ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jumlah Pembayaran</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($monthly_data)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data pembayaran yang ditemukan</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($monthly_data as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item['date_formatted'] ?></td>
                            <td class="text-center"><?= $item['count'] ?></td>
                            <td class="text-end">Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" class="text-end"><strong>Total</strong></td>
                        <td class="text-center"><strong><?= $totalCount ?></strong></td>
                        <td class="text-end"><strong>Rp <?= number_format($totalAmount, 0, ',', '.') ?></strong></td>
                    </tr>
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
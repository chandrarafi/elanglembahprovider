<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan Pemesanan - Elang Lembah Provider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
            margin: 0;
        }

        .header {
            position: relative;
            width: 100%;
            margin-bottom: 30px;
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
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }

        .company-info {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 22px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            text-transform: uppercase;
            width: 100%;
            display: block;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 15px 0;
        }

        .report-meta {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                padding: 10px;
                margin: 0;
                width: 100%;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            table {
                page-break-inside: auto;
                width: 100% !important;
                font-size: 10px !important;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            th,
            td {
                padding: 3px !important;
                font-size: 10px !important;
            }

            .status-pending {
                background-color: #ffc107 !important;
                color: black !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-waiting_confirmation {
                background-color: #0dcaf0 !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-confirmed {
                background-color: #0d6efd !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-paid {
                background-color: #198754 !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-completed {
                background-color: #6c757d !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-cancelled {
                background-color: #dc3545 !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: landscape;
                margin: 10mm;
            }
        }

        .status-pending {
            background-color: #ffc107;
            color: black;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-waiting_confirmation {
            background-color: #0dcaf0;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-confirmed {
            background-color: #0d6efd;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-paid {
            background-color: #198754;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-completed {
            background-color: #6c757d;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <button class="btn btn-primary print-btn" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
        </svg>
        Print
    </button>

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
        <table class="table table-bordered">
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
                        <td colspan="9" class="text-center">Tidak ada data pemesanan yang ditemukan</td>
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
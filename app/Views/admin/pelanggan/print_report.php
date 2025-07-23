<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pelanggan - Elang Lembah Provider</title>
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

            .status-active {
                background-color: #198754 !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-inactive {
                background-color: #dc3545 !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-admin {
                background-color: #0d6efd !important;
                color: white !important;
                padding: 2px 3px !important;
                border-radius: 2px !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .status-direktur {
                background-color: #0dcaf0 !important;
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

        .status-active {
            background-color: #198754;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-inactive {
            background-color: #dc3545;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-admin {
            background-color: #0d6efd;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .status-direktur {
            background-color: #0dcaf0;
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

        <div class="report-title">Laporan Data Pelanggan</div>

        <!-- Report Metadata -->
        <div class="report-meta">
            <p>Tanggal: <?= $tanggal ?></p>
            <p>Total Pelanggan: <?= count($pelanggan) ?></p>
        </div>

        <!-- Table -->
        <table class="table table-bordered">
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
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['phone'] ?? '-' ?></td>
                            <td><?= $row['address'] ?? '-' ?></td>
                            <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data pelanggan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            Laporan ini dicetak pada <?= date('d-m-Y H:i:s') ?> | Elang Lembah Provider
        </div>
    </div>

    <script>
        // Auto-print when the page loads
        window.onload = function() {
            // Give the browser a moment to render the page
            setTimeout(function() {
                // Uncomment the line below to automatically open print dialog
                // window.print();
            }, 1000);
        };
    </script>
</body>

</html>
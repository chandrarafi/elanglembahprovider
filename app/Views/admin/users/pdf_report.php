<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan User - Elang Lembah Provider</title>
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

        .role-admin {
            background-color: #0d6efd;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .role-direktur {
            background-color: #0dcaf0;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
        }

        .role-pelanggan {
            background-color: #198754;
            color: white;
            padding: 2px 3px;
            border-radius: 2px;
            font-size: 8px;
            display: inline-block;
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

        <div class="report-title">Laporan Data User</div>

        <!-- Report Metadata -->
        <div class="report-meta">
            <div>
                <p><strong>Tanggal Laporan:</strong> <?= $reportDate ?></p>
            </div>
            <div>
                <!-- <p><strong>Periode:</strong> <?= $reportPeriode ?></p> -->
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Login Terakhir</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center;">Tidak ada data user yang ditemukan</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <div class="role-admin">admin</div>
                                <?php elseif ($user['role'] === 'direktur'): ?>
                                    <div class="role-direktur">direktur</div>
                                <?php elseif ($user['role'] === 'pelanggan'): ?>
                                    <div class="role-pelanggan">pelanggan</div>
                                <?php else: ?>
                                    <?= $user['role'] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <div class="status-active">active</div>
                                <?php else: ?>
                                    <div class="status-inactive">inactive</div>
                                <?php endif; ?>
                            </td>
                            <td><?= $user['phone'] ?? '-' ?></td>
                            <td><?= $user['address'] ?? '-' ?></td>
                            <td><?= $user['last_login_formatted'] ?></td>
                            <td><?= $user['created_at_formatted'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Summary -->
        <!-- <p><strong>Total User:</strong> <?= count($users) ?></p> -->

        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> | Elang Lembah Provider &copy; <?= date('Y') ?></p>
        </div>
    </div>
</body>

</html>
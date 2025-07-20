<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= $nomor_invoice ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 14px;
        }

        .invoice-container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .invoice-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            overflow: hidden;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .invoice-details {
            float: right;
            width: 50%;
            text-align: right;
        }

        .invoice-title {
            font-size: 24px;
            color: #3b82f6;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .invoice-id {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #3b82f6;
        }

        .customer-details {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right {
            text-align: right;
        }

        .total-container {
            width: 60%;
            margin-left: auto;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }

        .total-row {
            overflow: hidden;
            margin-bottom: 5px;
        }

        .total-label {
            float: left;
            font-weight: bold;
        }

        .total-value {
            float: right;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #3b82f6;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 10px;
        }

        .notes {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            color: #64748b;
            font-size: 13px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <div class="invoice-title">ELANG LEMBAH TOURISM</div>
                <div>Jl. Pariwisata No. 123, Kota Wisata</div>
                <div>Telp: +62 812-3456-7890</div>
            </div>
            <div class="invoice-details">
                <div class="invoice-id">INVOICE: <?= $nomor_invoice ?></div>
                <div class="invoice-date">Tanggal: <?= date('d/m/Y', strtotime($tanggal_invoice)) ?></div>
                <div>Kode Booking: <?= $pemesanan['kode_booking'] ?></div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="section-title">Data Pelanggan</div>
        <div class="customer-details">
            <div><strong>Nama:</strong> <?= $user['name'] ?></div>
            <div><strong>Email:</strong> <?= $user['email'] ?></div>
            <div><strong>Telepon:</strong> <?= $user['phone'] ?></div>
        </div>

        <div class="section-title">Detail Pemesanan</div>
        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Tanggal</th>
                    <th>Durasi</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $paket['namapaket'] ?></td>
                    <td><?= date('d/m/Y', strtotime($pemesanan['tgl_berangkat'])) ?> - <?= date('d/m/Y', strtotime($pemesanan['tgl_selesai'])) ?></td>
                    <td><?= $paket['durasi'] ?> hari</td>
                    <td class="text-right">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-container">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">Rp <?= number_format($pemesanan['totalbiaya'], 0, ',', '.') ?></div>
                <div class="clear"></div>
            </div>
            <?php if ($pembayaran['tipe_pembayaran'] === 'dp'): ?>
                <div class="total-row">
                    <div class="total-label">Tipe Pembayaran:</div>
                    <div class="total-value">DP (50%)</div>
                    <div class="clear"></div>
                </div>
                <div class="total-row grand-total">
                    <div class="total-label">Total Dibayar:</div>
                    <div class="total-value">Rp <?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.') ?></div>
                    <div class="clear"></div>
                </div>
                <div class="total-row" style="color: #dc2626;">
                    <div class="total-label">Sisa Pembayaran:</div>
                    <div class="total-value">Rp <?= number_format($pemesanan['totalbiaya'] - $pembayaran['jumlah_bayar'], 0, ',', '.') ?></div>
                    <div class="clear"></div>
                </div>
            <?php else: ?>
                <div class="total-row">
                    <div class="total-label">Tipe Pembayaran:</div>
                    <div class="total-value">Lunas (100%)</div>
                    <div class="clear"></div>
                </div>
                <div class="total-row grand-total">
                    <div class="total-label">Total Dibayar:</div>
                    <div class="total-value">Rp <?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.') ?></div>
                    <div class="clear"></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="notes">
            <div class="section-title">Informasi Pembayaran</div>
            <div><strong>Metode Pembayaran:</strong> <?= $pembayaran['metode_pembayaran'] ?></div>
            <div><strong>Tanggal Pembayaran:</strong> <?= date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?></div>
            <div><strong>Status Pembayaran:</strong> <?= ucfirst($pembayaran['status_pembayaran']) ?></div>
        </div>

        <div class="footer">
            <p>Terima kasih telah mempercayakan perjalanan Anda bersama Elang Lembah Tourism.</p>
            <p>&copy; <?= date('Y') ?> Elang Lembah Tourism</p>
        </div>
    </div>
</body>

</html>
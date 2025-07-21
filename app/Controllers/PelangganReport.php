<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class PelangganReport extends Controller
{
    public function index()
    {
        return $this->response->setContentType('text/html')
            ->setBody('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Pelanggan</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #333; }
                    </style>
                </head>
                <body>
                    <h1>Laporan Pelanggan</h1>
                    <p>Halaman ini berhasil ditampilkan dari controller sederhana.</p>
                    <p>PelangganReport controller tanpa ketergantungan pada model.</p>
                </body>
                </html>
            ');
    }
}

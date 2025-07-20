<?php

// Script ini dirancang untuk dijalankan oleh cronjob setiap menit untuk memeriksa pembayaran yang kedaluwarsa

// Hanya jalankan dari CLI
if (php_sapi_name() !== 'cli') {
    exit('Hanya dapat dijalankan dari command line.');
}

// Path ke direktori root project
$rootPath = __DIR__;

// Load CodeIgniter environment
require $rootPath . '/vendor/autoload.php';

// Get environment variables
$env = file_exists('.env') ? parse_ini_file('.env') : [];
$token = isset($env['EXPIRED_CHECK_TOKEN']) ? $env['EXPIRED_CHECK_TOKEN'] : 'elanglembahsecret123';
$host = isset($env['APP_HOST']) ? $env['APP_HOST'] : 'localhost';
$port = isset($env['APP_PORT']) ? $env['APP_PORT'] : '8080';

// URL untuk memeriksa pembayaran kedaluwarsa
$url = "http://{$host}:{$port}/api/check-expired-payments?token={$token}";

echo "[" . date('Y-m-d H:i:s') . "] Memeriksa pembayaran kedaluwarsa...\n";

// Tentukan metode HTTP request berdasarkan OS
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

if ($isWindows && !extension_loaded('curl')) {
    // Gunakan PowerShell untuk Windows jika curl extension tidak tersedia
    $cmd = "powershell -command \"Invoke-RestMethod -Uri '{$url}' -Method Get\"";
    $response = shell_exec($cmd);
    $httpCode = ($response !== null) ? 200 : 500;
} else {
    // Gunakan curl untuk sistem lain atau Windows dengan curl extension
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
}

if ($httpCode == 200) {
    $result = json_decode($response, true);

    if (isset($result['cancelled_count'])) {
        if ($result['cancelled_count'] > 0) {
            echo "[" . date('Y-m-d H:i:s') . "] Berhasil membatalkan {$result['cancelled_count']} pemesanan kedaluwarsa.\n";

            if (!empty($result['details'])) {
                echo "Detail pemesanan yang dibatalkan:\n";
                foreach ($result['details'] as $detail) {
                    echo "- Kode booking: {$detail['kode_booking']}\n";
                }
            }
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] Tidak ada pemesanan kedaluwarsa.\n";
        }
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] Respons tidak valid: " . substr($response, 0, 100) . "...\n";
    }
} else {
    echo "[" . date('Y-m-d H:i:s') . "] Error saat memeriksa pemesanan kedaluwarsa: HTTP code {$httpCode}\n";
    if (isset($error) && $error) {
        echo "Error: {$error}\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Selesai.\n";

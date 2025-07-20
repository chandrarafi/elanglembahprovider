<?php

// Script ini menguji sistem pembayaran kedaluwarsa dengan membuat pemesanan dummy
// dan langsung mengatur expired_at ke waktu yang sudah lewat

require __DIR__ . '/vendor/autoload.php';

// Pastikan hanya berjalan dari CLI
if (php_sapi_name() !== 'cli') {
    die('Script ini hanya dapat dijalankan dari command line.');
}

// Set token secara manual untuk keamanan
$token = 'elanglembahsecret123';
$host = 'localhost';
$port = '8080';

// URL untuk memeriksa pembayaran kedaluwarsa
$url = "http://{$host}:{$port}/api/check-expired-payments?token={$token}";

echo "[" . date('Y-m-d H:i:s') . "] Testing expired payments system...\n";
echo "Making request to: {$url}\n";

// Tentukan metode HTTP request berdasarkan OS
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

if ($isWindows && !extension_loaded('curl')) {
    // Gunakan PowerShell untuk Windows jika curl extension tidak tersedia
    $cmd = "powershell -command \"Invoke-RestMethod -Uri '{$url}' -Method Get\"";
    echo "Running command: {$cmd}\n";
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
    echo "\nResponse: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";

    if (isset($result['cancelled_count']) && $result['cancelled_count'] > 0) {
        echo "Success! Cancelled " . $result['cancelled_count'] . " expired bookings.\n";

        if (!empty($result['details'])) {
            echo "Details of cancelled bookings:\n";
            foreach ($result['details'] as $detail) {
                echo "- Booking code: " . ($detail['kode_booking'] ?? 'N/A') . "\n";
            }
        }
    } else {
        echo "No expired bookings found.\n";
    }
} else {
    echo "Error: HTTP code {$httpCode}\n";
    echo "Response: {$response}\n";

    if (isset($error) && !empty($error)) {
        echo "Curl error: {$error}\n";
    }
}

echo "Done.\n";

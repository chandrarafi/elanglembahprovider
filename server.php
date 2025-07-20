<?php

require __DIR__ . '/vendor/autoload.php';

// Load CodeIgniter environment variables and helpers
$env = file_exists('.env') ? parse_ini_file('.env') : [];
foreach ($env as $key => $val) {
    putenv("$key=$val");
}

// Set base path for later use
define('BASEPATH', __DIR__);

// Import required classes
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\PaymentServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;

// Create event loop
$loop = Loop::get();

// Add periodic task to check for expired payments (every 60 seconds)
$loop->addPeriodicTimer(60, function () {
    echo "[" . date('Y-m-d H:i:s') . "] Running background check for expired payments\n";

    // Generate security token
    $token = getenv('EXPIRED_CHECK_TOKEN') ?: 'elanglembahsecret123';

    // Host and port from environment or defaults
    $host = getenv('APP_HOST') ?: 'localhost';
    $port = getenv('APP_PORT') ?: '8080';

    // URL to check expired payments
    $url = "http://{$host}:{$port}/api/check-expired-payments?token={$token}";

    // Execute HTTP request using curl
    $cmd = "curl -s '{$url}'";

    // Untuk Windows, gunakan powershell untuk menjalankan curl
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cmd = "powershell -command \"Invoke-RestMethod -Uri '{$url}' -Method Get\"";
    }

    // Jalankan command
    $result = shell_exec($cmd);

    try {
        $data = json_decode($result, true);

        if ($data && isset($data['cancelled_count'])) {
            if ($data['cancelled_count'] > 0) {
                echo "[" . date('Y-m-d H:i:s') . "] Successfully cancelled {$data['cancelled_count']} expired bookings\n";

                if (!empty($data['details'])) {
                    foreach ($data['details'] as $detail) {
                        echo " - Cancelled booking: {$detail['kode_booking']}\n";
                    }
                }
            } else {
                echo "[" . date('Y-m-d H:i:s') . "] No expired bookings found\n";
            }
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] Invalid response from server: " . substr($result, 0, 100) . "...\n";
        }
    } catch (Exception $e) {
        echo "[" . date('Y-m-d H:i:s') . "] Error processing response: " . $e->getMessage() . "\n";
    }
});

// Set up WebSocket server
echo "[" . date('Y-m-d H:i:s') . "] Starting Payment WebSocket server...\n";

// WebSocket server host and port
$wsHost = getenv('WS_HOST') ?: '0.0.0.0';
$wsPort = getenv('WS_PORT') ?: '8090';

// Create socket and server
$socket = new SocketServer("$wsHost:$wsPort", [], $loop);
$server = new IoServer(
    new HttpServer(
        new WsServer(
            new PaymentServer()
        )
    ),
    $socket
);

echo "[" . date('Y-m-d H:i:s') . "] Payment WebSocket server started on $wsHost:$wsPort\n";

// Run the loop
$loop->run();

<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class PaymentServer implements MessageComponentInterface
{
    protected $clients;
    protected $timers = [];
    protected $globalCheckTimer;
    protected $baseUrl;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;

        // Set base URL for API calls - adjust this based on your server configuration
        $this->baseUrl = getenv('APP_URL') ?: 'http://localhost:8080';

        echo "Payment WebSocket Server started!\n";

        // Start global periodic check for expired payments
        $this->setupGlobalExpirationCheck();
    }

    /**
     * Setup periodic global check for expired payments
     */
    protected function setupGlobalExpirationCheck()
    {
        // Check every 30 seconds for expired payments
        $this->globalCheckTimer = \React\EventLoop\Loop::addPeriodicTimer(30, function () {
            echo "Running global payment expiration check...\n";

            // Generate security token
            $token = getenv('EXPIRED_CHECK_TOKEN') ?: 'elanglembahsecret123';

            // URL to check expired payments
            $url = "{$this->baseUrl}/api/check-expired-payments?token={$token}";

            // Gunakan curl untuk HTTP request daripada React\Http\Browser
            $this->makeHttpRequest($url, function ($response) {
                $result = json_decode($response, true);

                if (isset($result['cancelled_count']) && $result['cancelled_count'] > 0) {
                    echo "Auto-cancelled {$result['cancelled_count']} expired bookings\n";

                    // Notify all connected clients about updated status
                    if (!empty($result['details'])) {
                        foreach ($this->clients as $client) {
                            // If client is connected to a specific booking that was cancelled
                            foreach ($result['details'] as $detail) {
                                if (isset($client->bookingId) && $client->bookingId == $detail['booking_id']) {
                                    $client->send(json_encode([
                                        'type' => 'expired',
                                        'message' => 'Pembayaran kedaluwarsa'
                                    ]));

                                    // Cancel any existing timer for this client
                                    if (isset($this->timers[$client->resourceId])) {
                                        \React\EventLoop\Loop::cancelTimer($this->timers[$client->resourceId]);
                                        unset($this->timers[$client->resourceId]);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    echo "No expired bookings found\n";
                }
            }, function ($error) {
                echo "Error checking expired payments: {$error}\n";
            });
        });
    }

    /**
     * Helper method to make HTTP requests using curl
     */
    protected function makeHttpRequest($url, callable $onSuccess, callable $onError)
    {
        // Buat fork process untuk menjalankan curl request
        $cmd = "curl -s '{$url}'";
        $descriptorspec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"],  // stderr
        ];

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // Tutup stdin
            fclose($pipes[0]);

            // Baca stdout
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            // Baca stderr
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // Tutup process
            $return_value = proc_close($process);

            if ($return_value === 0 && !empty($output)) {
                $onSuccess($output);
            } else {
                $onError($error ?: "HTTP request failed with code {$return_value}");
            }
        } else {
            $onError("Failed to create process");
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Received message from client {$from->resourceId}\n";
        try {
            $data = json_decode($msg, true);

            if (!$data) {
                echo "Failed to parse JSON message: {$msg}\n";
                return;
            }

            echo "Message type: " . ($data['type'] ?? 'unknown') . "\n";

            if (isset($data['type']) && $data['type'] === 'init') {
                // Store the payment ID with the connection
                $from->paymentId = $data['paymentId'] ?? null;
                $from->bookingId = $data['bookingId'] ?? null;
                echo "Client initialized with Booking ID: {$from->bookingId}\n";

                // First check if the booking is already expired
                if (isset($from->bookingId) && !empty($from->bookingId)) {
                    $this->checkBookingStatus($from);
                }

                // Check if remainingSeconds is provided directly by the client
                if (isset($data['remainingSeconds']) && is_numeric($data['remainingSeconds']) && $data['remainingSeconds'] > 0) {
                    $timeLeft = intval($data['remainingSeconds']);
                    echo "Using remaining seconds from client: {$timeLeft} seconds\n";
                    $this->startCountdown($from, $timeLeft);
                    return;
                }

                // Set expiration time if available
                if (isset($data['expiration'])) {
                    echo "Expiration time received: {$data['expiration']}\n";

                    $expiration = new \DateTime($data['expiration']);
                    $now = new \DateTime();
                    $timeLeft = max(0, $expiration->getTimestamp() - $now->getTimestamp());

                    echo "Calculated time left: {$timeLeft} seconds\n";

                    if ($timeLeft > 0) {
                        // Create timer for this connection
                        $this->startCountdown($from, $timeLeft);
                        echo "Timer started for client {$from->resourceId}\n";
                    } else {
                        // Payment already expired
                        echo "Payment expired for client {$from->resourceId}\n";
                        $from->send(json_encode([
                            'type' => 'expired',
                            'message' => 'Pembayaran sudah kedaluwarsa'
                        ]));
                    }
                } else {
                    echo "No expiration time provided, using default 10 minutes\n";
                    $this->startCountdown($from, 600); // Default to 10 minutes
                }
            }
        } catch (\Exception $e) {
            echo "Error processing message: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Check booking status through API
     */
    protected function checkBookingStatus(ConnectionInterface $conn)
    {
        if (!isset($conn->bookingId)) return;

        $bookingId = $conn->bookingId;
        $url = "{$this->baseUrl}/booking/checkPaymentExpiration/{$bookingId}";

        $this->makeHttpRequest($url, function ($response) use ($conn) {
            $result = json_decode($response, true);

            // Check if booking is expired or cancelled
            if (isset($result['status']) && $result['expired'] === true) {
                // Notify client
                $conn->send(json_encode([
                    'type' => 'expired',
                    'message' => 'Pembayaran kedaluwarsa',
                    'status' => $result['booking_status'] ?? 'cancelled'
                ]));

                // Cancel any timer
                if (isset($this->timers[$conn->resourceId])) {
                    \React\EventLoop\Loop::cancelTimer($this->timers[$conn->resourceId]);
                    unset($this->timers[$conn->resourceId]);
                }
            }
        }, function ($error) {
            echo "Error checking booking status: {$error}\n";
        });
    }

    protected function startCountdown(ConnectionInterface $conn, $seconds)
    {
        // Store the initial seconds for progress calculation
        $conn->initialSeconds = $seconds;
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        echo "Starting countdown for client {$conn->resourceId}: {$minutes}:{$remainingSeconds}\n";

        // Send initial time
        $conn->send(json_encode([
            'type' => 'countdown',
            'timeLeft' => $seconds,
            'minutes' => $minutes,
            'seconds' => $remainingSeconds,
            'percentage' => 100
        ]));

        // Create a timer that ticks every second
        $this->timers[$conn->resourceId] = \React\EventLoop\Loop::addPeriodicTimer(1, function () use ($conn, &$seconds) {
            $timeLeft = $seconds - 1;
            $minutes = floor($timeLeft / 60);
            $remainingSeconds = $timeLeft % 60;

            if ($timeLeft <= 0) {
                // Time's up!
                echo "Timer expired for client {$conn->resourceId}\n";

                // Try to update booking status in database
                try {
                    if (isset($conn->bookingId) && !empty($conn->bookingId)) {
                        // Make a request to your server API to update the booking status
                        $bookingId = $conn->bookingId;

                        echo "Sending status update request for booking {$bookingId}\n";

                        // Generate security token
                        $token = getenv('EXPIRED_CHECK_TOKEN') ?: 'elanglembahsecret123';

                        // Use curl untuk HTTP request daripada React\Http\Browser
                        $url = "{$this->baseUrl}/api/check-expired-payments?token={$token}";

                        $this->makeHttpRequest($url, function ($response) use ($bookingId) {
                            $result = json_decode($response, true);
                            echo "API response for expired booking {$bookingId}: " . json_encode($result) . "\n";
                        }, function ($error) {
                            echo "Error making API call: {$error}\n";
                        });
                    }
                } catch (\Exception $e) {
                    echo "Error updating booking status: " . $e->getMessage() . "\n";
                }

                // Send expired message to client
                $conn->send(json_encode([
                    'type' => 'expired',
                    'message' => 'Pembayaran kedaluwarsa'
                ]));

                // Cancel the timer
                \React\EventLoop\Loop::cancelTimer($this->timers[$conn->resourceId]);
                unset($this->timers[$conn->resourceId]);
            } else {
                // Calculate percentage remaining
                $percentage = ($timeLeft / $conn->initialSeconds) * 100;

                // Send updated time
                $conn->send(json_encode([
                    'type' => 'countdown',
                    'timeLeft' => $timeLeft,
                    'minutes' => $minutes,
                    'seconds' => $remainingSeconds,
                    'percentage' => $percentage
                ]));

                // Update the timer variable for the next iteration
                $seconds = $timeLeft;
            }
        });
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Clean up any timers
        if (isset($this->timers[$conn->resourceId])) {
            echo "Canceling timer for client {$conn->resourceId}\n";
            \React\EventLoop\Loop::cancelTimer($this->timers[$conn->resourceId]);
            unset($this->timers[$conn->resourceId]);
        }

        // Remove the connection
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

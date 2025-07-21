<?php
echo "Testing Pelanggan controller access...";

// Initialize CodeIgniter
require 'vendor/autoload.php';
$app = Config\Services::codeigniter();
$app->initialize();

// Create a mock request to test the controller
$request = \Config\Services::request();
$response = \Config\Services::response();

// Create an instance of the Pelanggan controller
$controller = new \App\Controllers\Pelanggan($request, $response);

try {
    // Call the report method
    echo "<p>Testing report method...</p>";
    $result = $controller->report();
    echo "<p>Success! Controller method returned a response.</p>";

    // Check if the controller is returning a response object
    if ($result instanceof \CodeIgniter\HTTP\ResponseInterface) {
        echo "<p>Returned a valid Response object.</p>";
    } else {
        echo "<p>Warning: Did not return a Response object.</p>";
    }
} catch (\Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
}

echo "<p>Test complete.</p>";

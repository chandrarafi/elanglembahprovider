<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\PaymentServer;

class WebSocketServer extends BaseCommand
{
    protected $group = 'WebSocket';
    protected $name = 'websocket:serve';
    protected $description = 'Start WebSocket server for real-time payment countdown';

    public function run(array $params)
    {
        // Composer autoload to include Ratchet library
        require ROOTPATH . 'vendor/autoload.php';

        CLI::write('Starting WebSocket Server...', 'green');
        CLI::write('Press Ctrl+C to stop the server', 'yellow');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new PaymentServer()
                )
            ),
            8080 // Port
        );

        CLI::write('WebSocket Server started on port 8080', 'green');
        $server->run();
    }
}

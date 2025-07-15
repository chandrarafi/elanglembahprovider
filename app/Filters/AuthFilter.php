<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri = $request->getUri()->getPath();

        // Jika belum login dan mencoba mengakses halaman yang butuh auth
        if (!$session->get('logged_in')) {
            // Simpan URL yang dicoba diakses untuk redirect setelah login
            $session->set('redirect_url', current_url());
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        // Jika sudah login dan mencoba mengakses halaman auth
        if ($session->get('logged_in') && strpos($uri, 'auth') === 0) {
            // Redirect ke halaman yang sesuai dengan role
            switch ($session->get('role')) {
                case 'admin':
                    return redirect()->to('/admin');
                case 'direktur':
                    return redirect()->to('/laporan');
                case 'pelanggan':
                    return redirect()->to('/');
                default:
                    return redirect()->to('/');
            }
        }

        // Jika mencoba mengakses halaman admin tapi bukan admin
        if (strpos($uri, 'admin') === 0 && $session->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }

        // Jika mencoba mengakses halaman laporan tapi bukan direktur atau admin
        if (strpos($uri, 'laporan') === 0 && !in_array($session->get('role'), ['direktur', 'admin'])) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

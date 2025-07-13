<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('auth');
        }

        // Cek role
        $userRole = session()->get('role');

        // Jika tidak ada role yang diperlukan, izinkan akses
        if (empty($arguments)) {
            return;
        }

        // Jika role user tidak sesuai dengan yang diizinkan
        if (!in_array($userRole, $arguments)) {
            // Jika mencoba akses admin, redirect ke home dengan pesan error
            $uri = $request->getUri()->getPath();
            if (strpos($uri, 'admin') === 0) {
                return redirect()->to('home')->with('error', 'Anda tidak memiliki akses ke halaman admin');
            }

            // Untuk halaman lain, redirect back dengan pesan error
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

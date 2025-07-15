<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class About extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Tentang Kami - Elang Lembah Travel',
            'is_logged_in' => session()->get('logged_in') ?? false,
            'user' => [
                'name' => session()->get('name') ?? '',
                'role' => session()->get('role') ?? ''
            ]
        ];

        return view('about/index', $data);
    }
}

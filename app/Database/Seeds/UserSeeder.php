<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'email' => 'admin@elanglembah.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'name' => 'Administrator',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'direktur',
                'email' => 'direktur@elanglembah.com',
                'password' => password_hash('direktur123', PASSWORD_DEFAULT),
                'name' => 'Direktur',
                'role' => 'direktur',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'pelanggan',
                'email' => 'pelanggan@example.com',
                'password' => password_hash('pelanggan123', PASSWORD_DEFAULT),
                'name' => 'Pelanggan',
                'role' => 'pelanggan',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Using Query Builder
        $this->db->table('users')->insertBatch($data);

        // Output info
        echo "Seeder: User berhasil ditambahkan!\n";
        echo "----------------------------------------\n";
        echo "Daftar akun yang tersedia:\n";
        echo "1. Admin\n";
        echo "   Username: admin\n";
        echo "   Password: admin123\n";
        echo "2. Direktur\n";
        echo "   Username: direktur\n";
        echo "   Password: direktur123\n";
        echo "3. Pelanggan\n";
        echo "   Username: pelanggan\n";
        echo "   Password: pelanggan123\n";
        echo "----------------------------------------\n";
    }
}

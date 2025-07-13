<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'idpelanggan' => 'PLG001',
                'namapelanggan' => 'Budi Santoso',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta Selatan',
                'nohp' => '081234567890',
                'iduser' => 3, // ID user dengan role pelanggan
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpelanggan' => 'PLG002',
                'namapelanggan' => 'Siti Rahayu',
                'alamat' => 'Jl. Pahlawan No. 45, Bandung',
                'nohp' => '082345678901',
                'iduser' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpelanggan' => 'PLG003',
                'namapelanggan' => 'Ahmad Hidayat',
                'alamat' => 'Jl. Diponegoro No. 78, Surabaya',
                'nohp' => '083456789012',
                'iduser' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpelanggan' => 'PLG004',
                'namapelanggan' => 'Dewi Lestari',
                'alamat' => 'Jl. Sudirman No. 56, Semarang',
                'nohp' => '084567890123',
                'iduser' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpelanggan' => 'PLG005',
                'namapelanggan' => 'Eko Prasetyo',
                'alamat' => 'Jl. Gatot Subroto No. 89, Yogyakarta',
                'nohp' => '085678901234',
                'iduser' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Insert data to table
        $this->db->table('pelanggan')->insertBatch($data);
    }
}

<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'idkategori' => 'KTGR001',
                'namakategori' => 'Wisata Alam',
                'status' => 'active',
                'foto' => 'wisata-alam.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idkategori' => 'KTGR002',
                'namakategori' => 'Wisata Pantai',
                'status' => 'active',
                'foto' => 'wisata-pantai.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idkategori' => 'KTGR003',
                'namakategori' => 'Wisata Gunung',
                'status' => 'active',
                'foto' => 'wisata-gunung.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idkategori' => 'KTGR004',
                'namakategori' => 'Wisata Budaya',
                'status' => 'active',
                'foto' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idkategori' => 'KTGR005',
                'namakategori' => 'Wisata Kuliner',
                'status' => 'active',
                'foto' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idkategori' => 'KTGR006',
                'namakategori' => 'Wisata Religi',
                'status' => 'inactive',
                'foto' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Insert data to table
        $this->db->table('kategori')->insertBatch($data);
    }
}

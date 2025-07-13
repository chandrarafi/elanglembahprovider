<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PantaiSeeder extends Seeder
{
    public function run()
    {
        // Data paket wisata pantai
        $data = [
            [
                'idpaket' => 'PKT009',
                'namapaket' => 'Paket Wisata Pantai Kuta Premium',
                'deskripsi' => 'Nikmati keindahan Pantai Kuta dengan paket premium yang mencakup akomodasi hotel bintang 5, makan 3 kali sehari, transportasi VIP, dan akses ke berbagai fasilitas pantai eksklusif. Cocok untuk honeymoon atau liburan keluarga.',
                'harga' => 3500000,
                'statuspaket' => 'active',
                'foto' => 'pantai-kuta.jpg',
                'idkategori' => 'KTGR002',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT010',
                'namapaket' => 'Paket Wisata Pantai Pink Lombok',
                'deskripsi' => 'Jelajahi keindahan Pantai Pink di Lombok yang terkenal dengan pasirnya yang berwarna merah muda. Paket termasuk transportasi, penginapan, dan pemandu wisata selama 2 hari 1 malam.',
                'harga' => 1800000,
                'statuspaket' => 'active',
                'foto' => 'pantai-pink.jpg',
                'idkategori' => 'KTGR002',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT011',
                'namapaket' => 'Paket Wisata Gili Trawangan',
                'deskripsi' => 'Nikmati keindahan pulau Gili Trawangan dengan aktivitas snorkeling, diving, dan bersepeda mengelilingi pulau. Paket termasuk penginapan, transportasi, dan beberapa aktivitas air.',
                'harga' => 2200000,
                'statuspaket' => 'active',
                'foto' => 'pantai-gili.jpg',
                'idkategori' => 'KTGR002',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Insert data to table
        $this->db->table('paket_wisata')->insertBatch($data);
    }
}

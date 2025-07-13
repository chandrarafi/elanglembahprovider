<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaketWisataSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'idpaket' => 'PKT001',
                'namapaket' => 'Paket Wisata Bali 3 Hari 2 Malam',
                'deskripsi' => 'Nikmati keindahan Pulau Bali dengan paket wisata 3 hari 2 malam. Kunjungi Pantai Kuta, Tanah Lot, dan Ubud. Termasuk akomodasi hotel bintang 4, transportasi, dan makan 3 kali sehari.',
                'harga' => 2500000,
                'statuspaket' => 'active',
                'foto' => 'bali-package.jpg',
                'idkategori' => 'KTGR002',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT002',
                'namapaket' => 'Paket Pendakian Gunung Bromo',
                'deskripsi' => 'Paket pendakian Gunung Bromo untuk menikmati keindahan sunrise. Termasuk transportasi, pemandu wisata, dan penginapan di area Bromo.',
                'harga' => 1800000,
                'statuspaket' => 'active',
                'foto' => 'bromo-package.jpg',
                'idkategori' => 'KTGR003',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT003',
                'namapaket' => 'Wisata Budaya Yogyakarta',
                'deskripsi' => 'Jelajahi kekayaan budaya Yogyakarta dengan mengunjungi Keraton, Candi Prambanan, dan Malioboro. Termasuk transportasi, pemandu wisata, dan akomodasi hotel.',
                'harga' => 1500000,
                'statuspaket' => 'active',
                'foto' => null,
                'idkategori' => 'KTGR004',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT004',
                'namapaket' => 'Paket Kuliner Jakarta',
                'deskripsi' => 'Nikmati berbagai kuliner khas Jakarta dari Sate Padang, Kerak Telor, hingga kuliner modern di kawasan Jakarta. Termasuk transportasi dan pemandu wisata kuliner.',
                'harga' => 1200000,
                'statuspaket' => 'active',
                'foto' => null,
                'idkategori' => 'KTGR005',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT005',
                'namapaket' => 'Wisata Alam Taman Nasional Ujung Kulon',
                'deskripsi' => 'Jelajahi keindahan alam Taman Nasional Ujung Kulon, habitat badak bercula satu. Termasuk transportasi, pemandu wisata, dan perlengkapan camping.',
                'harga' => 2800000,
                'statuspaket' => 'active',
                'foto' => null,
                'idkategori' => 'KTGR001',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT006',
                'namapaket' => 'Wisata Religi Walisongo',
                'deskripsi' => 'Kunjungi makam para Wali Songo di Jawa dengan paket wisata religi lengkap. Termasuk transportasi, pemandu wisata, dan penginapan.',
                'harga' => 1600000,
                'statuspaket' => 'inactive',
                'foto' => null,
                'idkategori' => 'KTGR006',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT007',
                'namapaket' => 'Paket Wisata Lombok 4 Hari 3 Malam',
                'deskripsi' => 'Jelajahi keindahan Pulau Lombok dengan mengunjungi Pantai Kuta Lombok, Gili Trawangan, dan Air Terjun Sendang Gile. Termasuk akomodasi, transportasi, dan makan 3 kali sehari.',
                'harga' => 3200000,
                'statuspaket' => 'active',
                'foto' => 'lombok-package.jpg',
                'idkategori' => 'KTGR002',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'idpaket' => 'PKT008',
                'namapaket' => 'Pendakian Gunung Rinjani',
                'deskripsi' => 'Paket pendakian Gunung Rinjani selama 3 hari 2 malam. Termasuk pemandu, porter, perlengkapan camping, dan makanan selama pendakian.',
                'harga' => 2200000,
                'statuspaket' => 'active',
                'foto' => 'rinjani-package.jpg',
                'idkategori' => 'KTGR003',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Insert data to table
        $this->db->table('paket_wisata')->insertBatch($data);
    }
}

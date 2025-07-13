<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Truncate the tables first to ensure clean data
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->db->table('users')->truncate();
        $this->db->table('kategori')->truncate();
        $this->db->table('paket_wisata')->truncate();
        $this->db->table('pelanggan')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        // Run seeders
        $this->call('UserSeeder');
        $this->call('KategoriSeeder');
        $this->call('PaketWisataSeeder');
        $this->call('PantaiSeeder');
        $this->call('PelangganSeeder');
    }
}

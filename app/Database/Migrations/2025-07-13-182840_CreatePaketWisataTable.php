<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaketWisataTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idpaket' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'namapaket' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'deskripsi' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0,
            ],
            'statuspaket' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'null'       => false,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'idkategori' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('idpaket', true);
        $this->forge->addForeignKey('idkategori', 'kategori', 'idkategori', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('paket_wisata');
    }

    public function down()
    {
        $this->forge->dropTable('paket_wisata');
    }
}

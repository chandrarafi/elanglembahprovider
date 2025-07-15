<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemesananTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idpesan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_booking' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'tanggal' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'iduser' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'idpaket' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
            'tgl_berangkat' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
            'tgl_kembali' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
            'jumlah_peserta' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => false,
                'default'    => 1,
            ],
            'totalbiaya' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
            'catatan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'confirmed', 'paid', 'completed', 'cancelled'],
                'default'    => 'pending',
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

        $this->forge->addKey('idpesan', true);
        $this->forge->addForeignKey('iduser', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('idpaket', 'paket_wisata', 'idpaket', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('pemesanan');
    }

    public function down()
    {
        $this->forge->dropTable('pemesanan');
    }
}

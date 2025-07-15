<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveJumlahPesertaFromPemesanan extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pemesanan', 'jumlah_peserta');
        $this->forge->dropColumn('pemesanan', 'tgl_kembali');
    }

    public function down()
    {
        $this->forge->addColumn('pemesanan', [
            'jumlah_peserta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'tgl_kembali' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
        ]);
    }
}

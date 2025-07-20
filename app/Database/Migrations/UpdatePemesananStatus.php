<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePemesananStatus extends Migration
{
    public function up()
    {
        // Tambahkan nilai down_payment dan waiting_confirmation ke enum status pada tabel pemesanan
        $this->forge->modifyColumn('pemesanan', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'down_payment', 'waiting_confirmation', 'confirmed', 'paid', 'completed', 'cancelled'],
                'default'    => 'pending',
                'null'       => false,
            ]
        ]);

        // Memastikan nilai tipe_pembayaran pada pembayaran sudah benar
        $this->forge->modifyColumn('pembayaran', [
            'tipe_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['dp', 'lunas'],
                'default'    => 'lunas',
                'null'       => false,
            ]
        ]);
    }

    public function down()
    {
        // Kembalikan ke nilai awal
        $this->forge->modifyColumn('pemesanan', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'confirmed', 'paid', 'completed', 'cancelled'],
                'default'    => 'pending',
                'null'       => false,
            ]
        ]);
    }
}

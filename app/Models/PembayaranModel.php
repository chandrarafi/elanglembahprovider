<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'idbayar';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idpesan',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_pembayaran',
        'tipe_pembayaran',
        'bukti_bayar',
        'status_pembayaran',
        'keterangan',
        'expired_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'idpesan'            => 'required|numeric',
        'tanggal_bayar'      => 'required',
        'jumlah_bayar'       => 'required|numeric',
        'metode_pembayaran'  => 'required',
        'tipe_pembayaran'    => 'required|in_list[dp,lunas]',
        'status_pembayaran'  => 'required|in_list[pending,verified,rejected]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Fungsi untuk mendapatkan pembayaran dengan detail pemesanan
    public function getPembayaranWithDetails($id = null)
    {
        $builder = $this->db->table('pembayaran pb');
        $builder->select('pb.*, p.kode_booking, p.totalbiaya, pw.namapaket, u.name as nama_user');
        $builder->join('pemesanan p', 'pb.idpesan = p.idpesan');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->join('users u', 'p.iduser = u.id');

        if ($id !== null) {
            $builder->where('pb.idbayar', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    // Fungsi untuk mendapatkan pembayaran berdasarkan pemesanan
    public function getPembayaranByPemesanan($idpesan)
    {
        return $this->where('idpesan', $idpesan)
            ->orderBy('tanggal_bayar', 'DESC')
            ->findAll();
    }

    // Fungsi untuk menghitung jumlah yang perlu dibayar berdasarkan tipe pembayaran
    public function hitungJumlahBayar($totalbiaya, $tipePembayaran)
    {
        if ($tipePembayaran == 'dp') {
            return $totalbiaya * 0.5; // 50% dari total biaya
        }
        return $totalbiaya; // Pembayaran lunas (100%)
    }
}

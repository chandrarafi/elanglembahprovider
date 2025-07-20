<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananModel extends Model
{
    protected $table            = 'pemesanan';
    protected $primaryKey       = 'idpesan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idpaket',
        'iduser',
        'kode_booking',
        'tanggal',
        'harga',
        'tgl_berangkat',
        'tgl_selesai',
        'jumlah_peserta',
        'totalbiaya',
        'catatan',
        'status',
        'expired_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
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

    /**
     * Mengecek ketersediaan paket pada rentang tanggal tertentu
     * 
     * @param string $idpaket ID Paket Wisata
     * @param string $tgl_berangkat Tanggal keberangkatan (Y-m-d)
     * @param string $tgl_selesai Tanggal selesai (Y-m-d)
     * @param int $id_pemesanan_current ID pemesanan saat ini (untuk pengecualian pada edit)
     * @return bool True jika tersedia, false jika sudah dipesan
     */
    public function cekKetersediaan($idpaket, $tgl_berangkat, $tgl_selesai, $id_pemesanan_current = null)
    {
        // Cek pesanan yang tumpang tindih dengan rentang tanggal yang diminta
        // dan sudah dikonfirmasi (status bukan cancelled atau pending)
        $query = $this->db->table('pemesanan')
            ->where('idpaket', $idpaket)
            ->whereIn('status', ['confirmed', 'paid', 'waiting_confirmation'])
            ->groupStart()
            // Kasus 1: tanggal berangkat yang diminta berada dalam rentang pesanan yang ada
            ->groupStart()
            ->where('tgl_berangkat <=', $tgl_berangkat)
            ->where('tgl_selesai >=', $tgl_berangkat)
            ->groupEnd()
            // ATAU
            ->orGroupStart()
            // Kasus 2: tanggal selesai yang diminta berada dalam rentang pesanan yang ada
            ->where('tgl_berangkat <=', $tgl_selesai)
            ->where('tgl_selesai >=', $tgl_selesai)
            ->groupEnd()
            // ATAU
            ->orGroupStart()
            // Kasus 3: rentang tanggal yang diminta mencakup seluruh rentang pesanan yang ada
            ->where('tgl_berangkat >=', $tgl_berangkat)
            ->where('tgl_selesai <=', $tgl_selesai)
            ->groupEnd()
            ->groupEnd();

        // Exclude current booking if we're editing
        if ($id_pemesanan_current) {
            $query->where('idpesan !=', $id_pemesanan_current);
        }

        // Also check for any pending bookings with active payments that haven't expired
        $subQuery = $this->db->table('pemesanan p')
            ->select('p.idpesan')
            ->join('pembayaran pb', 'p.idpesan = pb.idpesan')
            ->where('p.idpaket', $idpaket)
            ->where('p.status', 'pending')
            ->where('pb.status_pembayaran', 'pending')
            ->where('pb.expired_at >', date('Y-m-d H:i:s'))
            ->groupStart()
            // Same date range checks as above
            ->groupStart()
            ->where('p.tgl_berangkat <=', $tgl_berangkat)
            ->where('p.tgl_selesai >=', $tgl_berangkat)
            ->groupEnd()
            ->orGroupStart()
            ->where('p.tgl_berangkat <=', $tgl_selesai)
            ->where('p.tgl_selesai >=', $tgl_selesai)
            ->groupEnd()
            ->orGroupStart()
            ->where('p.tgl_berangkat >=', $tgl_berangkat)
            ->where('p.tgl_selesai <=', $tgl_selesai)
            ->groupEnd()
            ->groupEnd();

        if ($id_pemesanan_current) {
            $subQuery->where('p.idpesan !=', $id_pemesanan_current);
        }

        $pendingBookings = $subQuery->get()->getResultArray();

        $result = $query->get()->getResultArray();

        // If there are either confirmed bookings or pending bookings with active payments, the date is not available
        return count($result) === 0 && count($pendingBookings) === 0;
    }

    /**
     * Mengambil pemesanan beserta data paket wisata
     * 
     * @param string $id ID Pemesanan (opsional)
     * @return array Data pemesanan dengan detail paket wisata
     */
    public function getPemesananWithPaket($id = null)
    {
        $builder = $this->db->table('pemesanan p');
        $builder->select('p.*, pw.namapaket, pw.foto, pw.deskripsi, pw.durasi, u.name, u.email, u.phone');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->join('users u', 'p.iduser = u.id');

        if ($id) {
            $builder->where('p.idpesan', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil pemesanan pengguna
     * 
     * @param int $iduser ID Pengguna
     * @return array Data pemesanan pengguna
     */
    public function getUserPemesanan($iduser)
    {
        $builder = $this->db->table('pemesanan p');
        $builder->select('p.*, pw.namapaket, pw.foto, pw.deskripsi, pw.durasi');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->where('p.iduser', $iduser);
        $builder->orderBy('p.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil data pemesanan dengan filter
     * 
     * @param array $filters Array filter (status, start_date, end_date)
     * @param int $limit Jumlah data yang diambil
     * @param int $offset Offset data
     * @param string $orderBy Kolom untuk pengurutan
     * @param string $orderDir Arah pengurutan (ASC/DESC)
     * @return array Data pemesanan sesuai filter
     */
    public function getFilteredPemesanan($filters = [], $limit = null, $offset = null, $orderBy = 'tanggal', $orderDir = 'DESC')
    {
        $builder = $this->db->table('pemesanan p');
        $builder->select('p.*, pw.namapaket, pw.foto, u.name, u.email, u.phone');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->join('users u', 'p.iduser = u.id');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('p.status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $builder->where('p.tanggal >=', $filters['start_date']);
            $builder->where('p.tanggal <=', $filters['end_date'] . ' 23:59:59');
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('p.kode_booking', $filters['search'])
                ->orLike('pw.namapaket', $filters['search'])
                ->orLike('u.name', $filters['search'])
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);

        // Apply limit and offset
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Menghitung jumlah total data pemesanan sesuai filter
     * 
     * @param array $filters Array filter (status, start_date, end_date)
     * @return int Jumlah total data
     */
    public function countFilteredPemesanan($filters = [])
    {
        $builder = $this->db->table('pemesanan p');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->join('users u', 'p.iduser = u.id');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('p.status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $builder->where('p.tanggal >=', $filters['start_date']);
            $builder->where('p.tanggal <=', $filters['end_date'] . ' 23:59:59');
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('p.kode_booking', $filters['search'])
                ->orLike('pw.namapaket', $filters['search'])
                ->orLike('u.name', $filters['search'])
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}

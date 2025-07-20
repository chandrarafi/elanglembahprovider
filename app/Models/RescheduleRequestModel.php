<?php

namespace App\Models;

use CodeIgniter\Model;

class RescheduleRequestModel extends Model
{
    protected $table = 'reschedule_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'idpesan',
        'current_tgl_berangkat',
        'requested_tgl_berangkat',
        'current_tgl_selesai',
        'requested_tgl_selesai',
        'alasan',
        'status',
        'admin_notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'idpesan' => 'required|numeric',
        'current_tgl_berangkat' => 'required|valid_date',
        'requested_tgl_berangkat' => 'required|valid_date',
        'current_tgl_selesai' => 'required|valid_date',
        'requested_tgl_selesai' => 'required|valid_date',
        'alasan' => 'required'
    ];

    /**
     * Get reschedule requests by booking ID
     */
    public function getByBookingId($idpesan)
    {
        return $this->where('idpesan', $idpesan)->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get reschedule request with booking and user data
     */
    public function getRequestWithDetails($id = null)
    {
        $builder = $this->db->table('reschedule_requests r');
        $builder->select('r.*, p.kode_booking, p.totalbiaya, p.jumlah_peserta, p.status as booking_status, u.name as user_name, u.email, u.phone, pw.namapaket, pw.harga, pw.durasi');
        $builder->join('pemesanan p', 'p.idpesan = r.idpesan');
        $builder->join('users u', 'u.id = p.iduser');
        $builder->join('paket_wisata pw', 'pw.idpaket = p.idpaket');

        if ($id !== null) {
            $builder->where('r.id', $id);
            return $builder->get()->getRowArray();
        }

        $builder->orderBy('r.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get pending reschedule requests count
     */
    public function getPendingCount()
    {
        return $this->where('status', 'pending')->countAllResults();
    }
}

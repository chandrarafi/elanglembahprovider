<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'pelanggan';
    protected $primaryKey       = 'idpelanggan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idpelanggan',
        'namapelanggan',
        'alamat',
        'nohp',
        'iduser',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'namapelanggan' => 'required|min_length[3]|max_length[100]',
        'nohp'          => 'permit_empty|min_length[10]|max_length[20]',
    ];

    protected $validationMessages   = [
        'namapelanggan' => [
            'required'   => 'Nama pelanggan harus diisi',
            'min_length' => 'Nama pelanggan minimal 3 karakter',
            'max_length' => 'Nama pelanggan maksimal 100 karakter',
        ],
        'nohp' => [
            'min_length' => 'Nomor HP minimal 10 karakter',
            'max_length' => 'Nomor HP maksimal 20 karakter',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate kode pelanggan otomatis
     * Format: PLG001, PLG002, dst
     */
    public function generateKode()
    {
        $prefix = 'PLG';
        $lastKode = $this->selectMax('idpelanggan')->first();

        if (empty($lastKode) || !isset($lastKode['idpelanggan'])) {
            return $prefix . '001';
        }

        // Extract the numeric part
        $lastId = substr($lastKode['idpelanggan'], 3);
        $nextId = intval($lastId) + 1;

        // Format with leading zeros
        return $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get pelanggan with user data
     */
    public function getPelangganWithUser($id = null)
    {
        $builder = $this->db->table('pelanggan p');
        $builder->select('p.*, u.username, u.email, u.role');
        $builder->join('users u', 'p.iduser = u.id', 'left');

        if ($id) {
            $builder->where('p.idpelanggan', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Create user account for pelanggan
     */
    public function createUserAccount($data)
    {
        $userModel = new \App\Models\UserModel();

        // Gunakan password default
        $password = '123456';

        // Generate username dari nama pelanggan
        $baseUsername = $this->generateUsername($data['namapelanggan']);
        $username = $baseUsername;

        // Cek apakah username sudah ada, jika ada tambahkan angka di belakangnya
        $counter = 1;
        while ($userModel->where('username', $username)->first()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $userData = [
            'username' => $username,
            'password' => $password, // Password default: 123456
            'email'    => $data['email'] ?? null,
            'role'     => 'pelanggan',
            'name'     => $data['namapelanggan'], // Tambahkan nama
            'status'   => 'active', // Tambahkan status
        ];

        log_message('debug', 'Data user yang akan dibuat: ' . json_encode($userData));

        // Cek lagi apakah email sudah digunakan
        $existingUser = $userModel->where('email', $userData['email'])->first();
        if ($existingUser) {
            log_message('error', 'Email sudah digunakan: ' . $userData['email']);
            throw new \mysqli_sql_exception("Duplicate entry '{$userData['email']}' for key 'users.email'");
        }

        // Insert user (tanpa skip validasi)
        $userId = $userModel->insert($userData);

        if ($userId) {
            log_message('debug', 'User berhasil dibuat dengan ID: ' . $userId);
            return [
                'user_id'   => $userId,
                'username'  => $userData['username'],
                'password'  => $userData['password'],
            ];
        } else {
            log_message('error', 'Gagal membuat user: ' . json_encode($userModel->errors()));
            throw new \RuntimeException('Gagal membuat user: ' . json_encode($userModel->errors()));
        }
    }

    /**
     * Generate username from name
     */
    private function generateUsername($name)
    {
        // Convert to lowercase and remove spaces
        $username = strtolower(str_replace(' ', '', $name));

        // Remove special characters
        $username = preg_replace('/[^a-z0-9]/', '', $username);

        return $username;
    }
}

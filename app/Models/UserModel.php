<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'name',
        'role',
        'status',
        'phone',
        'address',
        'last_login',
        'last_page_visited',
        'remember_token',
        'otp',
        'otp_expiry',
        'email_verified'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'name'     => 'required|min_length[3]|max_length[100]',
        'role'     => 'required|in_list[admin,direktur,pelanggan]',
        'status'   => 'required|in_list[active,inactive]',
        'phone'    => 'permit_empty|min_length[10]|max_length[20]',
        'address'  => 'permit_empty'
    ];

    protected $validationRulesForUpdate = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'name'     => 'required|min_length[3]|max_length[100]',
        'role'     => 'required|in_list[admin,direktur,pelanggan]',
        'status'   => 'required|in_list[active,inactive]',
        'phone'    => 'permit_empty|min_length[10]|max_length[20]',
        'address'  => 'permit_empty'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 100 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah digunakan'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'name' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'role' => [
            'required' => 'Role harus diisi',
            'in_list' => 'Role tidak valid'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status tidak valid'
        ],
        'phone' => [
            'min_length' => 'Nomor telepon minimal 10 karakter',
            'max_length' => 'Nomor telepon maksimal 20 karakter'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function findUserByCredentials($username, $password)
    {
        $user = $this->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }

        return null;
    }

    public function getRedirectURL($role, $lastPageVisited = null)
    {
        switch ($role) {
            case 'admin':
                return '/admin';
            case 'direktur':
                return '/laporan';
            case 'pelanggan':
                return $lastPageVisited ?? '/';
            default:
                return '/';
        }
    }

    public function update($id = null, $data = null): bool
    {
        if ($id === null) {
            return false;
        }

        // Jika update user, password bisa kosong
        $rules = [
            'username' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,$id]",
            'email'    => "required|valid_email|is_unique[users.email,id,$id]",
            'password' => 'permit_empty|min_length[6]',
            'name'     => 'required|min_length[3]|max_length[100]',
            'role'     => 'required|in_list[admin,direktur,pelanggan]',
            'status'   => 'required|in_list[active,inactive]',
            'phone'    => 'permit_empty|min_length[10]|max_length[20]',
            'address'  => 'permit_empty'
        ];

        // Simpan validasi rules asli
        $originalRules = $this->validationRules;

        // Set validasi rules untuk update
        $this->validationRules = $rules;

        // Jalankan update
        $result = parent::update($id, $data);

        // Kembalikan validasi rules asli
        $this->validationRules = $originalRules;

        return $result;
    }
}

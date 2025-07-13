<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Admin extends ResourceController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('admin/dashboard');
    }

    public function users()
    {
        return view('admin/users/index');
    }

    // API untuk DataTables
    public function getUsers()
    {
        $request = $this->request->getGet();
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Filter berdasarkan role dan status jika ada
        if (!empty($request['role'])) {
            $builder->where('role', $request['role']);
        }
        if (!empty($request['status'])) {
            $builder->where('status', $request['status']);
        }

        // Search
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart()
                ->like('username', $searchValue)
                ->orLike('email', $searchValue)
                ->orLike('name', $searchValue)
                ->groupEnd();
        }

        // Total records with filter
        $totalRecordsWithFilter = $builder->countAllResults(false);

        // Fetch records
        $builder->orderBy($request['columns'][$request['order'][0]['column']]['data'], $request['order'][0]['dir'])
            ->limit($request['length'], $request['start']);

        $records = $builder->get()->getResultArray();

        $response = [
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordsWithFilter,
            "data" => $records
        ];

        return $this->response->setJSON($response);
    }

    public function getRoles()
    {
        $roles = ['admin', 'direktur', 'pelanggan'];
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $roles
        ]);
    }

    public function createUser()
    {
        $data = $this->request->getPost();

        // Hapus ID jika ada (untuk memastikan ini adalah operasi insert)
        if (isset($data['id'])) {
            unset($data['id']);
        }

        try {
            // Hash password sebelum disimpan (tidak perlu lagi karena sudah ada di model)

            // Log data yang akan disimpan (tanpa password untuk keamanan)
            $logData = $data;
            if (isset($logData['password'])) {
                $logData['password'] = '******';
            }
            log_message('debug', 'Data yang akan disimpan: ' . print_r($logData, true));

            if (!$this->userModel->insert($data)) {
                // Log error dari model
                log_message('error', 'Error dari model: ' . print_r($this->userModel->errors(), true));

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->userModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan user: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan user: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function updateUser($id = null)
    {
        $data = $this->request->getPost();

        try {
            // Jika password kosong, hapus dari data
            if (empty($data['password'])) {
                unset($data['password']);
            }

            // Log data yang akan diupdate (tanpa password untuk keamanan)
            $logData = $data;
            if (isset($logData['password'])) {
                $logData['password'] = '******';
            }
            log_message('debug', 'Data yang akan diupdate: ' . print_r($logData, true));

            if (!$this->userModel->update($id, $data)) {
                // Log error dari model
                log_message('error', 'Error dari model: ' . print_r($this->userModel->errors(), true));

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->userModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate user: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteUser($id = null)
    {
        try {
            $this->userModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus user'
            ])->setStatusCode(500);
        }
    }

    public function getUser($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($id);
        if ($user) {
            // Pastikan password tidak dikirim ke client
            if (isset($user['password'])) {
                unset($user['password']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $user
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ])->setStatusCode(404);
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PelangganModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Pelanggan extends BaseController
{
    protected $pelangganModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        helper('text'); // Load text helper for random_string
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Pelanggan'
        ];
        return view('admin/pelanggan/index', $data);
    }

    public function getPelanggan()
    {
        $request = $this->request->getGet();

        // Get data with join to users table
        $pelanggan = $this->pelangganModel->getPelangganWithUser();

        // Total records
        $totalRecords = count($pelanggan);

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $filteredData = [];

            foreach ($pelanggan as $p) {
                if (
                    stripos($p['idpelanggan'], $searchValue) !== false ||
                    stripos($p['namapelanggan'], $searchValue) !== false ||
                    stripos($p['nohp'], $searchValue) !== false ||
                    stripos($p['alamat'], $searchValue) !== false ||
                    stripos($p['username'] ?? '', $searchValue) !== false
                ) {
                    $filteredData[] = $p;
                }
            }

            $pelanggan = $filteredData;
        }

        // Total records with filter
        $totalRecordsWithFilter = count($pelanggan);

        // Sort functionality
        if (!empty($request['order'])) {
            $column = $request['columns'][$request['order'][0]['column']]['data'];
            $dir = $request['order'][0]['dir'];

            usort($pelanggan, function ($a, $b) use ($column, $dir) {
                if ($dir === 'asc') {
                    return $a[$column] <=> $b[$column];
                } else {
                    return $b[$column] <=> $a[$column];
                }
            });
        }

        // Pagination
        $start = !empty($request['start']) ? intval($request['start']) : 0;
        $length = !empty($request['length']) ? intval($request['length']) : 10;

        $pelanggan = array_slice($pelanggan, $start, $length);

        $response = [
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordsWithFilter,
            "data" => $pelanggan
        ];

        return $this->response->setJSON($response);
    }

    public function createPelanggan()
    {
        $data = $this->request->getPost();

        // Log data yang diterima
        log_message('debug', 'Data pelanggan: ' . json_encode($data));

        // Generate kode pelanggan otomatis
        $data['idpelanggan'] = $this->pelangganModel->generateKode();

        // Check if create user account is requested
        $createUser = isset($data['create_user']) && $data['create_user'] == '1';
        $userData = null;

        try {
            // Start transaction
            $this->db->transBegin();

            // Validasi email jika akan membuat akun user
            if ($createUser) {
                if (empty($data['email'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Email diperlukan untuk membuat akun user',
                        'errors' => ['email' => 'Email harus diisi']
                    ])->setStatusCode(400);
                }

                // Cek apakah email sudah digunakan
                $existingUser = $this->userModel->where('email', $data['email'])->first();
                if ($existingUser) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Email sudah digunakan',
                        'errors' => ['email' => 'Email sudah digunakan oleh akun lain']
                    ])->setStatusCode(400);
                }

                log_message('debug', 'Membuat akun user dengan email: ' . $data['email']);

                try {
                    $userData = $this->pelangganModel->createUserAccount([
                        'namapelanggan' => $data['namapelanggan'],
                        'email' => $data['email']
                    ]);
                } catch (\mysqli_sql_exception $e) {
                    // Tangkap khusus error duplicate entry
                    $this->db->transRollback();
                    log_message('error', 'SQL Error saat membuat akun user: ' . $e->getMessage());

                    // Periksa apakah error adalah duplicate email
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'email') !== false) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => 'Email sudah digunakan oleh akun lain',
                            'errors' => ['email' => 'Email sudah digunakan oleh akun lain']
                        ])->setStatusCode(400);
                    }

                    // Error SQL lainnya
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Terjadi kesalahan database saat membuat akun user'
                    ])->setStatusCode(500);
                }

                log_message('debug', 'Hasil createUserAccount: ' . json_encode($userData));

                if ($userData) {
                    $data['iduser'] = $userData['user_id'];
                    log_message('debug', 'ID User yang dibuat: ' . $userData['user_id']);
                } else {
                    $this->db->transRollback();
                    log_message('error', 'Gagal membuat akun user');
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal membuat akun user'
                    ])->setStatusCode(500);
                }
            }

            // Remove non-model fields
            unset($data['create_user']);
            unset($data['email']);

            // Insert pelanggan
            if (!$this->pelangganModel->insert($data)) {
                $this->db->transRollback();
                log_message('error', 'Validasi gagal: ' . json_encode($this->pelangganModel->errors()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->pelangganModel->errors()
                ])->setStatusCode(400);
            }

            // Commit transaction
            $this->db->transCommit();

            $response = [
                'status' => 'success',
                'message' => 'Pelanggan berhasil ditambahkan'
            ];

            // Add user account info to response if created
            if ($userData) {
                $response['user_account'] = [
                    'username' => $userData['username'],
                    'password' => $userData['password']
                ];
            }

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error saat menambahkan pelanggan: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getPelangganById($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        $pelanggan = $this->pelangganModel->getPelangganWithUser($id);
        if ($pelanggan) {
            // Get email from user if exists
            if (!empty($pelanggan['iduser'])) {
                $user = $this->userModel->find($pelanggan['iduser']);
                if ($user) {
                    $pelanggan['email'] = $user['email'];
                    $pelanggan['has_account'] = true;
                }
            } else {
                $pelanggan['has_account'] = false;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $pelanggan
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Pelanggan tidak ditemukan'
        ])->setStatusCode(404);
    }

    public function updatePelanggan($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        $data = $this->request->getPost();

        // Log data yang diterima
        log_message('debug', 'Data update pelanggan: ' . json_encode($data));

        $pelanggan = $this->pelangganModel->find($id);

        if (!$pelanggan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pelanggan tidak ditemukan'
            ])->setStatusCode(404);
        }

        // Check if create user account is requested
        $createUser = isset($data['create_user']) && $data['create_user'] == '1';
        $userData = null;

        try {
            // Start transaction
            $this->db->transBegin();

            // Create user account if requested and pelanggan doesn't have one
            if ($createUser && empty($pelanggan['iduser'])) {
                if (empty($data['email'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Email diperlukan untuk membuat akun user',
                        'errors' => ['email' => 'Email harus diisi']
                    ])->setStatusCode(400);
                }

                // Cek apakah email sudah digunakan
                $existingUser = $this->userModel->where('email', $data['email'])->first();
                if ($existingUser) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Email sudah digunakan',
                        'errors' => ['email' => 'Email sudah digunakan oleh akun lain']
                    ])->setStatusCode(400);
                }

                log_message('debug', 'Membuat akun user dengan email: ' . $data['email']);

                $userData = $this->pelangganModel->createUserAccount([
                    'namapelanggan' => $data['namapelanggan'],
                    'email' => $data['email']
                ]);

                log_message('debug', 'Hasil createUserAccount: ' . json_encode($userData));

                if ($userData) {
                    $data['iduser'] = $userData['user_id'];
                    log_message('debug', 'ID User yang dibuat: ' . $userData['user_id']);
                } else {
                    $this->db->transRollback();
                    log_message('error', 'Gagal membuat akun user');
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal membuat akun user'
                    ])->setStatusCode(500);
                }
            }

            // Update email if pelanggan already has an account
            if (!empty($pelanggan['iduser']) && !empty($data['email'])) {
                // Cek apakah email sudah digunakan oleh user lain
                $existingUser = $this->userModel->where('email', $data['email'])
                    ->where('id !=', $pelanggan['iduser'])
                    ->first();

                if ($existingUser) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Email sudah digunakan',
                        'errors' => ['email' => 'Email sudah digunakan oleh akun lain']
                    ])->setStatusCode(400);
                }

                $this->userModel->update($pelanggan['iduser'], ['email' => $data['email']]);
            }

            // Remove non-model fields
            unset($data['create_user']);
            unset($data['email']);

            // Update pelanggan
            if (!$this->pelangganModel->update($id, $data)) {
                $this->db->transRollback();
                log_message('error', 'Validasi gagal: ' . json_encode($this->pelangganModel->errors()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->pelangganModel->errors()
                ])->setStatusCode(400);
            }

            // Commit transaction
            $this->db->transCommit();

            $response = [
                'status' => 'success',
                'message' => 'Pelanggan berhasil diupdate'
            ];

            // Add user account info to response if created
            if ($userData) {
                $response['user_account'] = [
                    'username' => $userData['username'],
                    'password' => $userData['password']
                ];
            }

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error saat mengupdate pelanggan: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deletePelanggan($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        try {
            // Get pelanggan data
            $pelanggan = $this->pelangganModel->find($id);
            if (!$pelanggan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Pelanggan tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Start transaction
            $this->db->transBegin();

            // Delete user account if exists
            if (!empty($pelanggan['iduser'])) {
                $this->userModel->delete($pelanggan['iduser']);
            }

            // Delete pelanggan
            $this->pelangganModel->delete($id);

            // Commit transaction
            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pelanggan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error saat menghapus pelanggan: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

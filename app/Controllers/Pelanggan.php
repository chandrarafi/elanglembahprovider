<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Pelanggan extends BaseController
{
    protected $userModel;
    protected $db;

    public function __construct()
    {
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

        // Get data from users table with role pelanggan
        $builder = $this->db->table('users');
        $builder->where('role', 'pelanggan');
        $builder->where('deleted_at', null); // Exclude deleted users
        $pelanggan = $builder->get()->getResultArray();

        // Total records
        $totalRecords = count($pelanggan);

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $filteredData = [];

            foreach ($pelanggan as $p) {
                if (
                    stripos($p['id'], $searchValue) !== false ||
                    stripos($p['name'], $searchValue) !== false ||
                    stripos($p['phone'] ?? '', $searchValue) !== false ||
                    stripos($p['address'] ?? '', $searchValue) !== false ||
                    stripos($p['username'] ?? '', $searchValue) !== false ||
                    stripos($p['email'] ?? '', $searchValue) !== false
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

        try {
            // Prepare user data
            $userData = [
                'username' => $this->generateUsername($data['name']),
                'email' => $data['email'] ?? $this->generateUsername($data['name']) . '@example.com',
                'password' => '123456', // Password default
                'name' => $data['name'],
                'role' => 'pelanggan',
                'status' => 'active',
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null
            ];

            // Cek apakah email sudah digunakan
            $existingUser = $this->userModel->where('email', $userData['email'])->first();
            if ($existingUser) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email sudah digunakan',
                    'errors' => ['email' => 'Email sudah digunakan oleh akun lain']
                ])->setStatusCode(400);
            }

            // Cek apakah username sudah ada, jika ada tambahkan angka di belakangnya
            $baseUsername = $userData['username'];
            $username = $baseUsername;
            $counter = 1;
            while ($this->userModel->where('username', $username)->first()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            $userData['username'] = $username;

            log_message('debug', 'Data user yang akan dibuat: ' . json_encode($userData));

            // Insert user
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                log_message('error', 'Validasi gagal: ' . json_encode($this->userModel->errors()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->userModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pelanggan berhasil ditambahkan',
                'user_account' => [
                    'username' => $userData['username'],
                    'password' => $userData['password']
                ]
            ]);
        } catch (\Exception $e) {
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

        $pelanggan = $this->userModel->find($id);
        if ($pelanggan) {
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

        $pelanggan = $this->userModel->find($id);

        if (!$pelanggan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pelanggan tidak ditemukan'
            ])->setStatusCode(404);
        }

        try {
            // Prepare user data for update
            $userData = [
                'name' => $data['name'] ?? $pelanggan['name'],
                'phone' => $data['phone'] ?? $pelanggan['phone'],
                'address' => $data['address'] ?? $pelanggan['address']
            ];

            // Update email if provided and different
            if (!empty($data['email']) && $data['email'] !== $pelanggan['email']) {
                // Cek apakah email sudah digunakan oleh user lain
                $existingUser = $this->userModel->where('email', $data['email'])
                    ->where('id !=', $id)
                    ->first();

                if ($existingUser) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Email sudah digunakan',
                        'errors' => ['email' => 'Email sudah digunakan oleh akun lain']
                    ])->setStatusCode(400);
                }

                $userData['email'] = $data['email'];
            }

            // Update user
            if (!$this->userModel->update($id, $userData)) {
                log_message('error', 'Validasi gagal: ' . json_encode($this->userModel->errors()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->userModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pelanggan berhasil diupdate'
            ]);
        } catch (\Exception $e) {
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
            // Soft delete user
            if (!$this->userModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus pelanggan'
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pelanggan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus pelanggan: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // Report methods
    public function report()
    {
        // Just return the simple report view with no data
        return view('admin/pelanggan/report');
    }

    public function getReport()
    {
        // Default to empty data
        $data = [];

        // Try to get data from database
        try {
            $builder = $this->db->table('users');
            $builder->where('role', 'pelanggan');
            $builder->where('deleted_at', null); // Exclude deleted users
            $pelangganList = $builder->get()->getResultArray();

            foreach ($pelangganList as $row) {
                $data[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'phone' => $row['phone'] ?? '-',
                    'address' => $row['address'] ?? '-',
                    'created_at' => $row['created_at']
                ];
            }
        } catch (\Exception $e) {
            // Log error but don't fail
            log_message('error', 'Error fetching pelanggan data: ' . $e->getMessage());
        }

        // Always return data, even if empty
        return $this->response->setJSON(['data' => $data]);
    }

    public function generateReportPDF()
    {
        // Increase execution time limit for large reports
        ini_set('max_execution_time', 300); // 5 minutes

        // Default to empty data
        $pelanggan = [];

        // Try to get data from database
        try {
            $builder = $this->db->table('users');
            $builder->where('role', 'pelanggan');
            $builder->where('deleted_at', null); // Exclude deleted users
            $pelanggan = $builder->get()->getResultArray();
        } catch (\Exception $e) {
            // Log error but continue with empty data
            log_message('error', 'Error fetching pelanggan data for PDF: ' . $e->getMessage());
        }

        $data = [
            'pelanggan' => $pelanggan,
            'tanggal' => date('d-m-Y')
        ];

        $html = view('admin/pelanggan/pdf_report', $data);

        // Create instance of dompdf
        $options = new \Dompdf\Options();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Output the generated PDF
        $dompdf->stream('laporan-pelanggan-' . date('d-m-Y') . '.pdf', ['Attachment' => false]);
        exit();
    }

    public function generateReportPrint()
    {
        // Default to empty data
        $pelanggan = [];

        // Try to get data from database
        try {
            $builder = $this->db->table('users');
            $builder->where('role', 'pelanggan');
            $builder->where('deleted_at', null); // Exclude deleted users
            $pelanggan = $builder->get()->getResultArray();
        } catch (\Exception $e) {
            // Log error but continue with empty data
            log_message('error', 'Error fetching pelanggan data for print: ' . $e->getMessage());
        }

        $data = [
            'pelanggan' => $pelanggan,
            'tanggal' => date('d-m-Y')
        ];

        return view('admin/pelanggan/print_report', $data);
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

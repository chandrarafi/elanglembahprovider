<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use CodeIgniter\HTTP\ResponseInterface;

class Kategori extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        return view('admin/kategori/index');
    }

    public function getKategori()
    {
        $request = $this->request->getGet();
        $db = \Config\Database::connect();
        $builder = $db->table('kategori');

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Filter berdasarkan status jika ada
        if (!empty($request['status'])) {
            $builder->where('status', $request['status']);
        }

        // Search
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart()
                ->like('idkategori', $searchValue)
                ->orLike('namakategori', $searchValue)
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

    public function createKategori()
    {
        $data = $this->request->getPost();

        // Generate kode kategori otomatis
        $data['idkategori'] = $this->kategoriModel->generateKode();

        try {
            // Upload foto jika ada
            $foto = $this->request->getFile('foto');

            // Cek apakah ada file yang diupload dengan cara yang lebih sederhana
            if ($foto && $foto->getSize() > 0) {
                try {
                    $data['foto'] = $this->kategoriModel->uploadFoto($foto);
                } catch (\Exception $e) {
                    log_message('error', 'Error saat upload foto: ' . $e->getMessage());
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ])->setStatusCode(500);
                }
            }

            if (!$this->kategoriModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->kategoriModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan kategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getKategoriById($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        $kategori = $this->kategoriModel->find($id);
        if ($kategori) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $kategori
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Kategori tidak ditemukan'
        ])->setStatusCode(404);
    }

    public function updateKategori($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        $data = $this->request->getPost();

        try {
            // Upload foto baru jika ada
            $foto = $this->request->getFile('foto');

            // Cek apakah ada file yang diupload dengan cara yang lebih sederhana
            if ($foto && $foto->getSize() > 0) {
                try {
                    // Hapus foto lama jika ada
                    $kategori = $this->kategoriModel->find($id);
                    if ($kategori && !empty($kategori['foto'])) {
                        $this->kategoriModel->deleteFoto($kategori['foto']);
                    }

                    // Upload foto baru
                    $data['foto'] = $this->kategoriModel->uploadFoto($foto);
                } catch (\Exception $e) {
                    log_message('error', 'Error saat upload foto: ' . $e->getMessage());
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ])->setStatusCode(500);
                }
            }

            if (!$this->kategoriModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->kategoriModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kategori berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate kategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteKategori($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        try {
            // Hapus foto jika ada
            $kategori = $this->kategoriModel->find($id);
            if ($kategori && !empty($kategori['foto'])) {
                $this->kategoriModel->deleteFoto($kategori['foto']);
            }

            // Hapus data kategori secara permanen
            $this->kategoriModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus kategori: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

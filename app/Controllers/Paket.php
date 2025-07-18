<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PaketWisataModel;
use App\Models\KategoriModel;
use CodeIgniter\HTTP\ResponseInterface;

class Paket extends BaseController
{
    protected $paketModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->paketModel = new PaketWisataModel();
        $this->kategoriModel = new KategoriModel();
    }

    // Halaman publik untuk daftar paket wisata
    public function index()
    {
        // Filter berdasarkan kategori jika ada
        $kategori_id = $this->request->getGet('kategori');
        $sort = $this->request->getGet('sort');
        $search = $this->request->getGet('search');

        $query = $this->paketModel->where('statuspaket', 'active');

        // Filter by kategori
        if ($kategori_id) {
            $query->where('idkategori', $kategori_id);
        }

        // Sort options
        if ($sort == 'price_low') {
            $query->orderBy('harga', 'ASC');
        } elseif ($sort == 'price_high') {
            $query->orderBy('harga', 'DESC');
        } elseif ($sort == 'popular') {
            $query->orderBy('RAND()'); // Asumsi untuk popular (bisa diganti jika ada field khusus)
        } else {
            $query->orderBy('created_at', 'DESC'); // Default sort by newest
        }

        // Search
        if ($search) {
            $query->like('namapaket', $search);
        }

        // Get paket wisata
        $paketList = $query->findAll();

        // Get kategori untuk setiap paket
        foreach ($paketList as $key => $paket) {
            $kategoriDetail = $this->kategoriModel->find($paket['idkategori']);
            $paketList[$key]['kategori_nama'] = $kategoriDetail['namakategori'] ?? '';
        }

        // Data untuk view
        $data = [
            'title' => 'Paket Wisata - Elang Lembah Travel',
            'paketList' => $paketList,
            'kategori' => $this->kategoriModel->where('status', 'active')->findAll(),
            'selectedKategori' => $kategori_id,
            'sort' => $sort,
            'search' => $search,
            'is_logged_in' => session()->get('logged_in') ?? false,
            'user' => [
                'name' => session()->get('name') ?? '',
                'role' => session()->get('role') ?? ''
            ]
        ];

        return view('paket/index', $data);
    }

    // Halaman detail paket wisata
    public function detail($id = null)
    {
        if (!$id) {
            return redirect()->to('/paket')->with('error', 'ID paket tidak valid');
        }

        $paket = $this->paketModel->find($id);
        if (!$paket) {
            return redirect()->to('/paket')->with('error', 'Paket wisata tidak ditemukan');
        }

        // Pastikan hanya paket aktif yang bisa diakses
        if ($paket['statuspaket'] !== 'active') {
            return redirect()->to('/paket')->with('error', 'Paket wisata tidak tersedia');
        }

        // Get kategori
        $kategori = $this->kategoriModel->find($paket['idkategori']);

        // Get paket terkait (dari kategori yang sama)
        $related_pakets = $this->paketModel
            ->where('statuspaket', 'active')
            ->where('idkategori', $paket['idkategori'])
            ->where('idpaket !=', $id) // Exclude current paket
            ->orderBy('RAND()')
            ->limit(3)
            ->find();

        $data = [
            'title' => $paket['namapaket'] . ' - Elang Lembah Travel',
            'paket' => $paket,
            'kategori' => $kategori,
            'related_pakets' => $related_pakets,
            'is_logged_in' => session()->get('logged_in') ?? false,
            'user' => [
                'name' => session()->get('name') ?? '',
                'role' => session()->get('role') ?? ''
            ]
        ];

        return view('paket/detail', $data);
    }

    // Halaman admin (yang sudah ada sebelumnya)
    public function admin()
    {
        $data = [
            'title' => 'Kelola Paket Wisata',
            'kategori' => $this->kategoriModel->where('status', 'active')->findAll()
        ];
        return view('admin/paket/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Paket Wisata',
            'kategori' => $this->kategoriModel->where('status', 'active')->findAll()
        ];
        return view('admin/paket/create', $data);
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('/admin/paket')->with('error', 'ID paket tidak valid');
        }

        $paket = $this->paketModel->find($id);
        if (!$paket) {
            return redirect()->to('/admin/paket')->with('error', 'Paket tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Paket Wisata',
            'paket' => $paket,
            'kategori' => $this->kategoriModel->where('status', 'active')->findAll()
        ];
        return view('admin/paket/edit', $data);
    }

    public function getPaket()
    {
        $request = $this->request->getGet();
        $db = \Config\Database::connect();
        $builder = $db->table('paket_wisata p');
        $builder->select('p.*, k.namakategori');
        $builder->join('kategori k', 'p.idkategori = k.idkategori');

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Filter berdasarkan status jika ada
        if (!empty($request['statuspaket'])) {
            $builder->where('p.statuspaket', $request['statuspaket']);
        }

        // Filter berdasarkan kategori jika ada
        if (!empty($request['idkategori'])) {
            $builder->where('p.idkategori', $request['idkategori']);
        }

        // Search
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart()
                ->like('p.idpaket', $searchValue)
                ->orLike('p.namapaket', $searchValue)
                ->orLike('k.namakategori', $searchValue)
                ->groupEnd();
        }

        // Total records with filter
        $totalRecordsWithFilter = $builder->countAllResults(false);

        // Fetch records
        $builder->orderBy($request['columns'][$request['order'][0]['column']]['data'], $request['order'][0]['dir'])
            ->limit($request['length'], $request['start']);

        $records = $builder->get()->getResultArray();

        // Format harga
        foreach ($records as &$record) {
            $record['harga_formatted'] = 'Rp ' . number_format($record['harga'], 0, ',', '.');
        }

        $response = [
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordsWithFilter,
            "data" => $records
        ];

        return $this->response->setJSON($response);
    }

    public function store()
    {
        $data = $this->request->getPost();

        // Generate kode paket otomatis
        $data['idpaket'] = $this->paketModel->generateKode();

        try {
            // Upload foto jika ada
            $foto = $this->request->getFile('foto');

            if ($foto && $foto->getSize() > 0) {
                try {
                    $data['foto'] = $this->paketModel->uploadFoto($foto);
                } catch (\Exception $e) {
                    log_message('error', 'Error saat upload foto: ' . $e->getMessage());
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ])->setStatusCode(500);
                }
            }

            if (!$this->paketModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->paketModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Paket wisata berhasil ditambahkan',
                'redirect' => base_url('admin/paket')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambahkan paket: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function show($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        $paket = $this->paketModel->getPaketWithKategori($id);
        if ($paket) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $paket
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Paket tidak ditemukan'
        ])->setStatusCode(404);
    }

    public function update($id = null)
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

            if ($foto && $foto->getSize() > 0) {
                try {
                    // Hapus foto lama jika ada
                    $paket = $this->paketModel->find($id);
                    if ($paket && !empty($paket['foto'])) {
                        $this->paketModel->deleteFoto($paket['foto']);
                    }

                    // Upload foto baru
                    $data['foto'] = $this->paketModel->uploadFoto($foto);
                } catch (\Exception $e) {
                    log_message('error', 'Error saat upload foto: ' . $e->getMessage());
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ])->setStatusCode(500);
                }
            }

            if (!$this->paketModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $this->paketModel->errors()
                ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Paket wisata berhasil diupdate',
                'redirect' => base_url('admin/paket')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat mengupdate paket: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }

        try {
            // Hapus foto jika ada
            $paket = $this->paketModel->find($id);
            if ($paket && !empty($paket['foto'])) {
                $this->paketModel->deleteFoto($paket['foto']);
            }

            // Hapus data paket secara permanen
            $this->paketModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Paket wisata berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus paket: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class PaketWisataModel extends Model
{
    protected $table            = 'paket_wisata';
    protected $primaryKey       = 'idpaket';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idpaket',
        'namapaket',
        'deskripsi',
        'harga',
        'statuspaket',
        'foto',
        'idkategori',
        'minimalpeserta',
        'maximalpeserta',
        'fasilitas',
        'durasi',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'namapaket'   => 'required|min_length[3]|max_length[100]',
        'deskripsi'   => 'permit_empty',
        'harga'       => 'required|numeric|greater_than[0]',
        'statuspaket' => 'required|in_list[active,inactive]',
        'idkategori'  => 'required|is_not_unique[kategori.idkategori]',
    ];

    protected $validationMessages   = [
        'namapaket' => [
            'required'   => 'Nama paket harus diisi',
            'min_length' => 'Nama paket minimal 3 karakter',
            'max_length' => 'Nama paket maksimal 100 karakter',
        ],
        'harga' => [
            'required'     => 'Harga harus diisi',
            'numeric'      => 'Harga harus berupa angka',
            'greater_than' => 'Harga harus lebih besar dari 0',
        ],
        'statuspaket' => [
            'required' => 'Status harus diisi',
            'in_list'  => 'Status tidak valid',
        ],
        'idkategori' => [
            'required'      => 'Kategori harus diisi',
            'is_not_unique' => 'Kategori tidak ditemukan',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate kode paket otomatis
     * Format: PKT001, PKT002, dst
     */
    public function generateKode()
    {
        $prefix = 'PKT';
        $lastKode = $this->selectMax('idpaket')->first();

        if (empty($lastKode) || !isset($lastKode['idpaket'])) {
            return $prefix . '001';
        }

        // Extract the numeric part
        $lastId = substr($lastKode['idpaket'], 3);
        $nextId = intval($lastId) + 1;

        // Format with leading zeros
        return $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Upload foto paket
     */
    public function uploadFoto($file)
    {
        try {
            // Validasi manual file
            if (!$file->isValid()) {
                throw new \RuntimeException('File tidak valid: ' . $file->getErrorString());
            }

            if ($file->getSize() > 2 * 1024 * 1024) { // 2MB
                throw new \RuntimeException('Ukuran file terlalu besar (maksimal 2MB)');
            }

            // Validasi ekstensi file secara manual
            $ext = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                throw new \RuntimeException('Format file tidak didukung. Gunakan JPG, JPEG, atau PNG');
            }

            // Pastikan direktori upload ada
            $uploadPath = 'uploads/paket';
            $fullPath = FCPATH . $uploadPath;

            // Buat direktori jika belum ada
            if (!is_dir($fullPath)) {
                if (!mkdir($fullPath, 0777, true)) {
                    throw new \RuntimeException('Gagal membuat direktori upload');
                }
            }

            // Gunakan nama file asli dengan timestamp untuk menghindari duplikasi
            $newName = time() . '_' . str_replace(' ', '_', $file->getName());

            // Coba pindahkan file
            if (!$file->move($fullPath, $newName)) {
                throw new \RuntimeException('Gagal memindahkan file: ' . $file->getErrorString());
            }

            return $newName;
        } catch (\Exception $e) {
            log_message('error', 'Exception during file upload: ' . $e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Delete foto paket
     */
    public function deleteFoto($filename)
    {
        $path = FCPATH . 'uploads/paket/' . $filename;
        if ($filename && file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }

    /**
     * Get paket with kategori
     */
    public function getPaketWithKategori($id = null)
    {
        $builder = $this->db->table('paket_wisata p');
        $builder->select('p.*, k.namakategori');
        $builder->join('kategori k', 'p.idkategori = k.idkategori');

        if ($id) {
            $builder->where('p.idpaket', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'idkategori';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'idkategori',
        'namakategori',
        'status',
        'foto',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'namakategori' => 'required|min_length[3]|max_length[100]',
        'status'       => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages   = [
        'namakategori' => [
            'required'   => 'Nama kategori harus diisi',
            'min_length' => 'Nama kategori minimal 3 karakter',
            'max_length' => 'Nama kategori maksimal 100 karakter',
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list'  => 'Status tidak valid',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate kode kategori otomatis
     * Format: KTGR001, KTGR002, dst
     */
    public function generateKode()
    {
        $prefix = 'KTGR';
        $lastKode = $this->selectMax('idkategori')->first();

        if (empty($lastKode) || !isset($lastKode['idkategori'])) {
            return $prefix . '001';
        }

        // Extract the numeric part
        $lastId = substr($lastKode['idkategori'], 4);
        $nextId = intval($lastId) + 1;

        // Format with leading zeros
        return $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Upload foto kategori dengan pendekatan sederhana tanpa fileinfo
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
            $uploadPath = 'uploads/kategori';
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
     * Delete foto kategori
     */
    public function deleteFoto($filename)
    {
        $path = FCPATH . 'uploads/kategori/' . $filename;
        if ($filename && file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }
}

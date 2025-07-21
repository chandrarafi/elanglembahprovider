<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    // Menampilkan halaman laporan user
    public function usersReport()
    {
        return view('admin/users/report');
    }

    // API untuk mendapatkan data laporan user dengan filter
    public function getUsersReport()
    {
        $request = $this->request->getGet();
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Filter berdasarkan role
        if (!empty($request['role'])) {
            $builder->where('role', $request['role']);
        }

        // Filter berdasarkan status
        if (!empty($request['status'])) {
            $builder->where('status', $request['status']);
        }

        // Filter berdasarkan tanggal
        if (!empty($request['start_date']) && !empty($request['end_date'])) {
            $builder->where('created_at >=', $request['start_date'] . ' 00:00:00')
                ->where('created_at <=', $request['end_date'] . ' 23:59:59');
        }

        // Ambil data
        $records = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $records
        ]);
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

    // Generate print preview for users report
    public function generateUserReportPrint()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Ambil semua data user (tanpa filter)
        $users = $builder->get()->getResultArray();

        // Format data for display
        foreach ($users as &$user) {
            // Format role
            if ($user['role'] === 'admin') {
                $user['role_formatted'] = '<span class="badge bg-primary">admin</span>';
            } elseif ($user['role'] === 'direktur') {
                $user['role_formatted'] = '<span class="badge bg-info">direktur</span>';
            } elseif ($user['role'] === 'pelanggan') {
                $user['role_formatted'] = '<span class="badge bg-success">pelanggan</span>';
            } else {
                $user['role_formatted'] = '<span class="badge bg-secondary">' . $user['role'] . '</span>';
            }

            // Format status
            $user['status_formatted'] = $user['status'] === 'active' ?
                '<span class="badge bg-success">active</span>' :
                '<span class="badge bg-danger">inactive</span>';

            // Format dates
            $user['last_login_formatted'] = $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-';
            $user['created_at_formatted'] = date('d/m/Y H:i', strtotime($user['created_at']));
        }

        // Set report periods and dates
        $reportDate = date('d F Y');
        $reportPeriode = date('d F Y', strtotime('-1 month')) . " - " . date('d F Y');

        // Prepare data for view
        $data = [
            'users' => $users,
            'reportDate' => $reportDate,
            'reportPeriode' => $reportPeriode
        ];

        return view('admin/users/print_report', $data);
    }

    // Generate PDF report for users
    public function generateUserReportPDF()
    {
        // Set longer execution time limit to prevent timeout
        ini_set('max_execution_time', 300); // 5 minutes

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Limit query to prevent memory issues
        $builder->limit(1000);

        // Ambil semua data user (tanpa filter)
        $users = $builder->get()->getResultArray();

        // Format data for display
        foreach ($users as &$user) {
            // Format dates only
            $user['last_login_formatted'] = $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-';
            $user['created_at_formatted'] = date('d/m/Y H:i', strtotime($user['created_at']));
        }

        // Set report periods and dates
        $reportDate = date('d F Y');
        $reportPeriode = date('d F Y', strtotime('-1 month')) . " - " . date('d F Y');

        // Prepare data for view
        $data = [
            'users' => $users,
            'reportDate' => $reportDate,
            'reportPeriode' => $reportPeriode
        ];

        // Generate HTML for PDF
        $html = view('admin/users/pdf_report', $data);

        // Set up DOMPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('defaultMediaType', 'all');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('chroot', ROOTPATH);
        $options->setDpi(96);

        // Initialize DOMPDF
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF with memory optimization
        $dompdf->render();

        // Generate filename
        $filename = 'Laporan_User_' . date('Ymd_His') . '.pdf';

        // Stream the PDF to browser
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }

    public function paketWisataReport()
    {
        $data = [
            'title' => 'Laporan Paket Wisata',
            'page' => 'paket-wisata-report'
        ];
        return view('admin/paket-wisata/report', $data);
    }

    public function getPaketWisataReport()
    {
        $paketWisataModel = new \App\Models\PaketWisataModel();
        $paketWisata = $paketWisataModel->getPaketWithKategori();

        $data = [];
        foreach ($paketWisata as $row) {
            $data[] = [
                'idpaket' => $row['idpaket'],
                'namapaket' => $row['namapaket'],
                'namakategori' => $row['namakategori'],
                'harga' => number_format($row['harga'], 0, ',', '.'),
                'durasi' => $row['durasi'],
                'minimalpeserta' => $row['minimalpeserta'],
                'maximalpeserta' => $row['maximalpeserta'],
                'statuspaket' => $row['statuspaket'],
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function generatePaketWisataReportPDF()
    {
        // Increase execution time limit for large reports
        ini_set('max_execution_time', 300); // 5 minutes

        $paketWisataModel = new \App\Models\PaketWisataModel();
        $paketWisata = $paketWisataModel->getPaketWithKategori();

        $data = [
            'paketWisata' => $paketWisata,
            'tanggal' => date('d-m-Y')
        ];

        $html = view('admin/paket-wisata/pdf_report', $data);

        // Create instance of dompdf
        $options = new \Dompdf\Options();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Output the generated PDF
        $dompdf->stream('laporan-paket-wisata-' . date('d-m-Y') . '.pdf', ['Attachment' => false]);
        exit();
    }

    public function generatePaketWisataReportPrint()
    {
        $paketWisataModel = new \App\Models\PaketWisataModel();
        $paketWisata = $paketWisataModel->getPaketWithKategori();

        $data = [
            'paketWisata' => $paketWisata,
            'tanggal' => date('d-m-Y')
        ];

        return view('admin/paket-wisata/print_report', $data);
    }

    public function pelangganReport()
    {
        // Prepare data with default empty values
        $data = [
            'title' => 'Laporan Pelanggan',
            'page' => 'pelanggan-report',
            'pelanggan' => [],
            'count' => 0
        ];

        // Try to get data, but don't fail if there's none
        try {
            $db = \Config\Database::connect();
            $query = $db->query('SELECT * FROM pelanggan');
            $pelanggan = $query->getResultArray();

            // Update data with actual values if available
            $data['pelanggan'] = $pelanggan;
            $data['count'] = count($pelanggan);
        } catch (\Exception $e) {
            // Just log the error, don't return an error response
            log_message('error', 'Error fetching pelanggan data: ' . $e->getMessage());
        }

        // Always return the view
        return view('admin/pelanggan/report', $data);
    }

    public function getPelangganReport()
    {
        // Default to empty data
        $data = [];

        // Try to get data from database
        try {
            $db = \Config\Database::connect();
            $query = $db->query('SELECT * FROM pelanggan');
            $pelanggan = $query->getResultArray();

            foreach ($pelanggan as $row) {
                // Get user data if iduser exists
                $userData = [];
                if (!empty($row['iduser'])) {
                    $userQuery = $db->query('SELECT * FROM users WHERE id = ?', [$row['iduser']]);
                    $userData = $userQuery->getRowArray() ?? [];
                }

                $data[] = [
                    'idpelanggan' => $row['idpelanggan'],
                    'namapelanggan' => $row['namapelanggan'],
                    'email' => $userData['email'] ?? '-',
                    'nohp' => $row['nohp'] ?? '-',
                    'alamat' => $row['alamat'] ?? '-',
                    'username' => $userData['username'] ?? '-',
                    'role' => $userData['role'] ?? '-',
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

    public function generatePelangganReportPDF()
    {
        // Increase execution time limit for large reports
        ini_set('max_execution_time', 300); // 5 minutes

        // Default to empty data
        $pelanggan = [];

        // Try to get data from database
        try {
            $db = \Config\Database::connect();
            $query = $db->query('SELECT * FROM pelanggan');
            $pelangganList = $query->getResultArray();

            foreach ($pelangganList as $row) {
                // Get user data if iduser exists
                $userData = [];
                if (!empty($row['iduser'])) {
                    $userQuery = $db->query('SELECT * FROM users WHERE id = ?', [$row['iduser']]);
                    $userData = $userQuery->getRowArray() ?? [];
                }

                // Merge pelanggan and user data
                $pelanggan[] = array_merge($row, [
                    'email' => $userData['email'] ?? '-',
                    'username' => $userData['username'] ?? '-',
                    'role' => $userData['role'] ?? '-'
                ]);
            }
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

    public function generatePelangganReportPrint()
    {
        // Default to empty data
        $pelanggan = [];

        // Try to get data from database
        try {
            $db = \Config\Database::connect();
            $query = $db->query('SELECT * FROM pelanggan');
            $pelangganList = $query->getResultArray();

            foreach ($pelangganList as $row) {
                // Get user data if iduser exists
                $userData = [];
                if (!empty($row['iduser'])) {
                    $userQuery = $db->query('SELECT * FROM users WHERE id = ?', [$row['iduser']]);
                    $userData = $userQuery->getRowArray() ?? [];
                }

                // Merge pelanggan and user data
                $pelanggan[] = array_merge($row, [
                    'email' => $userData['email'] ?? '-',
                    'username' => $userData['username'] ?? '-',
                    'role' => $userData['role'] ?? '-'
                ]);
            }
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

    // Menampilkan halaman laporan pemesanan
    public function pemesananReport()
    {
        return view('admin/pemesanan/report');
    }

    // API untuk mendapatkan data laporan pemesanan dengan filter
    public function getPemesananReport()
    {
        $request = $this->request->getGet();

        // Load PemesananModel
        $pemesananModel = new \App\Models\PemesananModel();

        // Set up filters
        $filters = [
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null,
            'search' => $request['search'] ?? null
        ];

        $reportType = $request['report_type'] ?? 'daily';

        // For daily/custom report type, return detailed data
        if ($reportType == 'daily' || $reportType == 'custom') {
            // Get pemesanan data with filters
            $pemesanan = $pemesananModel->getFilteredPemesanan($filters);

            // Calculate total amount if needed
            $totalAmount = 0;
            foreach ($pemesanan as $item) {
                $totalAmount += $item['totalbiaya'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $pemesanan,
                'totalAmount' => $totalAmount,
                'reportTitle' => 'Laporan Pemesanan Per-Tanggal'
            ]);
        }
        // For monthly report type, group by date
        else if ($reportType == 'monthly') {
            $db = \Config\Database::connect();

            // Extract year and month from start_date
            $yearMonth = substr($filters['start_date'], 0, 7); // Format: YYYY-MM

            $query = $db->query("
                SELECT 
                    DATE(tanggal) as date,
                    COUNT(idpesan) as count,
                    SUM(totalbiaya) as total
                FROM pemesanan
                WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
                GROUP BY DATE(tanggal)
                ORDER BY date ASC
            ", [$yearMonth]);

            $monthlyData = $query->getResultArray();

            // Format dates
            foreach ($monthlyData as &$day) {
                $day['date'] = date('d/m/Y', strtotime($day['date']));
            }

            return $this->response->setJSON([
                'status' => 'success',
                'monthly_data' => $monthlyData
            ]);
        }
        // For yearly report type, group by month
        else if ($reportType == 'yearly') {
            $db = \Config\Database::connect();

            // Extract year from start_date
            $year = substr($filters['start_date'], 0, 4); // Format: YYYY

            $query = $db->query("
                SELECT 
                    MONTH(tanggal) as month,
                    COUNT(idpesan) as count,
                    SUM(totalbiaya) as total
                FROM pemesanan
                WHERE YEAR(tanggal) = ?
                GROUP BY MONTH(tanggal)
                ORDER BY month ASC
            ", [$year]);

            $yearlyData = $query->getResultArray();

            // Add month names and prepare full year data with all months
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            // Create array with all months initialized with zero values
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $fullYearData[$i] = [
                    'month' => $i,
                    'month_name' => $monthNames[$i],
                    'count' => 0,
                    'total' => 0
                ];
            }

            // Fill in actual data where available
            foreach ($yearlyData as $month) {
                $monthNum = (int)$month['month'];
                $fullYearData[$monthNum]['count'] = $month['count'];
                $fullYearData[$monthNum]['total'] = $month['total'];
            }

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($fullYearData as $month) {
                $totalAmount += $month['total'];
                $totalCount += $month['count'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'yearly_data' => array_values($fullYearData),
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount
            ]);
        }
    }

    // Generate print preview for pemesanan report
    public function generatePemesananReportPrint()
    {
        $request = $this->request->getGet();

        // Load PemesananModel
        $pemesananModel = new \App\Models\PemesananModel();

        // Set up filters
        $filters = [
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null
        ];

        $reportType = $request['report_type'] ?? 'daily';
        $data = [];

        // Format report period text
        $reportPeriode = '';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = date('d F Y', strtotime($filters['start_date']));
            $endDate = date('d F Y', strtotime($filters['end_date']));

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($filters['start_date']));
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                // For yearly reports, show year only
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = "Tahun " . $year;
            } else if ($filters['start_date'] === $filters['end_date']) {
                $reportPeriode = $startDate;
            } else {
                $reportPeriode = $startDate . ' - ' . $endDate;
            }
        } else {
            // Default to current date if no dates provided
            $today = date('Y-m-d');

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($today));
                $year = date('Y', strtotime($today));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                $reportPeriode = "Tahun " . date('Y', strtotime($today));
            } else {
                $reportPeriode = date('d F Y', strtotime($today));
            }

            // Also update the filters to use today's date
            $filters['start_date'] = $today;
            $filters['end_date'] = $today;
        }

        // Set report date
        $reportDate = date('d F Y');

        // For daily/custom report type
        if ($reportType == 'daily' || $reportType == 'custom') {
            // Get pemesanan data with filters
            $pemesanan = $pemesananModel->getFilteredPemesanan($filters);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($pemesanan as $item) {
                $totalAmount += $item['totalbiaya'];
            }

            $data = [
                'pemesanan' => $pemesanan,
                'totalAmount' => $totalAmount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode,
                'reportType' => $reportType,
                'reportTitle' => 'Laporan Pemesanan Per-Tanggal',
                'filterParams' => "Periode: {$reportPeriode}"
            ];

            return view('admin/pemesanan/print_report', $data);
        }
        // For monthly report type
        else if ($reportType == 'monthly') {
            $db = \Config\Database::connect();

            // Extract year and month from start_date
            $yearMonth = substr($filters['start_date'], 0, 7); // Format: YYYY-MM

            $query = $db->query("
                SELECT 
                    DATE(tanggal) as date,
                    COUNT(idpesan) as count,
                    SUM(totalbiaya) as total
                FROM pemesanan
                WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
                GROUP BY DATE(tanggal)
                ORDER BY date ASC
            ", [$yearMonth]);

            $monthlyData = $query->getResultArray();

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($monthlyData as &$day) {
                $day['date_formatted'] = date('d/m/Y', strtotime($day['date']));
                $totalAmount += $day['total'];
                $totalCount += $day['count'];
            }

            $data = [
                'monthly_data' => $monthlyData,
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}"
            ];

            return view('admin/pemesanan/print_report_monthly', $data);
        }
        // For yearly report type
        else if ($reportType == 'yearly') {
            $db = \Config\Database::connect();

            // Extract year from start_date
            $year = substr($filters['start_date'], 0, 4); // Format: YYYY

            $query = $db->query("
                SELECT 
                    MONTH(tanggal) as month,
                    COUNT(idpesan) as count,
                    SUM(totalbiaya) as total
                FROM pemesanan
                WHERE YEAR(tanggal) = ?
                GROUP BY MONTH(tanggal)
                ORDER BY month ASC
            ", [$year]);

            $yearlyData = $query->getResultArray();

            // Add month names and prepare full year data with all months
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            // Create array with all months initialized with zero values
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $fullYearData[$i] = [
                    'month' => $i,
                    'month_name' => $monthNames[$i],
                    'count' => 0,
                    'total' => 0
                ];
            }

            // Fill in actual data where available
            foreach ($yearlyData as $month) {
                $monthNum = (int)$month['month'];
                $fullYearData[$monthNum]['count'] = $month['count'];
                $fullYearData[$monthNum]['total'] = $month['total'];
            }

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($fullYearData as $month) {
                $totalAmount += $month['total'];
                $totalCount += $month['count'];
            }

            $data = [
                'yearly_data' => array_values($fullYearData),
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}"
            ];

            return view('admin/pemesanan/print_report_yearly', $data);
        }
    }

    // Generate PDF report for pemesanan
    public function generatePemesananReportPDF()
    {
        // Set longer execution time limit to prevent timeout
        ini_set('max_execution_time', 300); // 5 minutes

        $request = $this->request->getGet();

        // Load PemesananModel
        $pemesananModel = new \App\Models\PemesananModel();

        // Set up filters
        $filters = [
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null
        ];

        $reportType = $request['report_type'] ?? 'daily';
        $data = [];

        // Format report period text
        $reportPeriode = '';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = date('d F Y', strtotime($filters['start_date']));
            $endDate = date('d F Y', strtotime($filters['end_date']));

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($filters['start_date']));
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                // For yearly reports, show year only
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = "Tahun " . $year;
            } else if ($filters['start_date'] === $filters['end_date']) {
                $reportPeriode = $startDate;
            } else {
                $reportPeriode = $startDate . ' - ' . $endDate;
            }
        } else {
            // Default to current date if no dates provided
            $today = date('Y-m-d');

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($today));
                $year = date('Y', strtotime($today));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                $reportPeriode = "Tahun " . date('Y', strtotime($today));
            } else {
                $reportPeriode = date('d F Y', strtotime($today));
            }

            // Also update the filters to use today's date
            $filters['start_date'] = $today;
            $filters['end_date'] = $today;
        }

        // Set report date
        $reportDate = date('d F Y');

        // Set up DOMPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('defaultMediaType', 'all');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('chroot', ROOTPATH);
        $options->setDpi(96);

        // Initialize DOMPDF
        $dompdf = new Dompdf($options);

        // For daily/custom report type
        if ($reportType == 'daily' || $reportType == 'custom') {
            // Get pemesanan data with filters
            $pemesanan = $pemesananModel->getFilteredPemesanan($filters);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($pemesanan as $item) {
                $totalAmount += $item['totalbiaya'];
            }

            $data = [
                'pemesanan' => $pemesanan,
                'totalAmount' => $totalAmount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode,
                'reportType' => $reportType,
                'reportTitle' => 'Laporan Pemesanan Per-Tanggal',
                'filterParams' => "Periode: {$reportPeriode}"
            ];

            // Generate HTML for PDF
            $html = view('admin/pemesanan/pdf_report', $data);

            // Generate filename
            $filename = 'Laporan_Pemesanan_Per_Tanggal_' . date('Ymd_His') . '.pdf';
        }
        // For monthly report type
        else if ($reportType == 'monthly') {
            $db = \Config\Database::connect();

            // Extract year and month from start_date
            $yearMonth = substr($filters['start_date'], 0, 7); // Format: YYYY-MM

            $query = $db->query("
                SELECT 
                    DATE(tanggal) as date,
                    COUNT(idpesan) as count,
                    SUM(totalbiaya) as total
                FROM pemesanan
                WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?
                GROUP BY DATE(tanggal)
                ORDER BY date ASC
            ", [$yearMonth]);

            $monthlyData = $query->getResultArray();

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($monthlyData as &$day) {
                $day['date_formatted'] = date('d/m/Y', strtotime($day['date']));
                $totalAmount += $day['total'];
                $totalCount += $day['count'];
            }

            $data = [
                'monthly_data' => $monthlyData,
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}"
            ];

            // Generate HTML for PDF
            $html = view('admin/pemesanan/pdf_report_monthly', $data);

            // Generate filename
            $filename = 'Laporan_Pemesanan_Bulanan_' . date('Ymd_His') . '.pdf';
        }
        // For yearly report type
        else if ($reportType == 'yearly') {
            $db = \Config\Database::connect();

            // Extract year from start_date
            $year = substr($filters['start_date'], 0, 4); // Format: YYYY

            $query = $db->query("
                SELECT 
                    MONTH(tanggal) as month,
                    COUNT(idpesan) as count,
                    SUM(totalbiaya) as total
                FROM pemesanan
                WHERE YEAR(tanggal) = ?
                GROUP BY MONTH(tanggal)
                ORDER BY month ASC
            ", [$year]);

            $yearlyData = $query->getResultArray();

            // Add month names and prepare full year data with all months
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            // Create array with all months initialized with zero values
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $fullYearData[$i] = [
                    'month' => $i,
                    'month_name' => $monthNames[$i],
                    'count' => 0,
                    'total' => 0
                ];
            }

            // Fill in actual data where available
            foreach ($yearlyData as $month) {
                $monthNum = (int)$month['month'];
                $fullYearData[$monthNum]['count'] = $month['count'];
                $fullYearData[$monthNum]['total'] = $month['total'];
            }

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($fullYearData as $month) {
                $totalAmount += $month['total'];
                $totalCount += $month['count'];
            }

            $data = [
                'yearly_data' => array_values($fullYearData),
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}"
            ];

            // Generate HTML for PDF
            $html = view('admin/pemesanan/pdf_report_yearly', $data);

            // Generate filename
            $filename = 'Laporan_Pemesanan_Tahunan_' . date('Ymd_His') . '.pdf';
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF with memory optimization
        $dompdf->render();

        // Stream the PDF to browser
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }

    // Menampilkan halaman laporan pembayaran
    public function pembayaranReport()
    {
        return view('admin/pembayaran/report');
    }

    // API untuk mendapatkan data laporan pembayaran dengan filter
    public function getPembayaranReport()
    {
        $request = $this->request->getGet();

        // Load PembayaranModel
        $pembayaranModel = new \App\Models\PembayaranModel();

        // Set up filters
        $filters = [
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null,
            'status' => $request['status'] ?? null,
            'search' => $request['search'] ?? null
        ];

        $reportType = $request['report_type'] ?? 'daily';

        // For daily/custom report type, return detailed data
        if ($reportType == 'daily' || $reportType == 'custom') {
            // Get pembayaran data with filters
            $pembayaran = $this->getFilteredPembayaran($filters);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($pembayaran as $item) {
                $totalAmount += $item['jumlah_bayar'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $pembayaran,
                'totalAmount' => $totalAmount,
                'reportTitle' => 'Laporan Pembayaran Per-Tanggal'
            ]);
        }
        // For monthly report type, group by date
        else if ($reportType == 'monthly') {
            $db = \Config\Database::connect();

            // Extract year and month from start_date
            $yearMonth = substr($filters['start_date'], 0, 7); // Format: YYYY-MM

            $query = $db->query("
                SELECT 
                    DATE(tanggal_bayar) as date,
                    COUNT(idbayar) as count,
                    SUM(jumlah_bayar) as total
                FROM pembayaran
                WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = ?
                AND (? IS NULL OR status_pembayaran = ?)
                GROUP BY DATE(tanggal_bayar)
                ORDER BY date ASC
            ", [$yearMonth, $filters['status'], $filters['status']]);

            $monthlyData = $query->getResultArray();

            // Format dates
            foreach ($monthlyData as &$day) {
                $day['date_formatted'] = date('d/m/Y', strtotime($day['date']));
            }

            return $this->response->setJSON([
                'status' => 'success',
                'monthly_data' => $monthlyData
            ]);
        }
        // For yearly report type, group by month
        else if ($reportType == 'yearly') {
            $db = \Config\Database::connect();

            // Extract year from start_date
            $year = substr($filters['start_date'], 0, 4); // Format: YYYY

            $statusFilter = "";
            $params = [$year];

            if (!empty($filters['status'])) {
                $statusFilter = "AND status_pembayaran = ?";
                $params[] = $filters['status'];
            }

            $query = $db->query("
                SELECT 
                    MONTH(tanggal_bayar) as month,
                    COUNT(idbayar) as count,
                    SUM(jumlah_bayar) as total
                FROM pembayaran
                WHERE YEAR(tanggal_bayar) = ?
                $statusFilter
                GROUP BY MONTH(tanggal_bayar)
                ORDER BY month ASC
            ", $params);

            $yearlyData = $query->getResultArray();

            // Add month names and prepare full year data with all months
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            // Create array with all months initialized with zero values
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $fullYearData[$i] = [
                    'month' => $i,
                    'month_name' => $monthNames[$i],
                    'count' => 0,
                    'total' => 0
                ];
            }

            // Fill in actual data where available
            foreach ($yearlyData as $month) {
                $monthNum = (int)$month['month'];
                $fullYearData[$monthNum]['count'] = $month['count'];
                $fullYearData[$monthNum]['total'] = $month['total'];
            }

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($fullYearData as $month) {
                $totalAmount += $month['total'];
                $totalCount += $month['count'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'yearly_data' => array_values($fullYearData),
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount
            ]);
        }
    }

    // Helper method to get filtered payment data
    private function getFilteredPembayaran($filters = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pembayaran pb');
        $builder->select('pb.*, p.kode_booking, pw.namapaket, u.name as nama_pelanggan');
        $builder->join('pemesanan p', 'pb.idpesan = p.idpesan');
        $builder->join('paket_wisata pw', 'p.idpaket = pw.idpaket');
        $builder->join('users u', 'p.iduser = u.id');

        // Apply status filter
        if (!empty($filters['status'])) {
            $builder->where('pb.status_pembayaran', $filters['status']);
        }

        // Apply date filters
        if (!empty($filters['start_date'])) {
            // Add time to start date if not present
            $startDate = $filters['start_date'];
            if (strlen($startDate) == 10) { // YYYY-MM-DD
                $startDate .= ' 00:00:00';
            }
            $builder->where('pb.tanggal_bayar >=', $startDate);
        }

        if (!empty($filters['end_date'])) {
            // Add time to end date if not present
            $endDate = $filters['end_date'];
            if (strlen($endDate) == 10) { // YYYY-MM-DD
                $endDate .= ' 23:59:59';
            }
            $builder->where('pb.tanggal_bayar <=', $endDate);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('p.kode_booking', $filters['search'])
                ->orLike('pw.namapaket', $filters['search'])
                ->orLike('u.name', $filters['search'])
                ->groupEnd();
        }

        // Order by payment date
        $builder->orderBy('pb.tanggal_bayar', 'DESC');

        return $builder->get()->getResultArray();
    }

    // Generate print preview for pembayaran report
    public function generatePembayaranReportPrint()
    {
        $request = $this->request->getGet();

        // Set up filters
        $filters = [
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null,
            'status' => $request['status'] ?? null
        ];

        $reportType = $request['report_type'] ?? 'daily';
        $data = [];

        // Format report period text
        $reportPeriode = '';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = date('d F Y', strtotime($filters['start_date']));
            $endDate = date('d F Y', strtotime($filters['end_date']));

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($filters['start_date']));
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                // For yearly reports, show year only
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = "Tahun " . $year;
            } else if ($filters['start_date'] === $filters['end_date']) {
                $reportPeriode = $startDate;
            } else {
                $reportPeriode = $startDate . ' - ' . $endDate;
            }
        } else {
            // Default to current date if no dates provided
            $today = date('Y-m-d');

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($today));
                $year = date('Y', strtotime($today));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                $reportPeriode = "Tahun " . date('Y', strtotime($today));
            } else {
                $reportPeriode = date('d F Y', strtotime($today));
            }

            // Also update the filters to use today's date
            $filters['start_date'] = $today;
            $filters['end_date'] = $today;
        }

        // Set report date
        $reportDate = date('d F Y');

        // Add status to period text if specified
        $statusText = '';
        if (!empty($filters['status'])) {
            $statusLabels = [
                'pending' => 'Menunggu Verifikasi',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak'
            ];
            $statusText = ' - Status: ' . ($statusLabels[$filters['status']] ?? $filters['status']);
        }

        // For daily/custom report type
        if ($reportType == 'daily' || $reportType == 'custom') {
            // Get pembayaran data with filters
            $pembayaran = $this->getFilteredPembayaran($filters);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($pembayaran as $item) {
                $totalAmount += $item['jumlah_bayar'];
            }

            $data = [
                'pembayaran' => $pembayaran,
                'totalAmount' => $totalAmount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode . $statusText,
                'reportType' => $reportType,
                'reportTitle' => 'Laporan Pembayaran Per-Tanggal',
                'filterParams' => "Periode: {$reportPeriode}" . $statusText
            ];

            return view('admin/pembayaran/print_report', $data);
        }
        // For monthly report type
        else if ($reportType == 'monthly') {
            $db = \Config\Database::connect();

            // Extract year and month from start_date
            $yearMonth = substr($filters['start_date'], 0, 7); // Format: YYYY-MM

            $statusFilter = "";
            $params = [$yearMonth];

            if (!empty($filters['status'])) {
                $statusFilter = "AND status_pembayaran = ?";
                $params[] = $filters['status'];
            }

            $query = $db->query("
                SELECT 
                    DATE(tanggal_bayar) as date,
                    COUNT(idbayar) as count,
                    SUM(jumlah_bayar) as total
                FROM pembayaran
                WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = ?
                $statusFilter
                GROUP BY DATE(tanggal_bayar)
                ORDER BY date ASC
            ", $params);

            $monthlyData = $query->getResultArray();

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($monthlyData as &$day) {
                $day['date_formatted'] = date('d/m/Y', strtotime($day['date']));
                $totalAmount += $day['total'];
                $totalCount += $day['count'];
            }

            $data = [
                'monthly_data' => $monthlyData,
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode . $statusText,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}" . $statusText
            ];

            return view('admin/pembayaran/print_report_monthly', $data);
        }
        // For yearly report type
        else if ($reportType == 'yearly') {
            $db = \Config\Database::connect();

            // Extract year from start_date
            $year = substr($filters['start_date'], 0, 4); // Format: YYYY

            $statusFilter = "";
            $params = [$year];

            if (!empty($filters['status'])) {
                $statusFilter = "AND status_pembayaran = ?";
                $params[] = $filters['status'];
            }

            $query = $db->query("
                SELECT 
                    MONTH(tanggal_bayar) as month,
                    COUNT(idbayar) as count,
                    SUM(jumlah_bayar) as total
                FROM pembayaran
                WHERE YEAR(tanggal_bayar) = ?
                $statusFilter
                GROUP BY MONTH(tanggal_bayar)
                ORDER BY month ASC
            ", $params);

            $yearlyData = $query->getResultArray();

            // Add month names and prepare full year data with all months
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            // Create array with all months initialized with zero values
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $fullYearData[$i] = [
                    'month' => $i,
                    'month_name' => $monthNames[$i],
                    'count' => 0,
                    'total' => 0
                ];
            }

            // Fill in actual data where available
            foreach ($yearlyData as $month) {
                $monthNum = (int)$month['month'];
                $fullYearData[$monthNum]['count'] = $month['count'];
                $fullYearData[$monthNum]['total'] = $month['total'];
            }

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($fullYearData as $month) {
                $totalAmount += $month['total'];
                $totalCount += $month['count'];
            }

            $data = [
                'yearly_data' => array_values($fullYearData),
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode . $statusText,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}" . $statusText
            ];

            return view('admin/pembayaran/print_report_yearly', $data);
        }
    }

    // Generate PDF report for pembayaran
    public function generatePembayaranReportPDF()
    {
        // Set longer execution time limit to prevent timeout
        ini_set('max_execution_time', 300); // 5 minutes

        $request = $this->request->getGet();

        // Set up filters
        $filters = [
            'start_date' => $request['start_date'] ?? null,
            'end_date' => $request['end_date'] ?? null,
            'status' => $request['status'] ?? null
        ];

        $reportType = $request['report_type'] ?? 'daily';
        $data = [];

        // Format report period text
        $reportPeriode = '';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = date('d F Y', strtotime($filters['start_date']));
            $endDate = date('d F Y', strtotime($filters['end_date']));

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($filters['start_date']));
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                // For yearly reports, show year only
                $year = date('Y', strtotime($filters['start_date']));
                $reportPeriode = "Tahun " . $year;
            } else if ($filters['start_date'] === $filters['end_date']) {
                $reportPeriode = $startDate;
            } else {
                $reportPeriode = $startDate . ' - ' . $endDate;
            }
        } else {
            // Default to current date if no dates provided
            $today = date('Y-m-d');

            if ($reportType == 'monthly') {
                // For monthly reports, show only month name and year
                $monthNames = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $month = (int)date('n', strtotime($today));
                $year = date('Y', strtotime($today));
                $reportPeriode = $monthNames[$month] . ' ' . $year;
            } else if ($reportType == 'yearly') {
                $reportPeriode = "Tahun " . date('Y', strtotime($today));
            } else {
                $reportPeriode = date('d F Y', strtotime($today));
            }

            // Also update the filters to use today's date
            $filters['start_date'] = $today;
            $filters['end_date'] = $today;
        }

        // Set report date
        $reportDate = date('d F Y');

        // Add status to period text if specified
        $statusText = '';
        if (!empty($filters['status'])) {
            $statusLabels = [
                'pending' => 'Menunggu Verifikasi',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak'
            ];
            $statusText = ' - Status: ' . ($statusLabels[$filters['status']] ?? $filters['status']);
        }

        // Set up DOMPDF options
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('defaultMediaType', 'all');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('chroot', ROOTPATH);
        $options->setDpi(96);

        // Initialize DOMPDF
        $dompdf = new \Dompdf\Dompdf($options);

        // For daily/custom report type
        if ($reportType == 'daily' || $reportType == 'custom') {
            // Get pembayaran data with filters
            $pembayaran = $this->getFilteredPembayaran($filters);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($pembayaran as $item) {
                $totalAmount += $item['jumlah_bayar'];
            }

            $data = [
                'pembayaran' => $pembayaran,
                'totalAmount' => $totalAmount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode . $statusText,
                'reportType' => $reportType,
                'reportTitle' => 'Laporan Pembayaran Per-Tanggal',
                'filterParams' => "Periode: {$reportPeriode}" . $statusText
            ];

            // Generate HTML for PDF
            $html = view('admin/pembayaran/pdf_report', $data);

            // Generate filename
            $filename = 'Laporan_Pembayaran_Per_Tanggal_' . date('Ymd_His') . '.pdf';
        }
        // For monthly report type
        else if ($reportType == 'monthly') {
            $db = \Config\Database::connect();

            // Extract year and month from start_date
            $yearMonth = substr($filters['start_date'], 0, 7); // Format: YYYY-MM

            $statusFilter = "";
            $params = [$yearMonth];

            if (!empty($filters['status'])) {
                $statusFilter = "AND status_pembayaran = ?";
                $params[] = $filters['status'];
            }

            $query = $db->query("
                SELECT 
                    DATE(tanggal_bayar) as date,
                    COUNT(idbayar) as count,
                    SUM(jumlah_bayar) as total
                FROM pembayaran
                WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = ?
                $statusFilter
                GROUP BY DATE(tanggal_bayar)
                ORDER BY date ASC
            ", $params);

            $monthlyData = $query->getResultArray();

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($monthlyData as &$day) {
                $day['date_formatted'] = date('d/m/Y', strtotime($day['date']));
                $totalAmount += $day['total'];
                $totalCount += $day['count'];
            }

            $data = [
                'monthly_data' => $monthlyData,
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode . $statusText,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}" . $statusText
            ];

            // Generate HTML for PDF
            $html = view('admin/pembayaran/pdf_report_monthly', $data);

            // Generate filename
            $filename = 'Laporan_Pembayaran_Bulanan_' . date('Ymd_His') . '.pdf';
        }
        // For yearly report type
        else if ($reportType == 'yearly') {
            $db = \Config\Database::connect();

            // Extract year from start_date
            $year = substr($filters['start_date'], 0, 4); // Format: YYYY

            $statusFilter = "";
            $params = [$year];

            if (!empty($filters['status'])) {
                $statusFilter = "AND status_pembayaran = ?";
                $params[] = $filters['status'];
            }

            $query = $db->query("
                SELECT 
                    MONTH(tanggal_bayar) as month,
                    COUNT(idbayar) as count,
                    SUM(jumlah_bayar) as total
                FROM pembayaran
                WHERE YEAR(tanggal_bayar) = ?
                $statusFilter
                GROUP BY MONTH(tanggal_bayar)
                ORDER BY month ASC
            ", $params);

            $yearlyData = $query->getResultArray();

            // Add month names and prepare full year data with all months
            $monthNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            // Create array with all months initialized with zero values
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $fullYearData[$i] = [
                    'month' => $i,
                    'month_name' => $monthNames[$i],
                    'count' => 0,
                    'total' => 0
                ];
            }

            // Fill in actual data where available
            foreach ($yearlyData as $month) {
                $monthNum = (int)$month['month'];
                $fullYearData[$monthNum]['count'] = $month['count'];
                $fullYearData[$monthNum]['total'] = $month['total'];
            }

            // Calculate grand total
            $totalAmount = 0;
            $totalCount = 0;
            foreach ($fullYearData as $month) {
                $totalAmount += $month['total'];
                $totalCount += $month['count'];
            }

            $data = [
                'yearly_data' => array_values($fullYearData),
                'totalAmount' => $totalAmount,
                'totalCount' => $totalCount,
                'reportDate' => $reportDate,
                'reportPeriode' => $reportPeriode . $statusText,
                'reportType' => $reportType,
                'filterParams' => "Periode: {$reportPeriode}" . $statusText
            ];

            // Generate HTML for PDF
            $html = view('admin/pembayaran/pdf_report_yearly', $data);

            // Generate filename
            $filename = 'Laporan_Pembayaran_Tahunan_' . date('Ymd_His') . '.pdf';
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF with memory optimization
        $dompdf->render();

        // Stream the PDF to browser
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }
}

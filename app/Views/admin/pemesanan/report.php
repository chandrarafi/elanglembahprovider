<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Laporan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Pemesanan</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <button type="button" class="btn btn-danger px-3" id="pdfBtn">
            <i class="bx bxs-file-pdf"></i> Export PDF
        </button>
        <!-- <button type="button" class="btn btn-secondary px-3" id="printBtn">
            <i class="bx bx-printer"></i> Print
        </button> -->
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterType" class="form-label">Tipe Laporan</label>
                <select class="form-select" id="filterType">
                    <option value="daily">Per-Tanggal</option>
                    <option value="monthly">Bulanan</option>
                    <option value="yearly">Tahunan</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div class="col-md-3" id="dailyFilter">
                <label for="startDateDaily" class="form-label">Tanggal Awal</label>
                <input type="date" class="form-control" id="startDateDaily" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
            </div>

            <div class="col-md-3" id="endDateDaily">
                <label for="endDateDaily" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="endDateDaily" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-3 d-none" id="monthlyFilter">
                <label for="monthlyDate" class="form-label">Bulan</label>
                <input type="month" class="form-control" id="monthlyDate" value="<?= date('Y-m') ?>">
            </div>

            <div class="col-md-3 d-none" id="yearlyFilter">
                <label for="yearlyDate" class="form-label">Tahun</label>
                <select class="form-select" id="yearlyDate">
                    <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-3 d-none" id="startDateFilter">
                <label for="startDate" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="startDate" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
            </div>

            <div class="col-md-3 d-none" id="endDateFilter">
                <label for="endDate" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="endDate" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-primary px-3" id="filterBtn">
                    <i class="bx bx-filter"></i> Filter
                </button>
            </div>
        </div>

        <!-- Daily Report Table (Default) -->
        <div class="table-responsive" id="dailyReportTable">
            <table id="pemesananDailyTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Nama Paket</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal Pemesanan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Tanggal Selesai</th>
                        <th>Jumlah Peserta</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Monthly Report Table (Hidden by default) -->
        <div class="table-responsive d-none" id="monthlyReportTable">
            <table id="pemesananMonthlyTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah Pesanan</th>
                        <th>Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Yearly Report Table (Hidden by default) -->
        <div class="table-responsive d-none" id="yearlyReportTable">
            <table id="pemesananYearlyTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Pesanan</th>
                        <th>Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Template for PDF Report Header (Hidden on page) -->
<div id="reportHeader" style="display: none;">
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <div style="width: 120px; padding-right: 20px;">
            <img src="data:image/png;base64,<?= base64_encode(file_get_contents(ROOTPATH . 'public/assets/images/logo.png')) ?>" alt="Elang Lembah Provider" style="max-width: 100px;">
        </div>
        <div style="flex: 1; text-align: center;">
            <h2 style="margin: 10px 0;">Elang Lembah Provider</h2>
            <p style="margin: 5px 0;">
                Jl. Belati Barat IV No. 5, Lolong Belati, Padang Utara, Sumatera Barat<br>
                Telepon: 0822-1095-5523 | Email: elanglembahprovider@gmail.com
            </p>
        </div>
    </div>
    <hr style="border-top: 2px solid #000; margin: 15px 0;">
    <h3 style="margin: 10px 0; text-align: center;">Laporan Data Pemesanan</h3>
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
        <div>
            <p>Tanggal Laporan: <span id="reportDate"></span></p>
        </div>
        <div>
            <p>Periode: <span id="reportPeriode"></span></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let pemesananDailyTable, pemesananMonthlyTable, pemesananYearlyTable;
    let currentReportType = 'daily';

    $(document).ready(function() {
        // Initialize DataTables
        initDataTables();

        // Event listeners
        $("#pdfBtn").click(function() {
            generatePDF();
        });

        $("#printBtn").click(function() {
            printReport();
        });

        // Filter type change event
        $("#filterType").change(function() {
            currentReportType = $(this).val();
            showRelevantFilters();
            showRelevantTable();
        });

        // Filter button click
        $("#filterBtn").click(function() {
            loadReport();
        });

        // Set report date
        $('#reportDate').text(new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }));

        // Show default filters and load report
        showRelevantFilters();
        showRelevantTable();
        loadReport();
    });

    function initDataTables() {
        pemesananDailyTable = $('#pemesananDailyTable').DataTable({
            responsive: true,
            dom: 'frtip', // Removed the 'B' (buttons)
            order: [
                [3, 'desc']
            ] // Sort by tanggal pemesanan by default
        });

        pemesananMonthlyTable = $('#pemesananMonthlyTable').DataTable({
            responsive: true,
            dom: 'frtip',
            order: [
                [0, 'asc']
            ] // Sort by tanggal by default
        });

        pemesananYearlyTable = $('#pemesananYearlyTable').DataTable({
            responsive: true,
            dom: 'frtip',
            order: [
                [0, 'asc']
            ] // Sort by bulan by default
        });
    }

    function showRelevantFilters() {
        const filterType = $("#filterType").val();

        // Hide all date filters first
        $("#dailyFilter, #endDateDaily, #monthlyFilter, #yearlyFilter, #startDateFilter, #endDateFilter").addClass('d-none');

        // Show relevant filters based on selection
        switch (filterType) {
            case 'daily':
                $("#dailyFilter, #endDateDaily").removeClass('d-none');
                break;
            case 'monthly':
                $("#monthlyFilter").removeClass('d-none');
                break;
            case 'yearly':
                $("#yearlyFilter").removeClass('d-none');
                break;
            case 'custom':
                $("#startDateFilter, #endDateFilter").removeClass('d-none');
                break;
        }
    }

    function showRelevantTable() {
        const filterType = $("#filterType").val();

        // Hide all tables first
        $("#dailyReportTable, #monthlyReportTable, #yearlyReportTable").addClass('d-none');

        // Show relevant table based on selection
        switch (filterType) {
            case 'daily':
            case 'custom':
                $("#dailyReportTable").removeClass('d-none');
                break;
            case 'monthly':
                $("#monthlyReportTable").removeClass('d-none');
                break;
            case 'yearly':
                $("#yearlyReportTable").removeClass('d-none');
                break;
        }
    }

    function getFilterDates() {
        const filterType = $("#filterType").val();
        let startDate, endDate, periodText;

        switch (filterType) {
            case 'daily':
                startDate = $("#startDateDaily").val();
                endDate = $("#endDateDaily").val();
                if (startDate && endDate) {
                    periodText = `${new Date(startDate).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    })} - ${new Date(endDate).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    })}`;
                } else {
                    periodText = "Tanggal belum dipilih";
                }
                break;

            case 'monthly':
                const monthlyDate = $("#monthlyDate").val(); // Format: YYYY-MM
                if (monthlyDate) {
                    const [year, month] = monthlyDate.split('-');
                    const lastDay = new Date(year, month, 0).getDate();
                    startDate = `${monthlyDate}-01`;
                    endDate = `${monthlyDate}-${lastDay}`;

                    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                    ];
                    periodText = `${monthNames[parseInt(month) - 1]} ${year}`;
                } else {
                    // Default to current month if not selected
                    const now = new Date();
                    const currentYear = now.getFullYear();
                    const currentMonth = now.getMonth() + 1;
                    const lastDay = new Date(currentYear, currentMonth, 0).getDate();

                    startDate = `${currentYear}-${String(currentMonth).padStart(2, '0')}-01`;
                    endDate = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${lastDay}`;

                    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                    ];
                    periodText = `${monthNames[currentMonth - 1]} ${currentYear}`;
                }
                break;

            case 'yearly':
                const yearlyDate = $("#yearlyDate").val();
                if (yearlyDate) {
                    startDate = `${yearlyDate}-01-01`;
                    endDate = `${yearlyDate}-12-31`;
                    periodText = `Tahun ${yearlyDate}`;
                } else {
                    // Default to current year if not selected
                    const currentYear = new Date().getFullYear();
                    startDate = `${currentYear}-01-01`;
                    endDate = `${currentYear}-12-31`;
                    periodText = `Tahun ${currentYear}`;
                }
                break;

            case 'custom':
                startDate = $("#startDate").val();
                endDate = $("#endDate").val();
                if (startDate && endDate) {
                    periodText = `${new Date(startDate).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    })} - ${new Date(endDate).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    })}`;
                } else {
                    periodText = "Tanggal belum dipilih";
                }
                break;
        }

        // Ensure we have valid dates
        if (!startDate) {
            startDate = new Date().toISOString().split('T')[0]; // Today's date in YYYY-MM-DD format
        }
        if (!endDate) {
            endDate = new Date().toISOString().split('T')[0]; // Today's date in YYYY-MM-DD format
        }

        return {
            startDate,
            endDate,
            periodText,
            reportType: filterType
        };
    }

    function loadReport() {
        // Get filter values
        const {
            startDate,
            endDate,
            periodText,
            reportType
        } = getFilterDates();

        // Update report periode text
        $('#reportPeriode').text(periodText);

        // Clear all tables
        pemesananDailyTable.clear();
        pemesananMonthlyTable.clear();
        pemesananYearlyTable.clear();

        // Show loading in the active table
        if (reportType === 'daily' || reportType === 'custom') {
            $('#pemesananDailyTable tbody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');
        } else if (reportType === 'monthly') {
            $('#pemesananMonthlyTable tbody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        } else if (reportType === 'yearly') {
            $('#pemesananYearlyTable tbody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        }

        // Make AJAX request
        $.ajax({
            url: '/admin/getPemesananReport',
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                report_type: reportType
            },
            success: function(response) {
                if (reportType === 'daily' || reportType === 'custom') {
                    // Process daily/detailed data
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(pemesanan) {
                            // Format status with badge
                            let statusBadge = '';
                            switch (pemesanan.status) {
                                case 'pending':
                                    statusBadge = '<span class="badge bg-warning">Pending</span>';
                                    break;
                                case 'waiting_confirmation':
                                    statusBadge = '<span class="badge bg-info">Menunggu Konfirmasi</span>';
                                    break;
                                case 'confirmed':
                                    statusBadge = '<span class="badge bg-primary">Dikonfirmasi</span>';
                                    break;
                                case 'paid':
                                    statusBadge = '<span class="badge bg-success">Dibayar</span>';
                                    break;
                                case 'completed':
                                    statusBadge = '<span class="badge bg-secondary">Selesai</span>';
                                    break;
                                case 'cancelled':
                                    statusBadge = '<span class="badge bg-danger">Dibatalkan</span>';
                                    break;
                                default:
                                    statusBadge = '<span class="badge bg-secondary">' + pemesanan.status + '</span>';
                            }

                            pemesananDailyTable.row.add([
                                pemesanan.kode_booking,
                                pemesanan.namapaket,
                                pemesanan.name,
                                new Date(pemesanan.tanggal).toLocaleDateString('id-ID'),
                                new Date(pemesanan.tgl_berangkat).toLocaleDateString('id-ID'),
                                new Date(pemesanan.tgl_selesai).toLocaleDateString('id-ID'),
                                pemesanan.jumlah_peserta,
                                'Rp ' + new Intl.NumberFormat('id-ID').format(pemesanan.totalbiaya),
                                statusBadge
                            ]);
                        });
                    }
                    pemesananDailyTable.draw();

                    // Display total amount if available
                    if (response.totalAmount) {
                        const summaryHtml = `
                            <div class="mt-3">
                                <h5>Ringkasan:</h5>
                                <p><strong>Total Pemesanan:</strong> ${response.data.length} | 
                                <strong>Total Nilai Pemesanan:</strong> Rp ${new Intl.NumberFormat('id-ID').format(response.totalAmount)}</p>
                            </div>
                        `;
                        $('#dailyReportTable').append(summaryHtml);
                    }
                } else if (reportType === 'monthly') {
                    // Process monthly summary data
                    if (response.monthly_data && response.monthly_data.length > 0) {
                        let totalCount = 0;
                        let totalAmount = 0;

                        response.monthly_data.forEach(function(day) {
                            pemesananMonthlyTable.row.add([
                                day.date,
                                day.count,
                                'Rp ' + new Intl.NumberFormat('id-ID').format(day.total)
                            ]);

                            totalCount += parseInt(day.count);
                            totalAmount += parseInt(day.total);
                        });

                        pemesananMonthlyTable.draw();

                        // Display summary
                        const summaryHtml = `
                            <div class="mt-3">
                                <h5>Ringkasan:</h5>
                                <p><strong>Total Pemesanan:</strong> ${totalCount} | 
                                <strong>Total Nilai Pemesanan:</strong> Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}</p>
                            </div>
                        `;
                        $('#monthlyReportTable').append(summaryHtml);
                    }
                } else if (reportType === 'yearly') {
                    // Process yearly summary data
                    if (response.yearly_data && response.yearly_data.length > 0) {
                        response.yearly_data.forEach(function(month) {
                            pemesananYearlyTable.row.add([
                                month.month_name,
                                month.count,
                                'Rp ' + new Intl.NumberFormat('id-ID').format(month.total)
                            ]);
                        });

                        pemesananYearlyTable.draw();

                        // Display summary
                        if (response.totalAmount !== undefined && response.totalCount !== undefined) {
                            const summaryHtml = `
                                <div class="mt-3">
                                    <h5>Ringkasan Tahunan:</h5>
                                    <p><strong>Total Pemesanan:</strong> ${response.totalCount} | 
                                    <strong>Total Nilai Pemesanan:</strong> Rp ${new Intl.NumberFormat('id-ID').format(response.totalAmount)}</p>
                                </div>
                            `;
                            $('#yearlyReportTable').append(summaryHtml);
                        }
                    }
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memuat laporan pemesanan', 'error');

                // Clear loading message
                pemesananDailyTable.clear().draw();
                pemesananMonthlyTable.clear().draw();
                pemesananYearlyTable.clear().draw();
            }
        });
    }

    function generatePDF() {
        // Get filter values
        const {
            startDate,
            endDate,
            reportType
        } = getFilterDates();

        // Generate PDF with filters
        window.open(`/admin/generatePemesananReportPDF?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}`, '_blank');
    }

    function printReport() {
        // Get filter values
        const {
            startDate,
            endDate,
            reportType
        } = getFilterDates();

        // Open print preview with filters
        window.open(`/admin/generatePemesananReportPrint?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}`, '_blank');
    }
</script>
<?= $this->endSection() ?>
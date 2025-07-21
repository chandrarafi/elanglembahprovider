<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Laporan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Pembayaran</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <h5 class="mb-0">Laporan Pembayaran</h5>
        </div>
        <div class="mt-3">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filterType" class="form-label">Jenis Laporan</label>
                    <select class="form-select" id="filterType">
                        <option value="daily">Per-Tanggal</option>
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status Pembayaran</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>

            <!-- Daily Filter (Per-Tanggal) -->
            <div id="dailyFilter" class="row mb-3">
                <div class="col-md-3">
                    <label for="startDateDaily" class="form-label">Tanggal Awal</label>
                    <input type="date" class="form-control" id="startDateDaily" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label for="endDateDaily" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="endDateDaily" value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <!-- Monthly Filter -->
            <div id="monthlyFilter" class="row mb-3" style="display: none;">
                <div class="col-md-3">
                    <label for="monthlyDate" class="form-label">Bulan</label>
                    <input type="month" class="form-control" id="monthlyDate" value="<?= date('Y-m') ?>">
                </div>
            </div>

            <!-- Yearly Filter -->
            <div id="yearlyFilter" class="row mb-3" style="display: none;">
                <div class="col-md-3">
                    <label for="yearlyDate" class="form-label">Tahun</label>
                    <input type="number" class="form-control" id="yearlyDate" min="2020" max="2099" step="1" value="<?= date('Y') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" onclick="loadReport()">
                        <i class="bx bx-search"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-success" onclick="printReport()">
                        <i class="bx bx-printer"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-danger" onclick="generatePDF()">
                        <i class="bx bx-file"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Report Table -->
<div id="dailyReportSection" class="card">
    <div class="card-body">
        <h5 class="card-title">Laporan Pembayaran Per-Tanggal</h5>
        <div class="table-responsive">
            <table id="dailyReportTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Booking</th>
                        <th>Nama Paket</th>
                        <th>Pelanggan</th>
                        <th>Metode Pembayaran</th>
                        <th>Tipe Pembayaran</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-end">Total:</th>
                        <th id="totalAmount" colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Monthly Report Table -->
<div id="monthlyReportSection" class="card" style="display: none;">
    <div class="card-body">
        <h5 class="card-title">Laporan Pembayaran Bulanan</h5>
        <div class="table-responsive">
            <table id="monthlyReportTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah Pembayaran</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total:</th>
                        <th id="totalCountMonthly"></th>
                        <th id="totalAmountMonthly"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Yearly Report Table -->
<div id="yearlyReportSection" class="card" style="display: none;">
    <div class="card-body">
        <h5 class="card-title">Laporan Pembayaran Tahunan</h5>
        <div class="table-responsive">
            <table id="yearlyReportTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Jumlah Pembayaran</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded here -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total:</th>
                        <th id="totalCountYearly"></th>
                        <th id="totalAmountYearly"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize with current date
        loadReport();

        // Show/hide filters based on report type
        $('#filterType').change(function() {
            const filterType = $(this).val();

            // Hide all filter sections
            $('#dailyFilter, #monthlyFilter, #yearlyFilter').hide();
            $('#dailyReportSection, #monthlyReportSection, #yearlyReportSection').hide();

            // Show selected filter section
            switch (filterType) {
                case 'daily':
                    $('#dailyFilter').show();
                    $('#dailyReportSection').show();
                    break;
                case 'monthly':
                    $('#monthlyFilter').show();
                    $('#monthlyReportSection').show();
                    break;
                case 'yearly':
                    $('#yearlyFilter').show();
                    $('#yearlyReportSection').show();
                    break;
            }
        });
    });

    function loadReport() {
        const filterType = $('#filterType').val();

        switch (filterType) {
            case 'daily':
                loadDailyReport();
                break;
            case 'monthly':
                loadMonthlyReport();
                break;
            case 'yearly':
                loadYearlyReport();
                break;
        }
    }

    function loadDailyReport() {
        const startDate = $('#startDateDaily').val();
        const endDate = $('#endDateDaily').val();
        const status = $('#statusFilter').val();

        $.ajax({
            url: '/admin/getPembayaranReport',
            type: 'GET',
            data: {
                report_type: 'daily',
                start_date: startDate,
                end_date: endDate,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Clear table
                    $('#dailyReportTable tbody').empty();

                    // Add rows
                    let no = 1;
                    response.data.forEach(function(item) {
                        const statusBadge = getStatusBadge(item.status_pembayaran);
                        const formattedDate = new Date(item.tanggal_bayar).toLocaleDateString('id-ID');
                        const formattedAmount = formatRupiah(item.jumlah_bayar);

                        $('#dailyReportTable tbody').append(`
                            <tr>
                                <td>${no++}</td>
                                <td>${formattedDate}</td>
                                <td>${item.kode_booking}</td>
                                <td>${item.namapaket}</td>
                                <td>${item.nama_pelanggan}</td>
                                <td>${item.metode_pembayaran}</td>
                                <td>${item.tipe_pembayaran === 'dp' ? 'Down Payment' : 'Lunas'}</td>
                                <td class="text-end">${formattedAmount}</td>
                                <td>${statusBadge}</td>
                            </tr>
                        `);
                    });

                    // Update total
                    $('#totalAmount').text(formatRupiah(response.totalAmount));
                }
            },
            error: function(error) {
                console.error('Error loading report:', error);
                Swal.fire('Error', 'Gagal memuat laporan', 'error');
            }
        });
    }

    function loadMonthlyReport() {
        const monthlyDate = $('#monthlyDate').val();
        const status = $('#statusFilter').val();

        if (!monthlyDate) {
            Swal.fire('Error', 'Pilih bulan terlebih dahulu', 'error');
            return;
        }

        // Create start and end date for the selected month
        const [year, month] = monthlyDate.split('-');
        const startDate = `${monthlyDate}-01`;
        const lastDay = new Date(year, month, 0).getDate();
        const endDate = `${monthlyDate}-${lastDay}`;

        $.ajax({
            url: '/admin/getPembayaranReport',
            type: 'GET',
            data: {
                report_type: 'monthly',
                start_date: startDate,
                end_date: endDate,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Clear table
                    $('#monthlyReportTable tbody').empty();

                    // Add rows
                    let no = 1;
                    let totalCount = 0;
                    let totalAmount = 0;

                    response.monthly_data.forEach(function(item) {
                        const formattedAmount = formatRupiah(item.total);
                        totalCount += parseInt(item.count);
                        totalAmount += parseFloat(item.total);

                        $('#monthlyReportTable tbody').append(`
                            <tr>
                                <td>${no++}</td>
                                <td>${item.date_formatted}</td>
                                <td class="text-center">${item.count}</td>
                                <td class="text-end">${formattedAmount}</td>
                            </tr>
                        `);
                    });

                    // Update totals
                    $('#totalCountMonthly').text(totalCount);
                    $('#totalAmountMonthly').text(formatRupiah(totalAmount));
                }
            },
            error: function(error) {
                console.error('Error loading report:', error);
                Swal.fire('Error', 'Gagal memuat laporan', 'error');
            }
        });
    }

    function loadYearlyReport() {
        const yearlyDate = $('#yearlyDate').val();
        const status = $('#statusFilter').val();

        if (!yearlyDate) {
            Swal.fire('Error', 'Pilih tahun terlebih dahulu', 'error');
            return;
        }

        // Create start and end date for the selected year
        const startDate = `${yearlyDate}-01-01`;
        const endDate = `${yearlyDate}-12-31`;

        $.ajax({
            url: '/admin/getPembayaranReport',
            type: 'GET',
            data: {
                report_type: 'yearly',
                start_date: startDate,
                end_date: endDate,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Clear table
                    $('#yearlyReportTable tbody').empty();

                    // Add rows
                    let no = 1;

                    response.yearly_data.forEach(function(item) {
                        const formattedAmount = formatRupiah(item.total);

                        $('#yearlyReportTable tbody').append(`
                            <tr>
                                <td>${no++}</td>
                                <td>${item.month_name}</td>
                                <td class="text-center">${item.count}</td>
                                <td class="text-end">${formattedAmount}</td>
                            </tr>
                        `);
                    });

                    // Update totals
                    $('#totalCountYearly').text(response.totalCount);
                    $('#totalAmountYearly').text(formatRupiah(response.totalAmount));
                }
            },
            error: function(error) {
                console.error('Error loading report:', error);
                Swal.fire('Error', 'Gagal memuat laporan', 'error');
            }
        });
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

    function generatePDF() {
        // Get filter values
        const {
            startDate,
            endDate,
            reportType
        } = getFilterDates();
        const status = $('#statusFilter').val();

        // Generate PDF with filters
        window.open(`/admin/generatePembayaranReportPDF?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}&status=${status}`, '_blank');
    }

    function printReport() {
        // Get filter values
        const {
            startDate,
            endDate,
            reportType
        } = getFilterDates();
        const status = $('#statusFilter').val();

        // Open print preview with filters
        window.open(`/admin/generatePembayaranReportPrint?start_date=${startDate}&end_date=${endDate}&report_type=${reportType}&status=${status}`, '_blank');
    }

    function getStatusBadge(status) {
        switch (status) {
            case 'pending':
                return '<span class="badge bg-warning">Menunggu Verifikasi</span>';
            case 'verified':
                return '<span class="badge bg-success">Terverifikasi</span>';
            case 'rejected':
                return '<span class="badge bg-danger">Ditolak</span>';
            default:
                return '<span class="badge bg-secondary">' + status + '</span>';
        }
    }

    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }
</script>

<?= $this->endSection() ?>
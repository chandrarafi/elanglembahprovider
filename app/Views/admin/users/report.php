<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Laporan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan User</li>
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
        <div class="table-responsive">
            <table id="userReportTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Login Terakhir</th>
                        <th>Tanggal Daftar</th>
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
    <h3 style="margin: 10px 0; text-align: center;">Laporan Data User</h3>
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
        <div>
            <p>Tanggal Laporan: <span id="reportDate"></span></p>
        </div>
        <div>
            <!-- <p>Periode: <span id="reportPeriode"></span></p> -->
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let userReportTable;

    $(document).ready(function() {
        // Initialize DataTable
        initDataTable();

        // Event listeners
        $("#pdfBtn").click(function() {
            generatePDF();
        });

        $("#printBtn").click(function() {
            printReport();
        });

        // Set report date
        $('#reportDate').text(new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }));

        // Load report
        loadReport();
    });

    function initDataTable() {
        userReportTable = $('#userReportTable').DataTable({
            responsive: true,
            dom: 'frtip' // Removed the 'B' (buttons)
        });
    }

    function loadReport() {
        // Show loading
        userReportTable.clear().draw();
        $('#userReportTable tbody').html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: '/admin/getUsersReport',
            type: 'GET',
            success: function(response) {
                userReportTable.clear();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(user) {
                        const role = user.role;
                        let roleBadge = 'bg-secondary';
                        if (role === 'admin') roleBadge = 'bg-primary';
                        else if (role === 'direktur') roleBadge = 'bg-info';
                        else if (role === 'pelanggan') roleBadge = 'bg-success';

                        const status = user.status;
                        const statusBadge = status === 'active' ? 'bg-success' : 'bg-danger';

                        userReportTable.row.add([
                            user.id,
                            user.username,
                            user.name,
                            user.email,
                            `<span class="badge ${roleBadge}">${role}</span>`,
                            `<span class="badge ${statusBadge}">${status}</span>`,
                            user.phone || '-',
                            user.address || '-',
                            user.last_login ? new Date(user.last_login).toLocaleString() : '-',
                            new Date(user.created_at).toLocaleString()
                        ]);
                    });
                }

                userReportTable.draw();
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memuat laporan user', 'error');
                userReportTable.clear().draw();
            }
        });
    }

    function generatePDF() {
        // Generate PDF without filters
        window.open(`/admin/generateUserReportPDF`, '_blank');
    }

    function printReport() {
        // Open print preview without filters
        window.open(`/admin/generateUserReportPrint`, '_blank');
    }
</script>
<?= $this->endSection() ?>
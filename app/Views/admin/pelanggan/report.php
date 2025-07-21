<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Laporan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Pelanggan</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <button type="button" class="btn btn-danger px-3" id="exportPDF">
            <i class="bx bxs-file-pdf"></i> Export PDF
        </button>
        <!-- <button type="button" class="btn btn-secondary px-3" id="printReport">
            <i class="bx bx-printer"></i> Print
        </button> -->
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Username</th>
                        <th>Role</th>
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
    <h3 style="margin: 10px 0; text-align: center;">Laporan Data Pelanggan</h3>
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
        <div>
            <p>Tanggal Laporan: <span id="reportDate"></span></p>
        </div>
        <div>
            <p>Total Pelanggan: <span id="totalPelanggan">0</span></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let pelangganTable;

    $(document).ready(function() {
        // Initialize DataTable
        initDataTable();

        // Event listeners
        $("#exportPDF").click(function() {
            generatePDF();
        });

        $("#printReport").click(function() {
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
        pelangganTable = $('#dataTable').DataTable({
            responsive: true,
            dom: 'frtip' // Removed the 'B' (buttons)
        });
    }

    function loadReport() {
        // Show loading
        pelangganTable.clear().draw();
        $('#dataTable tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: '<?= base_url('admin/pelanggan/get-report') ?>',
            type: 'GET',
            success: function(response) {
                pelangganTable.clear();

                if (response.data && response.data.length > 0) {
                    $('#totalPelanggan').text(response.data.length);

                    response.data.forEach(function(pelanggan) {
                        const role = pelanggan.role || '-';
                        let roleBadge = 'bg-secondary';
                        if (role === 'admin') roleBadge = 'bg-primary';
                        else if (role === 'direktur') roleBadge = 'bg-info';
                        else if (role === 'pelanggan') roleBadge = 'bg-success';

                        pelangganTable.row.add([
                            pelanggan.idpelanggan,
                            pelanggan.namapelanggan,
                            pelanggan.email || '-',
                            pelanggan.nohp || '-',
                            pelanggan.alamat || '-',
                            pelanggan.username || '-',
                            `<span class="badge ${roleBadge}">${role}</span>`,
                            new Date(pelanggan.created_at).toLocaleDateString('id-ID')
                        ]);
                    });
                } else {
                    // If no data is available, show a message
                    $('#dataTable tbody').html('<tr><td colspan="8" class="text-center">Tidak ada data pelanggan yang tersedia</td></tr>');
                }

                pelangganTable.draw();
            },
            error: function(xhr) {
                // Display a user-friendly message in the table instead of an error popup
                $('#dataTable tbody').html('<tr><td colspan="8" class="text-center">Gagal memuat data. Silahkan coba lagi.</td></tr>');
                console.error('Error loading data:', xhr);
            }
        });
    }

    function generatePDF() {
        window.open('<?= base_url('admin/pelanggan/report-pdf') ?>', '_blank');
    }

    function printReport() {
        window.open('<?= base_url('admin/pelanggan/report-print') ?>', '_blank');
    }
</script>
<?= $this->endSection() ?>
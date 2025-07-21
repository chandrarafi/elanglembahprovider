<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Laporan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Paket Wisata</li>
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
                        <th>ID Paket</th>
                        <th>Nama Paket</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Min. Peserta</th>
                        <th>Max. Peserta</th>
                        <th>Status</th>
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
    <h3 style="margin: 10px 0; text-align: center;">Laporan Data Paket Wisata</h3>
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between;">
        <div>
            <p>Tanggal Laporan: <span id="reportDate"></span></p>
        </div>
        <div>
            <p>Total Paket: <span id="totalPaket">0</span></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let paketWisataTable;

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
        paketWisataTable = $('#dataTable').DataTable({
            responsive: true,
            dom: 'frtip' // Removed the 'B' (buttons)
        });
    }

    function loadReport() {
        // Show loading
        paketWisataTable.clear().draw();
        $('#dataTable tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: '<?= base_url('admin/paket-wisata/get-report') ?>',
            type: 'GET',
            success: function(response) {
                paketWisataTable.clear();

                if (response.data && response.data.length > 0) {
                    $('#totalPaket').text(response.data.length);

                    response.data.forEach(function(paket) {
                        const status = paket.statuspaket;
                        const statusBadge = status === 'active' ? 'bg-success' : 'bg-danger';
                        const statusText = status === 'active' ? 'Aktif' : 'Tidak Aktif';

                        paketWisataTable.row.add([
                            paket.idpaket,
                            paket.namapaket,
                            paket.namakategori,
                            'Rp ' + paket.harga,
                            paket.durasi + ' hari',
                            paket.minimalpeserta,
                            paket.maximalpeserta,
                            `<span class="badge ${statusBadge}">${statusText}</span>`
                        ]);
                    });
                }

                paketWisataTable.draw();
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memuat laporan paket wisata', 'error');
                paketWisataTable.clear().draw();
            }
        });
    }

    function generatePDF() {
        window.open('<?= base_url('admin/paket-wisata/report-pdf') ?>', '_blank');
    }

    function printReport() {
        window.open('<?= base_url('admin/paket-wisata/report-print') ?>', '_blank');
    }
</script>
<?= $this->endSection() ?>
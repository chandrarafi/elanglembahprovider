<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pemesanan</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="/admin"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="/admin/pemesanan">Manajemen Pemesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat Pemesanan Baru</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="/admin/pemesanan" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
    </div>
</div>

<!-- Alert for validation errors -->
<div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2 d-none" id="validationAlert">
    <div class="d-flex align-items-center">
        <div class="font-35 text-danger"><i class="bx bxs-message-square-x"></i></div>
        <div class="ms-3">
            <h6 class="mb-0 text-danger">Perhatian!</h6>
            <div id="alertMessage">Silakan pilih pelanggan terlebih dahulu!</div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-title d-flex align-items-center">
            <div><i class="bx bx-calendar-plus me-1 font-22 text-primary"></i></div>
            <h5 class="mb-0 text-primary">Form Pemesanan Baru</h5>
        </div>
        <hr>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-danger"><i class="bx bxs-message-square-x"></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-danger">Error!</h6>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php $validation = session()->getFlashdata('validation'); ?>

        <form action="<?= base_url('admin/pemesanan/store') ?>" method="POST" id="bookingForm">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pelanggan_name" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="pelanggan_name" name="pelanggan_name" placeholder="Pilih pelanggan" readonly>
                            <input type="hidden" id="iduser" name="iduser" value="<?= old('iduser') ?>">
                            <input type="hidden" id="idpelanggan" name="idpelanggan" value="<?= old('idpelanggan') ?>">
                            <button class="btn btn-primary" type="button" id="pilihPelangganBtn">
                                <i class="bx bx-search"></i> Pilih
                            </button>
                        </div>
                        <?php if (isset($validation['iduser']) || isset($validation['idpelanggan'])): ?>
                            <div class="text-danger"><?= isset($validation['iduser']) ? $validation['iduser'] : $validation['idpelanggan'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="paket_name" class="form-label">Paket Wisata <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="paket_name" name="paket_name" placeholder="Pilih paket wisata" readonly>
                            <input type="hidden" id="idpaket" name="idpaket" value="<?= old('idpaket') ?>">
                            <input type="hidden" id="paket_harga" name="paket_harga" value="">
                            <input type="hidden" id="paket_durasi" name="paket_durasi" value="">
                            <button class="btn btn-primary" type="button" id="pilihPaketBtn">
                                <i class="bx bx-search"></i> Pilih
                            </button>
                        </div>
                        <?php if (isset($validation['idpaket'])): ?>
                            <div class="text-danger"><?= $validation['idpaket'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tgl_berangkat" class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tgl_berangkat" name="tgl_berangkat"
                            value="<?= old('tgl_berangkat') ?? date('Y-m-d', strtotime('+1 day')) ?>"
                            min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        <?php if (isset($validation['tgl_berangkat'])): ?>
                            <div class="text-danger"><?= $validation['tgl_berangkat'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai"
                            value="<?= old('tgl_selesai') ?>" readonly>
                        <small class="text-muted">Tanggal selesai akan dihitung otomatis berdasarkan durasi paket</small>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jumlah_peserta" class="form-label">Jumlah Peserta <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta"
                            min="1" value="<?= old('jumlah_peserta') ?? 1 ?>" required>
                        <?php if (isset($validation['jumlah_peserta'])): ?>
                            <div class="text-danger"><?= $validation['jumlah_peserta'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= old('catatan') ?></textarea>
                    </div> -->
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Pemesanan <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select">
                            <option value="confirmed" selected>Confirmed (Pembayaran Offline)</option>
                            <option value="pending">Pending (Menunggu Pembayaran)</option>
                        </select>
                        <small class="text-muted">Untuk pemesanan offline, pilih "Confirmed"</small>
                    </div>

                    <div class="card border-0 rounded-4 shadow-sm bg-info bg-opacity-10 mt-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="font-35 text-info"><i class="bx bx-info-circle"></i></div>
                                <div class="ms-3">
                                    <h6 class="mb-1 fw-bold">Ringkasan Pemesanan</h6>
                                    <div id="paket-info" class="text-secondary mb-2">Silakan pilih paket wisata</div>
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <strong>Total Biaya:</strong>
                                        <span id="total-biaya" class="fs-5 text-primary fw-bold">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="d-md-flex align-items-center mt-3">
                        <div class="ms-auto">
                            <button type="reset" class="btn btn-light px-4">Reset</button>
                            <button type="submit" class="btn btn-primary px-4">Buat Pemesanan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pilih Pelanggan -->
<div class="modal fade" id="pelangganModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Search and display options -->
                <div class="p-3 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="d-flex align-items-center">
                                <label class="me-2">Tampilkan</label>
                                <select class="form-select form-select-sm" style="width: 60px;" id="showEntriesPelanggan">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <label class="ms-2">entri</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">Cari:</span>
                                <input type="text" class="form-control form-control-sm" id="searchPelanggan" placeholder="Cari pelanggan...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table content -->
                <div class="table-responsive">
                    <table id="tablePelanggan" class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $p): ?>
                                <tr>
                                    <td><?= $p['name'] ?></td>
                                    <td><?= $p['phone'] ?? '-' ?></td>
                                    <td><?= $p['email'] ?? '-' ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary pilih-pelanggan"
                                            data-id="<?= $p['id'] ?>"
                                            data-name="<?= $p['name'] ?>">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="showingInfoPelanggan">Menampilkan 1 sampai 5 dari <?= count($users) ?> entri</div>
                        <div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-secondary" id="prevPagePelanggan">Sebelumnya</button>
                                <button type="button" class="btn btn-sm btn-primary">1</button>
                                <button type="button" class="btn btn-sm btn-secondary">2</button>
                                <button type="button" class="btn btn-sm btn-secondary" id="nextPagePelanggan">Selanjutnya</button>
                            </div>
                            <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Paket -->
<div class="modal fade" id="paketModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Paket Wisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Search and display options -->
                <div class="p-3 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="d-flex align-items-center">
                                <label class="me-2">Tampilkan</label>
                                <select class="form-select form-select-sm" style="width: 60px;" id="showEntriesPaket">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <label class="ms-2">entri</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">Cari:</span>
                                <input type="text" class="form-control form-control-sm" id="searchPaket" placeholder="Cari paket wisata...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table content -->
                <div class="table-responsive">
                    <table id="tablePaket" class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama Paket</th>
                                <th>Durasi</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paket as $p): ?>
                                <tr>
                                    <td><?= $p['namapaket'] ?></td>
                                    <td><?= $p['durasi'] ?? 1 ?> hari</td>
                                    <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($p['statuspaket'] == 'active'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Non-aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary pilih-paket"
                                            data-id="<?= $p['idpaket'] ?>"
                                            data-name="<?= $p['namapaket'] ?>"
                                            data-harga="<?= $p['harga'] ?>"
                                            data-durasi="<?= $p['durasi'] ?? 1 ?>">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="showingInfoPaket">Menampilkan 1 sampai 5 dari <?= count($paket) ?> entri</div>
                        <div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-secondary" id="prevPagePaket">Sebelumnya</button>
                                <button type="button" class="btn btn-sm btn-primary">1</button>
                                <button type="button" class="btn btn-sm btn-secondary">2</button>
                                <button type="button" class="btn btn-sm btn-secondary" id="nextPagePaket">Selanjutnya</button>
                            </div>
                            <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize modals
        let pelangganModal = new bootstrap.Modal(document.getElementById('pelangganModal'));
        let paketModal = new bootstrap.Modal(document.getElementById('paketModal'));

        // Check if pelanggan and paket are already selected when page loads
        const checkPreselectedValues = function() {
            // Check if pelanggan_name has a value
            if ($('#pelanggan_name').val().trim() !== '') {
                // Hide validation error if present
                $('#validationAlert').addClass('d-none');
                $('#pelanggan_name').removeClass('is-invalid').addClass('is-valid');

                // Ensure iduser is set if name is displayed
                if (!$('#iduser').val()) {
                    // Try to find matching pelanggan by name
                    let foundMatch = false;
                    $('.pilih-pelanggan').each(function() {
                        if ($(this).data('name') === $('#pelanggan_name').val().trim()) {
                            $('#iduser').val($(this).data('id'));
                            foundMatch = true;
                            return false; // break loop
                        }
                    });
                }
            }

            // Check if paket_name has a value
            if ($('#paket_name').val().trim() !== '') {
                $('#paket_name').removeClass('is-invalid').addClass('is-valid');

                // Ensure related paket fields are set
                const paketName = $('#paket_name').val().trim();
                let foundMatch = false;

                $('.pilih-paket').each(function() {
                    if ($(this).data('name') === paketName) {
                        if (!$('#idpaket').val()) {
                            $('#idpaket').val($(this).data('id'));
                        }
                        if (!$('#paket_harga').val()) {
                            $('#paket_harga').val($(this).data('harga'));
                        }
                        if (!$('#paket_durasi').val()) {
                            $('#paket_durasi').val($(this).data('durasi'));
                        }

                        updatePackageInfo(paketName, $(this).data('harga'), $(this).data('durasi'));
                        calculateEndDate();

                        foundMatch = true;
                        return false; // break loop
                    }
                });

                // Update package info if we have all data
                if ($('#paket_name').val() && $('#paket_harga').val() && $('#paket_durasi').val()) {
                    updatePackageInfo(
                        $('#paket_name').val(),
                        $('#paket_harga').val(),
                        $('#paket_durasi').val()
                    );
                    calculateEndDate();
                }
            }
        };

        // Run check on page load
        checkPreselectedValues();

        // Open modals with buttons
        $('#pilihPelangganBtn').on('click', function() {
            pelangganModal.show();
        });

        $('#pilihPaketBtn').on('click', function() {
            paketModal.show();
        });

        // Simple approach without DataTables for pelanggan table
        const pelangganRows = $('#tablePelanggan tbody tr').get();
        const pelangganTotal = pelangganRows.length;
        let pelangganCurrentPage = 0;
        let pelangganItemsPerPage = 5;

        // Simple approach without DataTables for paket table  
        const paketRows = $('#tablePaket tbody tr').get();
        const paketTotal = paketRows.length;
        let paketCurrentPage = 0;
        let paketItemsPerPage = 5;

        // Function to paginate pelanggan table
        function displayPelangganRows() {
            const start = pelangganCurrentPage * pelangganItemsPerPage;
            const end = start + pelangganItemsPerPage;

            $('#tablePelanggan tbody tr').hide();

            for (let i = start; i < end && i < pelangganTotal; i++) {
                $(pelangganRows[i]).show();
            }

            // Update info text
            updatePelangganInfo();
        }

        // Update info text for pelanggan
        function updatePelangganInfo() {
            const start = pelangganCurrentPage * pelangganItemsPerPage + 1;
            const end = Math.min((pelangganCurrentPage + 1) * pelangganItemsPerPage, pelangganTotal);
            $('#showingInfoPelanggan').text(`Menampilkan ${start} sampai ${end} dari ${pelangganTotal} entri`);
        }

        // Function to paginate paket table
        function displayPaketRows() {
            const start = paketCurrentPage * paketItemsPerPage;
            const end = start + paketItemsPerPage;

            $('#tablePaket tbody tr').hide();

            for (let i = start; i < end && i < paketTotal; i++) {
                $(paketRows[i]).show();
            }

            // Update info text
            updatePaketInfo();
        }

        // Update info text for paket
        function updatePaketInfo() {
            const start = paketCurrentPage * paketItemsPerPage + 1;
            const end = Math.min((paketCurrentPage + 1) * paketItemsPerPage, paketTotal);
            $('#showingInfoPaket').text(`Menampilkan ${start} sampai ${end} dari ${paketTotal} entri`);
        }

        // Initialize pagination for pelanggan
        displayPelangganRows();

        // Initialize pagination for paket
        displayPaketRows();

        // Pelanggan pagination controls
        $('#prevPagePelanggan').on('click', function() {
            if (pelangganCurrentPage > 0) {
                pelangganCurrentPage--;
                displayPelangganRows();
            }
        });

        $('#nextPagePelanggan').on('click', function() {
            if ((pelangganCurrentPage + 1) * pelangganItemsPerPage < pelangganTotal) {
                pelangganCurrentPage++;
                displayPelangganRows();
            }
        });

        // Paket pagination controls
        $('#prevPagePaket').on('click', function() {
            if (paketCurrentPage > 0) {
                paketCurrentPage--;
                displayPaketRows();
            }
        });

        $('#nextPagePaket').on('click', function() {
            if ((paketCurrentPage + 1) * paketItemsPerPage < paketTotal) {
                paketCurrentPage++;
                displayPaketRows();
            }
        });

        // Entries per page for pelanggan
        $('#showEntriesPelanggan').on('change', function() {
            pelangganItemsPerPage = parseInt($(this).val());
            pelangganCurrentPage = 0; // Reset to first page
            displayPelangganRows();
        });

        // Entries per page for paket
        $('#showEntriesPaket').on('change', function() {
            paketItemsPerPage = parseInt($(this).val());
            paketCurrentPage = 0; // Reset to first page
            displayPaketRows();
        });

        // Search function for pelanggan
        $('#searchPelanggan').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                // Reset to show all rows if search is cleared
                $('#tablePelanggan tbody tr').show();
                pelangganCurrentPage = 0;
                displayPelangganRows();
                return;
            }

            // Hide all rows first
            $('#tablePelanggan tbody tr').hide();

            // Show matching rows
            $('#tablePelanggan tbody tr').filter(function() {
                return $(this).text().toLowerCase().indexOf(searchTerm) > -1;
            }).show();
        });

        // Search function for paket
        $('#searchPaket').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                // Reset to show all rows if search is cleared
                $('#tablePaket tbody tr').show();
                paketCurrentPage = 0;
                displayPaketRows();
                return;
            }

            // Hide all rows first
            $('#tablePaket tbody tr').hide();

            // Show matching rows
            $('#tablePaket tbody tr').filter(function() {
                return $(this).text().toLowerCase().indexOf(searchTerm) > -1;
            }).show();
        });

        // Pelanggan selection
        $('.pilih-pelanggan').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#iduser').val(id);
            $('#idpelanggan').val(id); // Set idpelanggan
            $('#pelanggan_name').val(name);

            // Add visual feedback and hide error if present
            $('#pelanggan_name').removeClass('is-invalid').addClass('is-valid');
            $('#validationAlert').addClass('d-none');

            setTimeout(function() {
                $('#pelanggan_name').removeClass('is-valid');
            }, 2000);

            pelangganModal.hide();
        });

        // Paket selection
        $('.pilih-paket').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var harga = $(this).data('harga');
            var durasi = $(this).data('durasi');

            // No validation for id type since idpaket is a char field
            // Just ensure it's not empty or undefined
            if (!id) {
                console.error('Invalid package ID:', $(this).data('id'));
                return;
            }

            $('#idpaket').val(id);
            $('#paket_name').val(name);
            $('#paket_harga').val(harga);
            $('#paket_durasi').val(durasi);

            // Add visual feedback
            $('#paket_name').removeClass('is-invalid').addClass('is-valid');

            setTimeout(function() {
                $('#paket_name').removeClass('is-valid');
            }, 2000);

            updatePackageInfo(name, harga, durasi);
            calculateEndDate();

            paketModal.hide();
        });

        // Calculate end date based on package duration
        function calculateEndDate() {
            const durasi = parseInt($('#paket_durasi').val() || 1);
            const startDate = $('#tgl_berangkat').val();

            if (durasi && startDate) {
                // Calculate end date (start date + duration - 1 day)
                const start = new Date(startDate);
                const end = new Date(start);
                end.setDate(start.getDate() + durasi);

                // Format date as YYYY-MM-DD
                const endFormatted = end.toISOString().split('T')[0];
                $('#tgl_selesai').val(endFormatted);
            }
        }

        // Update package info and total price
        function updatePackageInfo(name, harga, durasi) {
            if (name && harga && durasi) {
                $('#paket-info').html(`
                    <strong>${name}</strong><br>
                    Durasi: ${durasi} hari<br>
                    Harga: Rp ${new Intl.NumberFormat('id-ID').format(harga)}
                `);

                $('#total-biaya').text(`Rp ${new Intl.NumberFormat('id-ID').format(harga)}`);
            } else {
                $('#paket-info').text('Silakan pilih paket wisata');
                $('#total-biaya').text('Rp 0');
            }
        }

        // Event listener for date change
        $('#tgl_berangkat').on('change', calculateEndDate);

        // Form validation before submit
        $('#bookingForm').on('submit', function(e) {
            let isValid = true;
            let message = '';

            // Check if user is selected - check both field AND hidden input
            if (!$('#iduser').val() && !$('#idpelanggan').val()) {
                isValid = false;
                message = 'Silakan pilih pelanggan terlebih dahulu!';
                $('#pelanggan_name').addClass('is-invalid');
            } else {
                $('#pelanggan_name').removeClass('is-invalid');
            }

            // Check if package is selected - check both field AND hidden input
            if (!$('#idpaket').val() || $('#paket_name').val().trim() === '') {
                isValid = false;
                message = message || 'Silakan pilih paket wisata terlebih dahulu!';
                $('#paket_name').addClass('is-invalid');
            } else {
                $('#paket_name').removeClass('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                // Show alert
                $('#alertMessage').text(message);
                $('#validationAlert').removeClass('d-none').addClass('show');

                // Scroll to the top of the form to show the alert
                $('html, body').animate({
                    scrollTop: $('#validationAlert').offset().top - 100
                }, 500);

                return false;
            }

            return true;
        });

        // Reset button handler
        $('button[type="reset"]').on('click', function() {
            $('#iduser').val('');
            $('#idpelanggan').val('');
            $('#pelanggan_name').val('');
            $('#idpaket').val('');
            $('#paket_name').val('');
            $('#paket_harga').val('');
            $('#paket_durasi').val('');
            $('#paket-info').text('Silakan pilih paket wisata');
            $('#total-biaya').text('Rp 0');

            // Remove any validation styles
            $('.is-valid').removeClass('is-valid');
            $('.is-invalid').removeClass('is-invalid');

            // Hide any alerts
            $('#validationAlert').addClass('d-none').removeClass('show');
        });
    });
</script>
<?= $this->endSection() ?>
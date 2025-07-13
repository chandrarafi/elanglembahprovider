<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Pemesanan</p>
                        <h4 class="my-1 text-info">485</h4>
                        <p class="mb-0 font-13">+2.5% dari minggu lalu</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto">
                        <i class='bx bxs-cart'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Pendapatan</p>
                        <h4 class="my-1 text-danger">Rp 84.245.000</h4>
                        <p class="mb-0 font-13">+5.4% dari minggu lalu</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto">
                        <i class='bx bxs-wallet'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Tingkat Konversi</p>
                        <h4 class="my-1 text-success">34.6%</h4>
                        <p class="mb-0 font-13">-4.5% dari minggu lalu</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                        <i class='bx bxs-bar-chart-alt-2'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Pelanggan</p>
                        <h4 class="my-1 text-warning">8.4K</h4>
                        <p class="mb-0 font-13">+8.4% dari minggu lalu</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto">
                        <i class='bx bxs-group'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-8 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Statistik Pemesanan</h6>
                    </div>
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:;">Hari Ini</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Minggu Ini</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Bulan Ini</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center ms-auto font-13 gap-2 mb-3">
                    <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1" style="color: #14abef"></i>Pemesanan</span>
                    <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1" style="color: #ffc107"></i>Pengunjung</span>
                </div>
                <div class="chart-container-1">
                    <canvas id="chart1"></canvas>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-3 row-cols-xl-3 g-0 row-group text-center border-top">
                <div class="col">
                    <div class="p-3">
                        <h5 class="mb-0">24.15K</h5>
                        <small class="mb-0">Total Pengunjung <span> <i class="bx bx-up-arrow-alt align-middle"></i> 2.43%</span></small>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3">
                        <h5 class="mb-0">12:38</h5>
                        <small class="mb-0">Durasi Kunjungan <span> <i class="bx bx-up-arrow-alt align-middle"></i> 12.65%</span></small>
                    </div>
                </div>
                <div class="col">
                    <div class="p-3">
                        <h5 class="mb-0">639.82</h5>
                        <small class="mb-0">Halaman/Kunjungan <span> <i class="bx bx-up-arrow-alt align-middle"></i> 5.62%</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Destinasi Populer</h6>
                    </div>
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:;">Minggu Ini</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Bulan Ini</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Tahun Ini</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-2">
                    <canvas id="chart2"></canvas>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center border-top">
                    Bali <span class="badge bg-success rounded-pill">25</span>
                </li>
                <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">
                    Raja Ampat <span class="badge bg-danger rounded-pill">10</span>
                </li>
                <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">
                    Labuan Bajo <span class="badge bg-primary rounded-pill">65</span>
                </li>
                <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">
                    Lombok <span class="badge bg-warning text-dark rounded-pill">14</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="card radius-10">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div>
                <h6 class="mb-0">Pemesanan Terbaru</h6>
            </div>
            <div class="dropdown ms-auto">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                    <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="javascript:;">Lihat Semua</a></li>
                    <li><a class="dropdown-item" href="javascript:;">Export PDF</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paket Wisata</th>
                        <th>Pelanggan</th>
                        <th>ID Pemesanan</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Paket Wisata Bali 3D2N</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" class="rounded-circle" width="40" height="40" alt="">
                                <div class="ms-2">
                                    <h6 class="mb-0 font-14">John Doe</h6>
                                    <p class="mb-0 font-13 text-secondary">john@example.com</p>
                                </div>
                            </div>
                        </td>
                        <td>#BK-9405822</td>
                        <td>
                            <span class="badge bg-gradient-quepal text-white shadow-sm w-100">Lunas</span>
                        </td>
                        <td>Rp 5.250.000</td>
                        <td>03 Feb 2024</td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-gradient-quepal" role="progressbar" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Paket Wisata Raja Ampat 4D3N</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('assets/images/avatars/avatar-2.png') ?>" class="rounded-circle" width="40" height="40" alt="">
                                <div class="ms-2">
                                    <h6 class="mb-0 font-14">Jane Smith</h6>
                                    <p class="mb-0 font-13 text-secondary">jane@example.com</p>
                                </div>
                            </div>
                        </td>
                        <td>#BK-8304620</td>
                        <td>
                            <span class="badge bg-gradient-blooker text-white shadow-sm w-100">Pending</span>
                        </td>
                        <td>Rp 8.500.000</td>
                        <td>05 Feb 2024</td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-gradient-blooker" role="progressbar" style="width: 60%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-7 col-xl-8 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Persebaran Pelanggan</h6>
                    </div>
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:;">Export PDF</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Export Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-7 col-xl-8 border-end">
                        <div id="geographic-map-2"></div>
                    </div>
                    <div class="col-lg-5 col-xl-4">
                        <div class="mb-4">
                            <p class="mb-2"><i class="flag-icon flag-icon-id me-1"></i> Indonesia <span class="float-end">70%</span></p>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" style="width: 70%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="mb-2"><i class="flag-icon flag-icon-my me-1"></i> Malaysia <span class="float-end">65%</span></p>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: 65%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="mb-2"><i class="flag-icon flag-icon-sg me-1"></i> Singapura <span class="float-end">60%</span></p>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: 60%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="mb-2"><i class="flag-icon flag-icon-au me-1"></i> Australia <span class="float-end">55%</span></p>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" style="width: 55%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5 col-xl-4 d-flex">
        <div class="card w-100 radius-10">
            <div class="card-body">
                <div class="card radius-10 border shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Likes</p>
                                <h4 class="my-1">45.6K</h4>
                                <p class="mb-0 font-13">+6.2% dari minggu lalu</p>
                            </div>
                            <div class="widgets-icons-2 bg-gradient-cosmic text-white ms-auto">
                                <i class='bx bxs-heart-circle'></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card radius-10 border shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Komentar</p>
                                <h4 class="my-1">25.6K</h4>
                                <p class="mb-0 font-13">+3.7% dari minggu lalu</p>
                            </div>
                            <div class="widgets-icons-2 bg-gradient-ibiza text-white ms-auto">
                                <i class='bx bxs-comment-detail'></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card radius-10 mb-0 border shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Share</p>
                                <h4 class="my-1">85.4K</h4>
                                <p class="mb-0 font-13">+4.6% dari minggu lalu</p>
                            </div>
                            <div class="widgets-icons-2 bg-gradient-kyoto text-dark ms-auto">
                                <i class='bx bxs-share-alt'></i>
                            </div>
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
        // Load dashboard data
        $.ajax({
            url: '<?= site_url('admin/getUsers') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.data) {
                    // Count total users
                    $('#totalUsers').text(response.recordsTotal);

                    // Count active users
                    let activeUsers = 0;
                    // Count admin users
                    let adminUsers = 0;
                    // Count inactive users
                    let inactiveUsers = 0;

                    $.each(response.data, function(i, item) {
                        if (item.status === 'active') {
                            activeUsers++;
                        } else {
                            inactiveUsers++;
                        }
                        if (item.role === 'admin') {
                            adminUsers++;
                        }
                    });

                    $('#activeUsers').text(activeUsers);
                    $('#adminUsers').text(adminUsers);
                    $('#inactiveUsers').text(inactiveUsers);
                }
            }
        });

        // Initialize DataTable for recent users
        $('#recentUsers').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('admin/getUsers') ?>',
                type: 'GET'
            },
            columns: [{
                    data: 'username'
                },
                {
                    data: 'name'
                },
                {
                    data: 'role',
                    render: function(data) {
                        let badgeClass = 'bg-secondary';

                        if (data === 'admin') {
                            badgeClass = 'bg-primary';
                        } else if (data === 'manager') {
                            badgeClass = 'bg-info';
                        } else if (data === 'user') {
                            badgeClass = 'bg-dark';
                        }

                        return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        if (data === 'active') {
                            return '<span class="badge bg-success">Aktif</span>';
                        } else {
                            return '<span class="badge bg-danger">Tidak Aktif</span>';
                        }
                    }
                }
            ],
            order: [
                [0, 'desc']
            ],
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            dom: 't',
            responsive: true,
            language: {
                emptyTable: "Tidak ada data pengguna",
                zeroRecords: "Tidak ada data pengguna yang cocok",
                info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Initialize User Stats Chart
        var options = {
            series: [{
                name: 'Total Pengguna',
                data: [31, 40, 28, 51, 42, 109, 100, 120, 110, 125, 140, 150]
            }, {
                name: 'Pengguna Aktif',
                data: [25, 32, 25, 40, 39, 90, 85, 100, 95, 110, 120, 130]
            }, {
                name: 'Admin',
                data: [5, 5, 5, 6, 6, 8, 8, 8, 9, 9, 10, 10]
            }],
            chart: {
                height: 320,
                type: 'area',
                fontFamily: 'Nunito, sans-serif',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                labels: {
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return Math.round(value);
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " pengguna";
                    }
                },
                theme: 'dark',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Nunito, sans-serif'
                }
            },
            colors: ['#2c3e50', '#27ae60', '#f39c12'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                padding: {
                    left: 15,
                    right: 15
                }
            },
            markers: {
                size: 4,
                strokeWidth: 0,
                hover: {
                    size: 6
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                offsetY: -30,
                fontSize: '13px',
                fontFamily: 'Nunito, sans-serif',
                markers: {
                    width: 10,
                    height: 10,
                    radius: 100
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                }
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        offsetY: 0
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#userStatsChart"), options);
        chart.render();

        // Handle task checkbox behavior
        $('.form-check-input').on('change', function() {
            var label = $(this).parent().next('div').find('.fw-bold');
            if (this.checked) {
                label.css('text-decoration', 'line-through');
                label.css('opacity', '0.5');
            } else {
                label.css('text-decoration', 'none');
                label.css('opacity', '1');
            }
        });

        // Handle save task
        $('#saveTask').on('click', function() {
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
            $(this).attr('disabled', true);

            setTimeout(function() {
                $('#saveTask').html('<i class="bi bi-save me-1"></i> Simpan');
                $('#saveTask').attr('disabled', false);

                $('#taskModal').modal('hide');

                Swal.fire({
                    title: 'Sukses',
                    text: 'Tugas berhasil ditambahkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }, 1000);
        });

        // Add animation on scroll for task items
        function animateOnScroll() {
            $('.task-item').each(function(i) {
                setTimeout(function() {
                    $('.task-item').eq(i).addClass('animate__animated animate__fadeInRight');
                }, 300 * i);
            });
        }

        animateOnScroll();
    });
</script>
<?= $this->endSection() ?>
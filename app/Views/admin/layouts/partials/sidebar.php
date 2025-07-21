<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?= base_url('assets/images/logo-icon.png') ?>" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Travel Admin</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="<?= base_url('admin/dashboard') ?>">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li></li>
        <a href="<?= base_url('admin/users') ?>">
            <div class="parent-icon"><i class='bx bx-user'></i>
            </div>
            <div class="menu-title">Manajemen User</div>
        </a>
        </li>
        <li>
            <a href="<?= base_url('admin/pelanggan') ?>">
                <div class="parent-icon"><i class='bx bx-group'></i>
                </div>
                <div class="menu-title">Kelola Pelanggan</div>
            </a>
        </li>
        <li>
            <a href="<?= base_url('admin/kategori') ?>">
                <div class="parent-icon"><i class='bx bx-category'></i>
                </div>
                <div class="menu-title">Kelola Kategori</div>
            </a>
        </li>
        <li>
            <a href="<?= base_url('admin/paket') ?>">
                <div class="parent-icon"><i class='bx bx-package'></i>
                </div>
                <div class="menu-title">Kelola Paket Wisata</div>
            </a>
        </li>
        <li>
            <a href="<?= base_url('admin/pemesanan') ?>">
                <div class="parent-icon"><i class='bx bx-calendar-check'></i>
                </div>
                <div class="menu-title">Kelola Pemesanan</div>
            </a>
        </li>
        <li>
            <a href="<?= base_url('admin/reschedule') ?>">
                <div class="parent-icon"><i class='bx bx-calendar-edit'></i>
                </div>
                <div class="menu-title">Kelola Ubah Jadwal</div>
            </a>
        </li>
        <!-- Nav Item - Reports Collapse Menu -->
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-chart'></i>
                </div>
                <div class="menu-title">Laporan</div>
            </a>
            <ul>
                <li>
                    <a href="<?= base_url('admin/users/report') ?>">
                        <i class="bx bx-right-arrow-alt"></i>Laporan User
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/pelanggan/report') ?>">
                        <i class="bx bx-right-arrow-alt"></i>Laporan Pelanggan
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/paket-wisata/report') ?>">
                        <i class="bx bx-right-arrow-alt"></i>Laporan Paket Wisata
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/pemesanan/report') ?>">
                        <i class="bx bx-right-arrow-alt"></i>Laporan Pemesanan
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/pembayaranReport') ?>">
                        <i class="bx bx-right-arrow-alt"></i>Laporan Pembayaran
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
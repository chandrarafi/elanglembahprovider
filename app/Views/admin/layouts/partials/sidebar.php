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
        <li>
            <a href="<?= base_url('admin/users') ?>">
                <div class="parent-icon"><i class='bx bx-user'></i>
                </div>
                <div class="menu-title">Kelola User</div>
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
    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
<!--header-->
<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>

            <div class="search-bar d-lg-block d-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                <a href="javascript:;" class="btn d-flex align-items-center"><i class='bx bx-search'></i>Cari Destinasi</a>
            </div>

            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                        <a class="nav-link" href="javascript:;"><i class='bx bx-search'></i></a>
                    </li>
                    <li class="nav-item dropdown dropdown-laungauge d-none d-sm-flex">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                            <img src="<?= base_url('assets/flags/4x3/id.svg') ?>" width="20" height="14" alt="Indonesia">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="<?= base_url('assets/flags/4x3/id.svg') ?>" width="20" height="14" alt="Indonesia"><span class="ms-2">Indonesia</span></a>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="<?= base_url('assets/flags/4x3/gb.svg') ?>" width="20" height="14" alt="English"><span class="ms-2">English</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i></a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown">
                            <span class="alert-count">7</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifikasi</p>
                                    <p class="msg-header-badge">8 Baru</p>
                                </div>
                            </a>
                            <div class="header-notifications-list">
                                <!-- Notification items here -->
                            </div>
                            <a href="javascript:;">
                                <div class="text-center msg-footer">
                                    <button class="btn btn-primary w-100">Lihat Semua Notifikasi</button>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- User Profile -->
                    <div class="user-box dropdown px-3">
                        <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= base_url('assets/images/avatars/avatar-1.png') ?>" class="user-img" alt="user avatar">
                            <div class="user-info">
                                <p class="user-name mb-0"><?= session()->get('name') ?? 'Admin' ?></p>
                                <p class="designattion mb-0"><?= ucfirst(session()->get('role') ?? 'Administrator') ?></p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i class="bx bx-user fs-5"></i><span>Profile</span></a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i class="bx bx-cog fs-5"></i><span>Pengaturan</span></a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i class="bx bx-home-circle fs-5"></i><span>Dashboard</span></a></li>
                            <li>
                                <div class="dropdown-divider mb-0"></div>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:;" onclick="confirmLogout()"><i class="bx bx-log-out-circle"></i><span>Logout</span></a></li>
                        </ul>
                    </div>
                </ul>
            </div>
        </nav>
    </div>
</header>
<!--end header -->
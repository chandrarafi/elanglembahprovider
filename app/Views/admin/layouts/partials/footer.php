<!--start overlay-->
<div class="overlay toggle-icon"></div>
<!--end overlay-->

<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<!--End Back To Top Button-->

<footer class="page-footer">
    <p class="mb-0">Copyright © <?= date('Y') ?>. Elang Lembah Travel. All rights reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!--plugins-->
<script src="<?= base_url('assets/plugins/simplebar/js/simplebar.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/metismenu/js/metisMenu.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') ?>"></script>
<script src="<?= base_url('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') ?>"></script>
<script src="<?= base_url('assets/js/jvectormap-fix.js') ?>"></script>
<script src="<?= base_url('assets/plugins/chartjs/js/chart.js') ?>"></script>
<script src="<?= base_url('assets/js/index.js') ?>"></script>
<!--app JS-->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<!-- Additional Scripts -->
<?= $this->renderSection('scripts') ?>

<script>
    new PerfectScrollbar(".app-container")

    // Fungsi konfirmasi logout
    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: "Apakah Anda yakin ingin keluar dari sistem?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= site_url('auth/logout') ?>";
            }
        });
    }
</script>
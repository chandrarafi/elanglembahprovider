<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

// Halaman Publik
$routes->get('/paket', 'Paket::index');
$routes->get('/paket/detail/(:segment)', 'Paket::detail/$1');
$routes->get('/kategori', 'Kategori::index');
$routes->get('/kategori/(:segment)', 'Kategori::show/$1');
$routes->get('/about', 'About::index');

// Auth Routes (tidak perlu filter auth)
$routes->group('auth', function ($routes) {
    $routes->get('', 'Auth::index');
    $routes->get('login', 'Auth::index');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});

// Profile Routes (perlu filter auth)
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Profile::index');
    $routes->post('update', 'Profile::update');
});

// Booking History Routes (perlu filter auth)
$routes->group('booking', ['filter' => 'auth'], function ($routes) {
    $routes->get('history', 'Booking::history');
    $routes->get('detail/(:num)', 'Booking::detail/$1');
    $routes->get('create/(:segment)', 'Booking::create/$1');
    $routes->post('store', 'Booking::store');
    $routes->get('cancel/(:num)', 'Booking::cancel/$1');
    $routes->post('cancel/(:num)', 'Booking::cancel/$1');
    $routes->get('payment/(:num)', 'Booking::payment/$1');
    $routes->post('savePayment', 'Booking::savePayment');
    $routes->get('checkPaymentExpiration/(:num)', 'Booking::checkPaymentExpiration/$1');
    $routes->post('checkAvailability', 'Booking::checkAvailability');
});

// Admin routes (perlu filter auth dan role admin)
$routes->group('admin', ['filter' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('dashboard', 'Admin::index');

    // User Management
    $routes->get('users', 'Admin::users');
    $routes->get('getUsers', 'Admin::getUsers');
    $routes->get('getRoles', 'Admin::getRoles');
    $routes->get('getUser/(:num)', 'Admin::getUser/$1');
    $routes->post('createUser', 'Admin::createUser');
    $routes->post('updateUser/(:num)', 'Admin::updateUser/$1');
    $routes->delete('deleteUser/(:num)', 'Admin::deleteUser/$1');

    // User Report
    $routes->get('users/report', 'Admin::usersReport');
    $routes->get('getUsersReport', 'Admin::getUsersReport');
    $routes->get('generateUserReportPDF', 'Admin::generateUserReportPDF');
    $routes->get('generateUserReportPrint', 'Admin::generateUserReportPrint');

    // Kategori Management
    $routes->get('kategori', 'Kategori::admin');
    $routes->get('getKategori', 'Kategori::getKategori');
    $routes->get('getKategori/(:segment)', 'Kategori::getKategoriById/$1');
    $routes->post('createKategori', 'Kategori::createKategori');
    $routes->post('updateKategori/(:segment)', 'Kategori::updateKategori/$1');
    $routes->delete('deleteKategori/(:segment)', 'Kategori::deleteKategori/$1');

    // Paket Wisata Management
    $routes->get('paket', 'Paket::admin');
    $routes->get('paket/create', 'Paket::create');
    $routes->get('paket/edit/(:segment)', 'Paket::edit/$1');
    $routes->get('getPaket', 'Paket::getPaket');
    $routes->get('paket/show/(:segment)', 'Paket::show/$1');
    $routes->post('paket/store', 'Paket::store');
    $routes->post('paket/update/(:segment)', 'Paket::update/$1');
    $routes->delete('paket/delete/(:segment)', 'Paket::delete/$1');

    // Pelanggan Management
    $routes->get('pelanggan', 'Pelanggan::index');
    $routes->get('getPelanggan', 'Pelanggan::getPelanggan');
    $routes->get('getPelangganById/(:num)', 'Pelanggan::getPelangganById/$1');
    $routes->post('createPelanggan', 'Pelanggan::createPelanggan');
    $routes->post('updatePelanggan/(:num)', 'Pelanggan::updatePelanggan/$1');
    $routes->delete('deletePelanggan/(:num)', 'Pelanggan::deletePelanggan/$1');

    // Pemesanan Management
    $routes->get('pemesanan', 'Admin\Pemesanan::index');
    $routes->get('pemesanan/getPemesanan', 'Admin\Pemesanan::getPemesanan');
    $routes->get('pemesanan/detail/(:num)', 'Admin\Pemesanan::detail/$1');
    $routes->post('pemesanan/updateStatus/(:num)', 'Admin\Pemesanan::updateStatus/$1');
    $routes->post('pemesanan/verifyPayment/(:num)', 'Admin\Pemesanan::verifyPayment/$1');
    $routes->get('pemesanan/create', 'Admin\Pemesanan::create');
    $routes->post('pemesanan/store', 'Admin\Pemesanan::store');
    $routes->get('pemesanan/edit/(:num)', 'Admin\Pemesanan::edit/$1');
    $routes->post('pemesanan/update/(:num)', 'Admin\Pemesanan::update/$1');
    $routes->get('pemesanan/destroy/(:num)', 'Admin\Pemesanan::destroy/$1');


    $routes->get('reschedule', 'Admin\RescheduleRequest::index');
    $routes->get('reschedule/view/(:num)', 'Admin\RescheduleRequest::view/$1');
    $routes->post('reschedule/process', 'Admin\RescheduleRequest::process');
    $routes->get('reschedule/createRequest/(:num)', 'Admin\RescheduleRequest::createRequest/$1');
    $routes->post('reschedule/submitRequest', 'Admin\RescheduleRequest::submitRequest');
});

// Tambahkan routes berikut
$routes->get('register', 'Register::index');
$routes->post('register/process', 'Register::process');
$routes->get('register/verify', 'Register::verify');
$routes->add('register/verifyOtp', 'Register::verifyOtp');
$routes->add('register/resendOtp', 'Register::resendOtp');

// Booking routes
$routes->get('/booking/create/(:num)', 'Booking::create/$1');
$routes->post('/booking/store', 'Booking::store');
$routes->get('/booking/detail/(:num)', 'Booking::detail/$1');
$routes->get('/booking/payment/(:num)', 'Booking::payment/$1');
$routes->post('/booking/savePayment', 'Booking::savePayment');
$routes->get('/booking/cancel/(:num)', 'Booking::cancel/$1');
$routes->get('/booking/history', 'Booking::history');
$routes->get('/booking/downloadTicket/(:num)', 'Booking::downloadTicket/$1');
$routes->get('/booking/downloadInvoice/(:num)', 'Booking::downloadInvoice/$1');

// Ticket verification routes
$routes->get('/verify', 'Verify::index');
$routes->get('/verify/ticket/(:any)', 'Verify::ticket/$1');

// Test route - remove in production
$routes->get('/booking/testCreatePayment/(:num)', 'Booking::testCreatePayment/$1');

// API Routes untuk background check
$routes->get('/api/check-expired-payments', 'Api::checkExpiredPayments');

// Reschedule routes
$routes->get('reschedule/request/(:num)', 'Reschedule::request/$1');
$routes->post('reschedule/submit', 'Reschedule::submit');
$routes->get('reschedule/history/(:num)', 'Reschedule::history/$1');
$routes->get('reschedule/cancel/(:num)', 'Reschedule::cancel/$1');

// Paket Wisata Report Routes
$routes->get('/admin/paket-wisata/report', 'Admin::paketWisataReport');
$routes->get('/admin/paket-wisata/get-report', 'Admin::getPaketWisataReport');
$routes->get('/admin/paket-wisata/report-pdf', 'Admin::generatePaketWisataReportPDF');
$routes->get('/admin/paket-wisata/report-print', 'Admin::generatePaketWisataReportPrint');

// Pelanggan Report Routes
$routes->get('/admin/pelanggan/report', 'Pelanggan::report');
$routes->get('/admin/pelanggan/get-report', 'Pelanggan::getReport');
$routes->get('/admin/pelanggan/report-pdf', 'Pelanggan::generateReportPDF');
$routes->get('/admin/pelanggan/report-print', 'Pelanggan::generateReportPrint');

// Pemesanan Report Routes
$routes->get('/admin/pemesanan/report', 'Admin::pemesananReport');
$routes->get('/admin/getPemesananReport', 'Admin::getPemesananReport');
$routes->get('/admin/generatePemesananReportPDF', 'Admin::generatePemesananReportPDF');
$routes->get('/admin/generatePemesananReportPrint', 'Admin::generatePemesananReportPrint');

// Payment Reports
$routes->get('admin/pembayaranReport', 'Admin::pembayaranReport');
$routes->get('admin/getPembayaranReport', 'Admin::getPembayaranReport');
$routes->get('admin/generatePembayaranReportPrint', 'Admin::generatePembayaranReportPrint');
$routes->get('admin/generatePembayaranReportPDF', 'Admin::generatePembayaranReportPDF');

// Simplified Pelanggan Report Route
$routes->get('/admin/pelanggan-report', 'PelangganReport::index');

// Direct view route - simplest possible approach
$routes->get('/admin/pelanggan-simple', function () {
    return view('admin/pelanggan/simple_report');
});

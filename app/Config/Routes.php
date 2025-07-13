<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

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
    $routes->post('cancel/(:num)', 'Booking::cancel/$1');
});

// Admin routes (perlu filter auth dan role admin)
$routes->group('admin', ['filter' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'Admin::index');

    // User Management
    $routes->get('users', 'Admin::users');
    $routes->get('getUsers', 'Admin::getUsers');
    $routes->get('getRoles', 'Admin::getRoles');
    $routes->get('getUser/(:num)', 'Admin::getUser/$1');
    $routes->post('createUser', 'Admin::createUser');
    $routes->post('updateUser/(:num)', 'Admin::updateUser/$1');
    $routes->delete('deleteUser/(:num)', 'Admin::deleteUser/$1');

    // Kategori Management
    $routes->get('kategori', 'Kategori::index');
    $routes->get('getKategori', 'Kategori::getKategori');
    $routes->get('getKategori/(:segment)', 'Kategori::getKategoriById/$1');
    $routes->post('createKategori', 'Kategori::createKategori');
    $routes->post('updateKategori/(:segment)', 'Kategori::updateKategori/$1');
    $routes->delete('deleteKategori/(:segment)', 'Kategori::deleteKategori/$1');

    // Paket Wisata Management
    $routes->get('paket', 'Paket::index');
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
    $routes->get('pelanggan/(:segment)', 'Pelanggan::getPelangganById/$1');
    $routes->post('createPelanggan', 'Pelanggan::createPelanggan');
    $routes->post('updatePelanggan/(:segment)', 'Pelanggan::updatePelanggan/$1');
    $routes->delete('deletePelanggan/(:segment)', 'Pelanggan::deletePelanggan/$1');
});

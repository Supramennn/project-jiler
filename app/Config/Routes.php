<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('sitemap.xml', 'Seo::sitemap');
$routes->get('robots.txt', 'Seo::robots');

$routes->get('/', 'Home::index');

$routes->get('produk', 'Catalog::index');
$routes->get('produk/(:segment)', 'Catalog::show/$1');

$routes->get('checkout/(:segment)', 'Checkout::index/$1');
$routes->post('checkout/(:segment)', 'Checkout::create/$1');

$routes->get('pembayaran/(:segment)', 'Payment::show/$1');
$routes->post('pembayaran/(:segment)/metode', 'Payment::choose/$1');
$routes->post('pembayaran/(:segment)/simulasi', 'Payment::simulate/$1');
$routes->get('sukses/(:segment)', 'Payment::success/$1');
$routes->get('gagal/(:segment)', 'Payment::failed/$1');

$routes->get('status', 'Status::index');
$routes->post('status', 'Status::check');
$routes->get('status/(:segment)', 'Status::json/$1');

$routes->get('invoice/(:segment)/(:segment)', 'Invoice::show/$1/$2');

$routes->get('syarat', 'Legal::terms');
$routes->get('privasi', 'Legal::privacy');

$routes->post('webhook/midtrans', 'Webhook::midtrans');

$routes->get('cron/retry-email', 'Cron::retryEmails');

$routes->get('admin/login', 'Admin\Auth::login');
$routes->post('admin/login', 'Admin\Auth::attempt');
$routes->post('admin/logout', 'Admin\Auth::logout');

$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    $routes->get('', 'Admin\Dashboard::index');
    $routes->post('uji-pesanan', 'Admin\Dashboard::demoOrder');

    $routes->get('pesanan', 'Admin\Orders::index');
    $routes->post('pesanan/kirim-ulang-gagal', 'Admin\Orders::retryFailed');
    $routes->get('pesanan/(:segment)', 'Admin\Orders::show/$1');
    $routes->post('pesanan/(:segment)/kirim-ulang', 'Admin\Orders::resend/$1');
    $routes->post('pesanan/(:segment)/tandai-paid', 'Admin\Orders::markPaid/$1');
    $routes->get('pesanan/(:segment)/email', 'Admin\Orders::previewEmail/$1');
    $routes->get('pesanan/(:segment)/invoice', 'Admin\Orders::invoice/$1');

    $routes->get('produk', 'Admin\Products::index');
    $routes->get('produk/baru', 'Admin\Products::create');
    $routes->post('produk', 'Admin\Products::store');
    $routes->get('produk/(:num)/edit', 'Admin\Products::edit/$1');
    $routes->post('produk/(:num)', 'Admin\Products::update/$1');
    $routes->post('produk/(:num)/toggle', 'Admin\Products::toggle/$1');

    $routes->get('pengaturan', 'Admin\Settings::index');
    $routes->post('pengaturan/uji-email', 'Admin\Settings::testEmail');
});

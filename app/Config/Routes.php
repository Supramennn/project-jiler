<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('sitemap.xml', 'Seo::sitemap');
$routes->get('robots.txt', 'Seo::robots');
$routes->get('health', 'Health::index');

$routes->get('/', 'Home::index');

$routes->get('produk', 'Catalog::index');
$routes->get('produk/(:segment)', 'Catalog::show/$1');

$routes->get('checkout/(:segment)', 'Checkout::index/$1');
$routes->post('checkout/(:segment)', 'Checkout::create/$1', ['filter' => 'throttle:5,60,checkout']);

$routes->get('pembayaran/(:segment)/(:segment)', 'Payment::show/$1/$2');
$routes->post('pembayaran/(:segment)/(:segment)/metode', 'Payment::choose/$1/$2', ['filter' => 'throttle:10,60,payment-method']);
$routes->post('pembayaran/(:segment)/(:segment)/simulasi', 'Payment::simulate/$1/$2', ['filter' => 'throttle:10,60,payment-simulate']);
$routes->get('sukses/(:segment)/(:segment)', 'Payment::success/$1/$2');
$routes->get('gagal/(:segment)/(:segment)', 'Payment::failed/$1/$2');

$routes->get('status', 'Status::index');
$routes->post('status', 'Status::check', ['filter' => 'throttle:10,60,status']);
$routes->get('status/(:segment)', 'Status::json/$1');

$routes->get('invoice/(:segment)/(:segment)', 'Invoice::show/$1/$2');

$routes->get('syarat', 'Legal::terms');
$routes->get('privasi', 'Legal::privacy');

$routes->post('webhook/midtrans', 'Webhook::midtrans');

$routes->get('cron/retry-email', 'Cron::retryEmails');
$routes->get('cron/reconcile-payments', 'Cron::reconcilePayments');
$routes->get('cron/expire-orders', 'Cron::expireOrders');

$routes->get('admin/login', 'Admin\Auth::login');
$routes->post('admin/login', 'Admin\Auth::attempt', ['filter' => 'throttle:5,300,admin-login']);
$routes->post('admin/logout', 'Admin\Auth::logout');

$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    $routes->get('', 'Admin\Dashboard::index');
    $routes->post('uji-pesanan', 'Admin\Dashboard::demoOrder');

    $routes->get('pesanan', 'Admin\Orders::index');
    $routes->get('pesanan/ekspor', 'Admin\Orders::export');
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

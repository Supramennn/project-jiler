<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\OrderService;
use App\Models\OrderModel;
use App\Models\ProductModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $orderModel = model(OrderModel::class);
        $snapshot   = cache()->get('admin_dashboard_snapshot');

        if (! is_array($snapshot)) {
            $snapshot = $orderModel->dashboardSnapshot();
            cache()->save('admin_dashboard_snapshot', $snapshot, 30);
        }

        $daily = cache()->get('admin_dashboard_daily_revenue');

        if (! is_array($daily)) {
            $daily = $orderModel->dailyRevenue(7);
            cache()->save('admin_dashboard_daily_revenue', $daily, 30);
        }

        $maxDaily   = max(array_column($daily, 'total')) ?: 1;

        return $this->renderAdmin('admin/dashboard', [
            'revenueToday'      => $snapshot['revenueToday'],
            'revenueMonth'      => $snapshot['revenueMonth'],
            'paidCountMonth'    => $snapshot['paidCountMonth'],
            'pendingCount'      => $snapshot['pendingCount'],
            'failedEmailCount'  => $snapshot['failedEmailCount'],
            'failedEmails'      => $orderModel->failedEmailOrders(),
            'stalePending'      => $orderModel->stalePendingOrders(),
            'recentOrders'      => $orderModel->recentOrders(),
            'dailyRevenue'      => $daily,
            'maxDaily'          => $maxDaily,
            'paymentDriver'     => config('Payment')->effectiveDriver(),
            'mailDriver'        => config('Mailer')->effectiveDriver(),
            'isDevelopment'     => ENVIRONMENT !== 'production',
        ], [
            'title'  => 'Dashboard',
            'active' => 'dashboard',
        ]);
    }

    public function demoOrder()
    {
        if (ENVIRONMENT === 'production') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $product = model(ProductModel::class)->activeProducts()[0] ?? null;

        if ($product === null) {
            return redirect()->to('admin/produk/baru')->with('error', 'Tambahkan produk aktif dulu sebelum membuat pesanan uji.');
        }

        $order = (new OrderService())->create($product, [
            'nama'     => 'Pesanan Uji Dashboard',
            'email'    => 'uji@namausaha.test',
            'whatsapp' => '081234567890',
        ]);

        model(\App\Models\OrderEventModel::class)->log(
            $order['order_id'],
            'uji',
            'Pesanan uji dibuat dari dashboard untuk mengecek alur pembayaran dan email.'
        );

        return redirect()->to('pembayaran/' . $order['order_id'] . '/' . $order['access_token'])
            ->with('success', 'Pesanan uji dibuat. Pilih metode pembayaran, lalu gunakan tombol simulasi.');
    }
}

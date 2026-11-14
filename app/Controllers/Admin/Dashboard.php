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
        $daily      = $orderModel->dailyRevenue(7);
        $maxDaily   = max(array_column($daily, 'total')) ?: 1;

        return $this->renderAdmin('admin/dashboard', [
            'revenueToday'      => $orderModel->revenueToday(),
            'revenueMonth'      => $orderModel->revenueThisMonth(),
            'paidCountMonth'    => $orderModel->paidCountThisMonth(),
            'pendingCount'      => $orderModel->pendingCount(),
            'failedEmailCount'  => $orderModel->failedEmailCount(),
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

        return redirect()->to('pembayaran/' . $order['order_id'])
            ->with('success', 'Pesanan uji dibuat. Pilih metode pembayaran, lalu gunakan tombol simulasi.');
    }
}

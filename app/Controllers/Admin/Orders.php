<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\OrderFulfillment;
use App\Libraries\OrderMailer;
use App\Models\EmailLogModel;
use App\Models\OrderEventModel;
use App\Models\OrderModel;

class Orders extends BaseController
{
    public function index(): string
    {
        $q           = trim((string) $this->request->getGet('q'));
        $status      = (string) $this->request->getGet('status');
        $emailStatus = (string) $this->request->getGet('email_status');
        $model       = model(OrderModel::class);

        $orders = $model->applyFilters($q, $status, $emailStatus)->paginate(20, 'orders');
        $failedEmailCount = db_connect()->table('orders')
            ->where('status', 'paid')
            ->where('status_email', 'gagal')
            ->countAllResults();

        return $this->renderAdmin('admin/orders/index', [
            'orders'           => $orders,
            'pager'            => $model->pager,
            'q'                => $q,
            'status'           => $status,
            'emailStatus'      => $emailStatus,
            'failedEmailCount' => $failedEmailCount,
        ], [
            'title'  => 'Pesanan',
            'active' => 'orders',
        ]);
    }

    public function show(string $orderId): string
    {
        $order = $this->findOr404($orderId);
        $order = (new \App\Libraries\OrderService())->expireIfNeeded($order);

        return $this->renderAdmin('admin/orders/show', [
            'order'     => $order,
            'events'    => model(OrderEventModel::class)->forOrder($orderId),
            'emailLogs' => model(EmailLogModel::class)->forOrder($orderId),
        ], [
            'title'  => 'Pesanan ' . $orderId,
            'active' => 'orders',
        ]);
    }

    public function resend(string $orderId)
    {
        $order  = $this->findOr404($orderId);
        $result = (new OrderFulfillment())->resendAccessEmail($order);

        if (! $result['ok']) {
            return redirect()->back()->with('error', 'Email gagal dikirim: ' . ($result['error'] ?? 'kesalahan tidak diketahui'));
        }

        return redirect()->back()->with('success', 'Email akses sudah dikirim ulang ke ' . $order['email'] . '.');
    }

    public function retryFailed()
    {
        $orders = model(OrderModel::class)->failedEmailOrders(50);
        $ok     = 0;
        $failed = 0;

        foreach ($orders as $order) {
            $result = (new OrderFulfillment())->resendAccessEmail($order);

            if ($result['ok']) {
                $ok++;
            } else {
                $failed++;
            }
        }

        return redirect()->to('admin/pesanan?email_status=gagal')
            ->with($failed === 0 ? 'success' : 'error', $ok . ' email berhasil dikirim, ' . $failed . ' masih gagal.');
    }

    public function markPaid(string $orderId)
    {
        $order = $this->findOr404($orderId);

        if ($order['status'] === 'paid') {
            return redirect()->back()->with('success', 'Pesanan ini sudah berstatus dibayar.');
        }

        if (! in_array($order['status'], ['pending', 'expired'], true)) {
            return redirect()->back()->with('error', 'Status pesanan ini tidak bisa diubah menjadi dibayar.');
        }

        $changed = (new OrderFulfillment())->markPaid($order, [
            'sumber' => 'admin',
            'admin'  => (string) session()->get('admin_email'),
        ], 'admin');

        if (! $changed) {
            return redirect()->back()->with('error', 'Status pesanan tidak berubah. Muat ulang halaman lalu coba lagi.');
        }

        return redirect()->back()->with('success', 'Pesanan ditandai dibayar dan email akses diproses.');
    }

    public function previewEmail(string $orderId)
    {
        $order = $this->findOr404($orderId);

        if ($order['status'] !== 'paid') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pratinjau email hanya tersedia untuk pesanan yang sudah dibayar.');
        }

        $html = (new OrderMailer())->renderAccessEmail($order);

        $banner = '<div style="position:sticky;top:0;z-index:10;background:#201c16;color:#f7f2e8;'
            . 'font:14px/1.5 Arial,sans-serif;padding:12px 18px;">Pratinjau email untuk '
            . esc($order['email']) . ' · Pesanan ' . esc($order['order_id'])
            . ' · <a style="color:#e3c88f" href="' . site_url('admin/pesanan/' . $order['order_id']) . '">Kembali ke pesanan</a></div>';

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody($banner . $html);
    }

    public function invoice(string $orderId)
    {
        $order = $this->findOr404($orderId);

        return redirect()->to('invoice/' . $order['order_id'] . '/' . $order['access_token']);
    }

    private function findOr404(string $orderId): array
    {
        $order = model(OrderModel::class)->findByOrderId($orderId);

        if ($order === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return $order;
    }
}

<?php

namespace App\Controllers;

use App\Libraries\OrderService;
use App\Libraries\PaymentService;
use App\Libraries\WebhookService;
use App\Models\OrderModel;
use Throwable;

class Payment extends BaseController
{
    public function show(string $orderId)
    {
        $order = $this->findOr404($orderId);
        $order = (new OrderService())->expireIfNeeded($order);

        if ($order['status'] === 'paid') {
            return redirect()->to('sukses/' . $order['order_id']);
        }

        if (in_array($order['status'], ['expired', 'gagal'], true)) {
            return redirect()->to('gagal/' . $order['order_id']);
        }

        $payment      = config('Payment');
        $instructions = [];
        $showMethods  = $order['payment_reference'] === null || $this->request->getGet('ganti') === '1';

        if (! $showMethods && $order['payment_method'] !== null && $order['payment_reference'] !== null) {
            $instructions = (new PaymentService())->sandboxInstructions($order['payment_method'], $order['payment_reference']);
        }

        return $this->renderPublic('payment/show', [
            'order'        => $order,
            'driver'       => $payment->effectiveDriver(),
            'instructions' => $instructions,
            'methods'      => PaymentService::METHODS,
            'showMethods'  => $showMethods,
        ], [
            'title'       => 'Pembayaran ' . $order['order_id'],
            'description' => 'Pilih metode pembayaran untuk pesanan ' . $order['order_id'] . '.',
            'active'      => '',
            'robots'      => 'noindex,nofollow',
        ]);
    }

    public function choose(string $orderId)
    {
        $order = $this->findOr404($orderId);
        $order = (new OrderService())->expireIfNeeded($order);

        if ($order['status'] !== 'pending') {
            return redirect()->to($order['status'] === 'paid' ? 'sukses/' . $order['order_id'] : 'gagal/' . $order['order_id']);
        }

        $method = (string) $this->request->getPost('metode');

        if (! in_array($method, PaymentService::METHODS, true)) {
            return redirect()->back()->with('error', 'Pilih salah satu metode pembayaran yang tersedia.');
        }

        try {
            $result = (new PaymentService())->createTransaction($order, $method);
        } catch (Throwable $e) {
            log_message('error', 'Pembayaran gagal dibuat: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Transaksi pembayaran gagal dibuat: ' . $e->getMessage());
        }

        if ($result['driver'] === 'midtrans') {
            return redirect()->to($result['redirect_url']);
        }

        return redirect()->to('pembayaran/' . $order['order_id'])
            ->with('success', 'Instruksi pembayaran sudah dibuat. Selesaikan pembayaran sesuai petunjuk.');
    }

    public function simulate(string $orderId)
    {
        if (ENVIRONMENT === 'production' || config('Payment')->effectiveDriver() !== 'sandbox') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Simulasi hanya tersedia pada mode sandbox.');
        }

        $order = $this->findOr404($orderId);

        if ($order['status'] === 'paid') {
            return redirect()->to('sukses/' . $order['order_id']);
        }

        if ($order['status'] !== 'pending') {
            return redirect()->to('gagal/' . $order['order_id']);
        }

        if ($order['payment_method'] === null) {
            return redirect()->back()->with('error', 'Pilih metode pembayaran terlebih dahulu.');
        }

        $payload = (new PaymentService())->sandboxNotification($order);
        $result  = (new WebhookService())->handle($payload, (string) $payload['signature_key']);

        if (! $result['ok']) {
            return redirect()->back()->with('error', 'Simulasi gagal: ' . $result['message']);
        }

        return redirect()->to('sukses/' . $order['order_id'])
            ->with('success', 'Pembayaran sandbox diterima. Email akses diproses.');
    }

    public function success(string $orderId): string
    {
        $order = $this->findOr404($orderId);
        $order = (new OrderService())->expireIfNeeded($order);

        if (in_array($order['status'], ['expired', 'gagal'], true)) {
            return redirect()->to('gagal/' . $order['order_id'])->with('error', 'Pesanan ini tidak bisa dibayar lagi. Silakan buat pesanan baru.');
        }

        return $this->renderPublic('payment/success', [
            'order' => $order,
        ], [
            'title'       => 'Status pesanan ' . $order['order_id'],
            'description' => 'Status pembayaran dan pengiriman akses untuk pesanan ' . $order['order_id'] . '.',
            'active'      => '',
            'robots'      => 'noindex,nofollow',
        ]);
    }

    public function failed(string $orderId): string
    {
        $order = $this->findOr404($orderId);
        $order = (new OrderService())->expireIfNeeded($order);

        return $this->renderPublic('payment/failed', [
            'order' => $order,
        ], [
            'title'       => 'Pembayaran belum selesai',
            'description' => 'Pesanan ' . $order['order_id'] . ' belum berhasil dibayar.',
            'active'      => '',
            'robots'      => 'noindex,nofollow',
        ]);
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

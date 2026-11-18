<?php

namespace App\Controllers;

use App\Models\OrderModel;

class Status extends BaseController
{
    public function index(): string
    {
        return $this->renderPublic('status/index', [
            'order' => null,
            'error' => null,
            'input' => ['order_id' => '', 'email' => ''],
        ], [
            'title'       => 'Cek status pesanan',
            'description' => 'Cek status pembayaran dan pengiriman akses dengan nomor pesanan dan email pembeli.',
            'active'      => 'status',
            'robots'      => 'noindex,nofollow',
        ]);
    }

    public function check(): string
    {
        $orderId = trim((string) $this->request->getPost('order_id'));
        $email   = strtolower(trim((string) $this->request->getPost('email')));

        if ($orderId === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->renderPublic('status/index', [
                'order' => null,
                'error' => 'Isi nomor pesanan dan email yang dipakai saat checkout.',
                'input' => ['order_id' => $orderId, 'email' => $email],
            ], [
                'title'  => 'Cek status pesanan',
                'active' => 'status',
            ]);
        }

        $order = model(OrderModel::class)->findByOrderId($orderId);

        if ($order === null || ! hash_equals(strtolower($order['email']), $email)) {
            return $this->renderPublic('status/index', [
                'order' => null,
                'error' => 'Pesanan tidak ditemukan. Periksa nomor pesanan dan email, lalu coba lagi.',
                'input' => ['order_id' => $orderId, 'email' => $email],
            ], [
                'title'  => 'Cek status pesanan',
                'active' => 'status',
            ]);
        }

        return $this->renderPublic('status/index', [
            'order' => $order,
            'error' => null,
            'input' => ['order_id' => $orderId, 'email' => $email],
        ], [
            'title'  => 'Status pesanan ' . $order['order_id'],
            'active' => 'status',
        ]);
    }

    public function json(string $orderId)
    {
        $token = (string) ($this->request->getGet('token') ?? '');
        $order = model(OrderModel::class)->findByToken($orderId, $token);

        if ($order === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ]);
        }

        return $this->response->setJSON([
            'ok'           => true,
            'status'       => $order['status'],
            'status_label' => status_pesanan_label($order['status']),
            'status_email' => $order['status_email'],
            'email_label'  => status_email_label($order['status_email']),
            'paid_at'      => $order['paid_at'],
            'invoice_url'  => $order['status'] === 'paid'
                ? site_url('invoice/' . $order['order_id'] . '/' . $order['access_token'])
                : null,
        ]);
    }
}

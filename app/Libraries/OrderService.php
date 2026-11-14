<?php

namespace App\Libraries;

use App\Models\OrderEventModel;
use App\Models\OrderModel;
use RuntimeException;
use Throwable;

class OrderService
{
    public function create(array $product, array $data): array
    {
        $orderModel = model(OrderModel::class);
        $db         = db_connect();
        $expiry     = (int) config('Payment')->expiryHours;

        $db->transBegin();

        try {
            $orderId = $orderModel->generateOrderId();

            $orderModel->insert([
                'order_id'       => $orderId,
                'nomor_invoice'  => $orderModel->nextInvoiceNumber(),
                'access_token'   => bin2hex(random_bytes(16)),
                'product_id'     => (int) $product['id'],
                'product_nama'   => $product['nama'],
                'product_harga'  => (int) $product['harga'],
                'nama_pembeli'   => trim((string) $data['nama']),
                'email'          => strtolower(trim((string) $data['email'])),
                'whatsapp'       => normalize_phone((string) $data['whatsapp']),
                'nominal'        => (int) $product['harga'],
                'status'         => 'pending',
                'expires_at'     => date('Y-m-d H:i:s', time() + ($expiry * 3600)),
                'status_email'   => 'belum',
                'jumlah_retry'   => 0,
            ]);

            $id = (int) $orderModel->getInsertID();

            if ($id === 0) {
                throw new RuntimeException('Pesanan gagal disimpan.');
            }

            $db->transCommit();
        } catch (Throwable $e) {
            $db->transRollback();
            throw $e;
        }

        model(OrderEventModel::class)->log(
            $orderId,
            'dibuat',
            'Pesanan dibuat dari checkout.'
        );

        $order = $orderModel->find($id);

        if ($order === null) {
            throw new RuntimeException('Pesanan tidak ditemukan setelah disimpan.');
        }

        return $order;
    }

    public function expireIfNeeded(array $order): array
    {
        if ($order['status'] === 'pending' && $order['expires_at'] !== null && strtotime($order['expires_at']) < time()) {
            (new OrderFulfillment())->markExpired($order, [], 'sistem');

            return model(OrderModel::class)->find($order['id']) ?? $order;
        }

        return $order;
    }
}

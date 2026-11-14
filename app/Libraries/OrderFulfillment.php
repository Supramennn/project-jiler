<?php

namespace App\Libraries;

use App\Models\OrderEventModel;
use App\Models\OrderModel;

class OrderFulfillment
{
    public function markPaid(array $order, array $payload, string $source = 'webhook'): bool
    {
        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        $db->table('orders')
            ->where('order_id', $order['order_id'])
            ->whereIn('status', ['pending', 'expired'])
            ->update([
                'status'     => 'paid',
                'paid_at'    => $now,
                'updated_at' => $now,
            ]);

        if ($db->affectedRows() === 0) {
            model(OrderEventModel::class)->log(
                $order['order_id'],
                'webhook_duplikat',
                'Notifikasi pembayaran diterima, tetapi pesanan sudah tidak berstatus pending.',
                $payload
            );

            return false;
        }

        $fresh = model(OrderModel::class)->findByOrderId($order['order_id']);

        if ($fresh === null) {
            return false;
        }

        model(OrderEventModel::class)->log(
            $order['order_id'],
            'pembayaran',
            'Pembayaran diterima dari ' . $source . '.',
            $payload
        );

        (new OrderMailer())->sendAccess($fresh);

        return true;
    }

    public function markExpired(array $order, array $payload = [], string $source = 'webhook'): bool
    {
        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        $db->table('orders')
            ->where('order_id', $order['order_id'])
            ->where('status', 'pending')
            ->update([
                'status'     => 'expired',
                'updated_at' => $now,
            ]);

        if ($db->affectedRows() === 0) {
            return false;
        }

        model(OrderEventModel::class)->log(
            $order['order_id'],
            'kedaluwarsa',
            'Pesanan kedaluwarsa sebelum pembayaran diterima.',
            $payload
        );

        return true;
    }

    public function markFailed(array $order, array $payload = [], string $source = 'webhook'): bool
    {
        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        $db->table('orders')
            ->where('order_id', $order['order_id'])
            ->whereIn('status', ['pending'])
            ->update([
                'status'     => 'gagal',
                'updated_at' => $now,
            ]);

        if ($db->affectedRows() === 0) {
            return false;
        }

        model(OrderEventModel::class)->log(
            $order['order_id'],
            'pembayaran_gagal',
            'Pembayaran gagal atau dibatalkan oleh gateway.',
            $payload
        );

        return true;
    }

    /**
     * @return array{ok: bool, skipped?: bool, error?: string, driver?: string}
     */
    public function resendAccessEmail(array $order): array
    {
        if ($order['status'] !== 'paid') {
            return ['ok' => false, 'error' => 'Email akses hanya bisa dikirim untuk pesanan yang sudah dibayar.'];
        }

        return (new OrderMailer())->sendAccess($order, true);
    }
}

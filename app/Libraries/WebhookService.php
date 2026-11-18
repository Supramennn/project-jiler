<?php

namespace App\Libraries;

use App\Models\OrderEventModel;
use App\Models\OrderModel;

class WebhookService
{
    /**
     * @return array{ok: bool, code: int, message: string, changed: bool}
     */
    public function handle(array $payload, string $signature, string $source = 'webhook'): array
    {
        foreach (['order_id', 'transaction_status', 'status_code', 'gross_amount'] as $field) {
            if (! isset($payload[$field]) || $payload[$field] === '') {
                return ['ok' => false, 'code' => 400, 'message' => 'Payload tidak lengkap: ' . $field . '.', 'changed' => false];
            }
        }

        $order = model(OrderModel::class)->findByOrderId((string) $payload['order_id']);

        if ($order === null) {
            return ['ok' => false, 'code' => 404, 'message' => 'Pesanan tidak ditemukan.', 'changed' => false];
        }

        if (! $this->signatureIsValid($payload, $signature)) {
            model(OrderEventModel::class)->log(
                $order['order_id'],
                'webhook_invalid',
                'Signature webhook tidak valid. Notifikasi diabaikan.',
                ['status' => $payload['transaction_status']]
            );

            return ['ok' => false, 'code' => 403, 'message' => 'Signature tidak valid.', 'changed' => false];
        }

        $grossAmount = (int) round((float) $payload['gross_amount']);

        if ($grossAmount !== (int) $order['nominal']) {
            model(OrderEventModel::class)->log(
                $order['order_id'],
                'webhook_invalid',
                'Nominal webhook tidak sesuai dengan nominal pesanan.',
                $payload
            );

            return ['ok' => false, 'code' => 400, 'message' => 'Nominal tidak sesuai.', 'changed' => false];
        }

        $status = (string) $payload['transaction_status'];
        $fraud  = (string) ($payload['fraud_status'] ?? '');

        model(OrderEventModel::class)->log(
            $order['order_id'],
            'webhook',
            'Notifikasi gateway diterima: ' . $status . '.',
            $payload
        );

        $fulfillment = new OrderFulfillment();

        if ($status === 'settlement' || ($status === 'capture' && in_array($fraud, ['', 'accept'], true))) {
            $changed = $fulfillment->markPaid($order, $payload, $source);

            return ['ok' => true, 'code' => 200, 'message' => 'Pembayaran diproses.', 'changed' => $changed];
        }

        if ($status === 'pending') {
            return ['ok' => true, 'code' => 200, 'message' => 'Pembayaran masih menunggu.', 'changed' => false];
        }

        if ($status === 'expire') {
            $changed = $fulfillment->markExpired($order, $payload, $source);

            return ['ok' => true, 'code' => 200, 'message' => 'Pesanan ditandai kedaluwarsa.', 'changed' => $changed];
        }

        if (in_array($status, ['deny', 'cancel', 'failure'], true)) {
            $changed = $fulfillment->markFailed($order, $payload, $source);

            return ['ok' => true, 'code' => 200, 'message' => 'Pembayaran gagal dicatat.', 'changed' => $changed];
        }

        return ['ok' => true, 'code' => 200, 'message' => 'Status tidak mengubah pesanan.', 'changed' => false];
    }

    private function signatureIsValid(array $payload, string $signature): bool
    {
        if ($signature === '') {
            return false;
        }

        $payment = config('Payment');

        if ($payment->effectiveDriver() === 'midtrans') {
            $expected = (new PaymentService())->midtransSignature($payload);
        } else {
            $expected = (new PaymentService())->signature($payload);
        }

        return hash_equals($expected, $signature);
    }
}

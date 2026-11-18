<?php

namespace App\Libraries;

use App\Models\OrderModel;
use Throwable;

class PaymentReconciler
{
    /**
     * @return array{ok: bool, skipped: bool, message: string, checked: int, updated: int, not_found: int, errors: int, timed_out: bool}
     */
    public function run(int $limit = 10): array
    {
        $payment = config('Payment');

        if (! $payment->usesMidtrans()) {
            return [
                'ok'        => true,
                'skipped'   => true,
                'message'   => 'Rekonsiliasi hanya berjalan saat driver pembayaran memakai Midtrans.',
                'checked'   => 0,
                'updated'   => 0,
                'not_found' => 0,
                'errors'    => 0,
                'timed_out' => false,
            ];
        }

        $orders   = model(OrderModel::class)->reconcilableOrders($limit);
        $service  = new PaymentService();
        $deadline = microtime(true) + 25;
        $checked  = 0;
        $updated  = 0;
        $notFound = 0;
        $errors   = 0;
        $timedOut = false;

        foreach ($orders as $order) {
            if (microtime(true) >= $deadline) {
                $timedOut = true;
                break;
            }

            $checked++;

            try {
                $payload = $service->fetchMidtransStatus($order);
            } catch (Throwable $e) {
                $errors++;
                log_message('error', 'Rekonsiliasi {order} gagal: {message}', [
                    'order'   => $order['order_id'],
                    'message' => $e->getMessage(),
                ]);

                continue;
            }

            if ($payload === null) {
                $notFound++;

                continue;
            }

            $payload['signature_key'] = $service->midtransSignature($payload);
            $result                   = (new WebhookService())->handle(
                $payload,
                $payload['signature_key'],
                'rekonsiliasi'
            );

            if ($result['ok'] && $result['changed']) {
                $updated++;
            }

            if (! $result['ok']) {
                $errors++;
            }
        }

        return [
            'ok'        => $errors === 0,
            'skipped'   => false,
            'message'   => $errors === 0 ? 'Rekonsiliasi selesai.' : 'Rekonsiliasi selesai dengan sebagian error.',
            'checked'   => $checked,
            'updated'   => $updated,
            'not_found' => $notFound,
            'errors'    => $errors,
            'timed_out' => $timedOut,
        ];
    }
}

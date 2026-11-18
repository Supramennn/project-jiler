<?php

namespace App\Controllers;

use App\Libraries\OrderFulfillment;
use App\Libraries\PaymentReconciler;
use App\Models\OrderModel;
use CodeIgniter\HTTP\ResponseInterface;

class Cron extends BaseController
{
    public function retryEmails(): ResponseInterface
    {
        if ($response = $this->guard()) {
            return $response;
        }

        $limit  = min(50, max(1, (int) ($this->request->getGet('limit') ?? 20)));
        $orders = model(OrderModel::class)->failedEmailOrders($limit);
        $sent   = 0;
        $failed = 0;

        foreach ($orders as $order) {
            $result = (new OrderFulfillment())->resendAccessEmail($order);

            if ($result['ok']) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return $this->response->setJSON([
            'ok'        => $failed === 0,
            'processed' => count($orders),
            'sent'      => $sent,
            'failed'    => $failed,
        ]);
    }

    public function reconcilePayments(): ResponseInterface
    {
        if ($response = $this->guard()) {
            return $response;
        }

        $limit = min(25, max(1, (int) ($this->request->getGet('limit') ?? 10)));

        return $this->response->setJSON((new PaymentReconciler())->run($limit));
    }

    public function expireOrders(): ResponseInterface
    {
        if ($response = $this->guard()) {
            return $response;
        }

        $limit   = min(200, max(1, (int) ($this->request->getGet('limit') ?? 100)));
        $orders  = model(OrderModel::class)->expiredOrders($limit);
        $expired = 0;

        foreach ($orders as $order) {
            if ((new OrderFulfillment())->markExpired($order, [], 'cron')) {
                $expired++;
            }
        }

        return $this->response->setJSON([
            'ok'        => true,
            'processed' => count($orders),
            'expired'   => $expired,
        ]);
    }

    private function guard(): ?ResponseInterface
    {
        $secret = trim((string) env('CRON_SECRET', ''));

        if ($secret === '') {
            return $this->response->setStatusCode(503)->setJSON([
                'ok'      => false,
                'message' => 'CRON_SECRET belum diatur.',
            ]);
        }

        if (! hash_equals($secret, $this->providedSecret())) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok'      => false,
                'message' => 'Token cron tidak valid.',
            ]);
        }

        return null;
    }

    private function providedSecret(): string
    {
        $header = trim($this->request->getHeaderLine('Authorization'));

        if (stripos($header, 'Bearer ') === 0) {
            return trim(substr($header, 7));
        }

        return trim($this->request->getHeaderLine('X-Cron-Secret'));
    }
}

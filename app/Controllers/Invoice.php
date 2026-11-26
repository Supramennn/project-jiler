<?php

namespace App\Controllers;

use App\Models\OrderModel;

class Invoice extends BaseController
{
    public function show(string $orderId, string $token): string
    {
        $order = model(OrderModel::class)->findByToken($orderId, $token);

        if ($order === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice tidak ditemukan.');
        }

        $this->response
            ->setHeader('Cache-Control', 'private, no-store, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('X-Robots-Tag', 'noindex, nofollow');

        return view('invoice/show', [
            'order' => $order,
            'store' => config('Store'),
        ]);
    }
}

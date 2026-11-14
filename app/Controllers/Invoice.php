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

        return view('invoice/show', [
            'order' => $order,
            'store' => config('Store'),
        ]);
    }
}

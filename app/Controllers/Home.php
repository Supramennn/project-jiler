<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        $products = model(ProductModel::class)->activeProducts();
        $featured = $products[0] ?? null;

        return $this->renderPublic('home', [
            'products' => $products,
            'featured' => $featured,
        ], [
            'title'       => 'Template stok siap pakai',
            'description' => 'Beli template Manajemen Stok Barang sekali bayar. Invoice dan akses template dikirim otomatis ke email setelah pembayaran terverifikasi.',
            'active'      => 'home',
        ]);
    }
}

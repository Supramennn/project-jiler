<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Catalog extends BaseController
{
    public function index(): string
    {
        $products = model(ProductModel::class)->activeProducts();

        return $this->renderPublic('catalog/index', [
            'products' => $products,
        ], [
            'title'       => 'Katalog template stok',
            'description' => 'Pilih template Manajemen Stok Barang sesuai kebutuhan usaha. Harga sekali bayar, akses selamanya.',
            'active'      => 'catalog',
        ]);
    }

    public function show(string $slug): string
    {
        $product = model(ProductModel::class)->findBySlug($slug);

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        return $this->renderPublic('catalog/show', [
            'product' => $product,
            'fitur'   => product_features($product['fitur']),
        ], [
            'title'       => $product['nama'],
            'description' => mb_substr(strip_tags($product['deskripsi']), 0, 155),
            'active'      => 'catalog',
        ]);
    }
}

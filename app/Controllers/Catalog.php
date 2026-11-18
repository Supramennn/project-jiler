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
            'title'       => 'Katalog Template Manajemen Stok Barang',
            'description' => 'Bandingkan tiga template Manajemen Stok Barang: versi dasar, Plus, dan Multi-Gudang. Harga sekali bayar, akses selamanya.',
            'active'      => 'catalog',
            'jsonLd'      => [
                seo_breadcrumb_schema([
                    ['name' => 'Beranda', 'url' => site_url('/')],
                    ['name' => 'Produk', 'url' => site_url('produk')],
                ]),
            ],
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
            'description' => seo_first_sentence($product['deskripsi']),
            'active'      => 'catalog',
            'type'        => 'product',
            'product'     => $product,
            'image'       => seo_product_image($product),
            'jsonLd'      => [
                seo_product_schema($product),
                seo_breadcrumb_schema([
                    ['name' => 'Beranda', 'url' => site_url('/')],
                    ['name' => 'Produk', 'url' => site_url('produk')],
                    ['name' => $product['nama'], 'url' => site_url('produk/' . $product['slug'])],
                ]),
            ],
        ]);
    }
}

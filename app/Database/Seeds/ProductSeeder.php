<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $products = [
            [
                'nama'                 => 'Manajemen Stok Barang',
                'slug'                 => 'manajemen-stok-barang',
                'harga'                => 89000,
                'tingkat_kompleksitas' => 'Sederhana',
                'deskripsi'            => 'Template Google Sheets dengan web app Apps Script untuk mencatat stok masuk, stok keluar, dan sisa stok. Setelah menyalin template, Anda men-deploy web app di akun Google sendiri dengan panduan video dan teks.',
                'fitur'                => "Master barang dan kategori\nPencatatan barang masuk dan keluar\nSisa stok dihitung otomatis\nRingkasan stok untuk pengecekan cepat\nPanduan deploy video dan teks",
                'gambar'               => '',
                'link_demo'            => '',
                'link_template'        => 'https://example.com/ganti-link-template',
                'link_video'           => 'https://example.com/ganti-link-video',
                'link_panduan'         => 'https://example.com/ganti-link-panduan',
                'aktif'                => 1,
                'urutan'               => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'nama'                 => 'Manajemen Stok Barang Plus',
                'slug'                 => 'manajemen-stok-barang-plus',
                'harga'                => 149000,
                'tingkat_kompleksitas' => 'Menengah',
                'deskripsi'            => 'Versi lanjutan untuk usaha yang mencatat stok dari beberapa jenis transaksi. Termasuk template Google Sheets, web app Apps Script, serta panduan deploy yang sama lengkapnya dengan versi dasar.',
                'fitur'                => "Semua fitur versi dasar\nPemisahan beberapa jenis transaksi\nRingkasan per kategori barang\nPanduan deploy video dan teks",
                'gambar'               => '',
                'link_demo'            => '',
                'link_template'        => 'https://example.com/ganti-link-template-plus',
                'link_video'           => 'https://example.com/ganti-link-video-plus',
                'link_panduan'         => 'https://example.com/ganti-link-panduan-plus',
                'aktif'                => 1,
                'urutan'               => 2,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'nama'                 => 'Manajemen Stok Barang Multi-Gudang',
                'slug'                 => 'manajemen-stok-barang-multi-gudang',
                'harga'                => 199000,
                'tingkat_kompleksitas' => 'Kompleks',
                'deskripsi'            => 'Template untuk usaha yang menyimpan barang di lebih dari satu lokasi. Stok dicatat per gudang, lalu diringkas menjadi total stok yang mudah dibaca.',
                'fitur'                => "Semua fitur versi Plus\nPencatatan stok per gudang\nTotal stok lintas gudang\nPanduan deploy video dan teks",
                'gambar'               => '',
                'link_demo'            => '',
                'link_template'        => 'https://example.com/ganti-link-template-multi',
                'link_video'           => 'https://example.com/ganti-link-video-multi',
                'link_panduan'         => 'https://example.com/ganti-link-panduan-multi',
                'aktif'                => 1,
                'urutan'               => 3,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
        ];

        $table = $this->db->table('products');

        foreach ($products as $product) {
            $exists = $table->where('slug', $product['slug'])->countAllResults();

            if ($exists === 0) {
                $this->db->table('products')->insert($product);
            }
        }
    }
}

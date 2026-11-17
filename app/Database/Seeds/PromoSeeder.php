<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $promos = [
            'manajemen-stok-barang'              => 71000,
            'manajemen-stok-barang-plus'         => 119000,
            'manajemen-stok-barang-multi-gudang' => 159000,
        ];

        foreach ($promos as $slug => $hargaPromo) {
            $this->db->table('products')
                ->where('slug', $slug)
                ->where('harga_promo', null)
                ->update([
                    'harga_promo' => $hargaPromo,
                    'updated_at'  => $now,
                ]);
        }
    }
}

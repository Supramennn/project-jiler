<?php

namespace App\Controllers;

class Legal extends BaseController
{
    public function terms(): string
    {
        return $this->renderPublic('legal/terms', [], [
            'title'       => 'Syarat dan ketentuan',
            'description' => 'Ketentuan pembelian template, pengiriman akses lewat email, lisensi penggunaan, dan kebijakan refund.',
            'active'      => '',
            'jsonLd'      => [
                seo_breadcrumb_schema([
                    ['name' => 'Beranda', 'url' => site_url('/')],
                    ['name' => 'Syarat dan ketentuan', 'url' => site_url('syarat')],
                ]),
            ],
        ]);
    }

    public function privacy(): string
    {
        return $this->renderPublic('legal/privacy', [], [
            'title'       => 'Kebijakan privasi',
            'description' => 'Data yang dikumpulkan saat checkout, cara data dipakai untuk pesanan dan pengiriman akses, serta hak pembeli.',
            'active'      => '',
            'jsonLd'      => [
                seo_breadcrumb_schema([
                    ['name' => 'Beranda', 'url' => site_url('/')],
                    ['name' => 'Kebijakan privasi', 'url' => site_url('privasi')],
                ]),
            ],
        ]);
    }
}

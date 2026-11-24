<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        $products = model(ProductModel::class)->activeProducts();
        $featured = $products[0] ?? null;

        $faqs = [
            [
                'q' => 'Apakah saya perlu akun Google?',
                'a' => 'Ya. Template disalin ke Google Drive Anda dan web app di-deploy dari akun Google sendiri. Tanpa akun Google, template tidak bisa dipakai.',
            ],
            [
                'q' => 'Apakah harus bisa coding untuk deploy?',
                'a' => 'Tidak. Panduan video dan teks menunjukkan langkah menyalin template dan men-deploy web app. Anda hanya perlu mengikuti urutan langkahnya.',
            ],
            [
                'q' => 'Kapan link template dikirim?',
                'a' => 'Setelah gateway mengirim notifikasi pembayaran berhasil ke backend. Redirect dari halaman bayar tidak dianggap bukti bayar. Email biasanya diproses dalam waktu singkat setelah pembayaran terverifikasi.',
            ],
            [
                'q' => 'Apakah akses template punya masa berlaku?',
                'a' => 'Tidak. Akses berlaku selamanya. Link template tidak memiliki masa kedaluwarsa, tetapi hanya dikirim ke email pembeli setelah pembayaran berhasil.',
            ],
            [
                'q' => 'Bagaimana jika email tidak masuk?',
                'a' => 'Periksa folder spam terlebih dahulu, lalu buka halaman cek status pesanan memakai nomor pesanan dan email. Jika email tercatat gagal, admin bisa mengirim ulang dari dashboard.',
            ],
            [
                'q' => 'Metode pembayaran apa yang tersedia?',
                'a' => 'VA Permata, GoPay, dan QRIS. Dana masuk ke rekening Permata atau GoPay milik penjual melalui gateway pembayaran.',
            ],
            [
                'q' => 'Apakah ada refund?',
                'a' => 'Kebijakan refund belum ditetapkan. Jika ada kendala setelah pembelian, hubungi admin melalui email yang tertera di invoice agar bisa ditinjau.',
            ],
        ];

        return $this->renderPublic('home', [
            'products' => $products,
            'featured' => $featured,
            'faqs'     => $faqs,
        ], [
            'title'       => 'Template Apps Script Siap Pakai',
            'description' => 'Template Google Sheets dan web app Apps Script siap pakai untuk otomatisasi usaha. Bayar sekali, akses selamanya.',
            'active'      => 'home',
            'jsonLd'      => [seo_faq_schema($faqs)],
        ]);
    }
}

<?php

namespace App\Controllers;

use App\Libraries\OrderService;
use App\Models\ProductModel;
use Throwable;

class Checkout extends BaseController
{
    public function index(string $slug): string
    {
        $product = model(ProductModel::class)->findBySlug($slug);

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        return $this->renderPublic('checkout/index', [
            'product' => $product,
        ], [
            'title'       => 'Checkout ' . $product['nama'],
            'description' => 'Isi data pembeli untuk membeli ' . $product['nama'] . '.',
            'active'      => 'catalog',
        ]);
    }

    public function create(string $slug)
    {
        $product = model(ProductModel::class)->findBySlug($slug);

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        if (trim((string) $this->request->getPost('website')) !== '') {
            return redirect()->back()->with('error', 'Form tidak bisa diproses. Silakan coba lagi.');
        }

        $rules = [
            'nama'             => 'required|min_length[2]|max_length[120]',
            'email'            => 'required|valid_email|max_length[190]',
            'email_konfirmasi' => 'required|matches[email]',
            'whatsapp'         => 'required|min_length[8]|max_length[20]',
            'setuju'           => 'required',
        ];

        $messages = [
            'nama' => [
                'required'   => 'Nama pembeli wajib diisi.',
                'min_length' => 'Nama minimal 2 karakter.',
            ],
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email belum benar.',
            ],
            'email_konfirmasi' => [
                'required' => 'Ketik ulang email untuk konfirmasi.',
                'matches'  => 'Email dan ketik ulang email harus sama.',
            ],
            'whatsapp' => [
                'required'   => 'Nomor WhatsApp wajib diisi.',
                'min_length' => 'Nomor WhatsApp terlalu pendek.',
            ],
            'setuju' => [
                'required' => 'Centang persetujuan syarat dan kebijakan sebelum melanjutkan.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $order = (new OrderService())->create($product, [
                'nama'     => (string) $this->request->getPost('nama'),
                'email'    => (string) $this->request->getPost('email'),
                'whatsapp' => (string) $this->request->getPost('whatsapp'),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Checkout gagal: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Pesanan gagal dibuat. Silakan coba lagi.');
        }

        return redirect()->to('pembayaran/' . $order['order_id'])
            ->with('success', 'Pesanan dibuat. Pilih metode pembayaran untuk melanjutkan.');
    }
}

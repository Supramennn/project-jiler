<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Products extends BaseController
{
    public function index(): string
    {
        return $this->renderAdmin('admin/products/index', [
            'products' => model(ProductModel::class)->adminList(),
        ], [
            'title'  => 'Produk',
            'active' => 'products',
        ]);
    }

    public function create()
    {
        return $this->renderAdmin('admin/products/form', [
            'product' => null,
            'action'  => site_url('admin/produk'),
            'heading' => 'Tambah produk',
        ], [
            'title'  => 'Tambah produk',
            'active' => 'products',
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $promoErrors = $this->promoErrors();

        if ($promoErrors !== []) {
            return redirect()->back()->withInput()->with('errors', $promoErrors);
        }

        $data       = $this->payload();
        $data['slug'] = $this->uniqueSlug($data['slug'] !== '' ? $data['slug'] : $data['nama']);

        model(ProductModel::class)->insert($data);

        return redirect()->to('admin/produk')->with('success', 'Produk baru sudah ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = model(ProductModel::class)->find($id);

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        return $this->renderAdmin('admin/products/form', [
            'product' => $product,
            'action'  => site_url('admin/produk/' . $id),
            'heading' => 'Ubah produk',
        ], [
            'title'  => 'Ubah ' . $product['nama'],
            'active' => 'products',
        ]);
    }

    public function update(int $id)
    {
        $model   = model(ProductModel::class);
        $product = $model->find($id);

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        if (! $this->validate($this->rules(), $this->messages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $promoErrors = $this->promoErrors();

        if ($promoErrors !== []) {
            return redirect()->back()->withInput()->with('errors', $promoErrors);
        }

        $data         = $this->payload();
        $data['slug'] = $this->uniqueSlug($data['slug'] !== '' ? $data['slug'] : $data['nama'], $id);

        $model->update($id, $data);

        return redirect()->to('admin/produk')->with('success', 'Perubahan produk sudah disimpan.');
    }

    public function toggle(int $id)
    {
        $model   = model(ProductModel::class);
        $product = $model->find($id);

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        $model->update($id, ['aktif' => (int) $product['aktif'] === 1 ? 0 : 1]);

        return redirect()->to('admin/produk')->with('success', 'Status tampil produk sudah diubah.');
    }

    private function payload(): array
    {
        return [
            'nama'                 => trim((string) $this->request->getPost('nama')),
            'slug'                 => trim((string) $this->request->getPost('slug')),
            'harga'                => (int) $this->request->getPost('harga'),
            'harga_promo'          => trim((string) $this->request->getPost('harga_promo')) !== ''
                ? (int) $this->request->getPost('harga_promo')
                : null,
            'promo_mulai'          => $this->parseDateTime($this->request->getPost('promo_mulai')),
            'promo_berakhir'       => $this->parseDateTime($this->request->getPost('promo_berakhir')),
            'tingkat_kompleksitas' => (string) $this->request->getPost('tingkat_kompleksitas'),
            'deskripsi'            => trim((string) $this->request->getPost('deskripsi')),
            'fitur'                => trim((string) $this->request->getPost('fitur')),
            'gambar'               => trim((string) $this->request->getPost('gambar')),
            'link_demo'            => trim((string) $this->request->getPost('link_demo')),
            'link_template'        => trim((string) $this->request->getPost('link_template')),
            'link_video'           => trim((string) $this->request->getPost('link_video')),
            'link_panduan'         => trim((string) $this->request->getPost('link_panduan')),
            'aktif'                => $this->request->getPost('aktif') !== null ? 1 : 0,
            'urutan'               => (int) ($this->request->getPost('urutan') ?? 0),
        ];
    }

    private function rules(): array
    {
        return [
            'nama'                 => 'required|min_length[3]|max_length[150]',
            'slug'                 => 'permit_empty|max_length[160]',
            'harga'                => 'required|integer|greater_than[0]',
            'harga_promo'          => 'permit_empty|is_natural_no_zero',
            'tingkat_kompleksitas' => 'required|in_list[Sederhana,Menengah,Kompleks]',
            'deskripsi'            => 'required',
            'gambar'               => 'permit_empty|valid_url',
            'link_demo'            => 'permit_empty|valid_url',
            'link_template'        => 'permit_empty|valid_url',
            'link_video'           => 'permit_empty|valid_url',
            'link_panduan'         => 'permit_empty|valid_url',
            'urutan'               => 'permit_empty|integer',
        ];
    }

    private function messages(): array
    {
        return [
            'nama' => [
                'required'   => 'Nama produk wajib diisi.',
                'min_length' => 'Nama produk minimal 3 karakter.',
            ],
            'harga' => [
                'required'     => 'Harga wajib diisi.',
                'integer'      => 'Harga harus berupa angka tanpa titik atau koma.',
                'greater_than' => 'Harga harus lebih besar dari nol.',
            ],
            'harga_promo' => [
                'is_natural_no_zero' => 'Harga promo harus berupa angka bulat lebih besar dari nol.',
            ],
            'tingkat_kompleksitas' => [
                'required' => 'Pilih tingkat kompleksitas.',
                'in_list'  => 'Tingkat kompleksitas harus Sederhana, Menengah, atau Kompleks.',
            ],
            'deskripsi' => [
                'required' => 'Deskripsi produk wajib diisi.',
            ],
            'gambar' => [
                'valid_url' => 'URL gambar belum valid.',
            ],
            'link_demo' => [
                'valid_url' => 'URL demo belum valid.',
            ],
            'link_template' => [
                'valid_url' => 'URL template belum valid.',
            ],
            'link_video' => [
                'valid_url' => 'URL video belum valid.',
            ],
            'link_panduan' => [
                'valid_url' => 'URL panduan belum valid.',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function promoErrors(): array
    {
        $errors = [];
        $harga  = (int) $this->request->getPost('harga');

        $promoRaw = trim((string) $this->request->getPost('harga_promo'));

        if ($promoRaw !== '' && (int) $promoRaw >= $harga) {
            $errors['harga_promo'] = 'Harga promo harus lebih kecil dari harga normal.';
        }

        $mulaiRaw    = trim((string) $this->request->getPost('promo_mulai'));
        $berakhirRaw = trim((string) $this->request->getPost('promo_berakhir'));
        $mulai       = $this->parseDateTime($mulaiRaw);
        $berakhir    = $this->parseDateTime($berakhirRaw);

        if ($mulaiRaw !== '' && $mulai === null) {
            $errors['promo_mulai'] = 'Format tanggal mulai promo belum benar.';
        }

        if ($berakhirRaw !== '' && $berakhir === null) {
            $errors['promo_berakhir'] = 'Format tanggal berakhir promo belum benar.';
        }

        if ($mulai !== null && $berakhir !== null && strtotime($berakhir) <= strtotime($mulai)) {
            $errors['promo_berakhir'] = 'Tanggal berakhir promo harus setelah tanggal mulai.';
        }

        return $errors;
    }

    private function parseDateTime(mixed $value): ?string
    {
        $value = str_replace('T', ' ', trim((string) $value));

        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = url_title($value, '-', true);
        $base = $base !== '' ? $base : 'produk';
        $slug = $base;
        $i    = 2;
        $model = model(ProductModel::class);

        while (true) {
            $query = $model->where('slug', $slug);

            if ($ignoreId !== null) {
                $query->where('id !=', $ignoreId);
            }

            if ($query->countAllResults() === 0) {
                return $slug;
            }

            $slug = $base . '-' . $i;
            $i++;
        }
    }
}

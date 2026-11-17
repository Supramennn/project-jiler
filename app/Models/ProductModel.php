<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama', 'slug', 'harga', 'harga_promo', 'promo_mulai', 'promo_berakhir',
        'tingkat_kompleksitas', 'deskripsi', 'fitur',
        'gambar', 'link_demo', 'link_template', 'link_video', 'link_panduan',
        'aktif', 'urutan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    public function activeProducts(): array
    {
        return $this->where('aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findBySlug(string $slug, bool $onlyActive = true): ?array
    {
        if ($onlyActive) {
            $this->where('aktif', 1);
        }

        return $this->where('slug', $slug)->first();
    }

    public function adminList(): array
    {
        return $this->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
}

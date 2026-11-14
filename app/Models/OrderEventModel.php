<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderEventModel extends Model
{
    protected $table         = 'order_events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'tipe', 'pesan', 'payload', 'created_at'];
    protected $useTimestamps = false;

    public function log(string $orderId, string $tipe, string $pesan, ?array $payload = null): void
    {
        $this->insert([
            'order_id'   => $orderId,
            'tipe'       => $tipe,
            'pesan'      => $pesan,
            'payload'    => $payload === null ? null : json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function forOrder(string $orderId): array
    {
        return $this->where('order_id', $orderId)->orderBy('id', 'ASC')->findAll();
    }
}

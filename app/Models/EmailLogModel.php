<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailLogModel extends Model
{
    protected $table         = 'email_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'jenis', 'penerima', 'subjek', 'status', 'error', 'created_at'];
    protected $useTimestamps = false;

    public function record(
        ?string $orderId,
        string $jenis,
        string $penerima,
        string $subjek,
        string $status,
        ?string $error = null
    ): void {
        $this->insert([
            'order_id'   => $orderId,
            'jenis'      => $jenis,
            'penerima'   => $penerima,
            'subjek'     => $subjek,
            'status'     => $status,
            'error'      => $error,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function forOrder(string $orderId): array
    {
        return $this->where('order_id', $orderId)->orderBy('id', 'DESC')->findAll();
    }
}

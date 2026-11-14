<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_id', 'nomor_invoice', 'access_token', 'product_id', 'product_nama',
        'product_harga', 'nama_pembeli', 'email', 'whatsapp', 'nominal', 'status',
        'payment_method', 'payment_reference', 'payment_url', 'paid_at', 'expires_at',
        'status_email', 'email_sent_at', 'jumlah_retry', 'catatan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    public function findByOrderId(string $orderId): ?array
    {
        return $this->where('order_id', $orderId)->first();
    }

    public function findByToken(string $orderId, string $token): ?array
    {
        $order = $this->findByOrderId($orderId);

        if ($order === null || ! hash_equals($order['access_token'], $token)) {
            return null;
        }

        return $order;
    }

    public function generateOrderId(): string
    {
        $prefix = $this->idPrefix();

        do {
            $candidate = $prefix . '-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        } while ($this->where('order_id', $candidate)->countAllResults() > 0);

        return $candidate;
    }

    public function nextInvoiceNumber(): string
    {
        $prefix = $this->idPrefix();
        $start  = date('Y-m-01 00:00:00');
        $count  = $this->where('created_at >=', $start)->countAllResults() + 1;
        $number = $prefix . '/' . date('Ym') . '/' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        if ($this->where('nomor_invoice', $number)->countAllResults() > 0) {
            $number = $prefix . '/' . date('Ym') . '/' . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        }

        return $number;
    }

    public function revenueToday(): int
    {
        return $this->paidRevenue(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
    }

    public function revenueThisMonth(): int
    {
        return $this->paidRevenue(date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59'));
    }

    public function paidCountThisMonth(): int
    {
        return $this->where('status', 'paid')
            ->where('paid_at >=', date('Y-m-01 00:00:00'))
            ->countAllResults();
    }

    public function pendingCount(): int
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    public function failedEmailCount(): int
    {
        return $this->where('status_email', 'gagal')->countAllResults();
    }

    public function recentOrders(int $limit = 8): array
    {
        return $this->orderBy('id', 'DESC')->limit($limit)->findAll();
    }

    public function failedEmailOrders(int $limit = 5): array
    {
        return $this->where('status', 'paid')
            ->where('status_email !=', 'terkirim')
            ->orderBy('paid_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    public function stalePendingOrders(int $hours = 2, int $limit = 5): array
    {
        return $this->where('status', 'pending')
            ->where('created_at <', date('Y-m-d H:i:s', time() - ($hours * 3600)))
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    public function dailyRevenue(int $days = 7): array
    {
        $labels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date  = date('Y-m-d', strtotime("-{$i} days"));
            $total = $this->paidRevenue($date . ' 00:00:00', $date . ' 23:59:59');

            $data[] = [
                'date'  => $date,
                'label' => $labels[(int) date('w', strtotime($date))],
                'total' => $total,
            ];
        }

        return $data;
    }

    public function applyFilters(string $q = '', string $status = '', string $emailStatus = ''): self
    {
        if ($q !== '') {
            $this->groupStart()
                ->like('order_id', $q)
                ->orLike('nomor_invoice', $q)
                ->orLike('email', $q)
                ->orLike('nama_pembeli', $q)
                ->groupEnd();
        }

        if ($status !== '') {
            $this->where('status', $status);
        }

        if ($emailStatus !== '') {
            $this->where('status_email', $emailStatus);
        }

        return $this->orderBy('id', 'DESC');
    }

    private function paidRevenue(string $start, string $end): int
    {
        $row = $this->selectSum('nominal', 'total')
            ->where('status', 'paid')
            ->where('paid_at >=', $start)
            ->where('paid_at <=', $end)
            ->first();

        return (int) ($row['total'] ?? 0);
    }

    private function idPrefix(): string
    {
        $prefix = (string) config('Store')->invoicePrefix;
        $clean  = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $prefix));

        return $clean !== '' ? $clean : 'ND';
    }
}

<?php

namespace App\Libraries;

use RuntimeException;

class OrderCsv
{
    public const HEADERS = [
        'Nomor invoice',
        'Nomor pesanan',
        'Dibuat',
        'Nama pembeli',
        'Email',
        'WhatsApp',
        'Produk',
        'Harga normal',
        'Diskon',
        'Nominal dibayar',
        'Status',
        'Status email',
        'Metode bayar',
        'Referensi bayar',
        'Dibayar pada',
        'Kedaluwarsa pada',
    ];

    /**
     * @param list<array<string, mixed>> $orders
     */
    public function build(array $orders): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            throw new RuntimeException('Tidak bisa membuat berkas CSV.');
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, self::HEADERS, ',', '"', '');

        foreach ($orders as $order) {
            fputcsv($handle, $this->row($order), ',', '"', '');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv === false ? '' : $csv;
    }

    /**
     * @param array<string, mixed> $order
     *
     * @return list<string>
     */
    private function row(array $order): array
    {
        $harga   = (int) ($order['product_harga'] ?? 0);
        $nominal = (int) ($order['nominal'] ?? 0);

        return array_map([$this, 'cell'], [
            $order['nomor_invoice'] ?? '',
            $order['order_id'] ?? '',
            $order['created_at'] ?? '',
            $order['nama_pembeli'] ?? '',
            $order['email'] ?? '',
            $order['whatsapp'] ?? '',
            $order['product_nama'] ?? '',
            $harga,
            max(0, $harga - $nominal),
            $nominal,
            status_pesanan_label((string) ($order['status'] ?? '')),
            status_email_label((string) ($order['status_email'] ?? '')),
            payment_method_label(isset($order['payment_method']) ? (string) $order['payment_method'] : null),
            $order['payment_reference'] ?? '',
            $order['paid_at'] ?? '',
            $order['expires_at'] ?? '',
        ]);
    }

    private function cell(mixed $value): string
    {
        $value = (string) $value;

        if ($value !== '' && preg_match('/^[=+\-@\t\r]/', $value) === 1) {
            return "'" . $value;
        }

        return $value;
    }
}

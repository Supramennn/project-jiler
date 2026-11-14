<?php

namespace App\Libraries;

use App\Models\OrderEventModel;
use App\Models\OrderModel;
use RuntimeException;

class PaymentService
{
    public const METHODS = ['permata_va', 'gopay', 'qris'];

    /**
     * @return array{driver: string, redirect_url: string, instructions: list<string>, reference: string}
     */
    public function createTransaction(array $order, string $method): array
    {
        if (! in_array($method, self::METHODS, true)) {
            throw new RuntimeException('Metode pembayaran tidak dikenal.');
        }

        $config = config('Payment');

        if ($config->usesMidtrans()) {
            $result = $this->createSnapTransaction($order, $method);
            $this->savePayment($order, $method, $result['token'], $result['redirect_url']);

            return [
                'driver'       => 'midtrans',
                'redirect_url' => $result['redirect_url'],
                'instructions' => [],
                'reference'    => $result['token'],
            ];
        }

        $reference = $this->sandboxReference($method);

        $this->savePayment($order, $method, $reference, site_url('pembayaran/' . $order['order_id']));

        return [
            'driver'       => 'sandbox',
            'redirect_url' => site_url('pembayaran/' . $order['order_id']),
            'instructions' => $this->sandboxInstructions($method, $reference),
            'reference'    => $reference,
        ];
    }

    public function sandboxNotification(array $order): array
    {
        $payload = [
            'order_id'           => $order['order_id'],
            'transaction_status' => 'settlement',
            'status_code'        => '200',
            'gross_amount'       => number_format((float) $order['nominal'], 2, '.', ''),
            'payment_type'       => match ($order['payment_method']) {
                'gopay' => 'gopay',
                'qris'  => 'other_qris',
                default => 'bank_transfer',
            },
            'transaction_id'     => 'sandbox-' . strtolower((string) preg_replace('/[^A-Za-z0-9]/', '', $order['order_id'])),
            'fraud_status'       => 'accept',
            'status_message'     => 'Sandbox payment accepted',
        ];

        $payload['signature_key'] = $this->signature($payload);

        return $payload;
    }

    public function signature(array $payload): string
    {
        $key = (string) env('APP_KEY', 'sandbox-key');

        return hash_hmac(
            'sha256',
            $payload['order_id'] . '|' . $payload['status_code'] . '|' . $payload['gross_amount'],
            $key
        );
    }

    public function midtransSignature(array $payload): string
    {
        $serverKey = (string) config('Payment')->midtransServerKey;

        return hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey
        );
    }

    public function sandboxInstructions(string $method, string $reference): array
    {
        return match ($method) {
            'permata_va' => [
                'Buka mobile banking atau internet banking Permata.',
                'Pilih menu transfer ke virtual account.',
                'Masukkan nomor VA ' . $reference . ' dan nominal yang tertera.',
                'Mode sandbox: tidak ada uang yang benar-benar ditagih.',
            ],
            'gopay' => [
                'Buka aplikasi GoPay di ponsel.',
                'Pilih menu bayar, lalu masukkan kode ' . $reference . '.',
                'Periksa nominal, lalu konfirmasi pembayaran.',
                'Mode sandbox: tidak ada uang yang benar-benar ditagih.',
            ],
            'qris' => [
                'Buka aplikasi e-wallet atau mobile banking yang mendukung QRIS.',
                'Pilih menu scan QRIS.',
                'Gunakan kode simulasi ' . $reference . ' pada halaman ini.',
                'Mode sandbox: tidak ada uang yang benar-benar ditagih.',
            ],
            default => [],
        };
    }

    /**
     * @return array{token: string, redirect_url: string}
     */
    private function createSnapTransaction(array $order, string $method): array
    {
        $config = config('Payment');

        $payload = [
            'transaction_details' => [
                'order_id'     => $order['order_id'],
                'gross_amount' => (int) $order['nominal'],
            ],
            'item_details' => [[
                'id'       => (string) $order['product_id'],
                'price'    => (int) $order['nominal'],
                'quantity' => 1,
                'name'     => mb_substr($order['product_nama'], 0, 50),
            ]],
            'customer_details' => [
                'first_name' => $order['nama_pembeli'],
                'email'      => $order['email'],
                'phone'      => $order['whatsapp'],
            ],
            'enabled_payments' => match ($method) {
                'permata_va' => ['permata_va'],
                'gopay'      => ['gopay'],
                'qris'       => ['other_qris'],
                default      => [],
            },
            'expiry' => [
                'unit'     => 'hours',
                'duration' => (int) $config->expiryHours,
            ],
            'callbacks' => [
                'finish' => site_url('sukses/' . $order['order_id']),
            ],
        ];

        $curl = curl_init($config->midtransSnapUrl);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_USERPWD        => $config->midtransServerKey . ':',
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $status   = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false) {
            throw new RuntimeException('Tidak bisa menghubungi Midtrans: ' . $error);
        }

        $decoded = json_decode($response, true);

        if ($status < 200 || $status >= 300 || ! is_array($decoded)) {
            $message = is_array($decoded) && isset($decoded['error_messages'])
                ? implode(', ', (array) $decoded['error_messages'])
                : 'HTTP ' . $status;

            throw new RuntimeException('Midtrans menolak transaksi: ' . $message);
        }

        if (empty($decoded['token']) || empty($decoded['redirect_url'])) {
            throw new RuntimeException('Respons Midtrans tidak berisi token atau redirect_url.');
        }

        return ['token' => (string) $decoded['token'], 'redirect_url' => (string) $decoded['redirect_url']];
    }

    private function savePayment(array $order, string $method, string $reference, string $url): void
    {
        model(OrderModel::class)->update($order['id'], [
            'payment_method'    => $method,
            'payment_reference' => $reference,
            'payment_url'       => $url,
        ]);

        model(OrderEventModel::class)->log(
            $order['order_id'],
            'metode_bayar',
            'Metode pembayaran dipilih: ' . payment_method_label($method) . '.',
            ['reference' => $reference]
        );
    }

    private function sandboxReference(string $method): string
    {
        if ($method === 'qris') {
            return 'QRIS-' . strtoupper(bin2hex(random_bytes(5)));
        }

        $digits = '';

        for ($i = 0; $i < 12; $i++) {
            $digits .= (string) random_int(0, 9);
        }

        return $method === 'permata_va' ? '8' . $digits . '000' : 'GOPAY' . $digits;
    }
}

<?php

namespace App\Commands;

use App\Libraries\OrderFulfillment;
use App\Models\OrderModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RetryEmails extends BaseCommand
{
    protected $group       = 'Nexus Diji';
    protected $name        = 'email:retry';
    protected $description = 'Mengirim ulang email akses untuk pesanan dibayar yang emailnya gagal.';
    protected $usage       = 'email:retry [jumlah]';
    protected $arguments   = [
        'jumlah' => 'Jumlah maksimal pesanan yang diproses. Default 20.',
    ];

    public function run(array $params)
    {
        $limit  = max(1, (int) ($params[0] ?? 20));
        $orders = model(OrderModel::class)->failedEmailOrders($limit);

        if ($orders === []) {
            CLI::write('Tidak ada email gagal yang perlu dikirim ulang.', 'green');

            return 0;
        }

        $sent   = 0;
        $failed = 0;

        foreach ($orders as $order) {
            CLI::write('Mengirim ulang ' . $order['order_id'] . ' ke ' . $order['email'] . '...');

            $result = (new OrderFulfillment())->resendAccessEmail($order);

            if ($result['ok']) {
                $sent++;
                CLI::write('  terkirim', 'green');
                continue;
            }

            $failed++;
            CLI::write('  gagal: ' . ($result['error'] ?? 'kesalahan tidak diketahui'), 'red');
        }

        CLI::write('Selesai: ' . $sent . ' terkirim, ' . $failed . ' masih gagal.', $failed === 0 ? 'green' : 'yellow');

        return $failed === 0 ? 0 : 1;
    }
}

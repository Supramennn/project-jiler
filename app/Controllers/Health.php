<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Health extends BaseController
{
    public function index(): ResponseInterface
    {
        $checks   = [];
        $warnings = [];
        $healthy  = true;

        try {
            db_connect()->query('SELECT 1');
            $checks['database'] = 'ok';
        } catch (Throwable $e) {
            $healthy            = false;
            $checks['database'] = 'gagal';
            log_message('error', 'Health check database gagal: {message}', ['message' => $e->getMessage()]);
        }

        if ($healthy) {
            try {
                db_connect()->query('SELECT 1 FROM orders LIMIT 1');
                $checks['schema'] = 'ok';
            } catch (Throwable $e) {
                $healthy          = false;
                $checks['schema'] = 'gagal';
                log_message('error', 'Health check skema gagal: {message}', ['message' => $e->getMessage()]);
            }
        }

        $payment               = config('Payment');
        $mail                  = config('Mailer');
        $checks['payment']     = $payment->effectiveDriver();
        $checks['mail']        = $mail->effectiveDriver();
        $checks['cron_secret'] = trim((string) env('CRON_SECRET', '')) !== '';

        if (ENVIRONMENT === 'production' && $payment->effectiveDriver() !== 'midtrans') {
            $warnings[] = 'Pembayaran masih memakai driver sandbox.';
        }

        if (ENVIRONMENT === 'production' && $mail->effectiveDriver() !== 'smtp') {
            $warnings[] = 'Email masih memakai driver file, akses tidak terkirim ke pembeli.';
        }

        if ($checks['cron_secret'] === false) {
            $warnings[] = 'CRON_SECRET belum diatur; retry email dan rekonsiliasi tidak bisa dijalankan.';
        }

        return $this->response
            ->setStatusCode($healthy ? 200 : 503)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Robots-Tag', 'noindex, nofollow')
            ->setJSON([
                'status'      => $healthy ? 'ok' : 'down',
                'time'        => date('c'),
                'environment' => ENVIRONMENT,
                'checks'      => $checks,
                'warnings'    => $warnings,
            ]);
    }
}

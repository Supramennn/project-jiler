<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ThrottleRequests implements FilterInterface
{
    /**
     * @param list<string>|null $arguments [limit, window detik, nama bucket]
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $limit  = max(1, (int) ($arguments[0] ?? 10));
        $window = max(1, (int) ($arguments[1] ?? 60));
        $bucket = trim((string) ($arguments[2] ?? $request->getPath()));

        $key = 'rl_' . md5($bucket . '|' . $this->clientIp($request));

        try {
            $throttler = service('throttler');
            $allowed   = $throttler->check($key, $limit, $window);
        } catch (Throwable $e) {
            log_message('error', 'Rate limit tidak tersedia: {message}', ['message' => $e->getMessage()]);

            return null;
        }

        if ($allowed) {
            return null;
        }

        $retry    = max(1, $throttler->getTokenTime());
        $response = service('response')
            ->setStatusCode(429)
            ->setHeader('Retry-After', (string) $retry)
            ->setHeader('Cache-Control', 'no-store');

        $response = (new SecurityHeaders())->after($request, $response);

        $message = 'Terlalu banyak percobaan. Coba lagi dalam ' . $retry . ' detik.';

        if (str_contains($request->getHeaderLine('Accept'), 'application/json')) {
            return $response->setJSON(['ok' => false, 'message' => $message]);
        }

        return $response->setBody(view('errors/rate_limit', ['retry' => $retry]));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    /**
     * Vercel menimpa X-Forwarded-For dengan IP publik pengunjung.
     * FrankenPHP dapat mengisi REMOTE_ADDR dengan alamat socket, jadi header
     * ini dipakai lebih dulu agar bucket tetap per pengunjung.
     */
    private function clientIp(RequestInterface $request): string
    {
        foreach (['X-Forwarded-For', 'X-Real-IP'] as $header) {
            $value = trim($request->getHeaderLine($header));

            if ($value === '') {
                continue;
            }

            $candidate = trim(explode(',', $value)[0]);

            if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                return $candidate;
            }
        }

        return $request->getIPAddress();
    }
}

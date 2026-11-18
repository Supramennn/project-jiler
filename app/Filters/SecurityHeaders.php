<?php

namespace App\Filters;

use CodeIgniter\Filters\SecureHeaders as BaseSecureHeaders;

class SecurityHeaders extends BaseSecureHeaders
{
    /**
     * @var array<string, string>
     */
    protected $headers = [
        'X-Frame-Options'                    => 'SAMEORIGIN',
        'X-Content-Type-Options'             => 'nosniff',
        'X-Download-Options'                 => 'noopen',
        'X-Permitted-Cross-Domain-Policies'  => 'none',
        'Referrer-Policy'                    => 'strict-origin-when-cross-origin',
        'Permissions-Policy'                 => 'camera=(), geolocation=(), microphone=()',
    ];
}

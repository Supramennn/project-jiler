<?php

use Config\Store;

if (! function_exists('store')) {
    function store(): Store
    {
        return config('Store');
    }
}

if (! function_exists('store_name')) {
    function store_name(): string
    {
        $name = trim((string) config('Store')->name);

        return $name !== '' ? $name : 'Nama Usaha';
    }
}

if (! function_exists('store_has_name')) {
    function store_has_name(): bool
    {
        return trim((string) config('Store')->name) !== '';
    }
}

if (! function_exists('store_initials')) {
    function store_initials(): string
    {
        $name = trim((string) config('Store')->name);

        if ($name === '') {
            return 'NU';
        }

        $words = preg_split('/\s+/u', $name) ?: [];
        $words = array_values(array_filter($words, static fn (string $word): bool => $word !== ''));

        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((int) $amount, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    function tanggal_id(?string $datetime, bool $withTime = true): string
    {
        if ($datetime === null || $datetime === '') {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $ts = strtotime($datetime);

        if ($ts === false) {
            return '-';
        }

        $hasil = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);

        return $withTime ? $hasil . ', ' . date('H:i', $ts) : $hasil;
    }
}

if (! function_exists('status_pesanan_label')) {
    function status_pesanan_label(string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu bayar',
            'paid'    => 'Sudah dibayar',
            'expired' => 'Kedaluwarsa',
            'gagal'   => 'Gagal',
            default   => ucfirst($status),
        };
    }
}

if (! function_exists('status_pesanan_tone')) {
    function status_pesanan_tone(string $status): string
    {
        return match ($status) {
            'paid'    => 'ok',
            'pending' => 'warn',
            'expired' => 'muted',
            'gagal'   => 'danger',
            default   => 'muted',
        };
    }
}

if (! function_exists('status_email_label')) {
    function status_email_label(string $status): string
    {
        return match ($status) {
            'terkirim' => 'Terkirim',
            'gagal'    => 'Gagal',
            'belum'    => 'Belum dikirim',
            default    => ucfirst($status),
        };
    }
}

if (! function_exists('status_email_tone')) {
    function status_email_tone(string $status): string
    {
        return match ($status) {
            'terkirim' => 'ok',
            'gagal'    => 'danger',
            default    => 'muted',
        };
    }
}

if (! function_exists('mask_email')) {
    function mask_email(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $visible = mb_substr($name, 0, 2);

        return $visible . str_repeat('*', max(2, mb_strlen($name) - 2)) . '@' . $domain;
    }
}

if (! function_exists('normalize_phone')) {
    function normalize_phone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}

if (! function_exists('product_features')) {
    function product_features(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== ''));
    }
}

if (! function_exists('complexity_tone')) {
    function complexity_tone(string $complexity): string
    {
        return match (strtolower($complexity)) {
            'kompleks' => 'danger',
            'menengah' => 'warn',
            default    => 'muted',
        };
    }
}

if (! function_exists('payment_method_label')) {
    function payment_method_label(?string $method): string
    {
        return match ($method) {
            'permata_va' => 'VA Permata',
            'gopay'      => 'GoPay',
            'qris'       => 'QRIS',
            default      => 'Belum dipilih',
        };
    }
}

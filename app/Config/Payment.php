<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Payment extends BaseConfig
{
    public string $driver = 'sandbox';

    public int $expiryHours = 24;

    public string $midtransServerKey = '';
    public string $midtransClientKey = '';
    public bool $midtransProduction  = false;
    public string $midtransSnapUrl   = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    public function __construct()
    {
        parent::__construct();

        $this->driver             = (string) env('PAYMENT_DRIVER', $this->driver);
        $this->expiryHours        = (int) env('PAYMENT_EXPIRY_HOURS', $this->expiryHours);
        $this->midtransServerKey  = (string) env('MIDTRANS_SERVER_KEY', $this->midtransServerKey);
        $this->midtransClientKey  = (string) env('MIDTRANS_CLIENT_KEY', $this->midtransClientKey);
        $this->midtransProduction = filter_var(env('MIDTRANS_IS_PRODUCTION', $this->midtransProduction), FILTER_VALIDATE_BOOLEAN);

        $defaultSnapUrl = $this->midtransProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $this->midtransSnapUrl = (string) env('MIDTRANS_SNAP_URL', $defaultSnapUrl);
    }

    public function usesMidtrans(): bool
    {
        return $this->driver === 'midtrans' && $this->midtransServerKey !== '';
    }

    public function effectiveDriver(): string
    {
        return $this->usesMidtrans() ? 'midtrans' : 'sandbox';
    }
}

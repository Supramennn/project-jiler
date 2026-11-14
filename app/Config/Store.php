<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Store extends BaseConfig
{
    public string $name       = '';
    public string $tagline    = 'Template stok siap pakai untuk usaha kecil';
    public string $email      = 'halo@namausaha.test';
    public string $whatsapp   = '';
    public string $address    = 'Alamat usaha belum diatur';
    public string $invoicePrefix = 'INV';
    public string $producer   = 'Nexus Diji';

    public string $adminName         = 'Admin';
    public string $adminEmail        = 'admin@nexusdiji.test';
    public string $adminPasswordHash = '';
    public string $adminNotifyEmail  = 'admin@nexusdiji.test';

    public function __construct()
    {
        parent::__construct();

        $this->name       = (string) env('STORE_NAME', $this->name);
        $this->tagline    = (string) env('STORE_TAGLINE', $this->tagline);
        $this->email      = (string) env('STORE_EMAIL', $this->email);
        $this->whatsapp   = (string) env('STORE_WHATSAPP', $this->whatsapp);
        $this->address    = (string) env('STORE_ADDRESS', $this->address);
        $this->invoicePrefix = (string) env('INVOICE_PREFIX', $this->invoicePrefix);
        $this->producer   = (string) env('STORE_PRODUCER', $this->producer);

        $this->adminName         = (string) env('APP_ADMIN_NAME', $this->adminName);
        $this->adminEmail        = (string) env('APP_ADMIN_EMAIL', $this->adminEmail);
        $this->adminPasswordHash = (string) env('APP_ADMIN_PASSWORD_HASH', $this->adminPasswordHash);
        $this->adminNotifyEmail  = (string) env('ADMIN_NOTIFY_EMAIL', $this->adminEmail);
    }
}

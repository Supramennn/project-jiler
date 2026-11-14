<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Mailer extends BaseConfig
{
    public string $driver = 'file';

    public string $fromEmail = 'halo@namausaha.test';
    public string $fromName  = '';

    public string $smtpHost   = 'smtp.gmail.com';
    public int $smtpPort      = 587;
    public string $smtpUser  = '';
    public string $smtpPass  = '';
    public string $smtpCrypto = 'tls';

    public function __construct()
    {
        parent::__construct();

        $this->driver    = (string) env('MAIL_DRIVER', $this->driver);
        $this->fromEmail = (string) env('MAIL_FROM_EMAIL', $this->fromEmail);
        $this->fromName  = (string) env('MAIL_FROM_NAME', $this->fromName);
        $this->smtpHost  = (string) env('MAIL_SMTP_HOST', $this->smtpHost);
        $this->smtpPort  = (int) env('MAIL_SMTP_PORT', $this->smtpPort);
        $this->smtpUser  = (string) env('MAIL_SMTP_USER', $this->smtpUser);
        $this->smtpPass  = (string) env('MAIL_SMTP_PASS', $this->smtpPass);
        $this->smtpCrypto = (string) env('MAIL_SMTP_CRYPTO', $this->smtpCrypto);
    }

    public function usesSmtp(): bool
    {
        return $this->driver === 'smtp' && $this->smtpUser !== '' && $this->smtpPass !== '';
    }

    public function effectiveDriver(): string
    {
        return $this->usesSmtp() ? 'smtp' : 'file';
    }
}

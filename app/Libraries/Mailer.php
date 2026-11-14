<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;
use Config\Mailer as MailerConfig;
use RuntimeException;

class Mailer
{
    private MailerConfig $config;

    public function __construct(?MailerConfig $config = null)
    {
        $this->config = $config ?? config('Mailer');
    }

    /**
     * @return array{driver: string, path?: string}
     */
    public function send(string $to, string $subject, string $html): array
    {
        if ($this->config->effectiveDriver() === 'file') {
            return $this->writeFile($to, $subject, $html);
        }

        return $this->sendSmtp($to, $subject, $html);
    }

    /**
     * @return array{driver: string, path: string}
     */
    private function writeFile(string $to, string $subject, string $html): array
    {
        $directory = WRITEPATH . 'emails';

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Folder writable/emails tidak bisa dibuat.');
        }

        $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $to));
        $name = date('Ymd-His') . '-' . trim($slug, '-') . '-' . bin2hex(random_bytes(3)) . '.html';
        $path = $directory . DIRECTORY_SEPARATOR . $name;

        $document = "<!doctype html>\n<html lang=\"id\"><head><meta charset=\"utf-8\">"
            . '<title>' . esc($subject) . '</title></head><body>'
            . $html
            . '</body></html>';

        if (file_put_contents($path, $document) === false) {
            throw new RuntimeException('Email gagal ditulis ke folder writable/emails.');
        }

        return ['driver' => 'file', 'path' => $path];
    }

    /**
     * @return array{driver: string}
     */
    private function sendSmtp(string $to, string $subject, string $html): array
    {
        $email = new Email(new EmailConfig());

        $email->initialize([
            'protocol'   => 'smtp',
            'SMTPHost'   => $this->config->smtpHost,
            'SMTPUser'   => $this->config->smtpUser,
            'SMTPPass'   => $this->config->smtpPass,
            'SMTPPort'   => $this->config->smtpPort,
            'SMTPCrypto' => $this->config->smtpCrypto,
            'SMTPTimeout' => 20,
            'mailType'   => 'html',
            'charset'    => 'UTF-8',
            'wordWrap'   => false,
            'validate'   => true,
            'fromEmail'  => $this->config->fromEmail,
            'fromName'   => $this->config->fromName !== '' ? $this->config->fromName : store_name(),
        ]);

        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($html);
        $email->setAltMessage(trim(strip_tags($html)));

        if (! $email->send()) {
            $detail = trim(strip_tags($email->printDebugger(['headers'])));
            throw new RuntimeException('SMTP gagal: ' . ($detail !== '' ? $detail : 'tidak ada detail dari server.'));
        }

        return ['driver' => 'smtp'];
    }
}

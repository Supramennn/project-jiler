<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Mailer;
use App\Models\EmailLogModel;
use Throwable;

class Settings extends BaseController
{
    public function index(): string
    {
        $files = glob(WRITEPATH . 'emails/*.html') ?: [];
        rsort($files);

        return $this->renderAdmin('admin/settings/index', [
            'paymentDriver' => config('Payment')->effectiveDriver(),
            'paymentConfig' => config('Payment'),
            'mailDriver'    => config('Mailer')->effectiveDriver(),
            'mailConfig'    => config('Mailer'),
            'env'           => ENVIRONMENT,
            'webhookUrl'    => site_url('webhook/midtrans'),
            'emailFiles'    => array_slice($files, 0, 5),
        ], [
            'title'  => 'Pengaturan',
            'active' => 'settings',
        ]);
    }

    public function testEmail()
    {
        $store = config('Store');
        $subject = 'Uji pengiriman email ' . store_name();
        $html = view('emails/admin_alert', [
            'subject' => $subject,
            'message' => 'Jika email ini diterima, konfigurasi pengiriman email berjalan.',
            'orderId' => null,
            'store'   => $store,
        ]);

        try {
            $sent = (new Mailer())->send($store->adminNotifyEmail, $subject, $html);
            model(EmailLogModel::class)->record(null, 'uji', $store->adminNotifyEmail, $subject, 'terkirim');

            $message = $sent['driver'] === 'file'
                ? 'Email uji disimpan di ' . $sent['path'] . '. Mode file tidak mengirim email sungguhan.'
                : 'Email uji berhasil dikirim ke ' . $store->adminNotifyEmail . '.';

            return redirect()->to('admin/pengaturan')->with('success', $message);
        } catch (Throwable $e) {
            model(EmailLogModel::class)->record(null, 'uji', $store->adminNotifyEmail, $subject, 'gagal', $e->getMessage());

            return redirect()->to('admin/pengaturan')->with('error', 'Email uji gagal: ' . $e->getMessage());
        }
    }
}

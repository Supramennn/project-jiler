<?php

namespace App\Libraries;

use App\Models\EmailLogModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Throwable;

class OrderMailer
{
    private Mailer $mailer;

    public function __construct(?Mailer $mailer = null)
    {
        $this->mailer = $mailer ?? new Mailer();
    }

    /**
     * @return array{ok: bool, skipped?: bool, error?: string, driver?: string}
     */
    public function sendAccess(array $order, bool $force = false): array
    {
        if (($order['status_email'] ?? '') === 'terkirim' && ! $force) {
            return ['ok' => true, 'skipped' => true];
        }

        $orderModel = model(OrderModel::class);
        $logModel   = model(EmailLogModel::class);
        $subject    = 'Invoice ' . $order['nomor_invoice'] . ' dan akses template ' . $order['product_nama'];

        try {
            $html = $this->renderAccessEmail($order);
            $sent = $this->mailer->send($order['email'], $subject, $html);

            $orderModel->update($order['id'], [
                'status_email'  => 'terkirim',
                'email_sent_at' => date('Y-m-d H:i:s'),
            ]);

            $logModel->record($order['order_id'], 'akses', $order['email'], $subject, 'terkirim');

            model(\App\Models\OrderEventModel::class)->log(
                $order['order_id'],
                'email',
                'Email invoice dan akses terkirim ke pembeli.',
                ['driver' => $sent['driver']]
            );

            return ['ok' => true, 'driver' => $sent['driver']];
        } catch (Throwable $e) {
            $orderModel->update($order['id'], [
                'status_email' => 'gagal',
                'jumlah_retry' => ((int) $order['jumlah_retry']) + 1,
            ]);

            $logModel->record($order['order_id'], 'akses', $order['email'], $subject, 'gagal', $e->getMessage());

            model(\App\Models\OrderEventModel::class)->log(
                $order['order_id'],
                'email_gagal',
                'Email gagal dikirim: ' . $e->getMessage()
            );

            $this->notifyAdmin(
                'Email gagal untuk pesanan ' . $order['order_id'],
                'Email akses untuk ' . $order['email'] . ' gagal dikirim. Buka dashboard untuk mengirim ulang. Detail: ' . $e->getMessage(),
                $order['order_id']
            );

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function renderAccessEmail(array $order): string
    {
        $product = model(ProductModel::class)->find((int) $order['product_id']);
        $links   = [
            'template' => $product['link_template'] ?? '',
            'video'    => $product['link_video'] ?? '',
            'panduan'  => $product['link_panduan'] ?? '',
        ];

        return view('emails/order_paid', [
            'order'      => $order,
            'links'      => $links,
            'invoiceUrl' => site_url('invoice/' . $order['order_id'] . '/' . $order['access_token']),
            'store'      => config('Store'),
            'fitur'      => product_features($product['fitur'] ?? ''),
        ]);
    }

    public function notifyAdmin(string $subject, string $message, ?string $orderId = null): void
    {
        $store = config('Store');

        try {
            $html = view('emails/admin_alert', [
                'subject' => $subject,
                'message' => $message,
                'orderId' => $orderId,
                'store'   => $store,
            ]);

            $this->mailer->send($store->adminNotifyEmail, $subject, $html);
            model(EmailLogModel::class)->record($orderId, 'admin', $store->adminNotifyEmail, $subject, 'terkirim');
        } catch (Throwable $e) {
            model(EmailLogModel::class)->record($orderId, 'admin', $store->adminNotifyEmail, $subject, 'gagal', $e->getMessage());
            log_message('error', 'Notifikasi admin gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}

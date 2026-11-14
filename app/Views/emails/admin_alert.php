<?php
$adminUrl = $orderId !== null ? site_url('admin/pesanan/' . $orderId) : site_url('admin');
?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f1e7;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;background:#fbf8f1;border:1px solid #d9ceba;border-top:3px solid #a77b3b;">
                <tr>
                    <td style="padding:24px 28px;border-bottom:1px solid #d9ceba;">
                        <p style="margin:0;font-family:Georgia,serif;font-size:22px;font-weight:bold;color:#1e1b16;"><?= esc(store_name()) ?></p>
                        <p style="margin:5px 0 0;font-family:Arial,sans-serif;font-size:11px;color:#625a4d;">Notifikasi dashboard</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 28px;">
                        <h1 style="margin:0 0 12px;font-family:Georgia,serif;font-size:26px;line-height:1.2;color:#1e1b16;font-weight:normal;"><?= esc($subject) ?></h1>
                        <p style="margin:0;font-family:Arial,sans-serif;font-size:13px;line-height:1.7;color:#37322a;"><?= nl2br(esc($message)) ?></p>
                        <p style="margin:20px 0 0;">
                            <a href="<?= esc($adminUrl) ?>" style="display:inline-block;padding:11px 16px;background:#1e1b16;color:#f6f1e7;text-decoration:none;border-radius:4px;font-family:Arial,sans-serif;font-size:13px;font-weight:bold;">Buka dashboard</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 28px;border-top:1px solid #d9ceba;">
                        <p style="margin:0;font-family:Arial,sans-serif;font-size:10.5px;line-height:1.6;color:#8a8173;">Email otomatis dari <?= esc(store_name()) ?>. Jangan balas email ini.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

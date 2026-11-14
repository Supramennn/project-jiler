<?php
$hasTemplate = ! empty($links['template']);
$hasVideo    = ! empty($links['video']);
$hasGuide    = ! empty($links['panduan']);
$linkStyle   = 'display:inline-block;padding:12px 18px;margin:6px 8px 6px 0;background:#7a5620;color:#fffaf0;text-decoration:none;border-radius:4px;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;';
?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f1e7;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;background:#fbf8f1;border:1px solid #d9ceba;border-top:3px solid #a77b3b;">
                <tr>
                    <td style="padding:28px 32px 22px;border-bottom:2px solid #1e1b16;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-family:Georgia,serif;font-size:24px;font-weight:bold;color:#1e1b16;"><?= esc(store_name()) ?></td>
                                <td align="right" style="font-family:Arial,sans-serif;font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#7a5620;">Invoice dan akses</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 32px 8px;">
                        <p style="margin:0 0 10px;font-family:Arial,sans-serif;font-size:12px;color:#7a5620;font-weight:bold;">PEMBAYARAN DITERIMA</p>
                        <h1 style="margin:0;font-family:Georgia,serif;font-size:30px;line-height:1.15;color:#1e1b16;font-weight:normal;">Akses template Anda sudah siap.</h1>
                        <p style="margin:14px 0 0;font-family:Arial,sans-serif;font-size:14px;line-height:1.7;color:#625a4d;">Halo <?= esc($order['nama_pembeli']) ?>, pembayaran untuk pesanan <strong><?= esc($order['order_id']) ?></strong> sudah tercatat. Simpan email ini karena berisi invoice dan link akses.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fffdf8;border:1px solid #d9ceba;">
                            <tr>
                                <td style="padding:18px 20px;border-bottom:1px solid #d9ceba;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-family:Arial,sans-serif;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#625a4d;">Nomor invoice</td>
                                            <td align="right" style="font-family:Arial,sans-serif;font-size:14px;font-weight:bold;color:#1e1b16;"><?= esc($order['nomor_invoice']) ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:18px 20px;border-bottom:1px solid #d9ceba;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-family:Arial,sans-serif;font-size:13px;color:#625a4d;"><?= esc($order['product_nama']) ?><br><span style="font-size:11px;color:#8a8173;">Template Google Sheets dan web app Apps Script</span></td>
                                            <td align="right" style="font-family:Arial,sans-serif;font-size:14px;font-weight:bold;color:#1e1b16;white-space:nowrap;"><?= rupiah($order['nominal']) ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:16px 20px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-family:Arial,sans-serif;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#625a4d;">Status</td>
                                            <td align="right" style="font-family:Arial,sans-serif;font-size:14px;font-weight:bold;color:#2c6047;">LUNAS</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:14px 0 0;font-family:Arial,sans-serif;font-size:12px;color:#625a4d;">Tanggal bayar: <?= tanggal_id($order['paid_at']) ?></p>
                        <p style="margin:14px 0 0;">
                            <a href="<?= esc($invoiceUrl) ?>" style="display:inline-block;padding:10px 16px;border:1px solid #7a5620;color:#7a5620;text-decoration:none;border-radius:4px;font-family:Arial,sans-serif;font-size:13px;font-weight:bold;">Buka invoice online</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 32px 4px;">
                        <h2 style="margin:0;font-family:Georgia,serif;font-size:24px;color:#1e1b16;font-weight:normal;">Link akses</h2>
                        <p style="margin:8px 0 14px;font-family:Arial,sans-serif;font-size:13px;line-height:1.6;color:#625a4d;">Gunakan link berikut untuk menyalin template dan mengikuti panduan deploy.</p>

                        <?php if ($hasTemplate): ?>
                            <a href="<?= esc($links['template']) ?>" style="<?= $linkStyle ?>">Salin template Google Sheets</a>
                        <?php endif; ?>
                        <?php if ($hasVideo): ?>
                            <a href="<?= esc($links['video']) ?>" style="<?= $linkStyle ?>background:#1e1b16;">Tonton video panduan</a>
                        <?php endif; ?>
                        <?php if ($hasGuide): ?>
                            <a href="<?= esc($links['panduan']) ?>" style="<?= $linkStyle ?>background:#fbf8f1;border:1px solid #7a5620;color:#7a5620;">Baca panduan teks</a>
                        <?php endif; ?>

                        <?php if (! $hasTemplate || ! $hasVideo || ! $hasGuide): ?>
                            <p style="margin:12px 0 0;padding:12px 14px;background:#f4e8cf;border:1px solid #dec79b;font-family:Arial,sans-serif;font-size:12px;line-height:1.6;color:#624312;">Sebagian link belum diatur admin. Hubungi <?= esc($store->email) ?> dengan menyertakan nomor pesanan.</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($fitur !== []): ?>
                    <tr>
                        <td style="padding:16px 32px 4px;">
                            <h2 style="margin:0 0 10px;font-family:Georgia,serif;font-size:22px;color:#1e1b16;font-weight:normal;">Isi template</h2>
                            <ul style="margin:0;padding:0 0 0 18px;font-family:Arial,sans-serif;font-size:13px;line-height:1.8;color:#37322a;">
                                <?php foreach ($fitur as $item): ?>
                                    <li><?= esc($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding:20px 32px 4px;">
                        <h2 style="margin:0 0 10px;font-family:Georgia,serif;font-size:22px;color:#1e1b16;font-weight:normal;">Urutan deploy</h2>
                        <ol style="margin:0;padding:0 0 0 18px;font-family:Arial,sans-serif;font-size:13px;line-height:1.8;color:#37322a;">
                            <li>Buka link template, lalu pilih <strong>Buat salinan</strong> ke Google Drive Anda.</li>
                            <li>Tonton video panduan untuk melihat urutan deploy web app.</li>
                            <li>Buka editor Apps Script dari salinan spreadsheet, lalu deploy sebagai web app sesuai panduan.</li>
                            <li>Simpan link web app dan buka dengan akun Google Anda.</li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td style="padding:22px 32px 28px;">
                        <p style="margin:0;padding:14px 16px;background:#e5efe7;border:1px solid #b9d3c1;font-family:Arial,sans-serif;font-size:12px;line-height:1.6;color:#234c39;">Link akses ini berlaku selamanya. Jangan membagikannya ke orang lain. Jika email ini salah kirim, hubungi admin segera.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 32px;border-top:1px solid #d9ceba;">
                        <p style="margin:0;font-family:Arial,sans-serif;font-size:11px;line-height:1.7;color:#8a8173;"><?= esc(store_name()) ?><br><?= esc($store->address) ?><br><?= esc($store->email) ?></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

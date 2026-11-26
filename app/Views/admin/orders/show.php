<?php
$canMarkPaid = in_array($order['status'], ['pending', 'expired'], true);
$canResend   = $order['status'] === 'paid';
$diskon      = max(0, (int) $order['product_harga'] - (int) $order['nominal']);
?>
<div class="admin-page-head">
    <div>
        <div class="detail-head">
            <span class="chip chip-<?= status_pesanan_tone($order['status']) ?>"><?= esc(status_pesanan_label($order['status'])) ?></span>
            <span class="chip chip-<?= status_email_tone($order['status_email']) ?>">Email: <?= esc(status_email_label($order['status_email'])) ?></span>
            <?php if ($order['payment_method'] !== null): ?>
                <span class="chip chip-muted"><?= esc(payment_method_label($order['payment_method'])) ?></span>
            <?php endif; ?>
        </div>
        <h2><?= esc($order['nomor_invoice']) ?></h2>
        <p>Pesanan <?= esc($order['order_id']) ?> dibuat <?= tanggal_id($order['created_at']) ?> untuk <?= esc($order['product_nama']) ?>.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan') ?>">Kembali</a>
        <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan/' . $order['order_id'] . '/invoice') ?>">Buka invoice</a>
        <?php if ($canResend): ?>
            <a class="btn btn-quiet btn-small" href="<?= site_url('admin/pesanan/' . $order['order_id'] . '/email') ?>" target="_blank" rel="noopener">Pratinjau email</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($order['status'] === 'paid' && $order['status_email'] === 'gagal'): ?>
    <div class="alert-box" role="alert">
        <div>
            <strong>Pembayaran sudah diterima, tetapi email akses gagal dikirim.</strong>
            Periksa konfigurasi email di halaman pengaturan, lalu gunakan tombol kirim ulang. Pembeli bisa membuka invoice dari halaman cek status, tetapi link template hanya ada di email.
        </div>
    </div>
<?php elseif ($order['status'] === 'pending'): ?>
    <div class="alert-box" role="status">
        <div>
            <strong>Pesanan masih menunggu pembayaran.</strong>
            Akses template belum dikirim. Jangan tandai dibayar kecuali dana benar-benar sudah diterima. Tombol simulasi hanya tersedia di halaman pembayaran saat mode sandbox.
        </div>
    </div>
<?php elseif (in_array($order['status'], ['expired', 'gagal'], true)): ?>
    <div class="alert-box" role="status">
        <div>
            <strong>Pesanan ini tidak lagi menunggu pembayaran.</strong>
            Jika dana tetap masuk, periksa mutasi gateway lalu tandai dibayar secara manual. Tindakan ini akan memproses email akses.
        </div>
    </div>
<?php endif; ?>

<div class="detail-grid">
    <div>
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Ringkasan pesanan</h2>
                    <p>Data yang dipakai untuk invoice dan pengiriman akses.</p>
                </div>
            </div>
            <div class="panel-body">
                <dl class="kv">
                    <dt>Nomor pesanan</dt>
                    <dd><?= esc($order['order_id']) ?></dd>

                    <dt>Nomor invoice</dt>
                    <dd><?= esc($order['nomor_invoice']) ?></dd>

                    <dt>Produk</dt>
                    <dd><?= esc($order['product_nama']) ?></dd>

                    <dt>Harga normal</dt>
                    <dd><?= rupiah($order['product_harga']) ?></dd>

                    <?php if ($diskon > 0): ?>
                        <dt>Diskon promo</dt>
                        <dd>-<?= rupiah($diskon) ?></dd>
                    <?php endif; ?>

                    <dt>Nominal</dt>
                    <dd><strong><?= rupiah($order['nominal']) ?></strong></dd>

                    <dt>Status pesanan</dt>
                    <dd><span class="chip chip-<?= status_pesanan_tone($order['status']) ?>"><?= esc(status_pesanan_label($order['status'])) ?></span></dd>

                    <dt>Status email</dt>
                    <dd><span class="chip chip-<?= status_email_tone($order['status_email']) ?>"><?= esc(status_email_label($order['status_email'])) ?></span></dd>

                    <dt>Metode bayar</dt>
                    <dd><?= esc(payment_method_label($order['payment_method'])) ?></dd>

                    <dt>Referensi bayar</dt>
                    <dd><?= $order['payment_reference'] !== null && $order['payment_reference'] !== '' ? esc($order['payment_reference']) : '-' ?></dd>

                    <dt>Dibuat</dt>
                    <dd><?= tanggal_id($order['created_at']) ?></dd>

                    <dt>Kedaluwarsa</dt>
                    <dd><?= tanggal_id($order['expires_at']) ?></dd>

                    <dt>Dibayar</dt>
                    <dd><?= tanggal_id($order['paid_at']) ?></dd>

                    <dt>Email terkirim</dt>
                    <dd><?= tanggal_id($order['email_sent_at']) ?></dd>

                    <dt>Kegagalan kirim</dt>
                    <dd><?= (int) $order['jumlah_retry'] ?> kali</dd>
                </dl>
            </div>
        </section>

        <section class="panel" style="margin-top: 22px;">
            <div class="panel-head">
                <div>
                    <h2>Riwayat pesanan</h2>
                    <p>Catatan dari checkout, gateway, dan dashboard.</p>
                </div>
            </div>
            <div class="panel-body">
                <?php if ($events === []): ?>
                    <p class="form-hint">Belum ada catatan untuk pesanan ini.</p>
                <?php else: ?>
                    <ol class="timeline">
                        <?php foreach ($events as $event): ?>
                            <li>
                                <strong><?= esc(ucwords(str_replace('_', ' ', $event['tipe']))) ?></strong>
                                <span><?= tanggal_id($event['created_at']) ?></span>
                                <p><?= esc($event['pesan']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div>
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Pembeli</h2>
                    <p>Data yang diisi saat checkout.</p>
                </div>
            </div>
            <div class="panel-body">
                <dl class="kv">
                    <dt>Nama</dt>
                    <dd><?= esc($order['nama_pembeli']) ?></dd>

                    <dt>Email</dt>
                    <dd><a href="mailto:<?= esc($order['email']) ?>"><?= esc($order['email']) ?></a></dd>

                    <dt>WhatsApp</dt>
                    <dd><?= esc($order['whatsapp']) ?></dd>
                </dl>

                <div class="detail-actions">
                    <a class="btn btn-ghost btn-small" href="mailto:<?= esc($order['email']) ?>">Kirim email manual</a>
                </div>
                <p class="form-hint">Email manual tidak menggantikan catatan pengiriman otomatis. Gunakan tombol kirim ulang di panel tindakan supaya status email ikut diperbarui.</p>
            </div>
        </section>

        <section class="panel" style="margin-top: 22px;">
            <div class="panel-head">
                <div>
                    <h2>Tindakan</h2>
                    <p>Perubahan langsung disimpan ke database.</p>
                </div>
            </div>
            <div class="panel-body">
                <div class="detail-actions" style="margin-top: 0; padding-top: 0; border-top: 0;">
                    <?php if ($canMarkPaid): ?>
                        <form action="<?= site_url('admin/pesanan/' . $order['order_id'] . '/tandai-paid') ?>" method="post" data-confirm="Tandai pesanan ini dibayar? Pastikan dana sudah diterima. Email akses akan langsung diproses.">
                            <?= csrf_field() ?>
                            <button class="btn btn-gold btn-small" type="submit">Tandai dibayar</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($canResend): ?>
                        <form action="<?= site_url('admin/pesanan/' . $order['order_id'] . '/kirim-ulang') ?>" method="post" data-confirm="Kirim ulang email invoice dan akses ke <?= esc($order['email']) ?>?">
                            <?= csrf_field() ?>
                            <button class="btn btn-primary btn-small" type="submit"><?= $order['status_email'] === 'gagal' ? 'Coba kirim ulang email' : 'Kirim ulang email akses' ?></button>
                        </form>
                    <?php endif; ?>

                    <?php if ($order['status'] === 'pending'): ?>
                        <a class="btn btn-ghost btn-small" href="<?= site_url('pembayaran/' . $order['order_id'] . '/' . $order['access_token']) ?>">Halaman pembayaran</a>
                    <?php endif; ?>

                    <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan/' . $order['order_id'] . '/invoice') ?>">Invoice</a>
                </div>

                <?php if ($canResend): ?>
                    <p class="form-hint">Mengirim ulang email akan memperbarui status email dan menambah log pengiriman baru.</p>
                <?php elseif ($order['status'] === 'pending'): ?>
                    <p class="form-hint">Tandai dibayar hanya untuk pembayaran yang sudah terverifikasi di luar webhook, misalnya mutasi bank yang terlambat.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="panel" style="margin-top: 22px;">
            <div class="panel-head">
                <div>
                    <h2>Log email</h2>
                    <p>Termasuk notifikasi admin dan email uji.</p>
                </div>
            </div>
            <div class="panel-body">
                <?php if ($emailLogs === []): ?>
                    <p class="form-hint">Belum ada email yang dicatat untuk pesanan ini.</p>
                <?php else: ?>
                    <ul class="action-list">
                        <?php foreach ($emailLogs as $log): ?>
                            <li class="action-item">
                                <div class="action-item-main">
                                    <strong><?= esc($log['subjek']) ?></strong>
                                    <span>Ke <?= esc($log['penerima']) ?> · <?= tanggal_id($log['created_at']) ?></span>
                                    <?php if ($log['error'] !== null && $log['error'] !== ''): ?>
                                        <span class="action-reason"><?= esc(mb_substr($log['error'], 0, 180)) ?><?= mb_strlen($log['error']) > 180 ? '...' : '' ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="chip chip-<?= status_email_tone($log['status']) ?>"><?= esc(status_email_label($log['status'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

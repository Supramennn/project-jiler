<?php
$isProduction = $env === 'production';
$httpsWebhook = str_starts_with($webhookUrl, 'https://');
$smtpReady    = $mailDriver === 'smtp';
$midtransKey  = $paymentConfig->midtransServerKey !== '';
$adminHash    = config('Store')->adminPasswordHash !== '';
?>
<div class="admin-page-head">
    <div>
        <h2>Pengaturan integrasi</h2>
        <p>Halaman ini menampilkan status konfigurasi yang sedang aktif. Rahasia tidak ditampilkan dan tidak bisa diubah dari browser; ubah berkas <code>.env</code> di server lalu muat ulang halaman.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-ghost btn-small" href="<?= site_url('admin') ?>">Ringkasan</a>
    </div>
</div>

<?php if (! store_has_name()): ?>
    <div class="alert-box" role="status">
        <div>
            <strong>Nama usaha masih kosong.</strong>
            Isi <code>STORE_NAME</code> di <code>.env</code> sebelum website dipakai. Selama kosong, halaman publik memakai placeholder "Nama Usaha" dan invoice memakai nama yang sama.
        </div>
    </div>
<?php endif; ?>

<div class="stat-strip">
    <div class="stat-cell">
        <span class="stat-label">Lingkungan</span>
        <strong class="stat-value" style="font-size: 1.55rem;"><?= esc($env) ?></strong>
        <span class="stat-sub"><?= $isProduction ? 'Website berjalan sebagai produksi' : 'Cocok untuk pengujian lokal' ?></span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Pembayaran</span>
        <strong class="stat-value" style="font-size: 1.55rem;"><?= $paymentDriver === 'midtrans' ? 'Midtrans' : 'Sandbox' ?></strong>
        <span class="stat-sub"><?= $paymentDriver === 'midtrans' ? ($paymentConfig->midtransProduction ? 'Mode produksi' : 'Mode sandbox Midtrans') : 'Simulator lokal, tanpa tagihan' ?></span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Email</span>
        <strong class="stat-value" style="font-size: 1.55rem;"><?= $smtpReady ? 'SMTP' : 'File' ?></strong>
        <span class="stat-sub"><?= $smtpReady ? esc($mailConfig->smtpHost . ':' . $mailConfig->smtpPort) : 'Disimpan di writable/emails' ?></span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Masa bayar</span>
        <strong class="stat-value" style="font-size: 1.55rem;"><?= (int) $paymentConfig->expiryHours ?> jam</strong>
        <span class="stat-sub">Batas waktu sebelum pesanan kedaluwarsa</span>
    </div>
</div>

<div class="form-grid">
    <div>
        <section class="panel panel-accent">
            <div class="panel-head">
                <div>
                    <h2>Webhook pembayaran</h2>
                    <p>Alamat yang didaftarkan di dashboard Midtrans.</p>
                </div>
            </div>
            <div class="panel-body">
                <p class="form-hint">Buka Midtrans, masuk ke Settings, lalu isi Payment Notification URL dengan alamat berikut. Jangan menambahkan garis miring di akhir.</p>
                <div class="code-line">
                    <code><?= esc($webhookUrl) ?></code>
                    <button class="btn btn-ghost btn-small" type="button" data-copy="<?= esc($webhookUrl) ?>">Salin</button>
                </div>

                <dl class="kv" style="margin-top: 20px;">
                    <dt>Driver aktif</dt>
                    <dd><span class="chip chip-<?= $paymentDriver === 'midtrans' ? 'ok' : 'warn' ?>"><?= $paymentDriver === 'midtrans' ? 'Midtrans' : 'Sandbox' ?></span></dd>

                    <dt>Server key</dt>
                    <dd><?= $midtransKey ? 'Terisi' : '<span class="chip chip-danger">Kosong</span>' ?></dd>

                    <dt>Mode Midtrans</dt>
                    <dd><?= $paymentConfig->midtransProduction ? 'Produksi' : 'Sandbox' ?></dd>

                    <dt>Alamat Snap</dt>
                    <dd style="font-size: 11.5px;"><?= esc($paymentConfig->midtransSnapUrl) ?></dd>
                </dl>

                <?php if ($paymentDriver === 'sandbox'): ?>
                    <div class="alert-box" style="margin-top: 18px; margin-bottom: 0;">
                        <div>
                            <strong>Pembayaran masih memakai simulator.</strong>
                            Jangan gunakan mode ini untuk menerima uang sungguhan. Isi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY, lalu set PAYMENT_DRIVER = midtrans untuk beralih.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="panel" style="margin-top: 22px;">
            <div class="panel-head">
                <div>
                    <h2>Pengiriman email</h2>
                    <p>Email invoice dan akses dikirim setelah pembayaran terverifikasi.</p>
                </div>
            </div>
            <div class="panel-body">
                <dl class="kv">
                    <dt>Driver aktif</dt>
                    <dd><span class="chip chip-<?= $smtpReady ? 'ok' : 'warn' ?>"><?= $smtpReady ? 'SMTP' : 'File lokal' ?></span></dd>

                    <dt>Alamat pengirim</dt>
                    <dd><?= esc($mailConfig->fromEmail) ?></dd>

                    <dt>Nama pengirim</dt>
                    <dd><?= esc($mailConfig->fromName !== '' ? $mailConfig->fromName : store_name()) ?></dd>

                    <dt>Host SMTP</dt>
                    <dd><?= esc($mailConfig->smtpHost) ?>:<?= (int) $mailConfig->smtpPort ?></dd>

                    <dt>Enkripsi</dt>
                    <dd><?= esc($mailConfig->smtpCrypto !== '' ? strtoupper($mailConfig->smtpCrypto) : 'Tidak diatur') ?></dd>

                    <dt>Pengguna SMTP</dt>
                    <dd><?= $mailConfig->smtpUser !== '' ? esc($mailConfig->smtpUser) : '-' ?></dd>
                </dl>

                <?php if (! $smtpReady): ?>
                    <p class="form-hint">Mode file menulis email ke folder <code>writable/emails</code>. Email tidak benar-benar terkirim. Isi MAIL_SMTP_USER dan MAIL_SMTP_PASS dengan akun Gmail dan App Password, lalu set MAIL_DRIVER = smtp.</p>
                <?php endif; ?>

                <form action="<?= site_url('admin/pengaturan/uji-email') ?>" method="post" style="margin-top: 18px;" data-confirm="Kirim email uji ke <?= esc(config('Store')->adminNotifyEmail) ?>?">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary btn-small" type="submit">Kirim email uji</button>
                </form>
                <p class="form-hint">Email uji dikirim ke <?= esc(config('Store')->adminNotifyEmail) ?>. Pada mode file, hasilnya muncul di daftar bawah.</p>
            </div>
        </section>

        <section class="panel" style="margin-top: 22px;">
            <div class="panel-head">
                <div>
                    <h2>Email terakhir di folder lokal</h2>
                    <p>Hanya terisi saat driver email memakai mode file.</p>
                </div>
            </div>
            <div class="panel-body">
                <?php if ($emailFiles === []): ?>
                    <p class="form-hint">Belum ada berkas email. Kirim email uji atau buat pesanan uji untuk menghasilkan berkas.</p>
                <?php else: ?>
                    <ul class="action-list">
                        <?php foreach ($emailFiles as $file): ?>
                            <li class="action-item">
                                <div class="action-item-main">
                                    <strong><?= esc(basename($file)) ?></strong>
                                    <span><?= tanggal_id(date('Y-m-d H:i:s', (int) filemtime($file))) ?> · <?= number_format((int) filesize($file) / 1024, 1, ',', '.') ?> KB</span>
                                </div>
                                <span class="chip chip-muted">File</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="form-hint">Buka langsung dari server untuk memeriksa tampilan email. Berkas ini tidak bisa diakses lewat URL publik.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div>
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Kesiapan produksi</h2>
                    <p>Periksa sebelum website dipakai menerima pembayaran.</p>
                </div>
            </div>
            <div class="panel-body">
                <ul class="checklist">
                    <li>
                        <span class="mark <?= store_has_name() ? 'mark-ok' : 'mark-warn' ?>"><?= store_has_name() ? '&#10003;' : '!' ?></span>
                        <span><strong>Nama usaha.</strong> Isi <code>STORE_NAME</code> di <code>.env</code> supaya header, invoice, dan email memakai nama yang benar.</span>
                    </li>
                    <li>
                        <span class="mark <?= $isProduction ? 'mark-ok' : 'mark-warn' ?>"><?= $isProduction ? '&#10003;' : '!' ?></span>
                        <span><strong>Lingkungan produksi.</strong> Set CI_ENVIRONMENT = production agar halaman error tidak menampilkan detail teknis.</span>
                    </li>
                    <li>
                        <span class="mark <?= $paymentDriver === 'midtrans' ? 'mark-ok' : 'mark-warn' ?>"><?= $paymentDriver === 'midtrans' ? '&#10003;' : '!' ?></span>
                        <span><strong>Midtrans aktif.</strong> Isi server key dan client key, set PAYMENT_DRIVER = midtrans, lalu daftarkan webhook.</span>
                    </li>
                    <li>
                        <span class="mark <?= $paymentConfig->midtransProduction ? 'mark-ok' : 'mark-warn' ?>"><?= $paymentConfig->midtransProduction ? '&#10003;' : '!' ?></span>
                        <span><strong>Mode produksi Midtrans.</strong> Set MIDTRANS_IS_PRODUCTION = true hanya setelah pengujian sandbox selesai.</span>
                    </li>
                    <li>
                        <span class="mark <?= $smtpReady ? 'mark-ok' : 'mark-warn' ?>"><?= $smtpReady ? '&#10003;' : '!' ?></span>
                        <span><strong>Email SMTP.</strong> Pakai Gmail App Password, bukan kata sandi akun biasa. Uji pengiriman sebelum menerima pesanan.</span>
                    </li>
                    <li>
                        <span class="mark <?= $httpsWebhook ? 'mark-ok' : 'mark-warn' ?>"><?= $httpsWebhook ? '&#10003;' : '!' ?></span>
                        <span><strong>Alamat HTTPS.</strong> Midtrans membutuhkan webhook publik yang bisa diakses dari internet. Alamat localhost hanya untuk pengujian lokal.</span>
                    </li>
                    <li>
                        <span class="mark <?= $adminHash ? 'mark-ok' : 'mark-warn' ?>"><?= $adminHash ? '&#10003;' : '!' ?></span>
                        <span><strong>Kata sandi admin.</strong> Ganti APP_ADMIN_PASSWORD_HASH dengan hash baru sebelum website dibuka.</span>
                    </li>
                    <li>
                        <span class="mark mark-warn">!</span>
                        <span><strong>Link produk.</strong> Pastikan semua link template sudah diganti dari example.com dan bisa dibuka tanpa login.</span>
                    </li>
                    <li>
                        <span class="mark mark-warn">!</span>
                        <span><strong>Cadangan database.</strong> Siapkan backup harian untuk tabel orders dan email_logs.</span>
                    </li>
                </ul>
            </div>
        </section>

        <section class="panel" style="margin-top: 22px;">
            <div class="panel-head">
                <div>
                    <h2>Cara mengubah konfigurasi</h2>
                </div>
            </div>
            <div class="panel-body">
                <ol class="checklist" style="list-style: decimal; padding-left: 18px;">
                    <li>Buka berkas <code>.env</code> di folder proyek pada server.</li>
                    <li>Ubah nilai yang diperlukan, misalnya MIDTRANS_SERVER_KEY atau MAIL_SMTP_PASS.</li>
                    <li>Simpan berkas, lalu muat ulang halaman ini untuk memeriksa status.</li>
                    <li>Untuk perubahan konfigurasi PHP, restart layanan PHP-FPM atau web server.</li>
                </ol>
                <p class="form-hint">Jangan mengirim isi <code>.env</code> lewat chat atau email. Berkas itu berisi kunci pembayaran dan kata sandi email.</p>
            </div>
        </section>
    </div>
</div>

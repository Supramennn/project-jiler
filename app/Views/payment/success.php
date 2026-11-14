<?php
$isPaid = $order['status'] === 'paid';
$pollUrl = site_url('status/' . $order['order_id']) . '?token=' . $order['access_token'];
?>
<section class="wrap result-panel" <?= ! $isPaid ? 'data-poll-url="' . esc($pollUrl) . '"' : '' ?>>
    <div class="result-head">
        <div class="result-mark" aria-hidden="true"><?= $isPaid ? '&#10003;' : '&#8226;' ?></div>
        <h1><?= $isPaid ? 'Pembayaran diterima.' : 'Pembayaran sedang diverifikasi.' ?></h1>
        <p>
            <?php if ($isPaid): ?>
                Terima kasih, <?= esc($order['nama_pembeli']) ?>. Pesanan <?= esc($order['order_id']) ?> sudah tercatat dibayar. Akses template dikirim ke <?= esc($order['email']) ?>.
            <?php else: ?>
                Pesanan <?= esc($order['order_id']) ?> belum tercatat dibayar. Halaman ini memeriksa status secara otomatis. Jangan tutup halaman sampai status berubah.
            <?php endif; ?>
        </p>
    </div>

    <?php if ($isPaid): ?>
        <div class="result-card">
            <h2>Status pengiriman akses</h2>

            <?php if ($order['status_email'] === 'terkirim'): ?>
                <div class="notice notice-ok">
                    <div>
                        <strong>Email sudah dikirim.</strong>
                        Invoice dan link template dikirim ke <?= esc($order['email']) ?>. Jika belum terlihat, periksa folder spam atau promosi, lalu gunakan halaman cek status.
                    </div>
                </div>
            <?php elseif ($order['status_email'] === 'gagal'): ?>
                <div class="notice notice-danger">
                    <div>
                        <strong>Email tercatat gagal dikirim.</strong>
                        Admin sudah menerima catatan kegagalan dan bisa mengirim ulang. Buka halaman cek status untuk memeriksa pembaruan.
                    </div>
                </div>
            <?php else: ?>
                <div class="notice notice-warn">
                    <div>
                        <strong>Email sedang diproses.</strong>
                        Muat ulang halaman ini beberapa saat lagi untuk melihat status pengiriman.
                    </div>
                </div>
            <?php endif; ?>

            <div class="status-grid" style="margin-top: 22px;">
                <div class="status-item">
                    <span>Nomor pesanan</span>
                    <strong><?= esc($order['order_id']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Nomor invoice</span>
                    <strong><?= esc($order['nomor_invoice']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Produk</span>
                    <strong><?= esc($order['product_nama']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Total dibayar</span>
                    <strong><?= rupiah($order['nominal']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Waktu bayar</span>
                    <strong><?= tanggal_id($order['paid_at']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Status email</span>
                    <strong><?= esc(status_email_label($order['status_email'])) ?></strong>
                </div>
            </div>

            <div class="result-actions">
                <a class="btn btn-primary" href="<?= site_url('invoice/' . $order['order_id'] . '/' . $order['access_token']) ?>">Buka invoice</a>
                <a class="btn btn-ghost" href="<?= site_url('status') ?>">Cek status pesanan</a>
                <a class="btn btn-quiet" href="<?= site_url('produk') ?>">Kembali ke katalog</a>
            </div>

            <div class="content-block" style="margin-top: 28px;">
                <h2>Langkah berikutnya</h2>
                <div class="steps" style="border-top: 0;">
                    <div class="step">
                        <div class="step-number" aria-hidden="true"></div>
                        <h3>Buka email</h3>
                        <p>Cari email dari <?= esc(config('Store')->email) ?> dengan subjek invoice pesanan Anda.</p>
                    </div>
                    <div class="step">
                        <div class="step-number" aria-hidden="true"></div>
                        <h3>Salin template</h3>
                        <p>Klik link template Google Sheets di email, lalu buat salinan ke Google Drive Anda.</p>
                    </div>
                    <div class="step">
                        <div class="step-number" aria-hidden="true"></div>
                        <h3>Deploy web app</h3>
                        <p>Ikuti video dan panduan teks untuk men-deploy web app Apps Script di akun Google Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="result-card">
            <h2>Status pesanan</h2>
            <div class="status-grid">
                <div class="status-item">
                    <span>Nomor pesanan</span>
                    <strong><?= esc($order['order_id']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Status</span>
                    <strong><?= esc(status_pesanan_label($order['status'])) ?></strong>
                </div>
                <div class="status-item">
                    <span>Produk</span>
                    <strong><?= esc($order['product_nama']) ?></strong>
                </div>
                <div class="status-item">
                    <span>Total</span>
                    <strong><?= rupiah($order['nominal']) ?></strong>
                </div>
            </div>

            <p class="summary-note" data-poll-note style="margin-top: 18px;">Status diperiksa otomatis setiap beberapa detik. Anda juga bisa memuat ulang halaman ini.</p>

            <div class="result-actions">
                <a class="btn btn-primary" href="<?= site_url('pembayaran/' . $order['order_id']) ?>">Lanjutkan pembayaran</a>
                <a class="btn btn-ghost" href="<?= site_url('sukses/' . $order['order_id']) ?>">Muat ulang status</a>
                <a class="btn btn-quiet" href="<?= site_url('status') ?>">Cek status pesanan</a>
            </div>
        </div>
    <?php endif; ?>
</section>

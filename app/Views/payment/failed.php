<?php
$canRetry = $order['status'] === 'pending';
?>
<section class="wrap result-panel">
    <div class="result-head">
        <div class="result-mark" aria-hidden="true">!</div>
        <h1>Pembayaran belum selesai.</h1>
        <p>
            <?php if ($order['status'] === 'expired'): ?>
                Pesanan <?= esc($order['order_id']) ?> sudah kedaluwarsa sebelum pembayaran diterima. Silakan buat pesanan baru untuk membeli template.
            <?php elseif ($order['status'] === 'gagal'): ?>
                Pembayaran untuk pesanan <?= esc($order['order_id']) ?> gagal atau dibatalkan. Silakan buat pesanan baru atau hubungi admin jika dana sudah terpotong.
            <?php else: ?>
                Pesanan <?= esc($order['order_id']) ?> masih menunggu pembayaran. Anda bisa melanjutkan pembayaran atau membuat pesanan baru.
            <?php endif; ?>
        </p>
    </div>

    <div class="result-card">
        <h2>Ringkasan pesanan</h2>
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

        <div class="notice notice-warn">
            <div>
                <strong>Akses template belum dikirim.</strong>
                Link template hanya dikirim setelah gateway menyatakan pembayaran berhasil. Redirect dari halaman pembayaran tidak dianggap sebagai bukti bayar.
            </div>
        </div>

        <div class="result-actions">
            <?php if ($canRetry): ?>
                <a class="btn btn-primary" href="<?= site_url('pembayaran/' . $order['order_id'] . '/' . $order['access_token']) ?>">Lanjutkan pembayaran</a>
            <?php endif; ?>
            <a class="btn btn-ghost" href="<?= site_url('status') ?>">Cek status pesanan</a>
            <a class="btn btn-quiet" href="<?= site_url('produk') ?>">Buat pesanan baru</a>
        </div>
    </div>
</section>

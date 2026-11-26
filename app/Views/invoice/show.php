<?php
$diskon = max(0, (int) $order['product_harga'] - (int) $order['nominal']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice <?= esc($order['nomor_invoice']) ?> · <?= esc(store_name()) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" href="<?= base_url('favicon.ico') ?>?v=20261125" sizes="any">
    <link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>?v=20261125" sizes="180x180">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body class="invoice-page">
<div class="invoice-actions">
    <a class="btn btn-ghost btn-small" href="<?= site_url('status') ?>">Cek status pesanan</a>
    <button class="btn btn-primary btn-small" type="button" data-print>Cetak atau simpan PDF</button>
</div>

<main class="invoice-sheet">
    <?php if ($order['status'] === 'paid'): ?>
        <div class="invoice-paid-stamp">LUNAS</div>
    <?php else: ?>
        <div class="invoice-pending-stamp">BELUM DIBAYAR</div>
    <?php endif; ?>

    <header class="invoice-top">
        <div class="invoice-brand">
            <span class="brand-mark brand-mark-image" aria-hidden="true">
                <img src="<?= base_url('assets/img/brand/zafscript-mark.png?v=20261126') ?>" alt="" width="48" height="48">
            </span>
            <div>
                <strong><?= esc(store_name()) ?></strong>
                <span><?= esc($store->tagline) ?></span>
            </div>
        </div>
        <div class="invoice-title">
            <h1>Invoice</h1>
            <p><?= esc($order['nomor_invoice']) ?></p>
        </div>
    </header>

    <div class="invoice-meta">
        <div>
            <h2>Ditagihkan kepada</h2>
            <p><strong><?= esc($order['nama_pembeli']) ?></strong><br>
                <?= esc($order['email']) ?><br>
                <?= esc($order['whatsapp']) ?>
            </p>
        </div>
        <div>
            <h2>Detail invoice</h2>
            <p>
                Tanggal: <?= tanggal_id($order['status'] === 'paid' ? $order['paid_at'] : $order['created_at'], false) ?><br>
                Nomor pesanan: <?= esc($order['order_id']) ?><br>
                Metode bayar: <?= esc(payment_method_label($order['payment_method'])) ?><br>
                Status: <?= esc(status_pesanan_label($order['status'])) ?>
            </p>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th scope="col">Produk</th>
                <th scope="col">Jumlah</th>
                <th scope="col">Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong><?= esc($order['product_nama']) ?></strong>
                    <?php if ($diskon > 0): ?>
                        <span class="invoice-promo">Promo</span>
                    <?php endif; ?>
                    <br>
                    <span style="color: var(--muted); font-size: 12px;">Template Google Sheets dan web app Apps Script</span>
                </td>
                <td>1</td>
                <td><?= rupiah($order['product_harga']) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="invoice-totals">
        <div class="invoice-total-line">
            <span>Subtotal</span>
            <span><?= rupiah($order['product_harga']) ?></span>
        </div>
        <?php if ($diskon > 0): ?>
            <div class="invoice-total-line discount">
                <span>Diskon promo</span>
                <span>-<?= rupiah($diskon) ?></span>
            </div>
        <?php endif; ?>
        <div class="invoice-total-line grand">
            <span>Total</span>
            <span><?= rupiah($order['nominal']) ?></span>
        </div>
    </div>

    <footer class="invoice-foot">
        <p><strong><?= esc(store_name()) ?></strong> · <?= esc($store->address) ?> · <?= esc($store->email) ?></p>
        <p>Produk digital berupa template Google Sheets dan web app Apps Script. Akses dikirim ke email pembeli setelah pembayaran terverifikasi. Invoice ini dibuat otomatis oleh sistem.</p>
        <?php if ($order['status'] !== 'paid'): ?>
            <p>Invoice ini belum lunas. Status akan berubah setelah pembayaran dikonfirmasi gateway.</p>
        <?php endif; ?>
    </footer>
</main>

<script src="<?= base_url('assets/js/site.js') ?>" defer></script>
</body>
</html>

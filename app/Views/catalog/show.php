<?php
$features   = $fitur ?? product_features($product['fitur']);
$promoAktif = product_promo_active($product);
$hargaAsli  = (int) $product['harga'];
$hargaBayar = product_price($product);
$hemat      = product_discount_amount($product);
$persen     = product_discount_percent($product);
?>
<section class="wrap">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= site_url('/') ?>">Beranda</a>
        <span aria-hidden="true">/</span>
        <a href="<?= site_url('produk') ?>">Produk</a>
        <span aria-hidden="true">/</span>
        <span><?= esc($product['nama']) ?></span>
    </nav>
</section>

<section class="wrap product-detail">
    <div>
        <h1><?= esc($product['nama']) ?></h1>
        <div class="complexity">
            <span class="badge badge-<?= complexity_tone($product['tingkat_kompleksitas']) ?>"><?= esc($product['tingkat_kompleksitas']) ?></span>
            <span>Template Google Sheets dan web app Apps Script</span>
        </div>

        <?php if (! empty($product['gambar'])): ?>
            <div style="margin-top: 26px; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; background: var(--paper-2);">
                <img src="<?= esc($product['gambar']) ?>" alt="Gambar produk <?= esc($product['nama']) ?>" loading="lazy" decoding="async">
            </div>
        <?php endif; ?>

        <p class="product-description"><?= nl2br(esc($product['deskripsi'])) ?></p>

        <?php if ($features !== []): ?>
            <div class="content-block">
                <h2>Yang ada di dalam template</h2>
                <ul class="feature-check-list">
                    <?php foreach ($features as $fitur): ?>
                        <li><?= esc($fitur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="content-block">
            <h2>Cara menerima akses</h2>
            <div class="steps" style="border-top: 0;">
                <div class="step">
                    <div class="step-number" aria-hidden="true"></div>
                    <h3>Bayar pesanan</h3>
                    <p>Pilih VA Permata, GoPay, atau QRIS di halaman pembayaran.</p>
                </div>
                <div class="step">
                    <div class="step-number" aria-hidden="true"></div>
                    <h3>Webhook memverifikasi</h3>
                    <p>Backend mencatat pesanan hanya setelah gateway menyatakan pembayaran berhasil.</p>
                </div>
                <div class="step">
                    <div class="step-number" aria-hidden="true"></div>
                    <h3>Email berisi akses</h3>
                    <p>Invoice, link template, link video, dan panduan teks dikirim ke email pembeli.</p>
                </div>
            </div>
        </div>

        <div class="content-block">
            <h2>Sebelum membeli</h2>
            <ul class="feature-check-list">
                <li>Anda perlu akun Google untuk menyalin template dan men-deploy web app.</li>
                <li>Link template hanya dikirim lewat email setelah pembayaran berhasil.</li>
                <li>Akses berlaku selamanya dan tidak punya masa kedaluwarsa.</li>
                <li>Kebijakan refund belum ditetapkan. Kendala bisa disampaikan ke admin lewat email invoice.</li>
            </ul>
        </div>
    </div>

    <aside class="buy-card" aria-label="Ringkasan pembelian">
        <div class="buy-label"><?= $promoAktif ? 'Harga promo' : 'Harga template' ?></div>

        <?php if ($promoAktif): ?>
            <div class="buy-promo-row">
                <span class="promo-badge">Diskon <?= $persen ?>%</span>
                <?php if (! empty($product['promo_berakhir'])): ?>
                    <span class="promo-until">s.d. <?= tanggal_id($product['promo_berakhir'], false) ?></span>
                <?php endif; ?>
            </div>
            <div class="buy-price is-promo"><?= rupiah($hargaBayar) ?></div>
            <p class="buy-old">Harga normal <s><?= rupiah($hargaAsli) ?></s> &middot; Hemat <?= rupiah($hemat) ?></p>
        <?php else: ?>
            <div class="buy-price"><?= rupiah($hargaBayar) ?></div>
        <?php endif; ?>

        <p class="buy-note">Sekali bayar, akses selamanya.</p>

        <a class="btn btn-primary btn-block" href="<?= site_url('checkout/' . $product['slug']) ?>">Beli template</a>

        <?php if (! empty($product['link_demo'])): ?>
            <a class="btn btn-ghost btn-block" style="margin-top: 8px;" href="<?= esc($product['link_demo']) ?>" target="_blank" rel="noopener noreferrer">Lihat demo</a>
        <?php endif; ?>

        <ul class="buy-list">
            <li><span>Format</span><strong>Google Sheets</strong></li>
            <li><span>Web app</span><strong>Apps Script</strong></li>
            <li><span>Akses</span><strong>Selamanya</strong></li>
            <li><span>Panduan</span><strong>Video dan teks</strong></li>
            <li><span>Pengiriman</span><strong>Email otomatis</strong></li>
        </ul>

        <p class="buy-footnote">Link akses dikirim ke email setelah pembayaran terverifikasi. Pastikan alamat email di checkout sudah benar. Jika email tidak masuk, cek folder spam lalu buka halaman cek status pesanan.</p>
    </aside>
</section>

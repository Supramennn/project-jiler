<?php
$errors     = session()->getFlashdata('errors') ?? [];
$errorList  = is_array($errors) ? $errors : [];
$promoAktif = product_promo_active($product);
$hargaAsli  = (int) $product['harga'];
$hargaBayar = product_price($product);
$hemat      = product_discount_amount($product);
$persen     = product_discount_percent($product);
?>
<section class="page-head">
    <div class="wrap">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= site_url('/') ?>">Beranda</a>
            <span aria-hidden="true">/</span>
            <a href="<?= site_url('produk') ?>">Produk</a>
            <span aria-hidden="true">/</span>
            <a href="<?= site_url('produk/' . $product['slug']) ?>"><?= esc($product['nama']) ?></a>
            <span aria-hidden="true">/</span>
            <span>Checkout</span>
        </nav>
        <h1>Isi data pembeli.</h1>
        <p>Email dipakai untuk mengirim invoice dan akses template. Pastikan penulisannya benar, karena akses tidak bisa dikirim ke nomor WhatsApp.</p>
    </div>
</section>

<section class="wrap checkout-grid">
    <div>
        <?php if ($errorList !== []): ?>
            <div class="flash flash-error" role="alert" style="margin-top: 0; margin-bottom: 18px;">
                <div>
                    <strong>Periksa isian berikut:</strong>
                    <ul style="margin: 6px 0 0; padding-left: 18px;">
                        <?php foreach ($errorList as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form class="form-section" action="<?= site_url('checkout/' . $product['slug']) ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="hp-field" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off">
            </div>

            <h2>Data pembeli</h2>
            <p class="form-intro">Nama dan email akan tercetak di invoice. Nomor WhatsApp dipakai admin jika ada kendala pengiriman akses.</p>

            <div class="field <?= isset($errors['nama']) ? 'has-error' : '' ?>">
                <label for="nama">Nama pembeli</label>
                <input type="text" id="nama" name="nama" value="<?= esc(old('nama')) ?>" autocomplete="name" required>
                <?php if (isset($errors['nama'])): ?><span class="field-error"><?= esc($errors['nama']) ?></span><?php endif; ?>
            </div>

            <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" autocomplete="email" inputmode="email" required>
                <span class="hint">Invoice dan link akses dikirim ke alamat ini.</span>
                <?php if (isset($errors['email'])): ?><span class="field-error"><?= esc($errors['email']) ?></span><?php endif; ?>
            </div>

            <div class="field <?= isset($errors['email_konfirmasi']) ? 'has-error' : '' ?>">
                <label for="email_konfirmasi">Ketik ulang email</label>
                <input type="email" id="email_konfirmasi" name="email_konfirmasi" value="<?= esc(old('email_konfirmasi')) ?>" autocomplete="email" inputmode="email" required>
                <span class="hint">Dua alamat harus sama supaya akses tidak salah kirim.</span>
                <?php if (isset($errors['email_konfirmasi'])): ?><span class="field-error"><?= esc($errors['email_konfirmasi']) ?></span><?php endif; ?>
            </div>

            <div class="field <?= isset($errors['whatsapp']) ? 'has-error' : '' ?>">
                <label for="whatsapp">Nomor WhatsApp</label>
                <input type="tel" id="whatsapp" name="whatsapp" value="<?= esc(old('whatsapp')) ?>" autocomplete="tel" inputmode="tel" placeholder="08xxxxxxxxxx" required>
                <span class="hint">Boleh diawali 08 atau 62. Nomor tidak dipakai untuk mengirim akses.</span>
                <?php if (isset($errors['whatsapp'])): ?><span class="field-error"><?= esc($errors['whatsapp']) ?></span><?php endif; ?>
            </div>

            <div class="field <?= isset($errors['setuju']) ? 'has-error' : '' ?>">
                <label class="checkbox-field" for="setuju">
                    <input type="checkbox" id="setuju" name="setuju" value="1" <?= old('setuju') !== null && old('setuju') !== '' ? 'checked' : '' ?> required>
                    <span>Saya sudah membaca <a href="<?= site_url('syarat') ?>" target="_blank" rel="noopener">syarat dan ketentuan</a> serta <a href="<?= site_url('privasi') ?>" target="_blank" rel="noopener">kebijakan privasi</a>. Saya memahami bahwa akses template dikirim setelah pembayaran berhasil.</span>
                </label>
                <?php if (isset($errors['setuju'])): ?><span class="field-error"><?= esc($errors['setuju']) ?></span><?php endif; ?>
            </div>

            <button class="btn btn-primary btn-block" type="submit">Lanjut ke pembayaran</button>
        </form>
    </div>

    <aside class="order-summary" aria-label="Ringkasan pesanan">
        <h2>Ringkasan</h2>
        <div class="summary-line">
            <span>Produk</span>
            <strong><?= esc($product['nama']) ?></strong>
        </div>
        <div class="summary-line">
            <span>Kompleksitas</span>
            <strong><?= esc($product['tingkat_kompleksitas']) ?></strong>
        </div>
        <div class="summary-line">
            <span>Jumlah</span>
            <strong>1 template</strong>
        </div>

        <?php if ($promoAktif): ?>
            <div class="summary-line">
                <span>Harga normal</span>
                <strong><s><?= rupiah($hargaAsli) ?></s></strong>
            </div>
            <div class="summary-line summary-discount">
                <span>Diskon promo (<?= $persen ?>%)</span>
                <strong>-<?= rupiah($hemat) ?></strong>
            </div>
        <?php endif; ?>

        <div class="summary-total">
            <span>Total bayar</span>
            <strong><?= rupiah($hargaBayar) ?></strong>
        </div>

        <p class="summary-note">Setelah klik lanjut, Anda memilih metode pembayaran. Link template baru dikirim setelah gateway mengonfirmasi pembayaran berhasil. Invoice bisa dibuka dari email atau halaman status pesanan.</p>
    </aside>
</section>

<section class="page-head">
    <div class="wrap">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= site_url('/') ?>">Beranda</a>
            <span aria-hidden="true">/</span>
            <a href="<?= site_url('status') ?>">Cek pesanan</a>
            <span aria-hidden="true">/</span>
            <span><?= esc($order['order_id']) ?></span>
        </nav>
        <h1>Pembayaran pesanan.</h1>
        <p>Pesanan <strong><?= esc($order['order_id']) ?></strong> menunggu pembayaran. Setelah gateway mengonfirmasi, akses template dikirim otomatis ke email pembeli.</p>
    </div>
</section>

<section class="wrap checkout-grid">
    <div>
        <?php if ($driver === 'sandbox'): ?>
            <div class="sandbox-note" role="status">
                <div>
                    <strong>Mode sandbox.</strong>
                    Halaman ini untuk pengujian alur. Tidak ada uang yang benar-benar ditagih. Gunakan tombol simulasi pembayaran di bawah untuk menguji webhook dan email otomatis.
                </div>
            </div>
        <?php endif; ?>

        <?php if ($showMethods): ?>
            <div class="payment-box">
                <h2>Pilih cara bayar</h2>
                <p>Pilih salah satu metode. Dana masuk ke rekening penjual melalui gateway pembayaran.</p>
                <?php if ($order['payment_reference'] !== null): ?>
                    <p class="summary-note" style="margin-top: 10px;">Metode sebelumnya (<?= esc(payment_method_label($order['payment_method'])) ?>) akan diganti setelah Anda membuat instruksi baru.</p>
                <?php endif; ?>

                <form action="<?= site_url('pembayaran/' . $order['order_id'] . '/' . $order['access_token'] . '/metode') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="method-list">
                        <label class="method-option">
                            <input type="radio" name="metode" value="permata_va" required>
                            <span>
                                <strong>VA Permata</strong>
                                <span>Transfer dari mobile banking atau internet banking ke nomor virtual account.</span>
                            </span>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="metode" value="gopay" required>
                            <span>
                                <strong>GoPay</strong>
                                <span>Bayar dari aplikasi GoPay atau halaman pembayaran GoPay.</span>
                            </span>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="metode" value="qris" required>
                            <span>
                                <strong>QRIS</strong>
                                <span>Pindai kode QRIS dari aplikasi e-wallet atau mobile banking yang mendukung.</span>
                            </span>
                        </label>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit" style="margin-top: 20px;">Buat instruksi pembayaran</button>
                </form>
            </div>
        <?php else: ?>
            <div class="payment-box">
                <h2><?= esc(payment_method_label($order['payment_method'])) ?></h2>
                <p>Pesanan ini memakai metode <?= esc(payment_method_label($order['payment_method'])) ?>. Selesaikan pembayaran sebelum waktu habis.</p>
                <p style="margin-top: 10px; color: var(--gold-deep); font-weight: 600;" data-expires="<?= esc(date('c', strtotime($order['expires_at']))) ?>">Menghitung sisa waktu pembayaran...</p>

                <div class="payment-reference">
                    <div>
                        <span><?= $order['payment_method'] === 'permata_va' ? 'Nomor virtual account' : ($order['payment_method'] === 'gopay' ? 'Kode pembayaran GoPay' : 'Kode simulasi QRIS') ?></span>
                        <strong><?= esc($order['payment_reference']) ?></strong>
                    </div>
                    <button class="btn btn-ghost btn-small" type="button" data-copy="<?= esc($order['payment_reference']) ?>">Salin</button>
                </div>

                <?php if ($driver === 'sandbox' && $instructions !== []): ?>
                    <ol class="instruction-list">
                        <?php foreach ($instructions as $instruction): ?>
                            <li><?= esc($instruction) ?></li>
                        <?php endforeach; ?>
                    </ol>

                    <form action="<?= site_url('pembayaran/' . $order['order_id'] . '/' . $order['access_token'] . '/simulasi') ?>" method="post" style="margin-top: 22px;" data-confirm="Simulasikan pembayaran berhasil? Pesanan akan ditandai dibayar dan email akses diproses.">
                        <?= csrf_field() ?>
                        <button class="btn btn-gold btn-block" type="submit">Simulasikan pembayaran berhasil</button>
                    </form>
                    <p class="summary-note" style="margin-top: 14px;">Tombol simulasi hanya ada di mode sandbox. Pada mode Midtrans, pembayaran diselesaikan di halaman gateway.</p>
                <?php elseif ($driver === 'midtrans'): ?>
                    <a class="btn btn-gold btn-block" style="margin-top: 22px;" href="<?= esc($order['payment_url']) ?>">Buka halaman pembayaran Midtrans</a>
                    <p class="summary-note" style="margin-top: 14px;">Jika halaman pembayaran tertutup, klik tombol di atas lagi. Status pesanan akan berubah setelah webhook Midtrans diterima.</p>
                <?php endif; ?>

                <?php if ($driver === 'sandbox'): ?>
                    <p style="margin-top: 18px; font-size: 12.5px;"><a href="<?= site_url('pembayaran/' . $order['order_id'] . '/' . $order['access_token'] . '?ganti=1') ?>">Ganti metode pembayaran</a></p>
                <?php else: ?>
                    <p class="summary-note" style="margin-top: 18px;">Untuk memakai metode pembayaran lain, buat pesanan baru dari katalog. Midtrans mengikat satu nomor pesanan ke satu transaksi.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="order-summary" aria-label="Ringkasan pesanan">
        <h2>Ringkasan</h2>
        <div class="summary-line">
            <span>Nomor pesanan</span>
            <strong><?= esc($order['order_id']) ?></strong>
        </div>
        <div class="summary-line">
            <span>Invoice</span>
            <strong><?= esc($order['nomor_invoice']) ?></strong>
        </div>
        <div class="summary-line">
            <span>Produk</span>
            <strong><?= esc($order['product_nama']) ?></strong>
        </div>
        <div class="summary-line">
            <span>Email</span>
            <strong><?= esc($order['email']) ?></strong>
        </div>

        <div class="summary-total">
            <span>Total bayar</span>
            <strong><?= rupiah($order['nominal']) ?></strong>
        </div>

        <p class="summary-note">Status pesanan bisa dicek kapan saja di halaman cek pesanan memakai nomor pesanan dan email. Link template tidak dikirim sebelum pembayaran sukses.</p>
    </aside>
</section>

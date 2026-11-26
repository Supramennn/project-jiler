<section class="page-head">
    <div class="wrap">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= site_url('/') ?>">Beranda</a>
            <span aria-hidden="true">/</span>
            <span>Cek pesanan</span>
        </nav>
        <h1>Cek status pesanan.</h1>
        <p>Masukkan nomor pesanan dan email yang dipakai saat checkout. Sistem akan menampilkan status pembayaran dan pengiriman akses.</p>
    </div>
</section>

<section class="wrap checkout-grid">
    <div>
        <form class="form-section" action="<?= site_url('status') ?>" method="post">
            <?= csrf_field() ?>
            <h2>Data pesanan</h2>
            <p class="form-intro">Nomor pesanan ada di email invoice atau halaman setelah checkout, contohnya <?= esc(config('Store')->invoicePrefix) ?>-YYMMDD-XXXXXX.</p>

            <div class="field">
                <label for="order_id">Nomor pesanan</label>
                <input type="text" id="order_id" name="order_id" value="<?= esc($input['order_id']) ?>" autocomplete="off" required>
            </div>

            <div class="field">
                <label for="email">Email pembeli</label>
                <input type="email" id="email" name="email" value="<?= esc($input['email']) ?>" autocomplete="email" inputmode="email" required>
                <span class="hint">Gunakan email yang sama dengan yang diisi saat checkout.</span>
            </div>

            <?php if ($error !== null): ?>
                <div class="flash flash-error" role="alert"><?= esc($error) ?></div>
            <?php endif; ?>

            <button class="btn btn-primary btn-block" type="submit">Cek status</button>
        </form>

        <?php if ($order !== null): ?>
            <div class="result-card status-result">
                <div class="detail-head">
                    <span class="badge badge-<?= status_pesanan_tone($order['status']) ?>"><?= esc(status_pesanan_label($order['status'])) ?></span>
                    <span class="badge badge-<?= status_email_tone($order['status_email']) ?>">Email: <?= esc(status_email_label($order['status_email'])) ?></span>
                </div>
                <h2>Pesanan <?= esc($order['order_id']) ?></h2>
                <div class="status-grid" style="margin-top: 18px;">
                    <div class="status-item">
                        <span>Nomor invoice</span>
                        <strong><?= esc($order['nomor_invoice']) ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Produk</span>
                        <strong><?= esc($order['product_nama']) ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Email</span>
                        <strong><?= esc(mask_email($order['email'])) ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Total</span>
                        <strong><?= rupiah($order['nominal']) ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Dibuat</span>
                        <strong><?= tanggal_id($order['created_at']) ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Waktu bayar</span>
                        <strong><?= tanggal_id($order['paid_at']) ?></strong>
                    </div>
                </div>

                <?php if ($order['status'] === 'paid'): ?>
                    <div class="notice notice-ok">
                        <div>
                            <strong>Pembayaran sudah tercatat.</strong>
                            <?php if ($order['status_email'] === 'terkirim'): ?>
                                Email invoice dan akses sudah dikirim. Periksa inbox dan folder spam.
                            <?php elseif ($order['status_email'] === 'gagal'): ?>
                                Email sempat gagal dikirim. Admin sudah menerima catatan dan bisa mengirim ulang.
                            <?php else: ?>
                                Email akses masih diproses.
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="summary-note">Invoice tersedia melalui tautan aman di email pesanan atau halaman pembayaran yang Anda buka setelah checkout.</p>
                <?php elseif ($order['status'] === 'pending'): ?>
                    <div class="notice notice-warn">
                        <div>
                            <strong>Menunggu pembayaran.</strong>
                            Pembayaran belum dikonfirmasi gateway. Akses template belum dikirim.
                        </div>
                    </div>
                    <p class="summary-note">Untuk melanjutkan pembayaran, gunakan tautan aman dari halaman checkout. Jika tautan tidak tersedia, hubungi admin dengan nomor pesanan.</p>
                <?php else: ?>
                    <div class="notice notice-danger">
                        <div>
                            <strong>Pesanan tidak bisa dibayar lagi.</strong>
                            Buat pesanan baru untuk membeli template, atau hubungi admin jika dana sudah terpotong.
                        </div>
                    </div>
                    <div class="result-actions">
                        <a class="btn btn-primary" href="<?= site_url('produk') ?>">Buat pesanan baru</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="order-summary">
        <h2>Yang perlu diperiksa</h2>
        <ul class="feature-check-list" style="margin-top: 8px;">
            <li>Nomor pesanan sama persis dengan yang ada di email, termasuk tanda hubung.</li>
            <li>Email ditulis dengan huruf kecil atau besar, sistem tidak membedakannya.</li>
            <li>Jika email tidak masuk, cek folder spam dan promosi.</li>
            <li>Jika status email gagal, admin bisa mengirim ulang dari dashboard.</li>
        </ul>
        <p class="summary-note">Halaman ini tidak menampilkan link template. Link hanya ada di email yang dikirim setelah pembayaran berhasil.</p>
    </aside>
</section>

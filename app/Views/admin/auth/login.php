<div class="login-shell">
    <section class="login-brand-panel">
        <a class="brand" href="<?= site_url('/') ?>">
            <span class="brand-mark brand-mark-image" aria-hidden="true">
                <img src="<?= base_url('assets/img/brand/zafscript-monogram.jpg') ?>" alt="" width="48" height="48">
            </span>
            <span class="brand-text">
                <?= esc(store_name()) ?>
                <small>Dashboard</small>
            </span>
        </a>

        <div>
            <p class="login-quote">Kelola produk dan pesanan tanpa menyentuh kode.</p>
            <p>Ubah harga, deskripsi, dan link akses dari dashboard. Kirim ulang email jika pengiriman gagal.</p>
        </div>

        <div class="login-foot">
            <?= esc($store->address) ?><br>
            <?= esc($store->email) ?><br>
            <span class="login-credit">Diproduksi oleh <?= esc($store->producer) ?></span>
        </div>

        <span class="login-stamp" aria-hidden="true">Admin</span>
        <span class="login-ledger-scan" aria-hidden="true"></span>
    </section>

    <section class="login-form-panel">
        <div class="login-card">
            <h1>Masuk dashboard</h1>
            <p class="login-intro">Gunakan email dan kata sandi admin yang tersimpan di konfigurasi server.</p>

            <?= $this->include('partials/flash') ?>

            <form action="<?= site_url('admin/login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="email">Email admin</label>
                    <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" autocomplete="username" inputmode="email" required>
                </div>

                <div class="field">
                    <label for="password">Kata sandi</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>

                <button class="btn btn-primary btn-block" type="submit">Masuk</button>
            </form>

            <a class="login-back" href="<?= site_url('/') ?>">Kembali ke toko</a>

            <?php if (ENVIRONMENT !== 'production'): ?>
                <p class="login-dev-note">Mode pengembangan. Kredensial default tercatat di README proyek. Ganti sebelum website dipasang di server publik.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

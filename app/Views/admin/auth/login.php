<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk dashboard · <?= esc(store_name()) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <script>document.documentElement.classList.add('js');</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-login-body">
<div class="login-shell">
    <section class="login-brand-panel">
        <a class="brand" href="<?= site_url('/') ?>">
            <span class="brand-mark" aria-hidden="true"><?= esc(store_initials()) ?></span>
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

<script src="<?= base_url('assets/js/site.js') ?>"></script>
</body>
</html>

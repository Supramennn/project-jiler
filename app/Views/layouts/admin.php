<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> · <?= esc(store_name()) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#f6f1e7">
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
    <link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>">
    <script>document.documentElement.classList.add('js');</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" id="admin-sidebar" data-admin-sidebar>
        <a class="brand" href="<?= site_url('admin') ?>">
            <span class="brand-mark brand-mark-image" aria-hidden="true">
                <img src="<?= base_url('assets/img/brand/zafscript-photo.jpg') ?>" alt="" width="48" height="48">
            </span>
            <span class="brand-text">
                <?= esc(store_name()) ?>
                <small>Dashboard</small>
            </span>
        </a>

        <nav class="side-nav" aria-label="Navigasi dashboard">
            <a href="<?= site_url('admin') ?>" <?= $active === 'dashboard' ? 'aria-current="page"' : '' ?>>Ringkasan</a>
            <a href="<?= site_url('admin/pesanan') ?>" <?= $active === 'orders' ? 'aria-current="page"' : '' ?>>Pesanan</a>
            <a href="<?= site_url('admin/produk') ?>" <?= $active === 'products' ? 'aria-current="page"' : '' ?>>Produk</a>
            <a href="<?= site_url('admin/pengaturan') ?>" <?= $active === 'settings' ? 'aria-current="page"' : '' ?>>Pengaturan</a>
        </nav>

        <div class="sidebar-foot">
            <div class="who">
                Masuk sebagai<br>
                <strong><?= esc($admin['email']) ?></strong>
            </div>
            <a href="<?= site_url('/') ?>">Lihat toko</a>
            <form action="<?= site_url('admin/logout') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit">Keluar</button>
            </form>
            <p class="sidebar-credit">Diproduksi oleh <?= esc($store->producer) ?></p>
        </div>
    </aside>

    <div class="admin-overlay" data-admin-overlay></div>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-expanded="false" aria-controls="admin-sidebar" aria-label="Buka navigasi dashboard">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1><?= esc($title) ?></h1>
            </div>
            <div class="topbar-right">
                <div class="topbar-meta">
                    <strong><?= tanggal_id(date('Y-m-d'), false) ?></strong>
                    <?= esc($admin['name']) ?>
                </div>
                <?php if (config('Payment')->effectiveDriver() === 'sandbox'): ?>
                    <span class="chip chip-warn">Sandbox</span>
                <?php else: ?>
                    <span class="chip chip-ok">Live Midtrans</span>
                <?php endif; ?>
            </div>
        </header>

        <div class="admin-content">
            <div class="admin-flash">
                <?= $this->include('partials/flash') ?>
            </div>
            <?= $content ?>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/site.js') ?>" defer></script>
</body>
</html>

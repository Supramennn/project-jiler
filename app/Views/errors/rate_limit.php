<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Terlalu banyak percobaan</title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body class="site-body">
<main id="main" class="wrap-narrow prose" style="padding: 72px 0;">
    <p class="flash flash-warning" role="alert" style="margin-top: 0;">Batas percobaan tercapai.</p>
    <h1>Terlalu banyak percobaan.</h1>
    <p>Permintaan dari perangkat ini dikirim terlalu sering. Coba lagi dalam <strong><?= esc((string) $retry) ?> detik</strong>.</p>
    <p>Jika ini terjadi saat checkout, tunggu sebentar lalu kirim ulang form. Pesanan yang sudah dibuat tetap bisa dibuka lewat halaman status.</p>
    <p><a class="btn btn-primary" href="<?= site_url('/') ?>">Kembali ke beranda</a></p>
</main>
</body>
</html>

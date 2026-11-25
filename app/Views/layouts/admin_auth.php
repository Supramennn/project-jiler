<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> · <?= esc(store_name()) ?></title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#17140f">
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
    <link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>">
    <script>document.documentElement.classList.add('js');</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-auth.css') ?>">
</head>
<body class="admin-auth-body">
<?= $content ?>
</body>
</html>

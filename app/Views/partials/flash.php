<?php
$flashMessages = [
    'success' => 'flash-success',
    'error'   => 'flash-error',
    'warning' => 'flash-warning',
    'info'    => 'flash-info',
];
?>
<?php foreach ($flashMessages as $key => $class): ?>
    <?php $message = session()->getFlashdata($key); ?>
    <?php if ($message !== null && $message !== ''): ?>
        <div class="flash <?= $class ?>" role="<?= $key === 'error' ? 'alert' : 'status' ?>">
            <span><?= esc($message) ?></span>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

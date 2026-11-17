<?php
$promoAktif = product_promo_active($product);
$hargaAsli  = (int) $product['harga'];
$hargaBayar = product_price($product);
$hemat      = product_discount_amount($product);
$persen     = product_discount_percent($product);
?>
<div class="price-stack<?= $promoAktif ? ' has-promo' : '' ?>">
    <?php if ($promoAktif): ?>
        <span class="promo-badge">Diskon <?= $persen ?>%</span>
        <span class="price-old"><span class="sr-only">Harga normal </span><s><?= rupiah($hargaAsli) ?></s></span>
    <?php endif; ?>
    <span class="price-now"><?= rupiah($hargaBayar) ?></span>
    <?php if ($promoAktif): ?>
        <span class="price-save">Hemat <?= rupiah($hemat) ?></span>
    <?php endif; ?>
</div>

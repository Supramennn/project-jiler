<?php
$total   = count($products);
$visible = count(array_filter($products, static fn (array $product): bool => (int) $product['aktif'] === 1));
?>
<div class="admin-page-head">
    <div>
        <h2>Produk</h2>
        <p><?= $total ?> produk tersimpan, <?= $visible ?> tampil di katalog. Perubahan harga dan link akses langsung berlaku untuk pesanan berikutnya.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-ghost btn-small" href="<?= site_url('produk') ?>">Lihat katalog</a>
        <a class="btn btn-primary btn-small" href="<?= site_url('admin/produk/baru') ?>">Tambah produk</a>
    </div>
</div>

<?php if ($products === []): ?>
    <div class="empty-state">
        <h3>Belum ada produk.</h3>
        <p>Tambahkan produk pertama supaya katalog bisa dibuka dan checkout berjalan. Isi link template, video, dan panduan setelah produk dibuat.</p>
        <a class="btn btn-primary btn-small" href="<?= site_url('admin/produk/baru') ?>">Tambah produk</a>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th scope="col">Urutan</th>
                    <th scope="col">Produk</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Kompleksitas</th>
                    <th scope="col">Link akses</th>
                    <th scope="col">Tampil</th>
                    <th scope="col"><span class="sr-only">Tindakan</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td class="num"><?= (int) $product['urutan'] ?></td>
                        <td>
                            <a class="cell-main" href="<?= site_url('admin/produk/' . $product['id'] . '/edit') ?>"><?= esc($product['nama']) ?></a>
                            <span class="cell-sub">/<?= esc($product['slug']) ?></span>
                        </td>
                        <td class="num">
                            <?php $promoState = product_promo_state($product); ?>
                            <?= rupiah(product_price($product)) ?>
                            <?php if ($promoState === 'aktif'): ?>
                                <span class="cell-sub"><s><?= rupiah($product['harga']) ?></s> &middot; Diskon <?= product_discount_percent($product) ?>%</span>
                            <?php elseif ($promoState === 'terjadwal'): ?>
                                <span class="cell-sub">Promo mulai <?= tanggal_id($product['promo_mulai'], false) ?></span>
                            <?php elseif ($promoState === 'berakhir'): ?>
                                <span class="cell-sub">Promo berakhir <?= tanggal_id($product['promo_berakhir'], false) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="chip chip-<?= complexity_tone($product['tingkat_kompleksitas']) ?>"><?= esc($product['tingkat_kompleksitas']) ?></span>
                        </td>
                        <td>
                            <?php if (trim((string) $product['link_template']) === ''): ?>
                                <span class="chip chip-danger">Belum diisi</span>
                            <?php elseif (str_contains((string) $product['link_template'], 'example.com')): ?>
                                <span class="chip chip-warn">Masih placeholder</span>
                            <?php else: ?>
                                <span class="chip chip-ok">Siap</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="chip chip-<?= (int) $product['aktif'] === 1 ? 'ok' : 'muted' ?>"><?= (int) $product['aktif'] === 1 ? 'Aktif' : 'Nonaktif' ?></span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <?php if ((int) $product['aktif'] === 1): ?>
                                    <a class="btn btn-quiet" href="<?= site_url('produk/' . $product['slug']) ?>">Lihat</a>
                                <?php endif; ?>
                                <a class="btn btn-ghost" href="<?= site_url('admin/produk/' . $product['id'] . '/edit') ?>">Ubah</a>
                                <form action="<?= site_url('admin/produk/' . $product['id'] . '/toggle') ?>" method="post" data-confirm="<?= (int) $product['aktif'] === 1 ? 'Sembunyikan produk ini dari katalog?' : 'Tampilkan produk ini di katalog?' ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-quiet" type="submit"><?= (int) $product['aktif'] === 1 ? 'Sembunyikan' : 'Tampilkan' ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="alert-box" style="margin-top: 18px;">
        <div>
            <strong>Link akses adalah bagian paling penting.</strong>
            Produk dengan link template kosong atau masih memakai example.com tetap bisa dibeli, tetapi email pembeli tidak akan berisi link yang bisa dipakai. Ganti link dari menu Ubah sebelum produk dipromosikan.
        </div>
    </div>
<?php endif; ?>

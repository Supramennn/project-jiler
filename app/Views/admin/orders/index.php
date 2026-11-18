<?php
$hasFilter    = $q !== '' || $status !== '' || $emailStatus !== '';
$total        = $pager->getTotal('orders');
$exportQuery  = http_build_query(array_filter([
    'q'            => $q,
    'status'       => $status,
    'email_status' => $emailStatus,
], static fn ($value): bool => $value !== ''));
$exportUrl = site_url('admin/pesanan/ekspor' . ($exportQuery !== '' ? '?' . $exportQuery : ''));
?>
<div class="admin-page-head">
    <div>
        <h2>Pesanan</h2>
        <p><?= $total ?> pesanan<?= $hasFilter ? ' sesuai filter' : '' ?>. Buka detail untuk memeriksa riwayat webhook, mengirim ulang email, atau menandai pembayaran manual.</p>
    </div>
    <div class="admin-actions">
        <?php if ($failedEmailCount > 0): ?>
            <form action="<?= site_url('admin/pesanan/kirim-ulang-gagal') ?>" method="post" data-confirm="Kirim ulang email akses untuk semua pesanan yang emailnya gagal?">
                <?= csrf_field() ?>
                <button class="btn btn-gold btn-small" type="submit">Kirim ulang <?= $failedEmailCount ?> email gagal</button>
            </form>
        <?php endif; ?>
        <a class="btn btn-ghost btn-small" href="<?= esc($exportUrl) ?>">Ekspor CSV</a>
        <a class="btn btn-ghost btn-small" href="<?= site_url('admin') ?>">Ringkasan</a>
    </div>
</div>

<form class="filters" action="<?= site_url('admin/pesanan') ?>" method="get">
    <div class="field">
        <label for="q">Cari</label>
        <input type="text" id="q" name="q" value="<?= esc($q) ?>" placeholder="Nomor pesanan, invoice, nama, email">
    </div>

    <div class="field">
        <label for="status">Status pesanan</label>
        <select id="status" name="status">
            <option value="">Semua status</option>
            <?php foreach (['pending', 'paid', 'expired', 'gagal'] as $opsi): ?>
                <option value="<?= $opsi ?>" <?= $status === $opsi ? 'selected' : '' ?>><?= esc(status_pesanan_label($opsi)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field">
        <label for="email_status">Status email</label>
        <select id="email_status" name="email_status">
            <option value="">Semua email</option>
            <?php foreach (['terkirim', 'gagal', 'belum'] as $opsi): ?>
                <option value="<?= $opsi ?>" <?= $emailStatus === $opsi ? 'selected' : '' ?>><?= esc(status_email_label($opsi)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="btn btn-primary" type="submit">Terapkan</button>
    <?php if ($hasFilter): ?>
        <a class="btn btn-quiet" href="<?= site_url('admin/pesanan') ?>">Hapus filter</a>
    <?php endif; ?>
    <span class="filter-meta">Urut dari pesanan terbaru.</span>
</form>

<?php if ($orders === []): ?>
    <div class="empty-state">
        <h3>Tidak ada pesanan<?= $hasFilter ? ' untuk filter ini' : '' ?>.</h3>
        <p><?= $hasFilter ? 'Coba kata kunci lain atau hapus filter untuk melihat semua pesanan.' : 'Pesanan muncul otomatis setelah pembeli menyelesaikan checkout.' ?></p>
        <?php if ($hasFilter): ?>
            <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan') ?>">Lihat semua pesanan</a>
        <?php else: ?>
            <a class="btn btn-ghost btn-small" href="<?= site_url('produk') ?>">Buka katalog</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th scope="col">Invoice / pesanan</th>
                    <th scope="col">Pembeli</th>
                    <th scope="col">Produk</th>
                    <th scope="col">Nominal</th>
                    <th scope="col">Status</th>
                    <th scope="col">Email</th>
                    <th scope="col">Dibuat</th>
                    <th scope="col"><span class="sr-only">Tindakan</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <a class="cell-main" href="<?= site_url('admin/pesanan/' . $order['order_id']) ?>"><?= esc($order['nomor_invoice']) ?></a>
                            <span class="cell-sub"><?= esc($order['order_id']) ?></span>
                        </td>
                        <td>
                            <span class="cell-main"><?= esc($order['nama_pembeli']) ?></span>
                            <span class="cell-sub"><?= esc($order['email']) ?></span>
                        </td>
                        <td><?= esc($order['product_nama']) ?></td>
                        <td class="num"><?= rupiah($order['nominal']) ?></td>
                        <td>
                            <span class="chip chip-<?= status_pesanan_tone($order['status']) ?>"><?= esc(status_pesanan_label($order['status'])) ?></span>
                        </td>
                        <td>
                            <span class="chip chip-<?= status_email_tone($order['status_email']) ?>"><?= esc(status_email_label($order['status_email'])) ?></span>
                        </td>
                        <td class="num"><?= tanggal_id($order['created_at']) ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-ghost" href="<?= site_url('admin/pesanan/' . $order['order_id']) ?>">Buka</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php $pager->only(['q', 'status', 'email_status']); ?>
    <?= $pager->links('orders', 'default_full') ?>
<?php endif; ?>

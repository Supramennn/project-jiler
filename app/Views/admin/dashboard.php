<?php
$weekTotal = array_sum(array_column($dailyRevenue, 'total'));
$hasAction = $failedEmails !== [] || $stalePending !== [];
?>
<div class="admin-page-head">
    <div>
        <h2>Ringkasan toko</h2>
        <p>Pantau pembayaran masuk dan email yang perlu ditindaklanjuti. Angka di bawah diambil langsung dari database pesanan.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-ghost btn-small" href="<?= site_url('/') ?>">Lihat toko</a>
        <?php if ($isDevelopment): ?>
            <form action="<?= site_url('admin/uji-pesanan') ?>" method="post" data-confirm="Buat pesanan uji baru? Pesanan ini memakai produk aktif pertama dan email uji@namausaha.test.">
                <?= csrf_field() ?>
                <button class="btn btn-primary btn-small" type="submit">Buat pesanan uji</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if ($paymentDriver === 'sandbox' || $mailDriver === 'file'): ?>
    <div class="alert-box" role="status">
        <div>
            <strong>Mode pengembangan aktif.</strong>
            <?php if ($paymentDriver === 'sandbox'): ?>Pembayaran memakai simulator lokal, bukan Midtrans. <?php endif; ?>
            <?php if ($mailDriver === 'file'): ?>Email disimpan ke folder writable/emails, bukan dikirim ke inbox. <?php endif; ?>
            <a href="<?= site_url('admin/pengaturan') ?>">Buka pengaturan integrasi</a>.
        </div>
    </div>
<?php endif; ?>

<div class="stat-strip">
    <div class="stat-cell">
        <span class="stat-label">Pendapatan hari ini</span>
        <strong class="stat-value"><?= rupiah($revenueToday) ?></strong>
        <span class="stat-sub">Dari pesanan berstatus dibayar</span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Pendapatan bulan ini</span>
        <strong class="stat-value"><?= rupiah($revenueMonth) ?></strong>
        <span class="stat-sub"><?= $paidCountMonth ?> pesanan dibayar</span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Menunggu bayar</span>
        <strong class="stat-value"><?= $pendingCount ?></strong>
        <span class="stat-sub">Pesanan berstatus pending</span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Email gagal</span>
        <strong class="stat-value"><?= $failedEmailCount ?></strong>
        <span class="stat-sub">Perlu diperiksa dan dikirim ulang</span>
    </div>
</div>

<div class="dashboard-grid">
    <section class="panel panel-accent">
        <div class="panel-head">
            <div>
                <h2>Perlu tindakan</h2>
                <p>Pesanan yang tertahan atau email yang tidak terkirim.</p>
            </div>
            <a href="<?= site_url('admin/pesanan') ?>">Semua pesanan</a>
        </div>
        <div class="panel-body">
            <?php if (! $hasAction): ?>
                <div class="empty-state">
                    <h3>Tidak ada yang menunggu.</h3>
                    <p>Semua pesanan yang sudah dibayar punya email terkirim, dan tidak ada pembayaran tertahan lebih dari dua jam.</p>
                    <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan') ?>">Buka daftar pesanan</a>
                </div>
            <?php else: ?>
                <ul class="action-list">
                    <?php foreach ($failedEmails as $order): ?>
                        <li class="action-item">
                            <div class="action-item-main">
                                <strong><?= esc($order['order_id']) ?> · <?= esc($order['product_nama']) ?></strong>
                                <span>Email ke <?= esc($order['email']) ?> gagal dikirim. Jumlah kegagalan: <?= (int) $order['jumlah_retry'] ?>.</span>
                                <span class="action-reason">Email gagal</span>
                            </div>
                            <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan/' . $order['order_id']) ?>">Buka</a>
                        </li>
                    <?php endforeach; ?>

                    <?php foreach ($stalePending as $order): ?>
                        <li class="action-item">
                            <div class="action-item-main">
                                <strong><?= esc($order['order_id']) ?> · <?= esc($order['product_nama']) ?></strong>
                                <span>Dibuat <?= tanggal_id($order['created_at']) ?>, belum dibayar.</span>
                                <span class="action-reason is-warn">Menunggu lebih dari 2 jam</span>
                            </div>
                            <a class="btn btn-ghost btn-small" href="<?= site_url('admin/pesanan/' . $order['order_id']) ?>">Buka</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Pendapatan 7 hari</h2>
                <p>Total nominal pesanan berstatus dibayar per hari.</p>
            </div>
            <a href="<?= site_url('admin/pesanan?status=paid') ?>">Pesanan dibayar</a>
        </div>
        <div class="panel-body">
            <?php if ($weekTotal === 0): ?>
                <div class="empty-state">
                    <h3>Belum ada pembayaran.</h3>
                    <p>Grafik akan terisi setelah ada pesanan yang berhasil dibayar dalam tujuh hari terakhir.</p>
                    <?php if ($isDevelopment): ?>
                        <form action="<?= site_url('admin/uji-pesanan') ?>" method="post">
                            <?= csrf_field() ?>
                            <button class="btn btn-primary btn-small" type="submit">Buat pesanan uji</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="chart-wrap">
                    <div class="chart" role="img" aria-label="Grafik pendapatan tujuh hari terakhir. Total <?= rupiah($weekTotal) ?>.">
                        <?php foreach ($dailyRevenue as $day): ?>
                            <?php $height = $day['total'] > 0 ? max(4, round(($day['total'] / $maxDaily) * 100)) : 3; ?>
                            <div class="chart-col <?= $day['total'] === 0 ? 'is-empty' : '' ?>" title="<?= esc($day['date']) ?>: <?= rupiah($day['total']) ?>">
                                <div class="chart-bar" style="--h: <?= $height ?>%"></div>
                                <span class="chart-label"><?= esc($day['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="chart-total">
                        <span>Total 7 hari</span>
                        <strong><?= rupiah($weekTotal) ?></strong>
                    </div>
                    <table class="sr-only">
                        <caption>Rincian pendapatan tujuh hari terakhir</caption>
                        <thead><tr><th scope="col">Tanggal</th><th scope="col">Pendapatan</th></tr></thead>
                        <tbody>
                            <?php foreach ($dailyRevenue as $day): ?>
                                <tr><td><?= esc($day['date']) ?></td><td><?= rupiah($day['total']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Pesanan terbaru</h2>
            <p>Delapan pesanan terakhir, termasuk yang belum dibayar.</p>
        </div>
        <a href="<?= site_url('admin/pesanan') ?>">Lihat semua</a>
    </div>
    <div class="panel-body panel-body-flush">
        <?php if ($recentOrders === []): ?>
            <div class="empty-state" style="margin: 18px;">
                <h3>Belum ada pesanan.</h3>
                <p>Pesanan akan muncul di sini setelah pembeli melakukan checkout.</p>
                <a class="btn btn-ghost btn-small" href="<?= site_url('produk') ?>">Buka katalog</a>
            </div>
        <?php else: ?>
            <div class="table-wrap" style="border: 0; border-radius: 0;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Invoice</th>
                            <th scope="col">Pembeli</th>
                            <th scope="col">Produk</th>
                            <th scope="col">Nominal</th>
                            <th scope="col">Status</th>
                            <th scope="col">Email</th>
                            <th scope="col">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
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
                                <td><span class="chip chip-<?= status_pesanan_tone($order['status']) ?>"><?= esc(status_pesanan_label($order['status'])) ?></span></td>
                                <td><span class="chip chip-<?= status_email_tone($order['status_email']) ?>"><?= esc(status_email_label($order['status_email'])) ?></span></td>
                                <td class="num"><?= tanggal_id($order['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

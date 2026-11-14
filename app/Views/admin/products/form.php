<?php
$errors    = session()->getFlashdata('errors') ?? [];
$errors    = is_array($errors) ? $errors : [];
$isEdit    = $product !== null;
$aktif     = $errors === [] ? (! $isEdit || (int) $product['aktif'] === 1) : old('aktif') !== null;
$fieldErr  = static fn (string $key): string => isset($errors[$key]) ? '<span class="field-error">' . esc($errors[$key]) . '</span>' : '';
$fieldWrap = static fn (string $key): string => isset($errors[$key]) ? ' has-error' : '';
?>
<div class="admin-page-head">
    <div>
        <h2><?= esc($heading) ?></h2>
        <p><?= $isEdit ? 'Perubahan pada link akses hanya memengaruhi email yang dikirim setelah tombol simpan ditekan.' : 'Isi informasi dasar dulu. Link template bisa dilengkapi kemudian sebelum produk ditampilkan.' ?></p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-ghost btn-small" href="<?= site_url('admin/produk') ?>">Kembali ke daftar</a>
        <?php if ($isEdit && (int) $product['aktif'] === 1): ?>
            <a class="btn btn-quiet btn-small" href="<?= site_url('produk/' . $product['slug']) ?>">Lihat di katalog</a>
        <?php endif; ?>
    </div>
</div>

<form class="admin-form" action="<?= esc($action) ?>" method="post" novalidate>
    <?= csrf_field() ?>

    <div class="form-grid">
        <div>
            <section class="form-panel">
                <h2>Informasi produk</h2>
                <p class="panel-intro">Nama, harga, dan deskripsi tampil di halaman katalog dan invoice.</p>

                <div class="field<?= $fieldWrap('nama') ?>">
                    <label for="nama">Nama produk</label>
                    <input type="text" id="nama" name="nama" value="<?= esc(old('nama', $product['nama'] ?? '')) ?>" maxlength="150" required>
                    <?= $fieldErr('nama') ?>
                </div>

                <div class="field">
                    <label for="slug">Slug URL</label>
                    <input type="text" id="slug" name="slug" value="<?= esc(old('slug', $product['slug'] ?? '')) ?>" maxlength="160" placeholder="otomatis dari nama produk">
                    <span class="hint">Dipakai di alamat halaman produk. Kosongkan untuk dibuat otomatis. Mengubah slug membuat tautan lama tidak berlaku.</span>
                    <?= $fieldErr('slug') ?>
                </div>

                <div class="field-row">
                    <div class="field<?= $fieldWrap('harga') ?>">
                        <label for="harga">Harga (rupiah)</label>
                        <input type="number" id="harga" name="harga" value="<?= esc(old('harga', $product['harga'] ?? '')) ?>" min="1" step="1" inputmode="numeric" required>
                        <span class="hint">Angka saja, tanpa titik atau koma. Contoh: 89000.</span>
                        <?= $fieldErr('harga') ?>
                    </div>

                    <div class="field<?= $fieldWrap('tingkat_kompleksitas') ?>">
                        <label for="tingkat_kompleksitas">Tingkat kompleksitas</label>
                        <?php $kompleksitas = old('tingkat_kompleksitas', $product['tingkat_kompleksitas'] ?? 'Sederhana'); ?>
                        <select id="tingkat_kompleksitas" name="tingkat_kompleksitas" required>
                            <?php foreach (['Sederhana', 'Menengah', 'Kompleks'] as $opsi): ?>
                                <option value="<?= $opsi ?>" <?= $kompleksitas === $opsi ? 'selected' : '' ?>><?= $opsi ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $fieldErr('tingkat_kompleksitas') ?>
                    </div>
                </div>

                <div class="field<?= $fieldWrap('deskripsi') ?>">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" required><?= esc(old('deskripsi', $product['deskripsi'] ?? '')) ?></textarea>
                    <span class="hint">Jelaskan isi template dan untuk siapa produk ini dibuat. Paragraf baru dipisahkan dengan enter.</span>
                    <?= $fieldErr('deskripsi') ?>
                </div>

                <div class="field">
                    <label for="fitur">Daftar fitur</label>
                    <textarea id="fitur" name="fitur" placeholder="Master barang dan kategori&#10;Pencatatan barang masuk dan keluar&#10;Sisa stok dihitung otomatis"><?= esc(old('fitur', $product['fitur'] ?? '')) ?></textarea>
                    <span class="hint">Satu fitur per baris. Tampil sebagai daftar di halaman produk dan email akses.</span>
                </div>

                <div class="field<?= $fieldWrap('gambar') ?>">
                    <label for="gambar">URL gambar produk</label>
                    <input type="url" id="gambar" name="gambar" value="<?= esc(old('gambar', $product['gambar'] ?? '')) ?>" placeholder="https://...">
                    <span class="hint">Opsional. Gunakan gambar lanskap minimal 1200 piksel lebar.</span>
                    <?= $fieldErr('gambar') ?>
                </div>
            </section>

            <section class="form-panel">
                <h2>Link akses</h2>
                <p class="panel-intro">Link ini hanya dikirim ke email pembeli setelah pembayaran terverifikasi. Jangan taruh di halaman publik.</p>

                <div class="field<?= $fieldWrap('link_template') ?>">
                    <label for="link_template">Link salin template Google Sheets</label>
                    <input type="url" id="link_template" name="link_template" value="<?= esc(old('link_template', $product['link_template'] ?? '')) ?>" placeholder="https://docs.google.com/spreadsheets/...">
                    <span class="hint">Atur akses file menjadi "siapa saja yang memiliki link dapat melihat", lalu gunakan link salin.</span>
                    <?= $fieldErr('link_template') ?>
                </div>

                <div class="field<?= $fieldWrap('link_video') ?>">
                    <label for="link_video">Link video panduan</label>
                    <input type="url" id="link_video" name="link_video" value="<?= esc(old('link_video', $product['link_video'] ?? '')) ?>" placeholder="https://youtube.com/...">
                    <?= $fieldErr('link_video') ?>
                </div>

                <div class="field<?= $fieldWrap('link_panduan') ?>">
                    <label for="link_panduan">Link panduan teks</label>
                    <input type="url" id="link_panduan" name="link_panduan" value="<?= esc(old('link_panduan', $product['link_panduan'] ?? '')) ?>" placeholder="https://docs.google.com/document/...">
                    <?= $fieldErr('link_panduan') ?>
                </div>

                <div class="field<?= $fieldWrap('link_demo') ?>">
                    <label for="link_demo">Link demo</label>
                    <input type="url" id="link_demo" name="link_demo" value="<?= esc(old('link_demo', $product['link_demo'] ?? '')) ?>" placeholder="https://...">
                    <span class="hint">Opsional. Kalau diisi, tombol Lihat demo muncul di halaman produk.</span>
                    <?= $fieldErr('link_demo') ?>
                </div>
            </section>
        </div>

        <div>
            <section class="form-panel">
                <h2>Publikasi</h2>
                <p class="panel-intro">Atur urutan tampil dan status produk di katalog.</p>

                <div class="field">
                    <label for="urutan">Urutan tampil</label>
                    <input type="number" id="urutan" name="urutan" value="<?= esc(old('urutan', $product['urutan'] ?? 0)) ?>" step="1" inputmode="numeric">
                    <span class="hint">Angka kecil tampil lebih dulu.</span>
                    <?= $fieldErr('urutan') ?>
                </div>

                <label class="checkbox-field" for="aktif">
                    <input type="checkbox" id="aktif" name="aktif" value="1" <?= $aktif ? 'checked' : '' ?>>
                    <span><strong>Tampilkan di katalog</strong><br>Produk nonaktif tidak bisa dibuka pembeli, tetapi data dan pesanan lama tetap tersimpan.</span>
                </label>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Tambahkan produk' ?></button>
                    <a class="btn btn-ghost" href="<?= site_url('admin/produk') ?>">Batal</a>
                </div>
            </section>

            <section class="form-panel">
                <h2>Sebelum produk dijual</h2>
                <ul class="checklist">
                    <li>
                        <span class="mark <?= trim((string) old('link_template', $product['link_template'] ?? '')) === '' ? 'mark-warn' : 'mark-ok' ?>"><?= trim((string) old('link_template', $product['link_template'] ?? '')) === '' ? '!' : '&#10003;' ?></span>
                        <span>Link template sudah diisi dan bisa diakses pembeli.</span>
                    </li>
                    <li>
                        <span class="mark <?= trim((string) old('link_video', $product['link_video'] ?? '')) === '' ? 'mark-warn' : 'mark-ok' ?>"><?= trim((string) old('link_video', $product['link_video'] ?? '')) === '' ? '!' : '&#10003;' ?></span>
                        <span>Video panduan sudah direkam dan linknya bisa dibuka.</span>
                    </li>
                    <li>
                        <span class="mark <?= trim((string) old('link_panduan', $product['link_panduan'] ?? '')) === '' ? 'mark-warn' : 'mark-ok' ?>"><?= trim((string) old('link_panduan', $product['link_panduan'] ?? '')) === '' ? '!' : '&#10003;' ?></span>
                        <span>Panduan teks sudah ditulis dan tidak memuat catatan internal.</span>
                    </li>
                    <li>
                        <span class="mark mark-warn">!</span>
                        <span>Kirim satu pesanan uji dari dashboard, lalu cek email dan link sebelum promosi.</span>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</form>

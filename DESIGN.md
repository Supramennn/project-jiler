# Arah desain toko template

Dokumen ini menjelaskan keputusan visual dan interaksi yang dipakai di website. Tujuannya supaya perubahan berikutnya tetap konsisten, bukan sekadar menambah komponen baru.

## Identitas usaha

Nama usaha sengaja dikosongkan. Selama `STORE_NAME` belum diisi, antarmuka memakai placeholder **Nama Usaha** dan inisial **NU**. Pengisi nama toko adalah pemilik usaha, bukan bagian dari proyek ini.

**Nexus Diji** adalah produsen proyek. Nama itu hanya muncul sebagai kredit di footer publik, sidebar dashboard, dan halaman login, bukan sebagai nama toko.

## Design Read

Reading this as: katalog dan invoice toko produk digital untuk pembeli individu dan admin toko kecil, dengan bahasa visual buku besar yang tenang, dial **ENERGY 2 / RHYTHM 3 / MOTION 2**.

ENERGY 2 berarti halaman menyapa dengan jelas lewat judul serif dan aksen emas, tanpa efek berlebihan. RHYTHM 3 berarti komposisi tiap bagian berbeda: hero asimetris, strip fakta, baris ledger, lalu panel detail. MOTION 2 berarti ada animasi masuk yang halus saat elemen pertama kali terlihat, ditambah hover dan drawer, tetapi tidak ada parallax atau gerak yang menghalangi konten.

## Konsep

Tema yang dipakai adalah **buku besar toko**: kertas broken white, tinta gelap, garis tipis, dan aksen emas. Metafora ini cocok dengan produk yang dijual, yaitu pencatatan stok, invoice, dan pengiriman akses yang bisa dilacak.

Halaman publik terasa seperti lembar katalog dan invoice. Dashboard terasa seperti meja kerja admin: gelap di sisi navigasi, terang di area data. Tidak ada ilustrasi stok, avatar palsu, atau angka sosial yang tidak bisa dibuktikan.

## Warna

| Peran | Nilai | Pemakaian |
| --- | --- | --- |
| Kertas | `#f6f1e7` | Latar halaman publik |
| Kertas terang | `#fbf8f1` | Panel, kartu, formulir |
| Tinta | `#1e1b16` | Teks utama, tombol primer |
| Tinta sekunder | `#37322a` | Teks isi |
| Muted | `#625a4d` | Keterangan, label sekunder |
| Garis | `#d9ceba` | Pemisah tabel dan panel |
| Emas | `#a77b3b` | Aksen, garis aktif, grafik |
| Emas dalam | `#7a5620` | Tautan, tombol emas, label |
| Hijau | `#2c6047` | Status berhasil |
| Merah | `#8f3227` | Status gagal atau bahaya |
| Kuning tua | `#7a5418` | Status menunggu |

Emas dipakai sebagai penanda, bukan latar besar. Tidak ada gradien biru-ungu, glassmorphism, atau glow.

## Tipografi

- **Judul:** Cormorant Garamond, weight 500-700. Memberi kesan katalog cetak dan invoice.
- **Isi dan UI:** Plus Jakarta Sans, weight 400-700. Tetap terbaca di ukuran kecil, terutama tabel admin.
- **Angka:** memakai `font-variant-numeric: tabular-nums` pada harga, nominal, dan grafik supaya kolom rapi.

Ukuran judul memakai `clamp()` agar tetap proporsional dari ponsel sampai desktop. Tidak ada heading raksasa yang mendorong konten keluar layar.

## Layout publik

- Hero asimetris: narasi di kiri, panel "Isi paket" di kanan. Panel memakai garis horisontal seperti buku catatan, bukan kartu dengan ikon.
- Strip fakta empat kolom dengan garis pemisah, menggantikan deretan kartu statistik.
- Daftar produk memakai baris ledger: nama, kompleksitas, harga, dan tautan. Bukan grid tiga kartu yang seragam.
- Halaman detail memakai dua kolom: isi produk dan kartu beli yang menempel saat scroll di desktop.
- Halaman invoice memakai lembar kertas sendiri, cap LUNAS atau BELUM DIBAYAR, dan tombol cetak. Tata letak cetak menyembunyikan navigasi.
- Tidak ada testimoni, logo klien, atau angka "10.000 pengguna" karena belum ada data nyata.

## Layout dashboard

- Sidebar gelap `#17140f` dengan aksen emas, kontras terhadap area kerja terang.
- Statistik memakai satu strip dengan garis, bukan empat kartu mengambang.
- Grafik pendapatan berupa batang sederhana tanpa library tambahan. Ada tabel tersembunyi untuk pembaca layar.
- Tabel admin padat, bisa digulir horisontal di layar kecil, dengan chip status yang konsisten.
- Panel "Perlu tindakan" mengutamakan email gagal dan pesanan tertahan, karena itu pekerjaan admin yang sebenarnya.
- Form produk memakai dua kolom: informasi utama di kiri, publikasi dan checklist di kanan.

## Interaksi

- Tombol primer memakai tinta gelap. Tombol emas dipakai untuk aksi pembayaran atau pemulihan email.
- Fokus keyboard selalu terlihat: outline emas 3 piksel.
- Menu mobile berupa drawer dengan tombol yang bisa ditutup lewat Escape. Tombol CTA di header desktop disembunyikan dari drawer agar tidak duplikat.
- Halaman pembayaran memilih metode dengan radio asli, bukan div palsu. Baris terpilih diberi garis emas di kiri.
- Hitung mundur pembayaran punya teks awal sehingga tidak kosong jika JavaScript gagal.
- Animasi masuk memakai IntersectionObserver: elemen muncul sekali dengan geser 18 piksel dan opacity, bukan animasi berulang. Hero, dossier, dan grafik punya animasi pembuka sendiri.
- Baris produk memberi umpan balik hover lewat latar tipis, garis emas, dan harga yang bergeser. Tombol terangkat satu piksel saat hover.
- Drawer mobile memakai transisi opacity dan geser vertikal; `prefers-reduced-motion` mematikan animasi dan delay.
- Tombol salin memberi umpan balik "Tersalin" lalu kembali ke label awal.

## Aksesibilitas

- Skip link ke konten utama.
- Kontras teks utama dan muted di atas kertas sudah cukup untuk ukuran kecil; status memakai latar dan teks yang berbeda, bukan warna saja.
- Semua input punya label eksplisit. Pesan error tampil di dekat field dan juga diringkas di atas formulir checkout.
- Tabel memakai `th scope="col"`. Grafik punya `aria-label` dan tabel alternatif.
- Halaman admin memakai `aria-current="page"` pada navigasi aktif.
- Tombol ikon navigasi punya `aria-label`, `aria-expanded`, dan `aria-controls`.

## Batasan yang disengaja

- Gambar produk berupa URL, belum ada unggah berkas. Ini menghindari penyimpanan media setengah matang.
- Invoice berupa halaman cetak, belum PDF server-side. Pembeli bisa menyimpan PDF lewat dialog cetak browser.
- Email dikirim langsung saat webhook diproses. Antrean email bisa ditambahkan nanti tanpa mengubah tampilan.
- Satu akun admin dari `.env`, belum ada peran dan multi-pengguna. Untuk toko kecil ini lebih aman daripada membangun manajemen pengguna setengah jadi.

## Saat menambah halaman baru

1. Gunakan token warna dan font yang sudah ada, jangan menambah palet baru.
2. Pilih pola yang sudah ada: ledger rows, panel, kv, tabel admin, atau result card.
3. Tulis copy yang menjelaskan akibat, bukan slogan. Contoh: "Akses dikirim setelah pembayaran terverifikasi", bukan "Akses instan tanpa batas".
4. Uji di lebar 360 px, 768 px, dan 1280 px.
5. Pastikan status tidak hanya dibedakan lewat warna.

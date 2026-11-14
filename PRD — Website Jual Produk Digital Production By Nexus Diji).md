# PRD — Website Jual Produk Digital (Nexus Diji)

Nov 12, 2026 · @Noval S

## Ringkasan & Latar Belakang

Client butuh website toko untuk menjual produk berupa aplikasi web (produk digital), di mana pembeli bisa langsung membayar lalu otomatis menerima produknya lewat email, tanpa campur tangan admin. Pencatatan pesanan dan pengiriman akses dijalankan otomatis oleh backend website; produk yang dijual adalah aplikasi milik client yang dibangun di Google Apps Script (GAS).

Asumsi masalah saat ini: penjualan masih lewat chat/DM, sehingga admin harus online terus, proses lambat, dan rawan salah kirim. Solusinya adalah alur otomatis: pilih produk, bayar, verifikasi, lalu produk terkirim ke email pembeli.

Produk yang dijual adalah aplikasi "Manajemen Stok Barang" (PWA di Google Apps Script) berupa template Google Sheets siap salin; pembeli men-deploy web app-nya sendiri dengan panduan video dan teks. Dijual sekali bayar dengan akses selamanya; harga bergantung kompleksitas (di bawah Rp 100 rb untuk web app sederhana, di atas Rp 100 rb untuk yang kompleks).

Metode bayar: VA Permata, GoPay, dan QRIS (dana masuk ke Permata/GoPay). Email otomatis memakai Gmail biasa dan berisi invoice. Client meminta desain berwarna broken white dengan aksen gold dan nuansa elegan.

## Tujuan & Metrik Keberhasilan

Tujuan utama: client bisa menjual 24 jam dengan pengiriman produk otomatis, tanpa admin menjawab chat satu per satu.

| Tujuan | Metrik | Target |
| --- | --- | --- |
| Otomasi penuh | Pesanan terkirim tanpa intervensi manual | 100% pesanan sukses |
| Kecepatan | Waktu dari status bayar sukses sampai email terkirim | ≤ 1 menit |
| Keandalan | Pesanan berstatus paid tapi produk gagal terkirim | 0 (ada retry dan log) |
| Keamanan | Link aplikasi terkirim sebelum bayar | Tidak bisa |
| Kemudahan kelola | Client mengubah harga dan deskripsi produk tanpa developer | Cukup lewat dashboard database |

Target pengguna: pembeli umum (individu atau pelaku usaha kecil) yang membeli aplikasi manajemen stok, dan admin client yang mengelola produk dan pesanan.

## Ruang Lingkup

Versi 1 (MVP) fokus pada alur beli otomatis untuk produk digital; fitur lanjutan sengaja ditunda agar cepat rilis.

**In-scope**

- Landing page produk (fitur, preview/demo, harga, tombol beli); katalog beberapa produk
- Checkout dengan form data pembeli (nama, email, WhatsApp)
- Integrasi payment gateway dengan notifikasi otomatis (webhook)
- Backend website: terima webhook, validasi, catat pesanan, kirim email invoice dan akses template
- Database (usulan: Supabase) untuk produk dan pesanan
- Template email HTML (konfirmasi, invoice, akses template + panduan)
- Halaman status pesanan dan halaman sukses/gagal bayar

**Out-of-scope (v1)**

- Produk fisik, ongkir, dan resi
- Akun/login pembeli dan dashboard member
- Kupon/voucher dan program afiliasi
- Aplikasi mobile
- Panel admin custom (admin cukup memakai dashboard database)

**Asumsi**

- Produk adalah aplikasi "Manajemen Stok Barang" (PWA di Google Apps Script) berupa template Google Sheets siap salin yang di-deploy sendiri oleh pembeli (disertai video dan panduan teks), dijual sekali bayar dengan akses selamanya
- Volume penjualan kecil (sekitar 2 transaksi per hari), sehingga paket gratis atau murah untuk hosting, database, dan email sudah cukup
- Client sudah punya atau bersedia membuat akun payment gateway

**Arahan desain**

- Warna dasar broken white dengan aksen/garis gold (permintaan client)
- Nuansa elegan, bersih, dan lega

## User Story

| ID | Sebagai | Saya ingin | Agar | Prioritas |
| --- | --- | --- | --- | --- |
| US-01 | Pembeli | melihat fitur dan demo produk | bisa menilai sebelum membeli | Tinggi |
| US-02 | Pembeli | checkout langsung tanpa chat admin | prosesnya cepat | Tinggi |
| US-03 | Pembeli | membayar dengan VA Permata, GoPay, atau QRIS | memakai metode yang saya punya | Tinggi |
| US-04 | Pembeli | menerima invoice dan link template beserta panduan lewat email setelah bayar | langsung bisa memakai produk | Tinggi |
| US-05 | Pembeli | mengecek status pesanan | tahu pembayaran sudah diterima | Sedang |
| US-06 | Admin | mengubah harga dan deskripsi produk lewat dashboard | tidak perlu developer | Tinggi |
| US-07 | Admin | melihat daftar pesanan dan statusnya | memantau penjualan | Tinggi |
| US-08 | Admin | mengirim ulang email jika gagal | pembeli tidak dirugikan | Sedang |
| US-09 | Admin | mendapat notifikasi tiap ada penjualan | tahu ada pemasukan | Rendah |

## Kebutuhan Fungsional

Sepuluh kebutuhan berikut harus ada agar alur beli otomatis berjalan penuh.

| Kode | Modul | Kebutuhan |
| --- | --- | --- |
| FR-01 | Data produk | Nama, harga, deskripsi, gambar, link demo, link template, link video, link panduan, dan status aktif disimpan di database; katalog menampilkan beberapa produk dengan harga sesuai kompleksitas |
| FR-02 | Detail produk | Halaman produk dengan galeri, fitur, harga, dan tombol Beli |
| FR-03 | Checkout | Form nama, email, dan WhatsApp dengan validasi; email diketik dua kali atau dikonfirmasi agar tidak salah kirim |
| FR-04 | Pembayaran | Membuat transaksi di payment gateway, mengarahkan pembeli ke halaman bayar, mendukung VA Permata, GoPay, dan QRIS |
| FR-05 | Webhook | Endpoint backend menerima notifikasi bayar, memvalidasi signature, mengabaikan notifikasi duplikat |
| FR-06 | Pengiriman akses | Setelah status paid, kirim email HTML berisi invoice, link salin template Google Sheets, link video, dan panduan teks deploy ke pembeli; catat status terkirim; retry jika gagal |
| FR-07 | Pencatatan | Setiap pesanan dicatat di database: order ID, nomor invoice, produk, pembeli, nominal, status, waktu bayar, status email |
| FR-08 | Status pesanan | Halaman cek status berdasarkan order ID dan email |
| FR-09 | Admin via dashboard | Admin kelola data produk dan pantau pesanan lewat dashboard database; ada cara kirim ulang email |
| FR-10 | Notifikasi admin | Email atau WhatsApp ke admin tiap ada pesanan paid atau email gagal |

**Aturan penting pengiriman produk**

- Produk hanya dikirim jika webhook menyatakan paid; redirect dari halaman bayar tidak dianggap bukti bayar.
- Link template tidak punya masa berlaku (akses selamanya) dan hanya dikirim lewat email setelah bayar; pembeli menyalin dan men-deploy sendiri sesuai panduan.

## Alur Sistem

Produk terkirim otomatis hanya setelah webhook membuktikan pembayaran berhasil.

&#91;embedded content: alur pembayaran sampai email produk · 9 langkah, 2 jalur gagal\]

Notifikasi tidak valid atau duplikat diabaikan; jika email gagal terkirim, sistem mencoba ulang dan memberi kabar ke admin.

## Arsitektur Teknis

Website hanya menampilkan katalog dan checkout; logika pembayaran dan pengiriman produk berjalan di backend.

&#91;embedded content: arsitektur sistem · 6 komponen, 7 koneksi\]

Backend menjadi pusat: membaca produk dari database, menerima webhook dari gateway, mencatat pesanan, lalu mengirim email invoice dan akses lewat Gmail.

**Struktur data (database)**

| Tabel | Kolom |
| --- | --- |
| Produk | id\_produk, nama, harga, tingkat\_kompleksitas, deskripsi, gambar, link\_demo, link\_template, link\_video, link\_panduan, aktif |
| Pesanan | order\_id, nomor\_invoice, id\_produk, nama\_pembeli, email, whatsapp, nominal, status (pending/paid/expired/gagal), waktu\_bayar, status\_email (terkirim/gagal), jumlah\_retry |

## Non-Fungsional & Keamanan

Keamanan pembayaran dan keandalan pengiriman adalah prioritas utama; batasan kuota email Gmail perlu diketahui client sejak awal.

| Aspek | Kebutuhan |
| --- | --- |
| Verifikasi bayar | Validasi signature webhook; status paid hanya dari webhook, bukan dari redirect |
| Idempotensi | Order ID unik; webhook duplikat tidak mengirim email dua kali |
| Akses produk | Link template hanya dikirim lewat email setelah bayar; tanpa masa berlaku (akses selamanya) |
| Rahasia | API key gateway dan kredensial email disimpan di environment variable backend, tidak di kode frontend |
| Data pembeli | Hanya nama, email, WhatsApp; akses database dibatasi ke admin |
| Performa | Email terkirim ≤ 1 menit setelah bayar; halaman katalog termuat cepat di HP |
| Responsif | Tampilan nyaman di HP, tablet, dan desktop |
| Keandalan | Retry email otomatis, log error, notifikasi ke admin |

**Catatan teknis pengiriman email dan deploy**

- Gmail biasa punya batas kirim harian; dengan perkiraan sekitar 2 transaksi per hari, kuota sangat cukup (cek kuota resmi Google untuk kepastian).
- Pengiriman lewat Gmail dari backend memerlukan App Password atau OAuth; simpan kredensialnya sebagai rahasia di server.
- Karena pembeli men-deploy web app sendiri, panduan deploy (video dan teks) harus jelas dan diuji dari akun Google yang baru.
- Jika volume penjualan membesar, jalur migrasi: layanan email transaksional (mis. Resend) dan upgrade paket hosting/database.

## Timeline & Deliverables

Estimasi total 21–29 hari kerja dalam enam fase; angka ini perkiraan awal dan disesuaikan setelah scope dan jawaban client final.

| Fase | Aktivitas | Estimasi (hari kerja) |
| --- | --- | --- |
| 1. Discovery | Finalisasi scope, pilih payment gateway, siapkan akun | 2–3 |
| 2. Desain UI | Landing, katalog, detail produk, checkout, status pesanan | 4–5 |
| 3. Frontend | Implementasi halaman, responsif, integrasi data produk | 5–7 |
| 4. Backend | Database, endpoint order, webhook, validasi, email invoice dan akses | 5–7 |
| 5. Pengujian | Template email dan invoice, tes end-to-end di sandbox, kasus gagal dan duplikat | 3–4 |
| 6. Go-live | Gateway mode live, tes transaksi nyata, serah terima | 2–3 |

Fase desain dan frontend bisa tumpang tindih untuk mempercepat jadwal.

**Deliverables**

- Website toko produk digital siap pakai
- Backend (webhook, email, invoice) dan database terkonfigurasi
- Template email HTML
- Panduan admin (kelola produk, cek pesanan, kirim ulang email)
- Dokumentasi setup dan checklist uji sandbox sampai live

## Model Penjualan & Penawaran Harga

Produk dijual sekali bayar dengan akses selamanya: pembeli menerima invoice, link salin template Google Sheets, serta video dan panduan teks untuk men-deploy web app sendiri. Harga produk bergantung kompleksitas: di bawah Rp 100 rb untuk web app sederhana, di atas Rp 100 rb untuk yang kompleks.

Usulan harga jasa pembuatan website penjualan (perkiraan, sesuaikan dengan jam kerja):

| Paket | Isi | Harga |
| --- | --- | --- |
| Basic | Landing page elegan, katalog produk, checkout, payment gateway (VA Permata, GoPay, QRIS), email otomatis berisi invoice dan akses template | Rp 4,5 juta |
| Standard | Basic + halaman status pesanan, notifikasi admin, panduan admin | Rp 6–7,5 juta |

- Referensi pasar: landing page sederhana mulai sekitar Rp 3 juta, dan integrasi payment gateway menaikkan harga ([sumber](https://next-it.co.id/blog/berapa-biaya-buat-website-profesional-di-2026-panduan-lengkap-untuk-pemilik-bisnis)).
- Domain, hosting, dan fee payment gateway ditanggung client.
- Maintenance bulanan opsional sekitar Rp 150–300 rb.
- Video dan teks panduan deploy tiap produk diasumsikan disiapkan client (perlu konfirmasi).
- Revisi desain maksimal 2 kali; DP 50%.

## Risiko, Pertanyaan Terbuka & Acceptance Criteria

Risiko terbesar ada di keandalan webhook, pembeli yang kesulitan deploy sendiri, dan link template yang dibagikan.

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Kuota email Gmail habis | Pembeli tidak menerima produk | Pantau kuota, notifikasi admin, siapkan migrasi ke layanan email transaksional |
| Webhook gagal atau terlambat | Pesanan paid tapi belum terkirim | Retry, log, tombol kirim ulang manual |
| Link template dibagikan ke orang lain | Template dipakai tanpa bayar | Dikirim hanya via email setelah bayar; risiko diterima karena akses selamanya; kode lisensi sebagai opsi lanjutan |
| Pembeli kesulitan deploy sendiri | Banyak pertanyaan support dan risiko refund | Video dan teks panduan lengkap, FAQ, kontak support |
| Cold start backend | Respons webhook lambat | Pastikan gateway menerima retry; proses ringan di endpoint |
| Email masuk spam | Pembeli mengira produk tidak datang | Subjek jelas, info cek spam di halaman sukses, opsi kirim ulang |

**Pertanyaan terbuka untuk client**

- [x] Siapa yang menyiapkan video dan teks panduan deploy untuk tiap produk, dan dihosting di mana (mis. YouTube unlisted atau Drive)?
- [x] Berapa harga pasti tiap produk (arahan: di bawah Rp 100 rb untuk sederhana, di atas Rp 100 rb untuk kompleks)?
- [x] Gateway mana yang dipakai agar VA Permata, GoPay, dan QRIS masuk ke rekening Permata/GoPay client (kandidat: Midtrans), dan apakah akunnya sudah ada?
- [x] Data apa yang tampil di invoice (nama usaha, alamat, logo, format nomor invoice)?
- [x] Gmail mana yang dipakai sebagai pengirim email otomatis?
- [x] Perlu keterangan di halaman produk bahwa pembeli butuh akun Google untuk menyalin dan men-deploy template?
- [x] Apakah ada update atau garansi untuk template setelah dijual?
- [x] Kebijakan refund atau pembatalan (masih dipikirkan client)?
- [x] Ada domain dan hosting, atau perlu disiapkan?

**Acceptance criteria**

- [x] Pembeli bisa memilih produk, checkout, dan membayar di sandbox sampai selesai
- [x] Webhook berstatus paid memicu pencatatan pesanan dan email invoice serta akses template ≤ 1 menit
- [x] Link template tidak terkirim sebelum pembayaran sukses
- [x] Webhook duplikat atau signature tidak valid tidak mengirim email
- [x] Email gagal tercatat, di-retry, dan admin mendapat notifikasi
- [x] Admin bisa menambah produk dan mengirim ulang email tanpa developer
- [x] Tampilan berfungsi di HP dan desktop
- [x] Tes transaksi nyata di mode live berhasil sebelum serah terima
- [x] Dengan panduan, pembeli bisa menyalin template dan men-deploy web app sampai berjalan

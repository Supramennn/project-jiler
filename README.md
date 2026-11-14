# Dokumentasi Toko Template Digital

Website penjualan template **Manajemen Stok Barang** berbasis CodeIgniter 4. Pembeli memilih produk, membayar lewat gateway, lalu menerima invoice dan link akses lewat email setelah pembayaran terverifikasi.

Proyek ini dikembangkan oleh **Nexus Diji**. Nama usaha sengaja dikosongkan karena akan diisi oleh pemilik toko. Selama kosong, antarmuka memakai placeholder **Nama Usaha** dan inisial **NU**.

## Status

- Nama usaha: kosong, memakai placeholder.
- Kredit produsen: Nexus Diji, tampil di footer, sidebar dashboard, dan halaman login.
- Pembayaran lokal: sandbox.
- Email lokal: file, tersimpan di `writable/emails`.
- Database lokal: SQLite di `writable/database.sqlite`.
- Animasi: hero, reveal saat scroll, hover, drawer, dan grafik dashboard.

## Fitur

### Halaman publik

- Beranda dengan hero, isi paket, daftar produk, cara kerja, dan FAQ.
- Katalog dan detail produk.
- Checkout dengan validasi nama, email ganda, WhatsApp, dan persetujuan.
- Halaman pembayaran untuk VA Permata, GoPay, dan QRIS.
- Halaman status pesanan dan invoice yang bisa dicetak.
- Halaman syarat dan kebijakan privasi.

### Pembayaran dan webhook

- Driver sandbox lokal untuk pengujian tanpa uang sungguhan.
- Integrasi Midtrans Snap untuk produksi.
- Validasi signature webhook.
- Validasi nominal webhook terhadap nominal pesanan.
- Idempotensi: webhook berulang tidak mengirim email kedua.
- Pencatatan setiap event pesanan dan payload webhook.

### Email

- Email HTML berisi invoice, link template, link video, dan panduan.
- Driver file untuk lokal dan SMTP Gmail untuk produksi.
- Status email per pesanan: `belum`, `terkirim`, `gagal`.
- Notifikasi admin saat email pembeli gagal.
- Kirim ulang manual dari dashboard.
- Kirim ulang otomatis lewat `php spark email:retry`.

### Dashboard admin

- Statistik pendapatan, pesanan pending, dan email gagal.
- Daftar pesanan dengan pencarian dan filter status.
- Detail pesanan, timeline event, dan log email.
- Kirim ulang email dan tandai dibayar manual.
- CRUD produk, pengaturan harga, link akses, dan status tampil.
- Halaman pengaturan integrasi dan kirim email uji.

### Animasi dan aksesibilitas

- Hero muncul bertahap saat halaman dibuka.
- Elemen di bawah lipatan muncul sekali saat masuk viewport.
- Baris produk dan tombol punya umpan balik hover.
- Drawer mobile memakai transisi, bukan muncul mendadak.
- Grafik dashboard tumbuh dari bawah.
- `prefers-reduced-motion` mematikan animasi dan delay.
- Fokus keyboard terlihat, skip link tersedia, dan status tidak hanya dibedakan lewat warna.

## Teknologi

- PHP 8.2 atau lebih baru.
- CodeIgniter 4.7.
- SQLite untuk lokal, MySQL atau PostgreSQL untuk produksi.
- CSS dan JavaScript tanpa framework.
- Google Fonts: Cormorant Garamond dan Plus Jakarta Sans.

## Struktur folder

```text
app/
  Commands/         Perintah email:retry
  Config/           Store, Payment, Mailer, Routes, Filters
  Controllers/      Halaman publik dan admin
  Database/         Migrasi dan ProductSeeder
  Filters/          AdminAuth
  Helpers/          Format rupiah, tanggal, nama toko, status
  Libraries/        PaymentService, WebhookService, OrderFulfillment, OrderMailer, Mailer
  Models/           Product, Order, OrderEvent, EmailLog
  Views/            Publik, admin, email, invoice
public/
  assets/css/       site.css dan admin.css
  assets/js/        site.js
writable/
  database.sqlite   Database lokal
  emails/           Email mode file
  logs/             Log aplikasi
```

## Menjalankan di lokal

### Kebutuhan

- PHP 8.2+ dengan ekstensi `intl`, `mbstring`, `curl`, `sqlite3`, dan `pdo_sqlite`.
- Composer.
- Browser modern.

### Instalasi

```bash
composer install
```

Berkas `.env` sudah tersedia untuk pengembangan lokal. Jika belum ada, salin dari templat CodeIgniter lalu sesuaikan.

Nilai minimum yang perlu diperiksa:

```dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
app.appTimezone = 'Asia/Jakarta'
database.default.DBDriver = SQLite3
database.default.database = database.sqlite
PAYMENT_DRIVER = sandbox
MAIL_DRIVER = file
STORE_NAME = ''
STORE_PRODUCER = 'Nexus Diji'
```

Siapkan database dan produk contoh:

```bash
php spark migrate
php spark db:seed ProductSeeder
```

Jalankan server:

```bash
php spark serve --host localhost --port 8080
```

Buka `http://localhost:8080`.

Gunakan `localhost`, bukan `127.0.0.1`. `app.baseURL` memakai localhost, sehingga sesi admin bisa gagal jika alamatnya berbeda.

### Akun admin lokal

```text
URL      : http://localhost:8080/admin/login
Email    : admin@nexusdiji.test
Password : admin12345
```

Kredensial ini hanya untuk lokal. Ganti hash kata sandi sebelum website dipasang di server publik.

## Mengisi nama usaha

Isi `STORE_NAME` di `.env`:

```dotenv
STORE_NAME = 'Nama Toko Anda'
```

Setelah disimpan, muat ulang halaman. Nama itu dipakai di header, footer, invoice, email, dashboard, dan judul halaman. Inisial brand diambil dari dua kata pertama nama toko.

Selama `STORE_NAME` kosong:

- Header dan footer menampilkan **Nama Usaha**.
- Brand mark menampilkan **NU**.
- Invoice dan email memakai placeholder yang sama.
- Halaman pengaturan menampilkan peringatan agar nama segera diisi.

Nexus Diji tidak ikut berubah. Kredit **Diproduksi oleh Nexus Diji** tetap tampil karena itu adalah identitas produsen proyek.

Data usaha lain yang sebaiknya diisi:

```dotenv
STORE_TAGLINE = 'Template stok siap pakai untuk usaha kecil'
STORE_EMAIL = 'halo@namausaha.test'
STORE_WHATSAPP = ''
STORE_ADDRESS = 'Alamat usaha belum diatur'
INVOICE_PREFIX = 'INV'
```

`INVOICE_PREFIX` hanya memengaruhi nomor invoice dan order baru. Pesanan lama tetap memakai nomor yang sudah tersimpan.

## Konfigurasi penting

| Variabel | Keterangan |
| --- | --- |
| `CI_ENVIRONMENT` | `development` atau `production` |
| `app.baseURL` | Alamat website, harus diakhiri garis miring |
| `STORE_NAME` | Nama usaha, boleh kosong selama pengembangan |
| `STORE_PRODUCER` | Kredit produsen, default Nexus Diji |
| `STORE_EMAIL` | Email kontak yang tampil di invoice dan footer |
| `STORE_ADDRESS` | Alamat usaha di invoice dan email |
| `INVOICE_PREFIX` | Awalan nomor invoice, default `INV` |
| `PAYMENT_DRIVER` | `sandbox` atau `midtrans` |
| `MIDTRANS_SERVER_KEY` | Server key Midtrans |
| `MIDTRANS_CLIENT_KEY` | Client key Midtrans |
| `MIDTRANS_IS_PRODUCTION` | `false` untuk sandbox Midtrans, `true` untuk produksi |
| `MIDTRANS_SNAP_URL` | Kosongkan untuk memakai URL Snap sesuai mode |
| `MAIL_DRIVER` | `file` atau `smtp` |
| `MAIL_FROM_EMAIL` | Alamat pengirim email |
| `MAIL_FROM_NAME` | Nama pengirim, kosong berarti memakai nama usaha |
| `MAIL_SMTP_USER` | Alamat Gmail pengirim |
| `MAIL_SMTP_PASS` | App Password Gmail, bukan kata sandi akun |
| `APP_KEY` | Kunci tanda tangan webhook sandbox dan token internal |
| `APP_ADMIN_EMAIL` | Email login admin |
| `APP_ADMIN_PASSWORD_HASH` | Hash `password_hash()` untuk admin |
| `ADMIN_NOTIFY_EMAIL` | Tujuan notifikasi kegagalan email |

Membuat hash kata sandi baru:

```bash
php -r "echo password_hash('kata-sandi-baru', PASSWORD_DEFAULT), PHP_EOL;"
```

Salin hasilnya ke `APP_ADMIN_PASSWORD_HASH`.

## Alur pengujian end-to-end

1. Buka `/produk`, pilih **Manajemen Stok Barang**, lalu klik **Beli template**.
2. Isi nama, email, ketik ulang email, nomor WhatsApp, dan centang persetujuan.
3. Di halaman pembayaran, pilih **VA Permata**, **GoPay**, atau **QRIS**.
4. Pada mode sandbox, klik **Simulasikan pembayaran berhasil**.
5. Halaman sukses menampilkan status pembayaran dan status email. Buka invoice dari tombol yang tersedia.
6. Email tidak masuk ke inbox pada mode file. Buka `writable/emails/` untuk melihat HTML email.
7. Buka `/status`, masukkan nomor pesanan dan email untuk memeriksa status.
8. Masuk ke `/admin`, buka **Pesanan**, lalu buka detail pesanan untuk melihat timeline dan log email.
9. Gunakan **Kirim ulang email** untuk menguji pengiriman ulang.
10. Buka **Pengaturan** lalu klik **Kirim email uji**.

Di dashboard, tombol **Buat pesanan uji** hanya muncul saat `CI_ENVIRONMENT` bukan `production`.

## Kirim ulang email otomatis

Email akses dikirim saat webhook pembayaran diproses. Jika SMTP bermasalah, status email menjadi `gagal` dan pesanan tetap `paid`.

```bash
php spark email:retry
```

Perintah memproses maksimal 20 pesanan per jalan. Tambahkan angka untuk mengubah batas:

```bash
php spark email:retry 50
```

Perintah selesai dengan kode `0` jika semua email berhasil atau tidak ada yang perlu dikirim, dan kode `1` jika masih ada kegagalan.

**Linux/macOS (cron), setiap 15 menit:**

```cron
*/15 * * * * cd /path/ke/nexus-diji && php spark email:retry >> writable/logs/email-retry.log 2>&1
```

**Windows (Task Scheduler):**

1. Buat task baru dengan trigger harian, lalu ulangi setiap 15 menit.
2. Pilih action **Start a program**.
3. Isi **Program/script** dengan path `php.exe`, misalnya `C:\php\php.exe`.
4. Isi **Add arguments** dengan `spark email:retry`.
5. Isi **Start in** dengan folder proyek, misalnya `D:\project-jiler`.
6. Simpan, lalu jalankan sekali untuk memastikan tidak ada error.

Perintah ini tidak memproses pesanan `pending` dan tidak mengirim akses sebelum pembayaran terverifikasi.

## Pengujian yang sudah dilakukan

- Semua halaman publik merespons `200`.
- Checkout dan pemilihan VA Permata, GoPay, QRIS berhasil.
- Simulasi pembayaran sandbox mengubah status menjadi `paid`.
- Invoice menampilkan status `LUNAS`.
- Webhook dengan signature salah ditolak `403`, pesanan tetap pending, email tidak dikirim.
- Webhook dengan nominal salah ditolak `400`.
- Webhook duplikat dibalas `200` tanpa email kedua. Log email tetap satu sampai resend manual.
- Dashboard admin, pesanan, produk, edit produk, dan pengaturan merespons `200`.
- Kirim ulang email menambah file dan log email baru.
- Tandai dibayar memproses email akses.
- CSRF tanpa token ditolak `403`.
- Akses admin tanpa login dialihkan ke halaman login.
- Validasi form produk menampilkan error yang benar.
- `email:retry` diuji dengan pesanan paid berstatus email `gagal`, lalu berhasil menjadi `terkirim`.
- Layout 360 piksel diuji pada beranda, katalog, detail, checkout, dan login. Tidak ada overflow horisontal.
- Animasi reveal, hover, drawer, dan grafik diuji di browser Chromium.

## Checklist produksi

- Isi `STORE_NAME`, `STORE_EMAIL`, `STORE_ADDRESS`, dan `INVOICE_PREFIX`.
- Set `CI_ENVIRONMENT = production` dan `app.baseURL` ke domain HTTPS.
- Pindahkan database ke MySQL atau PostgreSQL, lalu jalankan migrasi.
- Isi server key dan client key Midtrans, set `PAYMENT_DRIVER = midtrans`.
- Daftarkan `https://domain-anda/webhook/midtrans` sebagai Payment Notification URL di Midtrans.
- Set `MAIL_DRIVER = smtp` dan isi kredensial Gmail App Password.
- Ganti `APP_KEY` dengan nilai acak baru, minimal 32 karakter.
- Ganti email admin dan hash kata sandi admin.
- Ganti semua link template, video, dan panduan yang masih memakai `example.com` dari menu **Produk**.
- Aktifkan HTTPS dan cookie sesi `Secure`.
- Pastikan folder `writable/` bisa ditulis proses PHP, tetapi tidak bisa diakses langsung dari browser.
- Jadwalkan `php spark email:retry` setiap 15 menit.
- Atur pencadangan database dan folder email.
- Uji satu pembayaran sandbox, lalu satu pembayaran produksi nominal kecil sebelum promosi.

Integrasi Midtrans live dan SMTP Gmail belum diuji karena belum ada kredensial asli. Mode lokal memakai sandbox dan file.

## Troubleshooting

### Login gagal atau sesi hilang

Buka `http://localhost:8080`, bukan `http://127.0.0.1:8080`. Alamat harus sama dengan `app.baseURL`.

### Email tidak ada di inbox

Pada mode file, email tidak dikirim. Buka `writable/emails/`. Untuk pengiriman sungguhan, set `MAIL_DRIVER = smtp` dan isi kredensial Gmail.

### Webhook ditolak 403

Signature tidak cocok. Periksa `APP_KEY` untuk sandbox atau server key Midtrans untuk produksi. Jangan mengubah payload setelah signature dibuat.

### Email gagal terkirim

Buka detail pesanan di dashboard, periksa log email, lalu klik **Kirim ulang email**. Untuk retry massal, jalankan `php spark email:retry`.

### Ingin mengulang dari database bersih

```bash
php spark migrate:refresh --all
php spark db:seed ProductSeeder
```

Perintah ini menghapus pesanan, log email, dan produk, lalu mengisi ulang tiga produk contoh.

### Perubahan CSS atau JavaScript tidak terlihat

Muat ulang dengan `Ctrl + F5`. Pastikan berkas di `public/assets` tidak di-cache oleh server produksi.

## Batasan

- Satu akun admin dari `.env`, belum ada peran atau multi-pengguna.
- Gambar produk memakai URL, belum ada unggah berkas.
- Invoice berupa halaman cetak browser, belum PDF server-side.
- Email dikirim sinkron saat webhook diproses. Untuk volume besar, pindahkan ke antrean.
- Refund dan pembatalan belum otomatis.
- Nama usaha masih placeholder sampai `STORE_NAME` diisi.

## Dokumen terkait

- `DESIGN.md`: arah visual, warna, tipografi, layout, animasi, dan aksesibilitas.
- `PRD - Website Jual Produk Digital Production By Nexus Diji).md`: kebutuhan produk dari pemilik proyek.

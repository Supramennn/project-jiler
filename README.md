# Dokumentasi Toko Template Digital

Website penjualan template **Apps Script** milik **ZafScript** berbasis CodeIgniter 4. Pembeli memilih produk, membayar lewat gateway, lalu menerima invoice dan link akses lewat email setelah pembayaran terverifikasi.

Proyek ini dikembangkan oleh **Nexus Diji**. Nama usaha default adalah **ZafScript** dan bisa diganti lewat `STORE_NAME`.

## Status

- Nama usaha: ZafScript.
- Kredit produsen: Nexus Diji, tampil di footer, sidebar dashboard, dan halaman login.
- Pembayaran lokal: sandbox.
- Email lokal: file, tersimpan di `writable/emails`.
- Database lokal: SQLite di `writable/database.sqlite`.
- Produksi: Vercel (container FrankenPHP) + Neon PostgreSQL, sesi database.
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
- CRUD produk, pengaturan harga, harga promo, jadwal promo, link akses, dan status tampil.
- Halaman pengaturan integrasi dan kirim email uji.

### Promo dan diskon

- Setiap produk punya tiga kolom promo: `harga_promo`, `promo_mulai`, dan `promo_berakhir`.
- Promo aktif hanya jika `harga_promo` lebih kecil dari `harga` dan waktu sekarang berada di dalam jadwal (jadwal opsional).
- Katalog, detail produk, dan checkout menampilkan badge diskon, harga normal yang dicoret, harga promo, dan nominal hemat.
- Checkout menagih harga promo. Pesanan menyimpan harga normal di `product_harga` dan nominal yang dibayar di `nominal`, sehingga invoice, email, dan detail pesanan admin menampilkan diskon yang sama.
- Harga promo diubah dari **Admin → Produk → Ubah** pada panel "Promo dan diskon". Kosongkan `harga_promo` untuk mematikan promo dan kembali ke harga normal.
- `PromoSeeder` mengisi contoh promo 20% untuk produk yang harganya masih kosong promonya: 89.000 → 71.000, 149.000 → 119.000, dan 199.000 → 159.000. Ganti atau hapus nilainya sebelum toko dipakai sungguhan.

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
- SQLite untuk lokal, PostgreSQL untuk produksi di Neon.
- Docker + FrankenPHP untuk deploy ke Vercel.
- CSS dan JavaScript tanpa framework.
- Google Fonts: Cormorant Garamond dan Plus Jakarta Sans.

## Struktur folder

```text
app/
  Commands/         Perintah email:retry
  Config/           Store, Payment, Mailer, Database, Session, Routes, Filters
  Controllers/      Halaman publik, admin, webhook, dan cron
  Database/         Migrasi dan ProductSeeder
  Filters/          AdminAuth
  Helpers/          Format rupiah, tanggal, nama toko, status
  Libraries/        PaymentService, WebhookService, OrderFulfillment, OrderMailer, Mailer
  Models/           Product, Order, OrderEvent, EmailLog
  Views/            Publik, admin, email, invoice
docker/
  php.ini           Konfigurasi PHP produksi
public/
  assets/css/       site.css dan admin.css
  assets/js/        site.js
writable/
  database.sqlite   Database lokal
  emails/           Email mode file
  logs/             Log aplikasi
Dockerfile          Image FrankenPHP untuk Vercel
Caddyfile           Web server container
docker-entrypoint.sh
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

## Nama dan identitas usaha

Nama default adalah **ZafScript**. Nilainya diatur di `app/Config/Store.php` dan bisa ditimpa lewat `.env`:

```dotenv
STORE_NAME = 'ZafScript'
```

Nama itu dipakai di header, footer, invoice, email, dashboard, dan judul halaman. Inisial brand diambil dari dua huruf pertama nama satu kata, sehingga ZafScript memakai **ZA**.

Jika `STORE_NAME` dikosongkan, antarmuka kembali memakai placeholder **Nama Usaha** dan inisial **NU**, dan halaman pengaturan menampilkan peringatan.

Nexus Diji tidak ikut berubah. Kredit **Diproduksi oleh Nexus Diji** tetap tampil karena itu adalah identitas produsen proyek.

Data usaha lain yang sebaiknya diisi:

```dotenv
STORE_TAGLINE = 'Template Apps Script siap pakai untuk usaha kecil'
STORE_EMAIL = 'halo@namausaha.test'
STORE_WHATSAPP = ''
STORE_ADDRESS = 'Alamat usaha belum diatur'
INVOICE_PREFIX = 'INV'
```

`INVOICE_PREFIX` hanya memengaruhi nomor invoice dan order baru. Pesanan lama tetap memakai nomor yang sudah tersimpan.

## SEO

Halaman publik mengirim title, meta description, canonical, robots, Open Graph, Twitter card, dan JSON-LD. Tidak ada `og:image` sampai produk punya gambar sendiri, jadi tautan yang dibagikan hanya memakai judul dan deskripsi.

Yang sudah berjalan:

- `/sitemap.xml` dibuat dari produk aktif, katalog, dan halaman legal.
- `/robots.txt` menutup admin, checkout, pembayaran, invoice, status, webhook, dan cron.
- Checkout, pembayaran, invoice, status, dan admin memakai `noindex,nofollow`.
- Halaman produk memakai schema `Product` dengan harga yang benar-benar ditagih, termasuk harga promo, ditambah `BreadcrumbList`.
- Beranda memakai schema `FAQPage` dari pertanyaan yang benar-benar tampil di halaman.
- `Organization` dan `WebSite` hanya dipasang jika `STORE_NAME` berisi nama usaha yang sebenarnya.

Langkah aktivasi:

1. Pastikan `STORE_NAME` berisi nama final, saat ini `ZafScript`. Jangan memakai teks contoh `Nama Toko Anda`, karena teks itu ikut masuk ke title dan schema.
2. Isi `APP_BASE_URL` dengan domain produksi, misalnya `https://project-jiler.vercel.app`.
3. Deploy, lalu buka `https://domain-anda.com/sitemap.xml` dan `https://domain-anda.com/robots.txt`.
4. Daftarkan domain di Google Search Console, pilih verifikasi HTML tag, lalu isi tokennya di `GOOGLE_SITE_VERIFICATION` pada environment Vercel.
5. Deploy ulang, kembali ke Search Console, dan kirim `/sitemap.xml`.

Pendaftaran Google Search Console dan Bing Webmaster Tools tidak bisa dilakukan dari kode karena memerlukan akun pemilik domain.

## Keamanan dan operasional

### Rate limit

Filter `App\Filters\ThrottleRequests` memakai Throttler CodeIgniter. Kunci disimpan per bucket dan IP, jadi satu endpoint tidak memengaruhi endpoint lain. IP diambil dari `X-Forwarded-For` yang ditimpa Vercel, lalu fallback ke `REMOTE_ADDR` untuk lokal.

| Endpoint | Batas | Jendela |
| --- | --- | --- |
| `POST /checkout/{slug}` | 5 | 60 detik |
| `POST /status` | 10 | 60 detik |
| `POST /pembayaran/{order}/metode` | 10 | 60 detik |
| `POST /pembayaran/{order}/simulasi` | 10 | 60 detik |
| `POST /admin/login` | 5 | 300 detik |

Respons `429` menyertakan header `Retry-After` dan halaman `errors/rate_limit.php`. Untuk request `Accept: application/json`, balasannya JSON.

Penyimpanan memakai cache file. Di Vercel, batas berlaku per instance Function, jadi saat trafik tersebar ke beberapa instance batas efektif bisa lebih longgar. Untuk batas yang ketat dan konsisten, konfigurasi Redis atau Upstash sebagai cache bersama.

### Security headers

Filter `secureheaders` dipasang global sebagai filter `after`, sehingga berlaku untuk halaman publik, admin, JSON, dan respons `429`:

- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `X-Download-Options: noopen`
- `X-Permitted-Cross-Domain-Policies: none`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), geolocation=(), microphone=()`

Header ini tidak memakai CSP karena halaman masih memuat font Google dan skrip inline. Menambahkan CSP membutuhkan audit inline script lebih dulu.

### Health check

`GET /health` mengembalikan JSON tanpa autentikasi dan `noindex`:

```json
{
  "status": "ok",
  "time": "2026-11-18T14:32:38+07:00",
  "environment": "production",
  "checks": {
    "database": "ok",
    "schema": "ok",
    "payment": "midtrans",
    "mail": "smtp",
    "cron_secret": true
  },
  "warnings": []
}
```

- `200` jika database dan tabel `orders` bisa dibaca.
- `503` jika database atau skema gagal.
- `warnings` memberi tahu driver pembayaran yang masih sandbox, email yang masih `file`, atau `CRON_SECRET` kosong.

Endpoint ini hanya membaca koneksi dan konfigurasi. Tidak ada data pesanan atau kredensial yang dikembalikan. Cocok dipakai UptimeRobot, Better Stack, atau monitor lain dengan interval 5 menit.

### Cron operasional

Tiga endpoint `GET` memakai autentikasi yang sama:

```text
Authorization: Bearer <CRON_SECRET>
```

atau:

```text
X-Cron-Secret: <CRON_SECRET>
```

| Endpoint | Fungsi | Batas per jalan |
| --- | --- | --- |
| `/cron/retry-email` | Kirim ulang email akses yang gagal | 50 |
| `/cron/reconcile-payments` | Cocokkan status pesanan dengan Midtrans | 25, anggaran 25 detik |
| `/cron/expire-orders` | Tandai pesanan pending yang lewat `expires_at` | 200 |

Semua endpoint menerima `?limit=` dalam batas di atas. Tanpa `CRON_SECRET` membalas `503`; token salah membalas `403`.

`reconcile-payments` hanya berjalan jika `PAYMENT_DRIVER=midtrans`. Pesanan yang sudah dibayar tetapi webhook-nya tidak masuk akan ditandai `paid` dan email akses dikirim lewat jalur yang sama dengan webhook. Sumber perubahan dicatat sebagai `rekonsiliasi` di riwayat pesanan.

`expire-orders` memakai `OrderFulfillment::markExpired()`, sehingga email akses tidak pernah dikirim untuk pesanan yang tidak dibayar.

### Ekspor CSV

Tombol **Ekspor CSV** di halaman **Admin → Pesanan** mengunduh pesanan sesuai filter `q`, `status`, dan `email_status` yang sedang aktif. Ketentuan:

- Maksimal 5.000 baris per ekspor.
- UTF-8 dengan BOM agar Excel membaca rupiah dan huruf beraksen dengan benar.
- Sel yang diawali `=`, `+`, `-`, `@`, tab, atau enter diberi awalan `'` untuk mencegah formula injection.
- Kolom diskon dihitung `harga normal - nominal`, tidak pernah negatif.
- Respons memakai `Cache-Control: no-store`.

Ekspor tidak memuat `access_token` atau isi payload webhook.

## Konfigurasi penting

| Variabel | Keterangan |
| --- | --- |
| `CI_ENVIRONMENT` | `development` atau `production` |
| `APP_BASE_URL` | Alamat publik website, misalnya `https://toko.vercel.app` |
| `app.baseURL` | Alamat lokal, harus diakhiri garis miring |
| `GOOGLE_SITE_VERIFICATION` | Token verifikasi Google Search Console, boleh kosong |
| `STORE_NAME` | Nama usaha, boleh kosong selama pengembangan |
| `STORE_PRODUCER` | Kredit produsen, default Nexus Diji |
| `STORE_EMAIL` | Email kontak yang tampil di invoice dan footer |
| `STORE_ADDRESS` | Alamat usaha di invoice dan email |
| `INVOICE_PREFIX` | Awalan nomor invoice, default `INV` |
| `DATABASE_URL` | Connection string Neon; `DATABASE_URL_UNPOOLED` dipakai lebih dulu bila ada |
| `DB_DRIVER` | `Postgre`, `MySQLi`, atau `SQLite3` |
| `DB_HOST` | Host database bila tidak memakai `DATABASE_URL` |
| `DB_PORT` | Port database, default 5432 untuk PostgreSQL |
| `DB_DATABASE` | Nama database |
| `DB_USERNAME` | User database |
| `DB_PASSWORD` | Kata sandi database |
| `DB_SSLMODE` | Mode SSL PostgreSQL, isi `require` untuk Neon |
| `DB_DEBUG` | `false` di produksi agar detail error tidak tampil |
| `SESSION_DRIVER` | `file` untuk lokal, `database` untuk Vercel |
| `SESSION_TABLE` | Nama tabel sesi, default `ci_sessions` |
| `SESSION_COOKIE_NAME` | Nama cookie sesi |
| `SESSION_EXPIRATION` | Umur sesi dalam detik |
| `COOKIE_SECURE` | `true` bila situs hanya lewat HTTPS |
| `CRON_SECRET` | Token untuk ketiga endpoint cron |
| `PAYMENT_DRIVER` | `sandbox` atau `midtrans` |
| `MIDTRANS_SERVER_KEY` | Server key Midtrans |
| `MIDTRANS_CLIENT_KEY` | Client key Midtrans |
| `MIDTRANS_IS_PRODUCTION` | `false` untuk sandbox Midtrans, `true` untuk produksi |
| `MIDTRANS_SNAP_URL` | Kosongkan untuk memakai URL Snap sesuai mode |
| `MIDTRANS_API_URL` | Kosongkan untuk memakai URL API status sesuai mode |
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

## Deploy ke Vercel + Neon

Vercel tidak menjalankan PHP secara langsung. Proyek ini memakai **Container preset** resmi Vercel: `Dockerfile` berisi FrankenPHP, lalu Vercel menjalankan image itu sebagai Function. SQLite tidak bisa dipakai karena filesystem Function bersifat sementara dan tidak dibagi antar instance. Karena itu produksi memakai Neon PostgreSQL dan sesi database.

Berkas deploy yang sudah disiapkan:

- `Dockerfile`: PHP 8.4 + FrankenPHP + ekstensi `intl`, `pdo_pgsql`, `pgsql`.
- `Caddyfile`: menyajikan `public/` di port 80 tanpa auto-HTTPS, lalu meneruskan request PHP.
- `Dockerfile` menyalin `Caddyfile` ke `/etc/caddy/Caddyfile`. Tanpa langkah ini FrankenPHP memakai config bawaan image dan dapat terjadi redirect `308` berulang.
- `docker-entrypoint.sh`: menyiapkan folder tulis di `/tmp`.
- `docker/php.ini`: konfigurasi produksi.
- `app/Database/Migrations/2026-11-14-000001_CreateCiSessionsTable.php`: tabel `ci_sessions`.
- `.env.example`: daftar variabel produksi.

### 1. Siapkan Neon

1. Buat akun dan project di [neon.tech](https://neon.tech).
2. Pilih region Singapore (`ap-southeast-1`) agar dekat dengan pengguna dan region Function Vercel.
3. Buka **Connect**, matikan **Connection pooling**, lalu salin connection string **direct**:

   ```text
   postgresql://user:password@ep-xxxx.ap-southeast-1.aws.neon.tech/neondb?sslmode=require
   ```

   Jangan memakai host `-pooler`. Sesi database CodeIgniter memakai PostgreSQL advisory lock, sedangkan PgBouncer transaction pooling bisa memindahkan lock ke koneksi lain.

4. Simpan string itu sebagai `DATABASE_URL`. Jangan commit nilainya. Jika memakai integrasi Neon-Vercel, biarkan integrasi mengisi `DATABASE_URL` dan `DATABASE_URL_UNPOOLED`; aplikasi memilih `DATABASE_URL_UNPOOLED` lebih dulu. Jika hanya ada host `-pooler`, aplikasi otomatis memakai endpoint direct-nya.

Alternatif yang lebih cepat: pasang integrasi **Neon** dari Vercel Marketplace. Integrasi ini membuat database Neon dan mengisi `DATABASE_URL`, `DATABASE_URL_UNPOOLED`, serta variabel `PG*`/`POSTGRES_*` ke environment Production, Preview, dan Development.

Neon gratis bisa tidur saat tidak ada trafik. Request pertama setelah idle bisa terasa lambat beberapa detik.

### 2. Jalankan migrasi ke Neon

Jalankan dari folder proyek. Perintah ini hanya memakai `DATABASE_URL` pada proses tersebut, sehingga `.env` lokal tetap SQLite.

**PowerShell:**

```powershell
$env:DATABASE_URL = 'postgresql://user:password@ep-xxxx.ap-southeast-1.aws.neon.tech/neondb?sslmode=require'
$env:DB_DRIVER = 'Postgre'
php spark migrate
php spark db:seed ProductSeeder
php spark migrate:status
Remove-Item Env:DATABASE_URL
Remove-Item Env:DB_DRIVER
```

**Bash:**

```bash
DATABASE_URL='postgresql://...' DB_DRIVER=Postgre php spark migrate
DATABASE_URL='postgresql://...' DB_DRIVER=Postgre php spark db:seed ProductSeeder
DATABASE_URL='postgresql://...' DB_DRIVER=Postgre php spark migrate:status
```

Migrasi membuat tabel commerce dan `ci_sessions`. Seeder mengisi tiga produk contoh. Jika database Neon sudah berisi data, cukup jalankan `php spark migrate`.

### 3. Hubungkan proyek ke Vercel

**Lewat CLI:**

```bash
npm install -g vercel
vercel login
vercel link
vercel project update --framework container --yes
vercel deploy --prod
```

`vercel project update` wajib bila project sebelumnya terdeteksi sebagai framework lain. Tanpa itu, Vercel tidak membangun `Dockerfile` dan situs tetap tidak bisa menjalankan PHP.

Jika `Caddyfile` atau `Dockerfile` baru diubah tetapi image lama masih terpakai, paksa build ulang:

```bash
vercel deploy --prod --force
```

**Lewat dashboard/Git:**

1. Import repository ke Vercel.
2. Pada **Framework Preset**, pilih **Container**.
3. Biarkan **Root Directory** kosong karena `Dockerfile` ada di root.
4. Tambahkan environment variable produksi, lalu deploy.

### 4. Environment variable Vercel

Tambahkan di **Project → Settings → Environment Variables**. Nama variabel Vercel sebaiknya huruf besar dan garis bawah; jangan memakai nama bertitik seperti `app.baseURL`.

Minimum yang harus diisi untuk produksi:

```dotenv
CI_ENVIRONMENT = production
APP_BASE_URL = 'https://domain-anda.com'
APP_KEY = 'kunci-acak-minimal-32-karakter'

DATABASE_URL = 'postgresql://user:password@ep-xxxx.ap-southeast-1.aws.neon.tech/neondb?sslmode=require'
DB_DRIVER = Postgre
DB_DEBUG = false

SESSION_DRIVER = database
SESSION_TABLE = ci_sessions
SESSION_COOKIE_NAME = nexus_session
SESSION_EXPIRATION = 7200
COOKIE_SECURE = true

STORE_NAME = 'ZafScript'
STORE_EMAIL = 'email-anda@gmail.com'
STORE_ADDRESS = 'Alamat usaha'
STORE_PRODUCER = 'Nexus Diji'
INVOICE_PREFIX = INV

APP_ADMIN_NAME = 'Admin'
APP_ADMIN_EMAIL = 'email-admin@gmail.com'
APP_ADMIN_PASSWORD_HASH = '$2y$12$...'
ADMIN_NOTIFY_EMAIL = 'email-admin@gmail.com'

PAYMENT_DRIVER = midtrans
MIDTRANS_SERVER_KEY = '...'
MIDTRANS_CLIENT_KEY = '...'
MIDTRANS_IS_PRODUCTION = true

MAIL_DRIVER = smtp
MAIL_FROM_EMAIL = 'email-anda@gmail.com'
MAIL_FROM_NAME = 'ZafScript'
MAIL_SMTP_HOST = 'smtp.gmail.com'
MAIL_SMTP_PORT = 587
MAIL_SMTP_USER = 'email-anda@gmail.com'
MAIL_SMTP_PASS = 'app-password-gmail'
MAIL_SMTP_CRYPTO = tls

CRON_SECRET = 'token-acak-panjang'
```

Catatan:

- `APP_BASE_URL` boleh dikosongkan pada deploy pertama. Aplikasi memakai `VERCEL_URL` atau `VERCEL_PROJECT_PRODUCTION_URL` sebagai cadangan. Setelah domain final dipasang, isi `APP_BASE_URL` lalu redeploy.
- Perubahan environment variable baru berlaku setelah redeploy.
- Gunakan **Preview** environment terpisah untuk uji Midtrans sandbox. Jangan mengaktifkan `PAYMENT_DRIVER=sandbox` di Production karena tombol simulasi bisa melewati pembayaran sungguhan.
- Untuk Production, `MIDTRANS_IS_PRODUCTION=true` dan `PAYMENT_DRIVER=midtrans`.

### 5. Verifikasi setelah deploy

1. Buka `/` dan `/produk`. Keduanya harus merespons halaman toko, bukan 404 atau halaman kosong.
2. Buka `/health`. Database dan skema harus `ok`, dan `warnings` harus kosong setelah semua kredensial diisi.
3. Periksa header respons `/` dengan `curl.exe -I`: `X-Frame-Options`, `X-Content-Type-Options`, dan `Referrer-Policy` harus ada.
4. Buka `/admin/login`, masuk, lalu buka **Pesanan** dan **Produk**. Jika login berhasil dan sesi bertahan, konfigurasi database dan `ci_sessions` benar.
5. Daftarkan `https://domain-anda.com/webhook/midtrans` sebagai **Payment Notification URL** di Midtrans.
6. Lakukan satu transaksi sandbox di Preview deployment, lalu satu transaksi nominal kecil di Production.
7. Pastikan email berisi invoice dan link akses masuk, dan invoice menampilkan `LUNAS`.
8. Uji ketiga endpoint cron:

   ```powershell
   curl.exe -i -H "Authorization: Bearer TOKEN_CRON" https://domain-anda.com/cron/retry-email
   curl.exe -i -H "Authorization: Bearer TOKEN_CRON" https://domain-anda.com/cron/reconcile-payments
   curl.exe -i -H "Authorization: Bearer TOKEN_CRON" https://domain-anda.com/cron/expire-orders
   ```

   Respons normal `retry-email`:

   ```json
   {"ok":true,"processed":0,"sent":0,"failed":0}
   ```

   Tanpa token, endpoint harus membalas `403`. Jika `CRON_SECRET` belum diatur, endpoint membalas `503`.

9. Coba `POST /status` lebih dari 10 kali dalam satu menit. Request ke-11 harus membalas `429` dengan `Retry-After`.
10. Buka **Admin → Pesanan**, klik **Ekspor CSV**, dan pastikan berkas terbuka rapi di Excel.

### 6. Jadwalkan cron operasional

Tiga endpoint `GET` memakai header yang sama:

```text
Authorization: Bearer <CRON_SECRET>
```

atau:

```text
X-Cron-Secret: <CRON_SECRET>
```

| Endpoint | Jadwal yang disarankan |
| --- | --- |
| `/cron/retry-email` | Setiap 15 menit |
| `/cron/reconcile-payments` | Setiap 15 menit, offset 5 menit |
| `/cron/expire-orders` | Setiap 15 menit, offset 10 menit |

**Vercel Hobby:** cron bawaan Vercel maksimal sekali sehari. Untuk jadwal 15 menit, pakai scheduler eksternal gratis seperti cron-job.org. Buat tiga job terpisah, arahkan ke URL endpoint, dan tambahkan header `Authorization: Bearer <CRON_SECRET>`. Jika jadwal harian cukup, tambahkan cron harian di `vercel.json`.

**Vercel Pro:** boleh memakai jadwal per menit. Contoh `vercel.json`:

```json
{
  "$schema": "https://openapi.vercel.sh/vercel.json",
  "crons": [
    { "path": "/cron/retry-email", "schedule": "*/15 * * * *" },
    { "path": "/cron/reconcile-payments", "schedule": "5,20,35,50 * * * *" },
    { "path": "/cron/expire-orders", "schedule": "10,25,40,55 * * * *" }
  ]
}
```

Vercel otomatis mengirim header `Authorization: Bearer <CRON_SECRET>` pada cron bila variabel `CRON_SECRET` diisi.

Tanpa scheduler, email gagal tetap bisa dikirim ulang manual dari dashboard **Pesanan → Kirim ulang email gagal**. Expire tetap berjalan saat halaman pembayaran atau status pesanan dibuka, tetapi rekonsiliasi pembayaran tidak berjalan tanpa scheduler atau pemicu manual ke `/cron/reconcile-payments`.

### 7. Build container secara lokal (opsional)

Jika Docker Desktop terpasang:

```bash
docker build -t nexus-diji .
docker run --rm -p 8080:80 --env-file .env.production nexus-diji
```

Buka `http://localhost:8080`. Build container sudah diuji lewat Vercel Container Runtime. Build lokal dengan Docker belum diuji karena Docker tidak tersedia di mesin pengembangan ini.


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

Di Vercel, `php spark` tidak bisa dijalankan dari dashboard. Gunakan endpoint `/cron/retry-email` dengan `CRON_SECRET` seperti dijelaskan pada bagian **Deploy ke Vercel + Neon**.

## Pengujian yang sudah dilakukan

- Semua halaman publik merespons `200`.
- Checkout dan pemilihan VA Permata, GoPay, QRIS berhasil.
- Pesanan baru memakai awalan `INV-` dan invoice `INV/`.
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
- Layout 360 piksel diuji pada beranda, katalog, detail, checkout, status, halaman legal, login, dan dashboard. Tidak ada overflow horisontal.
- Target sentuh tombol dan tautan aksi minimal 44 piksel pada semua halaman yang diuji.
- Skip link, fokus keyboard, dan penutupan drawer dengan Escape berfungsi.
- Audit kontras otomatis pada beranda, detail, checkout, login, dan dashboard tidak menemukan teks di bawah rasio WCAG AA.
- Animasi hero, reveal, hover, drawer, dan grafik diuji di Chromium pada 360 dan 1440 piksel. Tidak ada error konsol.
- Animasi login admin (cap, garis ledger, garis pindai, dan form bertahap) diuji pada 360, 1024, dan 1440 piksel. Login tetap berhasil dan tidak ada error konsol.
- `prefers-reduced-motion` mematikan animasi dan delay tanpa menyembunyikan konten.
- Migrasi `ci_sessions` berhasil membuat tabel sesi pada SQLite lokal.
- Endpoint `/cron/retry-email` membalas `503` tanpa `CRON_SECRET`, `403` untuk token salah, dan `200` untuk header `Authorization: Bearer` maupun `X-Cron-Secret`.
- `/cron/reconcile-payments` dan `/cron/expire-orders` memakai autentikasi yang sama; expire menandai pesanan pending yang lewat `expires_at` tanpa mengirim email akses.
- `GET /health` membalas `200` dengan `database` dan `schema` bernilai `ok` pada database lokal.
- Security headers muncul pada halaman HTML, JSON `/health`, dan respons `429`.
- `POST /status` yang dikirim 12 kali dalam satu menit membalas `200` pada 10 request pertama, lalu `429` dengan `Retry-After` dan halaman `errors/rate_limit.php`.
- Ekspor CSV lolos uji unit: BOM UTF-8, header kolom, perhitungan diskon, dan netralisasi formula `=SUM(...)` serta nomor `+62`.
- `php vendor\bin\phpunit --no-coverage` lulus 25 test dengan 72 assertion.
- Konfigurasi produksi diuji lewat probe sementara: `DATABASE_URL` Neon terbaca sebagai PostgreSQL, password ter-decode, `sslmode=require` dipakai, `APP_BASE_URL` dinormalisasi, sesi memilih `PostgreHandler`, dan `WRITABLE_PATH` mengarah ke folder tulis alternatif.
- Deploy produksi Vercel + Neon berhasil. `https://project-jiler.vercel.app` merespons `200` dengan `Content-Type: text/html; charset=UTF-8`.
- Beranda produksi menampilkan tiga produk dari Neon dan nama usaha ZafScript, dengan kredit "Nexus Diji".
- Login admin produksi berhasil dan dashboard dapat dibuka, sehingga sesi PostgreSQL dan tabel `ci_sessions` bekerja.
- Endpoint cron produksi membalas `403` tanpa token dan `200` dengan token yang benar.
- `public/index.php` dieksekusi sebagai PHP; `/.env`, `app/Config/Database.php`, dan `composer.json` tidak dapat diakses publik.
- Build image container dijalankan oleh Vercel Container Runtime. Build lokal dengan Docker belum diuji karena Docker tidak tersedia di mesin pengembangan.
- Setelah Function dipindah ke region `sin1`, TTFB produksi untuk `/`, `/produk`, detail produk, checkout, status, dan login admin berada di kisaran 0,17–0,18 detik. Sebelum perbaikan sekitar 3,0–3,5 detik karena Function berjalan di `iad1` dan database di Singapore.

## Checklist produksi

- Buat project Neon di region Singapore dan salin connection string **direct** (bukan `-pooler`), atau pasang integrasi Neon dari Vercel Marketplace.
- Jalankan `php spark migrate` dan `php spark db:seed ProductSeeder` ke Neon.
- Set **Framework Preset** Vercel ke **Container** dan pastikan `Dockerfile` ada di root.
- Pastikan `Dockerfile` menyalin `Caddyfile` ke `/etc/caddy/Caddyfile`; Caddy memakai `auto_https off` dan port 80.
- Set `CI_ENVIRONMENT = production`, `APP_BASE_URL` ke domain HTTPS, dan `DB_DEBUG = false`.
- Isi `DATABASE_URL`, `DB_DRIVER = Postgre`, `SESSION_DRIVER = database`, dan `COOKIE_SECURE = true`.
- Isi `STORE_NAME`, `STORE_EMAIL`, `STORE_ADDRESS`, dan `INVOICE_PREFIX`.
- Isi server key dan client key Midtrans, set `PAYMENT_DRIVER = midtrans` dan `MIDTRANS_IS_PRODUCTION = true`.
- Daftarkan `https://domain-anda/webhook/midtrans` sebagai Payment Notification URL di Midtrans.
- Set `MAIL_DRIVER = smtp` dan isi kredensial Gmail App Password.
- Set `MAIL_FROM_EMAIL` ke alamat Gmail yang sama dengan `MAIL_SMTP_USER`.
- Ganti `APP_KEY` dengan nilai acak baru, minimal 32 karakter.
- Ganti email admin dan hash kata sandi admin.
- Ganti semua link template, video, dan panduan yang masih memakai `example.com` dari menu **Produk**.
- Isi `CRON_SECRET`, lalu jadwalkan `/cron/retry-email`, `/cron/reconcile-payments`, dan `/cron/expire-orders` setiap 15 menit.
- Pasang monitor uptime ke `https://domain-anda/health` dengan interval 5 menit dan notifikasi email.
- Uji ekspor CSV setelah ada minimal satu pesanan asli.
- Atur region Function Vercel sedekat mungkin dengan Neon. Proyek ini memakai `vercel.json` berisi `"regions": ["sin1"]` agar Function berjalan di Singapore, sama dengan Neon.
- Uji satu pembayaran sandbox di Preview, lalu satu pembayaran produksi nominal kecil sebelum promosi.
- Atur pencadangan database Neon. Folder `writable/` di Vercel hanya sementara.

Integrasi Midtrans live dan SMTP Gmail belum diuji karena belum ada kredensial asli. Mode lokal memakai sandbox dan file.

## Troubleshooting

### Deploy Vercel masih 404 atau halaman statis

Framework preset belum **Container**, atau `Dockerfile` tidak terdeteksi. Jalankan:

```bash
vercel project update --framework container --yes
vercel deploy --prod
```

Pastikan `Dockerfile` dan `Caddyfile` ada di root project, bukan di subfolder.

### Link Vercel mengunduh file atau redirect 308 berulang

Gejalanya: membuka `https://...vercel.app` memicu unduhan `index.php`, atau browser berputar pada `308 Permanent Redirect` ke URL yang sama.

- Framework Preset masih **Other**, sehingga Vercel menyajikan `public/index.php` sebagai file statis. Set ke **Container**:

  ```bash
  vercel project update --framework container --yes
  ```

- `Caddyfile` tidak dipasang ke lokasi yang dibaca FrankenPHP. Pastikan `Dockerfile` memiliki:

  ```dockerfile
  COPY Caddyfile /etc/caddy/Caddyfile
  ```

- Caddy mengaktifkan HTTPS otomatis di port 443, sedangkan Vercel mengirim trafik ke port 80. `Caddyfile` harus memakai `auto_https off` dan `:80`, bukan `:{$PORT:80}`.
- Setelah memperbaiki, deploy ulang tanpa cache:

  ```bash
  vercel deploy --prod --force
  ```

- Periksa `Content-Type` respons. Halaman yang benar adalah `text/html; charset=UTF-8`, bukan `application/x-httpd-php`.

### Halaman terasa berat setiap pindah halaman

Gejalanya: aset statis cepat, tetapi setiap halaman PHP butuh sekitar 3 detik. Penyebabnya region Function Vercel tidak sama dengan region database.

1. Pastikan `vercel.json` berisi region yang sama dengan Neon:

   ```json
   {
     "regions": ["sin1"]
   }
   ```

2. Deploy ulang ke production, lalu pastikan build memakai region tersebut:

   ```bash
   vercel deploy --prod
   vercel inspect <deployment-url> | findstr container
   ```

   Output yang benar: `λ container (...) [sin1]`.

3. Ukur waktu respons per halaman:

   ```bash
   curl -sS -o NUL -w "%{http_code} ttfb=%{time_starttransfer}\n" https://domain-anda/produk
   ```

   Dengan Function dan Neon di Singapore, halaman hangat sekitar 0,2–0,7 detik. Sebelum diperbaiki bisa 3 detik lebih karena setiap request sesi dan produk melakukan round-trip lintas benua.

4. Request pertama setelah idle tetap lambat karena container dan Neon bangun dari tidur. Kirim ping ringan ke beranda setiap 4–5 menit dari layanan uptime eksternal bila ingin selalu hangat. Vercel Hobby hanya mengizinkan cron sekali sehari.

### Vercel error 500 `The WRITEPATH is not set correctly`

Folder tulis tidak ada. Image sudah menjalankan `docker-entrypoint.sh` yang membuat `/tmp/writable`. Pastikan `WRITABLE_PATH=/tmp/writable` dan entrypoint tidak dihapus dari `Dockerfile`.

### Database tidak bisa connect di Vercel

- Pastikan `DATABASE_URL` memakai host **direct** Neon, bukan `-pooler`.
- Pastikan URL menyertakan `?sslmode=require` atau set `DB_SSLMODE = require`.
- Pastikan project Neon tidak dihapus dan masih aktif.
- Cek `DB_DRIVER = Postgre` dan `DB_DEBUG = false`.
- Neon gratis bisa tidur; request pertama bisa lambat.

### Login berhasil tetapi kembali ke halaman login

- Pastikan `SESSION_DRIVER = database` dan tabel `ci_sessions` sudah dibuat lewat migrasi.
- Pastikan `APP_BASE_URL` sama dengan domain yang dibuka.
- Jika memakai domain kustom, set `COOKIE_SECURE = true` dan redeploy.
- Jangan mencampur domain `vercel.app` dan domain kustom dalam satu pengujian login.

### Endpoint cron membalas 503

`CRON_SECRET` belum diisi di environment Vercel. Isi, lalu redeploy.

### Endpoint cron membalas 403

Header tidak cocok. Gunakan `Authorization: Bearer <CRON_SECRET>` atau `X-Cron-Secret: <CRON_SECRET>`.

### Function Vercel ditolak karena ukuran image

Batas image Function adalah 250 MB tidak terkompresi. Image ini memakai FrankenPHP Alpine dan hanya dependensi produksi untuk menekan ukuran. Set `VERCEL_ANALYZE_BUILD_OUTPUT=1` untuk melihat ukuran tiap bagian, lalu hapus file yang tidak perlu dari build context.

### Login gagal atau sesi hilang di lokal

Buka `http://localhost:8080`, bukan `http://127.0.0.1:8080`. Alamat harus sama dengan `app.baseURL`.

### Email tidak ada di inbox

Pada mode file, email tidak dikirim. Buka `writable/emails/`. Untuk pengiriman sungguhan, set `MAIL_DRIVER = smtp` dan isi kredensial Gmail.

### Webhook ditolak 403

Signature tidak cocok. Periksa `APP_KEY` untuk sandbox atau server key Midtrans untuk produksi. Jangan mengubah payload setelah signature dibuat.

### Email gagal terkirim

Buka detail pesanan di dashboard, periksa log email, lalu klik **Kirim ulang email**. Untuk retry massal di lokal, jalankan `php spark email:retry`. Di Vercel, panggil `/cron/retry-email` dengan token cron.

### Ingin mengulang dari database bersih

```bash
php spark migrate:refresh --all
php spark db:seed ProductSeeder
```

Perintah ini menghapus pesanan, log email, dan produk, lalu mengisi ulang tiga produk contoh. Jangan jalankan saat `DATABASE_URL` mengarah ke Neon produksi.

### Perubahan CSS atau JavaScript tidak terlihat

Muat ulang dengan `Ctrl + F5`. Pastikan berkas di `public/assets` tidak di-cache oleh server produksi.

## Batasan

- Satu akun admin dari environment, belum ada peran atau multi-pengguna.
- Gambar produk memakai URL, belum ada unggah berkas.
- Invoice berupa halaman cetak browser, belum PDF server-side.
- Email dikirim sinkron saat webhook diproses. Untuk volume besar, pindahkan ke antrean.
- Refund dan pembatalan belum otomatis.
- Nama usaha masih placeholder sampai `STORE_NAME` diisi.
- Filesystem Vercel bersifat sementara. Email mode file dan log hanya ada di `/tmp` satu instance; produksi wajib memakai SMTP dan Neon.
- Cron bawaan Vercel Hobby hanya sekali sehari. Retry 15 menit perlu scheduler eksternal atau upgrade Pro.
- Container Runtime Vercel masih Beta, dan image Function dibatasi 250 MB tidak terkompresi.
- Neon gratis bisa tidur saat idle, sehingga request pertama terasa lebih lambat.

## Dokumen terkait

- `DESIGN.md`: arah visual, warna, tipografi, layout, animasi, dan aksesibilitas.
- `PRD - Website Jual Produk Digital Production By Nexus Diji).md`: kebutuhan produk dari pemilik proyek.

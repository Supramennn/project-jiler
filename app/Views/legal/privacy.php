<article class="wrap-narrow prose">
    <h1>Kebijakan privasi</h1>
    <p class="prose-date">Terakhir diperbarui: 13 November 2026</p>

    <p>Halaman ini menjelaskan data yang dikumpulkan saat pembeli melakukan checkout dan bagaimana data tersebut digunakan.</p>

    <h2>Data yang dikumpulkan</h2>
    <ul>
        <li>Nama pembeli.</li>
        <li>Alamat email.</li>
        <li>Nomor WhatsApp.</li>
        <li>Detail pesanan: produk, nominal, status pembayaran, status pengiriman email, dan waktu transaksi.</li>
    </ul>

    <h2>Tujuan penggunaan data</h2>
    <ul>
        <li>Membuat dan mencatat pesanan.</li>
        <li>Mengirim invoice dan akses template ke email pembeli.</li>
        <li>Memeriksa status pesanan dan menangani kendala pengiriman akses.</li>
        <li>Mengirim notifikasi internal ke admin jika terjadi kegagalan email.</li>
    </ul>

    <h2>Pembayaran</h2>
    <p>Pembayaran diproses oleh gateway pembayaran Midtrans. Data instrumen pembayaran diproses oleh gateway, bukan disimpan di database website ini.</p>

    <h2>Penyimpanan dan akses</h2>
    <p>Data pesanan disimpan di database website dan hanya dapat diakses oleh admin. Kredensial gateway dan email disimpan sebagai variabel lingkungan di server, bukan di halaman frontend.</p>

    <h2>Email</h2>
    <p>Email invoice dan akses dikirim melalui layanan email yang dikonfigurasi admin. Penyedia email dapat menyimpan catatan pengiriman sesuai kebijakan mereka.</p>

    <h2>Hak pembeli</h2>
    <p>Pembeli dapat menghubungi admin melalui alamat email di invoice untuk menanyakan data pesanan atau meminta koreksi data yang tidak akurat.</p>

    <h2>Perubahan kebijakan</h2>
    <p>Kebijakan ini dapat diperbarui bila alur layanan berubah. Versi terbaru selalu ditampilkan di halaman ini.</p>

    <p>Pertanyaan tentang privasi dapat dikirim ke <a href="mailto:<?= esc(config('Store')->email) ?>"><?= esc(config('Store')->email) ?></a>.</p>
</article>

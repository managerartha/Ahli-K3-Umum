# Panduan Pasang ke Hosting (untuk pemula)

Panduan ini memakai **cPanel**, panel hosting yang paling umum (Rumahweb, DomaiNesia, IDCloudHost, dan lain-lain).
Kalau tampilan panel hosting kamu berbeda, nama menunya biasanya mirip.

Hasil akhirnya:

| Alamat | Isinya |
|---|---|
| `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/` | Landing page Ahli K3 Umum |
| `https://pelatihan.arthasafetyindonesia.com/admin/` | Dashboard |
| `https://pelatihan.arthasafetyindonesia.com/` | Otomatis diarahkan ke landing page Ahli K3 Umum |

**Yang kamu butuhkan:** login cPanel hosting `arthasafetyindonesia.com`, dan file `paket-upload-pelatihan.zip`.

Perkiraan waktu: 20–30 menit.

---

## Langkah 1: Buat subdomain `pelatihan`

1. Login ke cPanel.
2. Di kolom pencarian atas, ketik **Domains**, lalu klik menu **Domains** (di cPanel lama namanya **Subdomains**).
3. Klik tombol **Create A New Domain**.
4. Di kolom **Domain**, ketik: `pelatihan.arthasafetyindonesia.com`
5. Kolom **Document Root** biasanya terisi otomatis, misalnya `pelatihan.arthasafetyindonesia.com` atau `public_html/pelatihan`.
   **Catat nama folder ini**, karena dipakai di langkah 3.
6. Klik **Submit**.

> **Kalau subdomain tidak bisa dibuka setelah beberapa jam:** DNS domain kamu mungkin diatur di tempat lain
> (misalnya Cloudflare atau penyedia domain). Minta orang yang mengatur DNS menambahkan record:
> tipe `A`, nama `pelatihan`, isi = alamat IP hosting kamu (lihat di cPanel → **Server Information → Shared IP Address**).

## Langkah 2: Aktifkan SSL (gembok hijau / https)

1. Di cPanel, cari **SSL/TLS Status**, lalu klik.
2. Centang `pelatihan.arthasafetyindonesia.com`, lalu klik **Run AutoSSL**.
3. Tunggu sampai muncul tanda hijau. Bisa beberapa menit sampai beberapa jam setelah subdomain dibuat.

SSL penting supaya password dashboard tidak bisa diintip orang lain.

## Langkah 3: Upload file

1. Di cPanel, buka **File Manager**.
2. Klik **Settings** (kanan atas), centang **Show Hidden Files (dotfiles)**, lalu klik **Save**.
   Ini supaya file pengaman bernama `.htaccess` terlihat.
3. Di panel kiri, buka folder subdomain yang kamu catat di Langkah 1.
   Jika di dalamnya sudah ada file bawaan (misalnya `index.html` atau `cgi-bin`), biarkan saja `cgi-bin`, tapi **hapus `index.html`**.
4. Klik **Upload** (menu atas), lalu pilih file `paket-upload-pelatihan.zip`. Tunggu sampai 100%, lalu klik **Go Back to ...**.
5. Klik kanan file `paket-upload-pelatihan.zip`, pilih **Extract**, lalu klik **Extract File(s)**.
6. Setelah selesai, isi folder harus terlihat seperti ini:

   ```
   .htaccess
   admin/
   ahli-k3-umum/
   api/
   data/
   includes/
   index.php
   paket-upload-pelatihan.zip
   ```

   Kalau semuanya malah masuk ke dalam folder tambahan (misalnya `paket-upload-pelatihan/`),
   masuk ke folder itu, pilih semua isinya (**Select All**), klik **Move**, lalu pindahkan ke folder subdomain.
7. Hapus file `paket-upload-pelatihan.zip` (sudah tidak dibutuhkan).

## Langkah 4: Atur izin folder `data`

Folder `data` adalah tempat menyimpan data pendaftar, jadi harus bisa ditulisi.

1. Klik kanan folder `data`, pilih **Change Permissions**.
2. Pastikan angkanya **755**, lalu klik **Change Permissions**.

## Langkah 5: Cek versi PHP

1. Di cPanel, cari **Select PHP Version** (atau **MultiPHP Manager**).
2. Pastikan versi PHP untuk subdomain `pelatihan` minimal **7.4** (lebih baik **8.1** atau lebih baru).
3. Jika ada daftar ekstensi, pastikan **pdo_sqlite** dan **sqlite3** tercentang.

## Langkah 6: Buat akun admin (lakukan segera!)

1. Buka `https://pelatihan.arthasafetyindonesia.com/admin/`
2. Muncul halaman **Buat Akun Admin**. Isi username dan password (minimal 8 karakter), lalu klik **Buat Akun & Masuk**.
3. **Simpan username dan password ini** di tempat aman.

> Halaman pembuatan akun hanya muncul **sekali**, yaitu sampai akun pertama dibuat.
> Lakukan langkah ini langsung setelah upload supaya tidak didahului orang lain.

## Langkah 7: Tes semuanya

1. Di dashboard, buka **Pengaturan**, lalu cek tanggal batch. Ubah kalau perlu, lalu klik **Simpan Tanggal**.
2. Buka `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/` dan pastikan tanggalnya sudah sesuai.
3. Isi form pendaftaran dengan data tes, lalu klik **Kirim Pendaftaran**.
4. Kembali ke dashboard, buka **Pendaftar**. Data tes harus muncul. Klik tombol hijau, dan WhatsApp akan terbuka.
5. Hapus data tes dengan ikon tempat sampah.

## Langkah 8: Cek keamanan (penting)

Buka dua alamat ini di browser. **Keduanya harus menampilkan "403 Forbidden" atau "Not Found":**

- `https://pelatihan.arthasafetyindonesia.com/data/.ht-artha.sqlite`
- `https://pelatihan.arthasafetyindonesia.com/includes/bootstrap.php`

Kalau salah satunya malah **mengunduh file** atau **menampilkan isi**, berarti data pendaftar bisa diakses orang lain.
Jangan pakai dulu, lalu minta bantuan Claude atau support hosting untuk memperbaikinya.

## Langkah 9: Ganti link di iklan dan bio

Ganti semua link lama ke landing page dengan:

```
https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/
```

Supaya sumber pendaftar terlihat di dashboard, tambahkan keterangan di belakang link, contoh:

| Dipasang di | Link |
|---|---|
| Iklan Facebook/Instagram | `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/?utm_source=facebook&utm_campaign=nama-iklan` |
| Bio Instagram | `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/?utm_source=instagram` |
| Iklan Google | `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/?utm_source=google` |

---

## Sehari-hari

- **Lihat pendaftar baru:** login dashboard → **Pendaftar**. Pendaftar yang belum dihubungi berlabel hijau **BARU**.
- **Hubungi pendaftar:** klik tombol hijau berisi nomor WA. Statusnya otomatis berubah menjadi **Sudah dihubungi**.
- **Ganti tanggal batch:** **Pengaturan** → isi tanggal → **Simpan Tanggal**. Landing page langsung berubah.
- **Lihat satu program saja:** pilih program di kotak kanan atas dashboard.
- **Backup data:** di **Pendaftar**, klik **Unduh CSV / Excel**. Lakukan rutin, misalnya seminggu sekali.

## Kalau ada masalah

| Masalah | Coba ini |
|---|---|
| Halaman putih / "500 Internal Server Error" | Cek Langkah 5 (versi PHP). Jika masih error, buka cPanel → **Errors** dan kirim isinya ke Claude atau developer |
| Form terkirim tapi data tidak muncul di dashboard | Cek Langkah 4 (izin folder `data`) |
| Lupa password dashboard | Minta bantuan Claude atau developer. Password bisa direset tanpa menghapus data |
| Mau tambah landing page pelatihan baru | Kirim kode/isi landing page-nya ke Claude untuk disiapkan |

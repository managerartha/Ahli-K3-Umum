# Ahli K3 Umum: Landing Page + Dashboard

Target hosting: **https://ak3u.arthasafetyindonesia.com**

Landing page pendaftaran Pelatihan Ahli K3 Umum dengan dashboard admin sendiri.
Semua data (pendaftar, kunjungan, tanggal batch) disimpan di hosting sendiri,
tanpa Google Apps Script atau Spreadsheet.

## Fitur dashboard (`/admin`)

| Halaman | Isi |
|---|---|
| **Ringkasan** | Jumlah pengunjung, pendaftar, dan konversi (hari ini / 7 hari / 30 hari), grafik 14 hari, sumber kunjungan, pendaftar terbaru |
| **Pendaftar** | Semua data form, cari & filter, tombol WhatsApp langsung ke nomor pendaftar, status "Baru / Sudah dihubungi", hapus, unduh CSV (Excel) |
| **Pengaturan** | Ubah tanggal batch di landing page, template pesan WhatsApp, ganti password |

Klik tombol WhatsApp pendaftar otomatis menandainya **Sudah dihubungi**.

## Kebutuhan hosting

- PHP **7.4 atau lebih baru** dengan ekstensi `pdo_sqlite` (aktif secara bawaan di hampir semua hosting cPanel)
- Server Apache / LiteSpeed (bawaan cPanel). Folder `data/` dan `includes/` dikunci lewat `.htaccess`.
  Jika hosting memakai Nginx, minta admin hosting memblokir akses ke folder `data/` dan `includes/`.

Tidak perlu membuat database MySQL. Database SQLite dibuat otomatis di `data/` saat pertama dipakai.

## Cara pasang (cPanel)

1. Buat subdomain `ak3u` di cPanel → **Domains / Subdomains**. Catat folder dokumennya (misal `public_html/ak3u`).
2. Upload **semua isi repo** ke folder tersebut (`index.php`, `admin/`, `api/`, `includes/`, `data/`, beserta file `.htaccess` di dalamnya).
   - Di File Manager, aktifkan **Settings → Show Hidden Files** agar file `.htaccess` ikut terlihat.
   - Jika masih ada `index.html` lama di folder itu, **hapus**. Kalau tidak, server bisa menampilkan versi lama.
3. Pastikan folder `data/` bisa ditulisi oleh PHP (permission `755`; jika gagal coba `775`).
4. **Segera** buka `https://ak3u.arthasafetyindonesia.com/admin/` lalu buat akun admin.
   Halaman pembuatan akun hanya muncul sekali, yaitu sampai akun pertama dibuat.
5. Buka **Pengaturan**, cek tanggal batch, lalu isi form di landing page sekali untuk tes.
6. Aktifkan SSL (cPanel → **SSL/TLS Status → Run AutoSSL**) agar login dashboard aman.

## Cek keamanan setelah upload

Buka alamat berikut di browser. Keduanya **harus** menampilkan "403 Forbidden":

- `https://ak3u.arthasafetyindonesia.com/data/.ht-artha.sqlite`
- `https://ak3u.arthasafetyindonesia.com/includes/bootstrap.php`

## Backup data

Seluruh data ada di satu file: `data/.ht-artha.sqlite`. Unduh file ini lewat File Manager untuk backup,
atau gunakan tombol **Unduh CSV** di halaman Pendaftar.

## Lacak sumber iklan

Tambahkan parameter UTM di link iklan agar sumbernya tercatat di dashboard, contoh:

```
https://ak3u.arthasafetyindonesia.com/?utm_source=facebook&utm_campaign=batch-desember
```

## Struktur

```
index.php              Landing page (tanggal batch diambil dari dashboard)
api/submit.php         Menyimpan data form pendaftaran
api/track.php          Mencatat kunjungan landing page
admin/                 Dashboard (login, ringkasan, pendaftar, pengaturan, ekspor CSV)
includes/              Kode bersama (database, login, tampilan) — tidak bisa diakses publik
data/                  Database SQLite — tidak bisa diakses publik
apps-script/Code.gs    Webhook Google Apps Script lama (tidak dipakai lagi, disimpan sebagai arsip)
```

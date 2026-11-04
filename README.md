# Landing Page Pelatihan + Dashboard: PT Artha Safety Indonesia

Hosting: **https://pelatihan.arthasafetyindonesia.com**

Beberapa landing page pelatihan dalam satu subdomain, dengan satu dashboard admin.
Semua data (pendaftar, kunjungan, tanggal batch) disimpan di hosting sendiri,
tanpa Google Apps Script atau Spreadsheet.

👉 **Cara pasang ke hosting: lihat [PANDUAN-PASANG.md](PANDUAN-PASANG.md)**

## Alamat

| Alamat | Isi |
|---|---|
| `/ahli-k3-umum/` | Landing page Ahli K3 Umum Kemnaker RI |
| `/admin/` | Dashboard (login) |
| `/` | Otomatis diarahkan ke program pertama |

## Fitur dashboard

| Halaman | Isi |
|---|---|
| **Ringkasan** | Pengunjung, pendaftar, konversi (hari ini / 7 / 30 hari), perbandingan per program, grafik 14 hari, sumber kunjungan, pendaftar terbaru |
| **Pendaftar** | Semua data form, cari & filter, tombol WhatsApp langsung, tombol Cold/Warm/Hot & Closing, catatan, impor & unduh CSV |
| **Pengaturan** | Tanggal batch & template pesan WhatsApp per program, ganti password |

Pilihan program di kanan atas dashboard: **Semua program** atau satu program tertentu.

## Menambah landing page pelatihan baru

1. Buat folder baru, misalnya `ak3-kebakaran/`, berisi `index.php` landing page.
   Salin pola dari `ahli-k3-umum/index.php`:
   - baris `$PROGRAM = 'ak3-kebakaran';` (harus sama dengan nama folder)
   - tanggal ditampilkan dengan `<?= htmlspecialchars($batchLong) ?>` / `$batchShort`
   - form mengirim ke `../api/submit.php` dengan `program: LANDING_PROGRAM`
   - pencatat kunjungan ke `../api/track.php`
2. Tambahkan satu baris di `includes/programs.php`:

   ```php
   'ak3-kebakaran' => ['name' => 'AK3 Kebakaran', 'batch_start' => '2026-12-07', 'batch_end' => '2026-12-11'],
   ```
3. Upload folder baru dan `includes/programs.php` ke hosting. Program langsung muncul di dashboard.

## Kebutuhan hosting

- PHP **7.4+** dengan ekstensi `pdo_sqlite`
- Apache / LiteSpeed (Hostinger, cPanel). Folder `data/` dan `includes/` dikunci lewat `.htaccess`.
  Jika hosting memakai Nginx, folder `data/` dan `includes/` harus diblokir manual di konfigurasi server.

## Struktur

```
index.php              Mengarahkan ke landing page program pertama
ahli-k3-umum/          Landing page Ahli K3 Umum
api/submit.php         Menyimpan data form pendaftaran
api/track.php          Mencatat kunjungan landing page
admin/                 Dashboard (login, ringkasan, pendaftar, pengaturan, ekspor CSV)
includes/programs.php  Daftar program pelatihan
includes/              Kode bersama (database, login, tampilan); tidak bisa diakses publik
data/                  Database SQLite; tidak bisa diakses publik
apps-script/Code.gs    Webhook Google Apps Script lama (tidak dipakai, arsip)
```

## Backup

Seluruh data ada di satu file: `data/.ht-artha.sqlite`. Unduh lewat File Manager,
atau pakai tombol **Unduh CSV** di halaman Pendaftar.

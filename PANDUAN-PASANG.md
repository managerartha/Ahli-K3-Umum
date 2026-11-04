# Panduan Pasang ke Hostinger (hPanel) untuk pemula

Panduan ini untuk hosting **Hostinger** dengan panel **hPanel**.
Tampilan hPanel kadang diperbarui. Kalau ada nama menu yang tidak ketemu, pakai **kolom pencarian** di bagian atas hPanel
dan ketik nama menunya (misalnya "Subdomains" atau "File Manager").

Hasil akhirnya:

| Alamat | Isinya |
|---|---|
| `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/` | Landing page Ahli K3 Umum |
| `https://pelatihan.arthasafetyindonesia.com/admin/` | Dashboard |
| `https://pelatihan.arthasafetyindonesia.com/` | Otomatis diarahkan ke landing page Ahli K3 Umum |

**Yang kamu butuhkan:** login Hostinger (akun yang memegang `arthasafetyindonesia.com`), dan file `paket-upload-pelatihan.zip`.

Perkiraan waktu: 20–30 menit.

---

## Langkah 1: Buat subdomain `pelatihan`

1. Login ke **hpanel.hostinger.com**.
2. Klik menu **Websites**, cari `arthasafetyindonesia.com`, lalu klik **Manage** (atau **Dashboard**) di sebelahnya.
3. Di menu kiri, buka **Domains → Subdomains**.
4. Di kolom **Create a New Subdomain**, ketik `pelatihan` (bagian `.arthasafetyindonesia.com` sudah otomatis).
5. Biarkan pilihan **Custom folder for subdomain** tidak dicentang. Hostinger otomatis membuat foldernya,
   biasanya `public_html/pelatihan`.
6. Klik **Create**.
7. Subdomain muncul di daftar di bawahnya. **Catat nama foldernya** (kolom *Folder* / *Directory*), karena dipakai di Langkah 3.

> **Kalau subdomain tidak bisa dibuka setelah beberapa jam:** domain kamu mungkin memakai DNS di luar Hostinger
> (misalnya Cloudflare). Minta orang yang mengatur DNS menambahkan record: tipe `A`, nama `pelatihan`,
> isi = alamat IP website kamu (lihat di hPanel → **Websites → Manage → Dashboard**, bagian *Website IP* / *Plan details*).

## Langkah 2: Pastikan SSL aktif (gembok / https)

Hostinger biasanya memasang SSL gratis otomatis untuk subdomain baru, tapi tetap dicek:

1. Di menu kiri website, buka **Security → SSL**.
2. Cari `pelatihan.arthasafetyindonesia.com` di daftar.
   - Jika statusnya **Active**, lanjut ke langkah berikutnya.
   - Jika belum ada atau belum aktif, klik **Install SSL**, pilih subdomain `pelatihan.arthasafetyindonesia.com`, lalu klik **Install**.
3. Pemasangan bisa memakan waktu beberapa menit sampai beberapa jam.

SSL penting supaya password dashboard tidak bisa diintip orang lain.

## Langkah 3: Upload file

1. Di menu kiri website, buka **Files → File Manager**.
2. Masuk ke folder subdomain yang kamu catat di Langkah 1 (biasanya `public_html` → `pelatihan`), dengan klik dua kali.
3. Jika di dalamnya sudah ada file bawaan Hostinger seperti `default.php` atau `index.html`, **hapus** (klik kanan → **Delete**).
4. Klik ikon **Upload** di kanan atas (ikon panah ke atas), pilih **File**, lalu pilih `paket-upload-pelatihan.zip`. Tunggu sampai selesai.
5. Klik kanan file `paket-upload-pelatihan.zip`, lalu pilih **Extract**.
   - Jika muncul kolom nama folder tujuan, **kosongkan** atau isi dengan titik `.` supaya isinya diekstrak ke folder ini.
   - Klik **Extract**.
6. Setelah selesai, isi folder `pelatihan` harus terlihat seperti ini:

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

   - Kalau file `.htaccess` tidak terlihat, tidak apa-apa. File itu tersembunyi tapi tetap ada.
   - Kalau semuanya malah masuk ke folder tambahan (misalnya `paket-upload-pelatihan/`): masuk ke folder itu,
     centang semua isinya, klik kanan → **Move**, lalu pindahkan ke folder `pelatihan`. Setelah itu hapus folder tambahan yang kosong.
7. Hapus file `paket-upload-pelatihan.zip` (sudah tidak dibutuhkan).

## Langkah 4: Atur izin folder `data`

Folder `data` adalah tempat menyimpan data pendaftar, jadi harus bisa ditulisi. Di Hostinger biasanya sudah benar, tapi tetap dicek:

1. Klik kanan folder `data`, lalu pilih **Permissions**.
2. Pastikan angkanya **755** (Owner: Read, Write, Execute; Group & Public: Read, Execute), lalu klik **Update**.

## Langkah 5: Cek versi PHP

1. Di menu kiri website, buka **Advanced → PHP Configuration**.
2. Di tab **PHP version**, pilih **8.2** (atau versi 8 terbaru yang tersedia), lalu klik **Update**.
3. Buka tab **PHP extensions**, lalu pastikan **pdo_sqlite** dan **sqlite3** tercentang. Klik **Save** jika ada yang diubah.

> Pengaturan PHP di Hostinger berlaku untuk seluruh website `arthasafetyindonesia.com` beserta subdomainnya.
> Jika website utama kamu juga ada di hosting yang sama, PHP 8.2 umumnya aman. Kalau ragu, tanyakan ke developer website utama dulu.

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
| Halaman putih / "500 Internal Server Error" | Cek Langkah 5 (versi PHP dan ekstensi). Jika masih error, kirim screenshot halaman error ke Claude atau developer |
| Form terkirim tapi data tidak muncul di dashboard | Cek Langkah 4 (izin folder `data`) |
| Subdomain menampilkan halaman "default" Hostinger | File `default.php` belum dihapus (Langkah 3 nomor 3) |
| Gembok/https belum muncul | Tunggu beberapa jam setelah subdomain dibuat, lalu cek lagi Langkah 2 |
| Lupa password dashboard | Minta bantuan Claude atau developer. Password bisa direset tanpa menghapus data |
| Mau tambah landing page pelatihan baru | Kirim kode/isi landing page-nya ke Claude untuk disiapkan |

---

# Bagian B: Menghubungkan Meta Ads (CPL, CPA, performa per iklan)

Halaman **Iklan & Closing** di dashboard butuh dua hal:

1. **Access token Meta**, supaya dashboard bisa membaca biaya iklan. Dikerjakan sekali saja.
2. **Parameter URL di setiap iklan**, supaya dashboard tahu setiap lead datang dari iklan mana.

Tanpa keduanya, tombol Cold/Warm/Hot, Closing, dan closing rate tetap bisa dipakai. Hanya CPL, CPA, dan tabel per iklan yang kosong.

> Tampilan Meta sering berubah. Kalau nama menu berbeda, cari menu dengan nama yang mirip, atau kirim screenshot ke Claude.

## B1. Buat aplikasi Meta (sekali saja)

Token Meta harus "menempel" ke sebuah aplikasi. Aplikasi ini tidak dipublikasikan ke mana pun.

1. Buka **developers.facebook.com**, login dengan akun Facebook yang menjadi admin di Business **Artha Safety GM**.
2. Klik **My Apps / Aplikasi Saya** → **Create App / Buat Aplikasi**.
3. Isi nama aplikasi, misalnya `Dashboard Pelatihan`, lalu isi email.
4. Saat ditanya kegunaan (*use case*), pilih **Other / Lainnya**, lalu tipe **Business**.
5. Di pilihan **Business portfolio**, pilih **Artha Safety GM**, lalu klik **Create app**.

## B2. Buat System User dan token

1. Buka **business.facebook.com/settings** (Pengaturan Bisnis), lalu pastikan yang terpilih adalah **Artha Safety GM** (bukan PT. Artha Safety Indonesia).
2. Menu kiri: **Users → System users / Pengguna sistem** → **Add / Tambah**.
   Nama: `Dashboard Pelatihan`, peran: **Employee / Karyawan**, lalu klik **Create**.
3. Klik system user tersebut → **Assign assets / Tetapkan aset** → **Ad accounts / Akun iklan** → centang **Artha GM** →
   izin **View performance / Lihat performa** sudah cukup → **Save / Simpan**.
4. Masih di system user yang sama, klik **Generate new token / Buat token baru**:
   - App: pilih **Dashboard Pelatihan** (dari langkah B1)
   - Token expiration: **Never / Tidak pernah**
   - Permission: centang **ads_read**
   - Klik **Generate token**.
5. **Salin token** yang muncul. Token ini hanya ditampilkan sekali.

⚠️ **Token ini rahasia, seperti password.** Jangan kirim ke chat, email, atau siapa pun. Tempel hanya di dashboard (langkah B3).

## B3. Tempel di dashboard

1. Dashboard → **Pengaturan** → bagian **Koneksi Meta Ads**.
2. **ID Akun Iklan:** `2229317924648301` (akun **Artha GM**).
3. **Access Token:** tempel token dari B2.
4. Klik **Simpan & Tes Koneksi**. Jika berhasil, muncul tulisan hijau: *"Koneksi Meta Ads berhasil. … baris data biaya iklan diambil."*

Setelah ini, biaya iklan diperbarui otomatis setiap 3 jam saat halaman Iklan & Closing dibuka. Bisa juga diperbarui manual dengan tombol **Perbarui sekarang**.

## B4. Atur kata kunci kampanye per program

Jika akun **Artha GM** hanya dipakai untuk iklan landing page Ahli K3 Umum, **langkah ini boleh dilewati** (kosongkan = semua kampanye dihitung).

Jika di akun itu ada kampanye untuk **pelatihan lain**, isi kata kunci supaya biayanya tidak ikut terhitung.
Kampanye **klik ke WhatsApp** untuk Ahli K3 Umum tetap dihitung, karena lead-nya dicatat lewat tombol **Tambah Lead WA** (lihat B6).

1. Dashboard → **Pengaturan** → kartu **Ahli K3 Umum Kemnaker RI** → kolom **Kata kunci nama kampanye Meta**.
2. Isi bagian nama kampanye yang khusus untuk landing page ini, pisahkan dengan koma. Contoh:
   ```
   AK3U Web, Landing AK3U
   ```
   Tips: beri nama kampanye setiap program dengan kata yang sama (misalnya selalu mengandung `AK3U`), supaya mudah dikenali.
   Untuk **mengecualikan**, awali dengan tanda minus, misalnya `-BNSP`. Kata kunci dicocokkan ke **nama kampanye maupun nama iklan**,
   jadi iklan BNSP yang ada di dalam kampanye campuran (misalnya *FG- SMA - BNSP WA*) juga bisa dikecualikan.
3. Klik **Simpan**.

## B5. Pasang parameter URL di iklan

Ini supaya setiap lead tercatat berasal dari iklan (konten) yang mana.

1. Dashboard → **Pengaturan** → **Koneksi Meta Ads** → klik **Salin** di kotak *Parameter URL*.
   Isinya:
   ```
   utm_source=meta&utm_medium=paid_social&utm_campaign={{campaign.name}}&utm_term={{adset.name}}&utm_content={{ad.name}}&ad_id={{ad.id}}
   ```
2. Buka **Ads Manager** → tab **Ads / Iklan**.
3. Centang semua iklan web yang mengarah ke landing page ini → klik **Edit**.
4. Di bagian **Destination / Tujuan**:
   - Pastikan **Website URL** adalah `https://pelatihan.arthasafetyindonesia.com/ahli-k3-umum/`
   - Cari kolom **URL parameters / Parameter URL** (kadang di bawah tombol *Build a URL parameter*), lalu tempel teks dari langkah 1.
5. Klik **Publish / Terbitkan**.

Catatan:
- Mengubah iklan bisa membuat iklan masuk peninjauan ulang sebentar, dan kadang fase pembelajaran (*learning*) Meta dimulai ulang. Sebaiknya lakukan sekali untuk semua iklan sekaligus.
- Iklan baru: tempel parameter yang sama setiap kali membuat iklan.
- Lead sebelum parameter dipasang akan tampil di baris **"Tanpa data iklan"**.

## B6. Mencatat lead dari iklan WhatsApp (klik ke WA)

Iklan klik-ke-WhatsApp tidak lewat landing page, jadi nama dan nomor pengirim chat tidak tercatat otomatis.
Yang tercatat otomatis dari Meta hanya **jumlah chat yang dimulai** (kolom **Chat WA** di tabel per iklan).

Setiap ada chat masuk dari iklan:

1. Dashboard → **Pendaftar** → tombol hijau **Tambah Lead WA**.
2. Pilih **Dari iklan**. Iklan yang punya chat WA ada di urutan paling atas. Cocokkan dengan pratinjau iklan yang biasanya muncul di pesan pertama WhatsApp.
3. Isi **Nama** dan **Nomor WhatsApp**. Field lain boleh diisi nanti.
4. Klik **Simpan Lead**, atau **Simpan & Tambah Lagi** jika ada beberapa chat sekaligus (iklan yang sama otomatis terpilih lagi).

Setelah itu lead WA muncul di daftar Pendaftar dengan label **Lead WA**, dan bisa ditandai Cold/Warm/Hot dan Closing seperti lead dari form.
Nomor yang sudah pernah terdaftar akan diperingatkan supaya tidak dobel.

Lead WA **tidak** dihitung dalam konversi landing page (karena tidak lewat landing page), tetapi **dihitung** dalam Lead, CPL, CPA, dan closing rate di halaman Iklan & Closing.

## Cara membaca halaman Iklan & Closing

| Angka | Artinya |
|---|---|
| **Biaya iklan** | Total belanja iklan Meta pada periode itu (sesuai kata kunci kampanye program) |
| **Lead** | Jumlah pendaftar pada periode itu (dari form, lead WA, dan data impor) |
| **Chat WA** | Jumlah percakapan WhatsApp yang dimulai dari iklan, menurut Meta |
| **Iklan ke landing page / Iklan klik ke WhatsApp** | Dashboard memisahkan keduanya otomatis: iklan yang pernah menghasilkan chat WA dianggap iklan WA. CPL landing page hanya memakai biaya iklan landing page |
| **Sejak awal** | Pilihan periode untuk melihat semua data sejak kampanye pertama. Tombol **Tarik ulang sejak awal** menarik ulang semua data dari Meta |
| **CPL** | Biaya iklan ÷ lead |
| **Closing** | Jumlah pendaftar yang kamu tandai **Closing** |
| **CPA** | Biaya iklan ÷ closing |
| **Closing rate** | Closing ÷ lead |
| **Tabel per iklan** | Biaya, klik, pengunjung landing page, lead, kualitas lead, closing, CPL, dan CPA untuk setiap iklan |

Angka CPA dan closing rate hanya akurat jika setiap pendaftar ditandai **Closing / Tidak** dan **Cold / Warm / Hot** di menu **Pendaftar**.

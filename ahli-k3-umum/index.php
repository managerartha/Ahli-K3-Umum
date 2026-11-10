<?php
/**
 * Landing page Pelatihan Ahli K3 Umum Kemnaker RI — kelas Blended Yogyakarta.
 * Tanggal batch, batas promo, dan kuota diatur dari dashboard (admin → Pengaturan).
 */
$PROGRAM = 'ahli-k3-umum';

// Nilai bawaan jika database belum siap: halaman tetap tampil.
$batchLong = '16 - 28 November 2026';
$batchShort = '16-28 Nov 2026';
$batchStart = '2026-11-16';
$promoEnd = '';
$quotaTotal = 0;
$quotaLeft = null;
try {
    require_once dirname(__DIR__) . '/includes/bootstrap.php';
    $batchLong = batch_long($PROGRAM) ?: $batchLong;
    $batchShort = batch_short($PROGRAM) ?: $batchShort;
    $batchStart = get_setting('batch_start', $PROGRAM) ?: $batchStart;
    $promoEnd = get_setting('promo_end', $PROGRAM);
    $quotaTotal = (int) get_setting('quota_total', $PROGRAM);
    $ql = get_setting('quota_left', $PROGRAM);
    $quotaLeft = $ql === '' ? null : (int) $ql;
} catch (Throwable $e) {
    error_log('Landing page settings: ' . $e->getMessage());
}
date_default_timezone_set('Asia/Jakarta');

$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$rp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
$jt = fn($n) => 'Rp ' . rtrim(rtrim(number_format($n / 1000000, 2, ',', '.'), '0'), ',') . ' jt';

// ---------- Urgensi ----------
$today = date('Y-m-d');
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tgl = fn($d) => (int) date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d));
$promoActive = $promoEnd !== '' && $promoEnd >= $today;
$promoDaysLeft = $promoActive ? (int) round((strtotime($promoEnd) - strtotime($today)) / 86400) : null;
$promoText = !$promoActive ? '' : ($promoDaysLeft === 0 ? 'Harga promo berakhir HARI INI' : ($promoDaysLeft === 1 ? 'Harga promo berakhir BESOK' : 'Harga promo s/d ' . $tgl($promoEnd)));
$hasQuota = $quotaLeft !== null;
$quotaFull = $hasQuota && $quotaLeft <= 0;
$quotaPct = $hasQuota && $quotaTotal > 0 ? max(4, min(100, round(($quotaTotal - $quotaLeft) / $quotaTotal * 100))) : 0;
$batchStartTs = strtotime($batchStart . ' 08:00:00');
$batchStarted = time() >= $batchStartTs;

// ---------- Harga (normal → promo) ----------
$prices = [
    'umum' => [
        'title' => 'Umum', 'sub' => 'Fresh graduate atau belum bekerja',
        'gets' => ['Sertifikat Calon Ahli K3 Umum Kemnaker RI'],
        'rows' => [
            'Non penginapan'   => ['basic' => [4850000, 3850000], 'full' => [5200000, 4200000]],
            'Dengan penginapan' => ['basic' => [5350000, 4350000], 'full' => [5700000, 4700000]],
        ],
    ],
    'utusan' => [
        'title' => 'Utusan Perusahaan', 'sub' => 'Sudah bekerja atau diutus perusahaan',
        'gets' => ['Sertifikat Ahli K3 Umum Kemnaker RI', 'SKP & Lisensi Kemnaker RI (berlaku 3 tahun)'],
        'rows' => [
            'Non penginapan'   => ['basic' => [6550000, 5550000], 'full' => [7000000, 6000000]],
            'Dengan penginapan' => ['basic' => [7000000, 6000000], 'full' => [7450000, 6450000]],
        ],
    ],
];
$packageOptions = [];
foreach ($prices as $key => $p) {
    foreach ($p['rows'] as $stay => $tiers) {
        foreach ($tiers as $tier => [$normal, $promo]) {
            $label = ($key === 'umum' ? 'Umum' : 'Perusahaan') . ' · ' . ($stay === 'Non penginapan' ? 'Non penginapan' : 'Penginapan') . ' · ' . ucfirst($tier) . ' (' . $rp($promo) . ')';
            $packageOptions[$key . '-' . ($stay === 'Non penginapan' ? 'non' : 'inap') . '-' . $tier] = $label;
        }
    }
}
$fromNormal = 4850000;
$fromPromo = 3850000;

// ---------- Konten ----------
$curriculum = [
    [1, 'Kebijakan K3 & Dasar-dasar K3', 'Filosofi dasar Keselamatan dan Kesehatan Kerja, landasan kebijakan K3 nasional, dan arah pembinaan perlindungan tenaga kerja di Indonesia.', ['Kebijakan K3', 'Dasar-dasar K3'], 'Online · Zoom'],
    [2, 'UU No. 1 Tahun 1970 & Kelembagaan K3', 'Ketentuan pokok UU No. 1 Tahun 1970, pembentukan dan tata kelola P2K3, serta kewenangan Ahli K3 Umum.', ['Undang-undang No. 1 Tahun 1970', 'Pengawasan Norma Kelembagaan & Keahlian K3'], 'Online · Zoom'],
    [3, 'Norma Listrik & Penanggulangan Kebakaran', 'Pengawasan instalasi listrik, penangkal petir, serta proteksi kebakaran aktif, pasif, dan tanggap darurat.', ['Norma Keselamatan Kerja Listrik', 'Norma Penanggulangan Kebakaran'], 'Online · Zoom'],
    [4, 'Norma Konstruksi Bangunan & Mekanik', 'Pengendalian risiko pekerjaan konstruksi, bekerja di ketinggian, serta pesawat tenaga produksi, angkat, dan angkut.', ['Norma K3 Konstruksi & Bangunan', 'Norma K3 Mekanik'], 'Online · Zoom'],
    [5, 'Norma Pesawat Uap & Bejana Tekan', 'Instalasi, pengujian, pemeliharaan, dan inspeksi berkala boiler, bejana bertekanan, dan tangki timbun.', ['Norma K3 Pesawat Uap & Bejana Tekan'], 'Online · Zoom'],
    [6, 'Norma Kesehatan Kerja & Bahan Berbahaya', 'Pencegahan Penyakit Akibat Kerja, pelayanan kesehatan kerja, ergonomi, dan pengelolaan Bahan Kimia Berbahaya (B3).', ['Norma Kesehatan Kerja', 'Norma Bahan Berbahaya'], 'Online · Zoom'],
    [7, 'Norma Lingkungan Kerja', 'Nilai Ambang Batas faktor fisika, kimia, biologi, ergonomi, dan psikologi kerja sesuai Permenaker No. 5 Tahun 2018.', ['Norma Lingkungan Kerja'], 'Online · Zoom'],
    [8, 'Sistem Manajemen K3 (SMK3)', 'PP No. 50 Tahun 2012, prinsip dasar SMK3, kriteria audit (64, 122, 166), dan penyusunan kebijakan K3 perusahaan.', ['Norma SMK3', 'Audit SMK3'], 'Online · Zoom'],
    [9, 'Manajemen Risiko & Analisa Kecelakaan', 'Identifikasi bahaya dan penilaian risiko (HIRADC/IBPR), investigasi akar penyebab, dan pelaporan kecelakaan kerja.', ['Manajemen Risiko', 'Analisa Kecelakaan'], 'Online · Zoom'],
    [10, 'Praktik Pemeriksaan K3 (PKL)', 'Observasi langsung objek K3 di perusahaan mitra, didampingi Ahli K3 Umum/HSE Officer, lalu menyusun laporan PKL.', ['Observasi objek K3 di industri', 'Penyusunan laporan PKL'], 'Tatap muka · Jogja'],
    [11, 'Seminar Laporan PKL', 'Presentasi hasil observasi dan rekomendasi perbaikan K3 per kelompok, diuji langsung oleh tim evaluator.', ['Seminar hasil observasi K3', 'Tanya jawab & pengujian teknis'], 'Tatap muka · Jogja'],
    [12, 'Ujian Teori & Pembekalan Lisensi', 'Ujian akhir berbasis sistem resmi, verifikasi berkas SKP & Lisensi, dan penutupan pelatihan.', ['Evaluasi / post test', 'Pembekalan SKP & Lisensi Kemnaker'], 'Tatap muka · Jogja'],
];
$personas = [
    ['cocok-fresh-graduate.jpg', 'Lulusan Baru D3/S1', 'Membangun karier sebagai profesional K3/HSE dengan sertifikasi resmi sejak awal.'],
    ['cocok-utusan-perusahaan.jpg', 'Utusan Perusahaan', 'Memenuhi kewajiban penunjukan Ahli K3 Umum sesuai regulasi Kemnaker RI.'],
    ['cocok-safety-officer.jpg', 'Safety Officer & Praktisi HSE', 'Memperkuat kompetensi dan legalitas profesi melalui sertifikasi Kemnaker RI.'],
    ['cocok-supervisor.jpg', 'Supervisor & Manajer', 'Memimpin penerapan K3 dan budaya kerja aman di unit yang kamu kelola.'],
];
$sectors = [['fa-industry', 'Manufaktur'], ['fa-helmet-safety', 'Konstruksi'], ['fa-oil-well', 'Migas & Energi'], ['fa-mountain', 'Pertambangan'], ['fa-hospital', 'Rumah Sakit'], ['fa-warehouse', 'Logistik & Gudang']];
$reviews = [
    ['IR', 'Iqbal Romzy', 'Ahli K3 Umum · Yogyakarta', 'Mengikuti pelatihan bersama PT Artha Safety Indonesia sangat menambah ilmu baik teori maupun praktiknya. Penyampaian materi menarik dan panitianya super ramah!', 'bg-brand-navy text-white'],
    ['NM', 'Nixon Marbun', 'Utusan Perusahaan', 'Seru, panitia membuat suasana pelatihan feels like home. Pemateri ahli di bidangnya masing-masing. Siap rekomendasikan untuk rekan kantor!', 'bg-brand-blue text-white'],
    ['RB', 'Renanda Bayu', 'Alumni Ahli K3 Umum', 'Tim panitianya asik dan pematerinya berkompeten langsung dari Kemnaker RI. Sangat berkualitas dan terpercaya.', 'bg-brand-accent text-brand-dark'],
    ['HT', 'Herdina Theresia', 'Ahli K3 Umum', 'Tempat pelatihan K3 KEMNAKER terpercaya, 100% batch saya lulus! Dapat relasi baru yang profesional dan suportif.', 'bg-emerald-600 text-white'],
    ['AA', 'Anisa Andriyani', 'Ahli K3 Umum', 'Pematerinya keren dan materinya mudah dimengerti. Saya juga dapat banyak relasi baru. Wajib ambil sertifikasi di sini! ✨', 'bg-pink-600 text-white'],
    ['DC', 'Dwi Cahyono', 'Utusan Perusahaan', 'Lulus dan berkas SKP & Lisensi Kemnaker RI terverifikasi di TemanK3. Pendampingan administrasinya transparan dan sigap.', 'bg-cyan-700 text-white'],
];
$faqs = [
    ['Saya bukan lulusan K3, apakah bisa ikut?', 'Bisa. Peserta terbuka untuk lulusan minimal D3/D4/S1 dari <strong>semua jurusan</strong>. Materi disusun dari dasar dan dipandu sampai siap ujian.'],
    ['Saya baru lulus dan ijazah belum keluar. Bisa daftar?', 'Belum bisa. Sesuai regulasi Kemnaker RI, peserta wajib memiliki ijazah asli atau legalisir cap basah, minimal D3 atau S1 semua jurusan. Kamu bisa konsultasi dulu dan mengamankan jadwal batch berikutnya.'],
    ['Bagaimana kelas online-nya berlangsung?', 'Hari 1–9 lewat Zoom Meeting, dua sesi setiap hari (08.00–12.00 dan 13.00–16.00 WIB). Siapkan laptop/tablet dan internet yang stabil. Hari 10–12 (praktik lapangan, seminar, dan ujian) tatap muka di Emersia Malioboro Hotel Yogyakarta.'],
    ['Saya tinggal di luar Jogja, bagaimana?', 'Mayoritas peserta dari luar kota. Sesi online diikuti dari rumah, dan hanya 3 hari tatap muka di Yogyakarta. Tersedia paket <strong>dengan penginapan</strong> supaya kamu tidak perlu repot mencari hotel.'],
    ['Apakah harus bayar lunas di awal?', 'Tidak. Cukup <strong>bayar DP untuk mengamankan kursi</strong>, pelunasan bisa di hari pertama pelatihan.'],
    ['Apa beda jalur Umum dan Utusan Perusahaan?', 'Jalur Umum mendapat Sertifikat Calon Ahli K3 Umum Kemnaker RI. Jalur Utusan Perusahaan mendapat Sertifikat, <strong>SKP, dan Lisensi</strong> Ahli K3 Umum Kemnaker RI (wajib surat keterangan bekerja dari perusahaan).'],
    ['Apa beda paket Basic dan Full?', 'Keduanya mendapat sertifikat resmi dan semua bonus. Paket <strong>Full</strong> ditambah modul cetak, himpunan perundang-undangan K3, dan training kit (tumbler, kemeja & t-shirt safety, ransel, blocknote, pulpen).'],
    ['Apakah sertifikatnya resmi?', '<strong>100% resmi.</strong> Sertifikat, SKP, dan Lisensi diterbitkan Direktorat Bina Kelembagaan K3, Ditjen Binwasnaker & K3 Kemnaker RI, dengan nomor registrasi yang bisa dicek di TemanK3. PT Artha Safety Indonesia adalah PJK3 resmi penyelenggara pembinaan.'],
    ['Berapa lama sertifikatnya berlaku?', 'Sertifikat Ahli K3 Umum berlaku seumur hidup. SKP dan Lisensi (utusan perusahaan) berlaku 3 tahun dan dapat diperpanjang melalui PJK3 resmi.'],
    ['Bagaimana kalau nilai ujian teori belum lulus?', 'Peserta mendapat satu kali kesempatan remedial. Mentor juga membimbing persiapan ujian sejak sesi online.'],
    ['Bisa ikut jadwal beberapa bulan lagi?', 'Bisa. Batch dibuka setiap bulan. Kamu bisa mengunci kursi untuk batch berikutnya dengan DP.'],
    ['Apakah perusahaan bisa mendaftarkan karyawan?', 'Bisa. Tersedia jalur Utusan Perusahaan lengkap dengan SKP & Lisensi, invoice dan faktur pajak resmi, hingga program in-house training di lokasi perusahaan.'],
    ['Bagaimana kalau saya masih harus izin orang tua?', 'Silakan hubungkan orang tua dengan advisor kami. Kami jelaskan legalitas Kemnaker RI, fasilitas, dan prospek karier HSE secara lengkap.'],
];
$docs = [
    ['https://lh3.googleusercontent.com/d/16pSUSYQY_Mkd4he5iUJ1JgbZ1RVHTJm7', 'Praktik Pemadaman Api APAR', 'Basic Safety Training', 'Praktik Lapangan'],
    ['https://lh3.googleusercontent.com/d/1lM46Tm8njbAlHOlrMPSo0MCqrazKAxpc', 'Observasi Objek Norma K3', 'Praktek Kerja Lapangan', 'Kunjungan Pabrik'],
    ['https://lh3.googleusercontent.com/d/1NJqo1ick-ssTvCQTD1D0qBDwfsK970Cu', 'Simulasi Evakuasi & Fire Drill', 'Tanggap Darurat', 'Instruktur Damkar'],
    ['https://lh3.googleusercontent.com/d/1vLw_oUJ4zRDh7URJi6sHuzfJC_bGKfLZ', 'Pelatihan Penggunaan SCBA', 'Ruang Terbatas & Asap', 'Safety Drill'],
];
$logoNames = [2 => 'Kilang Pertamina Internasional', 5 => 'PT SIMBA', 6 => 'Columbus', 7 => 'Eka Hospital', 10 => 'Nana Yamano Technic', 15 => 'BPJS Kesehatan', 16 => 'BPJS Ketenagakerjaan', 21 => 'KAI', 22 => 'Laboratorium Klinik Budi Sehat', 26 => 'KOBEX', 27 => 'PT Sinergi Tunggal Abadi', 29 => 'Precious Contractor', 30 => 'Adhi Karya', 32 => 'PetroChina', 33 => 'Radiant Utama Interinsco Tbk', 34 => 'Total Bangun Persada', 35 => 'PT Puji Rejeki', 39 => 'Karya Podo Moro', 41 => 'MSI', 42 => 'Institut Teknologi PLN', 43 => 'PT PPIS', 44 => 'IKB', 45 => 'Sinarmas CEPSR', 46 => 'PT Cahaya Mulia Persada Nusa', 47 => 'Pelindo'];
$logoRows = [range(1, 24), range(25, 47)];

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
$base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'pelatihan.arthasafetyindonesia.com');
$pageUrl = $base . '/' . $PROGRAM . '/';
$waNumber = '6281337261261';
// Foto & logo diambil dari website utama Artha (dipakai juga oleh LP Artha lainnya).
$assets = 'https://arthasafetyindonesia.co.id/ads/assets';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <title>Pelatihan Ahli K3 Umum Kemnaker RI Blended Yogyakarta | Artha Safety Indonesia</title>
  <meta name="description" content="Pelatihan & sertifikasi Ahli K3 Umum Kemnaker RI: 9 hari online via Zoom + 3 hari tatap muka di Emersia Malioboro Yogyakarta. Batch <?= $h($batchLong) ?>. Mulai <?= $rp($fromPromo) ?>, cukup DP.">
  <link rel="canonical" href="<?= $h($pageUrl) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Artha Safety Indonesia">
  <meta property="og:title" content="Pelatihan Ahli K3 Umum Kemnaker RI · Batch <?= $h($batchShort) ?>">
  <meta property="og:description" content="9 hari online + 3 hari tatap muka di Yogyakarta. Sertifikat resmi Kemnaker RI, gratis Basic Safety Training & 43 e-sertifikat HSE. Mulai <?= $jt($fromPromo) ?>, cukup DP.">
  <meta property="og:url" content="<?= $h($pageUrl) ?>">
  <meta property="og:image" content="<?= $assets ?>/ahli-k3-umum/hero-ahli-k3.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="theme-color" content="#071C6E">
  <link rel="icon" href="<?= $assets ?>/logo-artha.svg" type="image/svg+xml">

  <!-- Meta Pixel Code -->
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1399743914287047');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id=1399743914287047&ev=PageView&noscript=1"></noscript>
  <!-- End Meta Pixel Code -->

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preload" as="image" href="<?= $assets ?>/ahli-k3-umum/hero-ahli-k3.jpg">
  <!-- Tampilan: CSS hasil build Tailwind (lihat tools/build-css.sh) -->
  <link rel="stylesheet" href="../assets/css/ahli-k3-umum.css?v=<?= @filemtime(dirname(__DIR__) . '/assets/css/ahli-k3-umum.css') ?: 1 ?>">
</head>
<body class="bg-white text-slate-800 antialiased font-sans pb-20 lg:pb-0 selection:bg-brand-accent selection:text-brand-dark">

  <!-- ============ BAR URGENSI ============ -->
  <div class="bg-brand-dark text-white text-xs sm:text-sm">
    <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-center">
      <span class="inline-flex items-center gap-1.5 font-bold"><i class="fa-regular fa-calendar text-brand-accent"></i>Batch <?= $h($batchShort) ?></span>
      <?php if ($hasQuota): ?>
        <span class="inline-flex items-center gap-1.5 font-bold <?= $quotaFull ? 'text-red-300' : '' ?>"><i class="fa-solid fa-user-group text-brand-accent"></i><?= $quotaFull ? 'Kuota batch ini penuh' : 'Sisa ' . $quotaLeft . ' kursi' ?></span>
      <?php endif; ?>
      <?php if ($promoActive): ?>
        <span class="inline-flex items-center gap-1.5 font-bold text-brand-accent"><i class="fa-solid fa-fire"></i><?= $h($promoText) ?></span>
      <?php endif; ?>
      <a href="#daftar" class="hidden sm:inline-flex underline font-bold hover:text-brand-accent">Amankan kursi</a>
    </div>
  </div>

  <!-- ============ HEADER ============ -->
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
      <a href="#" class="flex items-center shrink-0"><img src="<?= $assets ?>/logo-artha.svg" alt="Artha Safety Indonesia" class="h-9 sm:h-10 w-auto" width="160" height="40"></a>
      <nav class="hidden lg:flex items-center gap-7 text-sm font-bold text-slate-600">
        <a href="#benefit" class="hover:text-brand-blue">Benefit</a>
        <a href="#materi" class="hover:text-brand-blue">Materi</a>
        <a href="#biaya" class="hover:text-brand-blue">Biaya</a>
        <a href="#persyaratan" class="hover:text-brand-blue">Persyaratan</a>
        <a href="#ulasan" class="hover:text-brand-blue">Ulasan</a>
        <a href="#faq" class="hover:text-brand-blue">FAQ</a>
      </nav>
      <div class="flex items-center gap-2">
        <button type="button" onclick="openModal('advisorModal')" class="hidden sm:inline-flex items-center gap-2 border border-brand-navy/20 text-brand-navy font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-slate-50">
          <i class="fa-brands fa-whatsapp text-emerald-600"></i>Konsultasi
        </button>
        <a href="#daftar" class="inline-flex items-center gap-2 bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-btn">Daftar<i class="fa-solid fa-arrow-right text-xs"></i></a>
      </div>
    </div>
  </header>

  <!-- ============ HERO ============ -->
  <section class="relative bg-brand-royal text-white overflow-hidden">
    <div class="absolute inset-0 opacity-[0.08]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 22px 22px;"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12 pb-10 lg:pb-14">
      <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-center">
        <div class="lg:col-span-6 text-center lg:text-left">
          <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-brand-accent text-xs font-bold px-3.5 py-1.5 rounded-full"><i class="fa-solid fa-shield-halved"></i>Kelas Blended Yogyakarta · PJK3 Resmi Kemnaker RI</span>
          <h1 class="hero-title text-[2.35rem] leading-[1.05] sm:text-5xl lg:text-[3.6rem] mt-4">Pelatihan <span class="text-brand-accent">Ahli K3 Umum</span> Bersertifikat Kemnaker RI</h1>
          <p class="mt-4 text-base sm:text-lg text-blue-100 max-w-xl mx-auto lg:mx-0"><strong class="text-white">Belajar dari rumah, praktik di lapangan.</strong> 9 hari online via Zoom, lalu 3 hari tatap muka di Emersia Malioboro Yogyakarta. Terbuka untuk D3/S1 semua jurusan.</p>

          <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
            <a href="#daftar" class="inline-flex items-center justify-center gap-2 bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-extrabold text-base px-7 py-4 rounded-2xl shadow-lime">
              <i class="fa-solid fa-lock"></i>Amankan Kursi, Cukup DP
            </a>
            <a href="#biaya" class="inline-flex items-center justify-center gap-2 border-2 border-white/40 hover:bg-white/10 text-white font-bold text-base px-6 py-4 rounded-2xl">Lihat Biaya</a>
          </div>

          <div class="mt-5 flex flex-wrap justify-center lg:justify-start gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-bold">
            <span class="bg-white text-brand-navy px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full"><i class="fa-solid fa-certificate text-brand-blue mr-1"></i>Sertifikat Kemnaker RI</span>
            <span class="bg-white text-brand-navy px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full"><span class="bg-brand-accent text-brand-dark px-1.5 rounded mr-1">GRATIS</span>Basic Safety Training</span>
            <span class="bg-white text-brand-navy px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full"><span class="bg-brand-accent text-brand-dark px-1.5 rounded mr-1">GRATIS</span>43 E-Sertifikat HSE</span>
          </div>

          <div class="mt-7 grid grid-cols-4 gap-2 max-w-xl mx-auto lg:mx-0">
            <?php foreach ([['4.000+', 'Alumni'], ['100+', 'Perusahaan'], ['50+', 'Perguruan Tinggi'], ['5,0 ★', '1.139 ulasan Google']] as [$n, $l]): ?>
              <div class="bg-white/10 border border-white/15 rounded-xl px-2 py-2.5 text-center">
                <p class="text-lg sm:text-xl font-extrabold text-brand-accent leading-none"><?= $n ?></p>
                <p class="text-[10px] sm:text-[11px] text-blue-100 mt-1 leading-tight"><?= $l ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Kartu urgensi -->
        <div class="lg:col-span-6">
          <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-brand-navy">
            <img src="<?= $assets ?>/ahli-k3-umum/hero-ahli-k3.jpg" alt="Peserta pelatihan Ahli K3 Umum Artha Safety Indonesia" class="w-full aspect-[16/10] object-cover" width="900" height="560" fetchpriority="high">
            <button type="button" onclick="openVideoModal()" class="absolute top-3 right-3 inline-flex items-center gap-2 bg-black/60 hover:bg-black/75 backdrop-blur text-white text-xs font-bold px-3 py-2 rounded-full"><i class="fa-solid fa-play"></i>Tonton video</button>
          </div>
          <div class="relative -mt-8 mx-3 sm:mx-6 bg-white text-slate-800 rounded-2xl shadow-xl p-4 sm:p-5">
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jadwal terdekat</p>
                <p class="font-extrabold text-brand-navy"><?= $h($batchLong) ?></p>
              </div>
              <div class="text-right">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Biaya mulai</p>
                <p class="text-slate-400 line-through text-xs"><?= $rp($fromNormal) ?></p>
                <p class="font-extrabold text-brand-blue text-xl leading-none"><?= $rp($fromPromo) ?></p>
              </div>
            </div>
            <?php if (!$batchStarted): ?>
              <div class="mt-4 rounded-xl bg-brand-subtle px-3 py-2.5">
                <p class="text-[11px] font-bold text-brand-navy mb-1.5"><i class="fa-regular fa-clock mr-1"></i>Pelatihan dimulai dalam</p>
                <div class="grid grid-cols-4 gap-2 text-center" data-countdown="<?= $batchStartTs * 1000 ?>">
                  <?php foreach (['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'] as $k => $l): ?>
                    <div class="bg-white rounded-lg py-1.5"><p class="text-xl font-extrabold text-brand-navy leading-none" data-cd="<?= $k ?>">–</p><p class="text-[10px] text-slate-500 mt-0.5"><?= $l ?></p></div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            <?php if ($hasQuota): ?>
              <div class="mt-3">
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span class="<?= $quotaFull ? 'text-red-600' : 'text-brand-navy' ?>"><?= $quotaFull ? 'Kuota batch ini sudah penuh' : 'Sisa ' . $quotaLeft . ($quotaTotal ? ' dari ' . $quotaTotal : '') . ' kursi' ?></span>
                  <?php if ($quotaTotal && !$quotaFull): ?><span class="text-slate-400"><?= $quotaPct ?>% terisi</span><?php endif; ?>
                </div>
                <?php if ($quotaTotal): ?><div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-amber-400 to-red-500 rounded-full" style="width: <?= $quotaPct ?>%"></div></div><?php endif; ?>
              </div>
            <?php endif; ?>
            <?php if ($promoActive): ?>
              <p class="mt-3 text-xs font-bold text-red-600"><i class="fa-solid fa-fire mr-1"></i><?= $h($promoText) ?>. Setelah itu kembali ke harga normal.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ LOGO KLIEN ============ -->
  <section class="bg-white py-8 border-b border-slate-100 overflow-hidden">
    <p class="text-center text-sm font-bold text-slate-500 mb-5">Dipercaya <span class="text-brand-navy">100+ perusahaan &amp; instansi</span> untuk melatih tenaga K3-nya</p>
    <div class="space-y-3 marquee-container">
      <?php foreach ($logoRows as $i => $row): ?>
        <div class="overflow-hidden">
          <div class="<?= $i ? 'animate-marquee-right' : 'animate-marquee-left' ?> gap-3">
            <?php foreach (array_merge($row, $row) as $j => $n): $n2 = str_pad((string) $n, 2, '0', STR_PAD_LEFT); ?>
              <div class="h-14 w-32 shrink-0 rounded-xl border border-slate-100 bg-white flex items-center justify-center px-3">
                <img src="<?= $assets ?>/mitra/mitra-<?= $n2 ?>.png" alt="<?= $j < count($row) ? $h('Logo ' . ($logoNames[$n] ?? 'mitra Artha Safety Indonesia')) : '' ?>" class="max-h-9 max-w-full object-contain" decoding="async" width="120" height="36">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============ COCOK UNTUK ============ -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
    <p class="eyebrow">Untuk siapa</p>
    <h2 class="section-title">Ahli K3 Umum Kemnaker Cocok Untuk</h2>
    <p class="section-lead">Terbuka bagi lulusan D3/S1 semua jurusan, baik yang baru memulai karier maupun profesional yang sudah bekerja.</p>
    <div class="mt-8 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
      <?php foreach ($personas as [$img, $title, $desc]): ?>
        <div class="rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-card border-t-4 border-t-brand-blue">
          <img src="<?= $assets ?>/ahli-k3-umum/<?= $img ?>" alt="" class="w-14 h-14 rounded-full object-cover ring-4 ring-brand-subtle" loading="lazy" width="56" height="56">
          <h3 class="mt-3 font-extrabold text-brand-navy text-sm sm:text-base leading-snug"><?= $title ?></h3>
          <p class="mt-1 text-xs sm:text-sm text-slate-500 leading-relaxed"><?= $desc ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="mt-6 text-xs font-bold text-slate-500">Dibutuhkan di berbagai sektor industri</p>
    <div class="mt-2 flex flex-wrap gap-2">
      <?php foreach ($sectors as [$icon, $label]): ?>
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-navy bg-brand-subtle px-3 py-1.5 rounded-full"><i class="fa-solid <?= $icon ?> text-brand-blue"></i><?= $label ?></span>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============ MENGAPA PENTING ============ -->
  <section class="bg-brand-royal text-white py-14 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="eyebrow text-brand-accent">Mengapa penting</p>
      <h2 class="section-title text-white">Kenapa Harus Ahli K3 Umum Kemnaker?</h2>
      <p class="section-lead text-blue-100">Bukan sekadar gelar tambahan. Ahli K3 Umum adalah peran resmi yang diatur undang-undang dan dibutuhkan setiap tempat kerja.</p>
      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ([
            ['fa-scale-balanced', 'Diwajibkan regulasi', 'Perusahaan dengan 100+ pekerja atau berisiko tinggi wajib membentuk P2K3, dengan Ahli K3 sebagai sekretarisnya. Permintaannya selalu ada.'],
            ['fa-shield-halved', 'Kewenangan resmi', 'Ditunjuk Menteri Ketenagakerjaan untuk mengawasi dan membina penerapan K3 di tempat kerja.'],
            ['fa-briefcase', 'Nilai tambah karier', 'Sertifikat Kemnaker RI diakui nasional dan sering menjadi syarat lowongan K3/HSE.'],
            ['fa-helmet-safety', 'Tempat kerja lebih aman', 'Mampu mengenali bahaya, menilai risiko, dan mencegah kecelakaan serta penyakit akibat kerja.'],
        ] as [$icon, $t, $d]): ?>
          <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
            <span class="w-10 h-10 rounded-xl bg-brand-accent text-brand-dark flex items-center justify-center"><i class="fa-solid <?= $icon ?>"></i></span>
            <h3 class="mt-3 font-extrabold"><?= $t ?></h3>
            <p class="mt-1 text-sm text-blue-100 leading-relaxed"><?= $d ?></p>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="mt-8 text-xs font-bold uppercase tracking-wider text-brand-accent">Dasar hukum</p>
      <div class="mt-2 flex flex-wrap gap-2 text-xs">
        <?php foreach ([['UU No. 1 Tahun 1970', 'Keselamatan Kerja'], ['Permenaker No. PER.02/MEN/1992', 'Penunjukan Ahli K3'], ['Permenaker No. PER.04/MEN/1987', 'P2K3'], ['PP No. 50 Tahun 2012', 'SMK3']] as [$a, $b]): ?>
          <span class="bg-white/10 border border-white/15 rounded-full px-3 py-1.5"><strong><?= $a ?></strong> · <?= $b ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ BENEFIT & BONUS ============ -->
  <section id="benefit" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 scroll-mt-20">
    <p class="eyebrow">Benefit</p>
    <h2 class="section-title">Benefit yang Kamu Dapatkan</h2>
    <p class="section-lead">Lebih dari sekadar sertifikat: bekal kompetensi, praktik lapangan, dan keterampilan keselamatan yang langsung terpakai di tempat kerja.</p>
    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ([
          ['01', 'Sertifikat Kemnaker RI', 'Sertifikat Ahli K3 Umum bagi peserta yang lulus, berlaku seumur hidup.', 'Semua peserta'],
          ['02', 'SKP & Lisensi', 'Penunjukan resmi sebagai Ahli K3 Umum di perusahaan. Berlaku 3 tahun dan dapat diperpanjang.', 'Jalur utusan'],
          ['03', 'Praktik pemeriksaan K3', 'Turun langsung ke perusahaan mitra, didampingi Ahli K3 Umum atau HSE Officer.', 'Hari ke-10'],
          ['04', 'Mentor praktisi', 'Dibimbing mentor Ahli K3 profesional dan narasumber dari Kemnaker RI.', 'Setiap sesi'],
      ] as [$n, $t, $d, $tag]): ?>
        <div class="rounded-2xl border border-slate-200 p-5 shadow-card">
          <div class="flex items-center justify-between"><span class="text-2xl font-extrabold text-brand-blue/30"><?= $n ?></span><span class="text-[10px] font-bold uppercase tracking-wider bg-brand-subtle text-brand-blue px-2 py-0.5 rounded-full"><?= $tag ?></span></div>
          <h3 class="mt-2 font-extrabold text-brand-navy"><?= $t ?></h3>
          <p class="mt-1 text-sm text-slate-500 leading-relaxed"><?= $d ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Bonus dengan nilai rupiah -->
    <div class="mt-10 rounded-3xl bg-gradient-to-br from-brand-navy to-brand-blue text-white p-6 sm:p-9 overflow-hidden relative">
      <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-brand-accent">Bonus peserta blended Yogyakarta</p>
          <h3 class="text-2xl sm:text-3xl font-extrabold mt-1">Bonus Senilai <span class="line-through decoration-red-400 decoration-2 text-blue-200"><?= $rp(1250000) ?></span> <span class="text-brand-accent">GRATIS</span></h3>
        </div>
        <a href="#daftar" class="inline-flex items-center gap-2 bg-brand-accent text-brand-dark font-extrabold text-sm px-5 py-3 rounded-xl">Ambil Bonusnya<i class="fa-solid fa-arrow-right text-xs"></i></a>
      </div>
      <div class="mt-6 grid lg:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-white text-slate-800 p-5">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h4 class="font-extrabold text-brand-navy text-lg">Praktik Basic Safety Training</h4>
              <p class="text-xs text-slate-500">Dipandu langsung petugas pemadam kebakaran &amp; PMI</p>
            </div>
            <div class="text-right shrink-0"><p class="text-xs text-slate-400 line-through"><?= $rp(750000) ?></p><p class="font-extrabold text-emerald-600">GRATIS</p></div>
          </div>
          <div class="mt-4 grid sm:grid-cols-3 gap-2 text-xs">
            <?php foreach ([['fa-fire-extinguisher', 'Basic Fire Fighting', 'Praktik memadamkan api dengan APAR'], ['fa-mask-ventilator', 'SCBA', 'Praktik memakai alat bantu napas di area berasap'], ['fa-kit-medical', 'Basic First Aid', 'RJP dasar, penanganan luka & pembidaian']] as [$i, $t, $d]): ?>
              <div class="rounded-xl bg-brand-subtle p-3"><i class="fa-solid <?= $i ?> text-brand-blue"></i><p class="font-bold text-brand-navy mt-1"><?= $t ?></p><p class="text-slate-500 mt-0.5"><?= $d ?></p></div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="rounded-2xl bg-white text-slate-800 p-5 flex gap-4">
          <img src="<?= $assets ?>/ahli-k3-umum/bonus-pegang-sertifikat.jpg" alt="Peserta memegang sertifikat pelatihan K3" class="hidden sm:block w-32 h-full object-cover rounded-xl" loading="lazy" width="128" height="160">
          <div class="flex-1">
            <div class="flex items-start justify-between gap-3">
              <h4 class="font-extrabold text-brand-navy text-lg">43 E-Sertifikat HSE</h4>
              <div class="text-right shrink-0"><p class="text-xs text-slate-400 line-through"><?= $rp(500000) ?></p><p class="font-extrabold text-emerald-600">GRATIS</p></div>
            </div>
            <ul class="mt-3 space-y-1.5 text-sm text-slate-600">
              <li><i class="fa-solid fa-check text-emerald-500 mr-1.5"></i>43 topik pelatihan HSE dalam satu program</li>
              <li><i class="fa-solid fa-check text-emerald-500 mr-1.5"></i>E-sertifikat untuk setiap topik</li>
              <li><i class="fa-solid fa-check text-emerald-500 mr-1.5"></i>Menambah nilai CV &amp; portofolio K3/HSE</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ LEGALITAS ============ -->
  <section class="bg-slate-50 border-y border-slate-200/70 py-14 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <p class="eyebrow">Legalitas PJK3</p>
        <h2 class="section-title">Terdaftar Resmi &amp; Bisa Kamu Cek di TemanK3</h2>
        <p class="section-lead">Sebelum mendaftar pelatihan K3, pastikan penyelenggaranya PJK3 resmi Kemnaker RI. PT Artha Safety Indonesia bisa kamu cek langsung di portal TemanK3 lewat menu Pencarian PJK3.</p>
        <ul class="mt-6 space-y-3 text-sm">
          <?php foreach ([['SKP Bidang Keahlian K3 Umum', '5/000666/AS.01.02.4103.6.15/V/2026'], ['Resmi sebagai TUK LSP BNSP', 'SKT.02/122/LSP-K3N/X/2023'], ['Terdaftar di Kementerian (AHU)', 'AHU-0072648.AH.01.01.Tahun 2023'], ['NIB Perusahaan', '2609230190968']] as [$a, $b]): ?>
            <li class="flex items-start gap-3"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i><span><strong class="text-brand-navy"><?= $a ?></strong><br><span class="text-slate-500"><?= $b ?></span></span></li>
          <?php endforeach; ?>
        </ul>
        <a href="https://temank3.kemnaker.go.id" target="_blank" rel="noopener" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-brand-blue hover:text-brand-navy"><i class="fa-solid fa-arrow-up-right-from-square"></i>Cek di portal resmi TemanK3 Kemnaker RI</a>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <figure class="rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-card">
          <img src="https://lh3.googleusercontent.com/d/1NZAFSA0FI4mrz5nG6yJg6AdI0v3nraiR" alt="Bukti PT Artha Safety Indonesia terdaftar di TemanK3 Kemnaker RI" class="w-full h-56 sm:h-72 object-cover object-top" loading="lazy">
          <figcaption class="text-[11px] font-bold text-brand-navy px-3 py-2"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Terverifikasi di TemanK3</figcaption>
        </figure>
        <figure class="rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-card">
          <img src="https://lh3.googleusercontent.com/d/1ydQnc8vwT24SwXUK7HS2JBcynGQSHGCw" alt="Contoh sertifikat Ahli K3 Umum Kemnaker RI" class="w-full h-56 sm:h-72 object-cover object-top" loading="lazy">
          <figcaption class="text-[11px] font-bold text-brand-navy px-3 py-2"><i class="fa-solid fa-certificate text-brand-blue mr-1"></i>Sertifikat dengan barcode</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- ============ MATERI ============ -->
  <section id="materi" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 scroll-mt-20">
    <p class="eyebrow">Program 12 hari · blended</p>
    <h2 class="section-title">Cakupan Materi K3</h2>
    <p class="section-lead">Hari 1–9 online via Zoom, dua sesi 08.00–12.00 dan 13.00–16.00 WIB. Hari 10–12 tatap muka di Emersia Malioboro Hotel Yogyakarta.</p>
    <div class="mt-6 grid grid-cols-3 gap-2 text-center text-xs sm:text-sm">
      <div class="rounded-xl bg-brand-subtle p-3"><p class="font-extrabold text-brand-navy">Hari 1–9</p><p class="text-slate-500">Materi online (Zoom)</p></div>
      <div class="rounded-xl bg-brand-subtle p-3"><p class="font-extrabold text-brand-navy">Hari 10</p><p class="text-slate-500">Praktik lapangan</p></div>
      <div class="rounded-xl bg-brand-subtle p-3"><p class="font-extrabold text-brand-navy">Hari 11–12</p><p class="text-slate-500">Seminar &amp; ujian</p></div>
    </div>
    <div class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-200 shadow-card">
      <?php foreach ($curriculum as $i => [$day, $title, $desc, $points, $mode]): ?>
        <div class="px-4 sm:px-6">
          <button type="button" onclick="toggleCurriculum(<?= $day ?>)" class="w-full flex items-center justify-between gap-4 py-4 text-left">
            <span class="flex items-center gap-3 sm:gap-4">
              <span class="w-9 text-sm font-extrabold text-brand-blue"><?= str_pad((string) $day, 2, '0', STR_PAD_LEFT) ?></span>
              <span class="font-bold text-slate-900 text-sm sm:text-base"><?= $title ?></span>
            </span>
            <span class="flex items-center gap-3 shrink-0">
              <span class="hidden sm:inline text-[10px] font-bold uppercase tracking-wider <?= $day >= 10 ? 'bg-brand-accent/30 text-brand-dark' : 'bg-brand-subtle text-brand-blue' ?> px-2 py-0.5 rounded-full"><?= $mode ?></span>
              <i id="curr-icon-<?= $day ?>" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform <?= $i === 0 ? 'rotate-180' : '' ?>"></i>
            </span>
          </button>
          <div id="curr-content-<?= $day ?>" class="<?= $i === 0 ? '' : 'hidden' ?> pb-4 pl-12 sm:pl-14 pr-2 text-sm text-slate-600">
            <p><?= $desc ?></p>
            <ul class="mt-2 grid sm:grid-cols-2 gap-1.5">
              <?php foreach ($points as $pt): ?><li class="text-xs font-semibold text-slate-700"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i><?= $pt ?></li><?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="px-4 sm:px-6 py-4 bg-brand-accent/10 rounded-b-2xl">
        <p class="text-sm font-bold text-brand-navy"><span class="bg-brand-accent text-brand-dark text-[10px] font-extrabold px-2 py-0.5 rounded-full mr-2">BONUS</span>Praktik Basic Safety Training: pemadaman api (APAR), SCBA, dan pertolongan pertama bersama Damkar &amp; PMI.</p>
      </div>
    </div>
  </section>

  <!-- ============ DOKUMENTASI ============ -->
  <section class="bg-slate-50 border-y border-slate-200/70 py-14 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="eyebrow">Dokumentasi</p>
      <h2 class="section-title">Suasana Pelatihan Kami</h2>
      <p class="section-lead">Kelas interaktif, observasi industri, dan praktik langsung Basic Safety Training bersama mentor praktisi.</p>
      <div id="docGalleryContainer" class="mt-8 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 select-none">
        <?php foreach ($docs as $i => [$img, $title, $cat, $badge]): ?>
          <div id="docSlot<?= $i ?>" class="relative w-full aspect-[3/4] rounded-2xl overflow-hidden shadow-card bg-brand-navy">
            <div class="doc-card-layer absolute inset-0">
              <img src="<?= $img ?>" alt="<?= $h($title) ?>" class="w-full h-full object-cover" loading="lazy">
              <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/50 to-transparent"></div>
              <div class="absolute bottom-0 inset-x-0 p-3 sm:p-5">
                <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider"><?= $badge ?></span>
                <h3 class="mt-1.5 text-sm sm:text-lg font-extrabold text-white leading-tight"><?= $title ?></h3>
                <p class="text-[11px] sm:text-xs text-slate-300"><?= $cat ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ ULASAN ============ -->
  <section id="ulasan" class="py-14 sm:py-20 overflow-hidden scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
      <div>
        <p class="eyebrow">Kata mereka</p>
        <h2 class="section-title">Dipercaya Ribuan Alumni</h2>
        <p class="section-lead">Ulasan alumni pelatihan Ahli K3 Umum di profil Google PT Artha Safety Indonesia.</p>
      </div>
      <a href="https://maps.google.com/?cid=4002387960243989151" target="_blank" rel="noopener" class="flex items-center gap-4 rounded-2xl border border-slate-200 shadow-card px-5 py-4 hover:border-brand-blue self-start lg:self-auto">
        <span class="w-12 h-12 rounded-full bg-white border border-slate-200 flex items-center justify-center text-2xl"><i class="fa-brands fa-google text-brand-blue"></i></span>
        <span>
          <span class="flex items-center gap-2"><strong class="text-3xl font-extrabold text-brand-navy">5,0</strong><span class="text-amber-400 text-sm">★★★★★</span></span>
          <span class="block text-xs text-slate-500 font-semibold">1.139 ulasan Google · <span class="text-brand-blue">Lihat semua</span></span>
        </span>
      </a>
    </div>
    <div class="mt-8 marquee-container">
      <div class="animate-marquee-left gap-4 px-4">
        <?php foreach (array_merge($reviews, $reviews) as [$ini, $name, $role, $text, $color]): ?>
          <div class="w-[300px] sm:w-[360px] shrink-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <div class="text-amber-400 text-xs">★★★★★ <i class="fa-brands fa-google text-slate-300 float-right"></i></div>
            <p class="mt-3 text-sm text-slate-600 leading-relaxed">"<?= $text ?>"</p>
            <div class="mt-4 flex items-center gap-3 pt-3 border-t border-slate-100">
              <span class="w-9 h-9 rounded-full <?= $color ?> text-xs font-extrabold flex items-center justify-center"><?= $ini ?></span>
              <span><strong class="block text-sm text-slate-900"><?= $name ?></strong><span class="text-[11px] text-slate-500"><?= $role ?></span></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ BIAYA ============ -->
  <section id="biaya" class="bg-slate-50 border-y border-slate-200/70 py-14 sm:py-20 scroll-mt-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <p class="eyebrow">Biaya</p>
      <h2 class="section-title">Pilih Jalur &amp; Paket</h2>
      <p class="section-lead">Pilih jalur sesuai status kerja, dengan atau tanpa penginapan, lalu paket <strong>Basic</strong> atau <strong>Full</strong>. <strong>Cukup bayar DP untuk mengamankan kursi</strong>, pelunasan di hari pertama pelatihan.</p>

      <?php if ($promoActive || $hasQuota): ?>
        <div class="mt-5 flex flex-wrap gap-2">
          <?php if ($promoActive): ?><span class="inline-flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 text-xs sm:text-sm font-bold px-3.5 py-2 rounded-full"><i class="fa-solid fa-fire"></i><?= $h($promoText) ?></span><?php endif; ?>
          <?php if ($hasQuota): ?><span class="inline-flex items-center gap-2 bg-amber-50 text-amber-800 border border-amber-200 text-xs sm:text-sm font-bold px-3.5 py-2 rounded-full"><i class="fa-solid fa-user-group"></i><?= $quotaFull ? 'Kuota batch ' . $h($batchShort) . ' penuh' : 'Sisa ' . $quotaLeft . ' kursi untuk batch ' . $h($batchShort) ?></span><?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="mt-8 grid lg:grid-cols-2 gap-6">
        <?php foreach ($prices as $key => $p): ?>
          <div class="rounded-3xl bg-white p-5 sm:p-7 shadow-card <?= $key === 'utusan' ? 'border-2 border-brand-blue' : 'border border-slate-200' ?>">
            <div class="flex items-start justify-between gap-2">
              <div>
                <h3 class="text-xl font-extrabold text-brand-navy"><?= $p['title'] ?></h3>
                <p class="text-sm text-slate-500"><?= $p['sub'] ?></p>
              </div>
              <?php if ($key === 'utusan'): ?><span class="bg-brand-blue text-white text-[11px] font-bold px-2.5 py-1 rounded-full">+ SKP &amp; Lisensi</span><?php endif; ?>
            </div>
            <ul class="mt-4 space-y-1.5 text-sm">
              <?php foreach ($p['gets'] as $g): ?><li><i class="fa-solid fa-circle-check text-emerald-500 mr-2"></i><?= $g ?></li><?php endforeach; ?>
            </ul>
            <div class="mt-5 space-y-4">
              <?php foreach ($p['rows'] as $stay => $tiers): ?>
                <div class="rounded-2xl bg-slate-50 p-3">
                  <p class="text-xs font-bold text-slate-500 mb-2"><i class="fa-solid <?= $stay === 'Non penginapan' ? 'fa-house-laptop' : 'fa-hotel' ?> mr-1"></i><?= $stay ?></p>
                  <div class="grid grid-cols-2 gap-2">
                    <?php foreach ($tiers as $tier => [$normal, $promo]): $pkg = $key . '-' . ($stay === 'Non penginapan' ? 'non' : 'inap') . '-' . $tier; ?>
                      <a href="#daftar" onclick="pickPackage('<?= $pkg ?>')" class="block rounded-xl bg-white p-3 hover:shadow-md transition <?= $tier === 'full' ? 'border-2 border-brand-blue' : 'border border-slate-200' ?>">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider <?= $tier === 'full' ? 'text-brand-blue' : 'text-slate-500' ?>"><?= $tier ?></span>
                        <span class="block text-xs text-red-500 line-through"><?= $rp($normal) ?></span>
                        <span class="block text-base sm:text-lg font-extrabold text-brand-navy leading-tight"><?= $rp($promo) ?></span>
                        <span class="block text-[10px] font-bold text-emerald-600 mt-0.5">Hemat <?= $jt($normal - $promo) ?></span>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Perbandingan Basic vs Full -->
      <div class="mt-6 rounded-3xl bg-white border border-slate-200 shadow-card p-5 sm:p-7">
        <h3 class="font-extrabold text-brand-navy">Perbedaan Paket Basic &amp; Full</h3>
        <div class="mt-3 overflow-x-auto">
          <table class="w-full text-sm">
            <thead><tr class="text-[11px] uppercase tracking-wider text-slate-500"><th class="text-left py-2 font-bold">Fasilitas</th><th class="py-2 w-16 font-bold">Basic</th><th class="py-2 w-16 font-bold text-brand-blue">Full</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ([['Sertifikat Kemnaker RI', '', 1, 1], ['SKP & Lisensi Kemnaker RI', 'khusus utusan perusahaan', 1, 1], ['Bonus Basic Safety Training & 43 e-sertifikat', '', 1, 1], ['Free career support, konsultasi & webinar 1 tahun', '', 1, 1], ['Modul pelatihan cetak (hardcopy)', '', 0, 1], ['Himpunan perundang-undangan K3', '', 0, 1], ['Training kit', 'tumbler, kemeja & t-shirt safety, ransel, blocknote, pulpen', 0, 1]] as [$f, $note, $b, $fu]): ?>
                <tr>
                  <td class="py-2.5 pr-3"><span class="font-semibold text-slate-700"><?= $f ?></span><?php if ($note): ?><br><span class="text-[11px] text-slate-400"><?= $note ?></span><?php endif; ?></td>
                  <td class="text-center"><?= $b ? '<i class="fa-solid fa-circle-check text-emerald-500"></i>' : '<i class="fa-solid fa-minus text-slate-300"></i>' ?></td>
                  <td class="text-center"><?= $fu ? '<i class="fa-solid fa-circle-check text-emerald-500"></i>' : '<i class="fa-solid fa-minus text-slate-300"></i>' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-4 grid sm:grid-cols-2 gap-3 text-xs">
          <div class="rounded-xl bg-brand-subtle p-3"><p class="font-bold text-brand-navy mb-1"><i class="fa-solid fa-utensils mr-1"></i>Semua paket termasuk konsumsi</p><p class="text-slate-600">Coffee break 2× &amp; makan siang selama sesi tatap muka (hari 10–12)</p></div>
          <div class="rounded-xl bg-brand-subtle p-3"><p class="font-bold text-brand-navy mb-1"><i class="fa-solid fa-award mr-1"></i>Atribut K3</p><p class="text-slate-600">Pin K3 &amp; lencana K3 untuk semua peserta</p></div>
        </div>
        <p class="mt-4 text-[11px] text-slate-400">Biaya dapat berubah. Admin mengonfirmasi biaya final sebelum invoice diterbitkan. Training kit dan modul dikirim ke alamat peserta setelah pelunasan.</p>
      </div>
    </div>
  </section>

  <!-- ============ PERSYARATAN ============ -->
  <section id="persyaratan" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 scroll-mt-20">
    <p class="eyebrow">Persyaratan &amp; pendaftaran</p>
    <h2 class="section-title">Siapkan Dokumen, Lalu Daftar</h2>
    <p class="section-lead">Cukup 6 persyaratan dan 4 langkah mudah. Semua dokumen dikirim lewat WhatsApp.</p>
    <div class="mt-8 grid lg:grid-cols-2 gap-6">
      <div class="rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-card">
        <h3 class="font-extrabold text-brand-navy"><span class="text-brand-blue mr-2">A</span>Dokumen persyaratan</h3>
        <ul class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
          <?php foreach ([['fa-graduation-cap', 'Ijazah D3/S1', 'Semua jurusan, asli atau legalisir cap basah'], ['fa-id-card', 'Scan KTP', 'Jelas dan terbaca'], ['fa-image-portrait', 'Pas foto', 'Latar belakang merah'], ['fa-file-signature', 'Surat pernyataan', 'Pernyataan personel & pakta integritas'], ['fa-building', 'Surat keterangan bekerja', 'Khusus utusan perusahaan'], ['fa-laptop', 'Laptop/tablet', 'Bisa mengoperasikan Zoom']] as [$i, $t, $d]): ?>
            <li class="flex gap-3"><span class="w-9 h-9 shrink-0 rounded-xl bg-brand-subtle text-brand-blue flex items-center justify-center"><i class="fa-solid <?= $i ?>"></i></span><span><strong class="block text-brand-navy"><?= $t ?></strong><span class="text-xs text-slate-500"><?= $d ?></span></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-card">
        <h3 class="font-extrabold text-brand-navy"><span class="text-brand-blue mr-2">B</span>4 langkah mendaftar</h3>
        <ol class="mt-4 space-y-4 text-sm">
          <?php foreach ([['Isi form & chat admin', 'Sebutkan jadwal dan paket pilihan kamu.'], ['Kirim dokumen', 'Admin memeriksa kelengkapan persyaratan.'], ['Bayar DP', 'Kursi kamu langsung aman. Pelunasan di hari pertama pelatihan.'], ['Siap pelatihan', 'Masuk grup WhatsApp peserta untuk info teknis.']] as $n => [$t, $d]): ?>
            <li class="flex gap-3"><span class="w-9 h-9 shrink-0 rounded-full bg-brand-navy text-white font-extrabold flex items-center justify-center"><?= $n + 1 ?></span><span><strong class="block text-brand-navy"><?= $t ?></strong><span class="text-slate-500"><?= $d ?></span></span></li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <!-- ============ DAFTAR ============ -->
  <section id="daftar" class="bg-brand-royal text-white py-14 sm:py-20 scroll-mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-start">
      <div>
        <p class="eyebrow text-brand-accent">Daftar sekarang</p>
        <h2 class="section-title text-white">Siap Menjadi Ahli K3 Umum Bersertifikat Kemnaker RI?</h2>
        <p class="section-lead text-blue-100">Bergabung bersama 4.000+ alumni. Amankan kursi kamu di batch <strong class="text-white"><?= $h($batchLong) ?></strong> cukup dengan DP, pelunasan di hari pertama pelatihan.</p>
        <ul class="mt-6 space-y-2.5 text-sm">
          <?php foreach (['Sertifikat resmi Kemnaker RI, berlaku seumur hidup', '9 hari online via Zoom + 3 hari tatap muka di Emersia Malioboro Yogyakarta', 'Dibimbing mentor praktisi & narasumber Kemnaker RI', 'Bonus Basic Safety Training & 43 e-sertifikat HSE (senilai ' . $rp(1250000) . ')'] as $b): ?>
            <li class="flex gap-2.5"><i class="fa-solid fa-circle-check text-brand-accent mt-0.5"></i><span><?= $b ?></span></li>
          <?php endforeach; ?>
        </ul>
        <div class="mt-6 rounded-2xl bg-white/10 border border-white/15 p-4 text-xs space-y-1.5">
          <p class="font-bold text-brand-accent uppercase tracking-wider text-[11px]">Akreditasi &amp; legalitas</p>
          <p>SKP Bidang Keahlian K3 Umum No. 5/000666/AS.01.02.4103.6.15/V/2026</p>
          <p>TUK LSP BNSP SKT.02/122/LSP-K3N/X/2023 · AHU-0072648.AH.01.01.Tahun 2023</p>
        </div>
      </div>

      <div class="rounded-3xl bg-white text-slate-800 p-5 sm:p-8 shadow-2xl">
        <h3 class="text-xl font-extrabold text-brand-navy">Dapatkan Jadwal &amp; Penawaran Terbaik</h3>
        <?php if ($promoActive || $hasQuota): ?>
          <p class="mt-1 text-xs font-bold <?= $quotaFull ? 'text-red-600' : 'text-red-600' ?>"><i class="fa-solid fa-fire mr-1"></i><?= $h(trim(($promoActive ? $promoText : '') . ($promoActive && $hasQuota ? ' · ' : '') . ($hasQuota ? ($quotaFull ? 'Kuota batch ini penuh, daftar untuk batch berikutnya' : 'sisa ' . $quotaLeft . ' kursi') : ''))) ?></p>
        <?php else: ?>
          <p class="mt-1 text-sm text-slate-500">Isi data singkat, lalu lanjut chat dengan admin di WhatsApp.</p>
        <?php endif; ?>

        <form id="regMainForm" onsubmit="handleRegistrationSubmit(event); return false;" class="mt-5 space-y-4">
          <div>
            <label for="regFullName" class="block text-xs font-bold text-slate-700 mb-1">Nama lengkap <span class="text-red-500">*</span></label>
            <input id="regFullName" required autocomplete="name" placeholder="Nama sesuai ijazah" class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy">
          </div>
          <div>
            <label for="regWhatsApp" class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
            <input id="regWhatsApp" type="tel" required inputmode="tel" autocomplete="tel" placeholder="Contoh: 081234567890" class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy">
          </div>
          <div>
            <label for="regSelectedScheme" class="block text-xs font-bold text-slate-700 mb-1">Paket yang diminati <span class="text-slate-400 font-semibold">(opsional)</span></label>
            <select id="regSelectedScheme" class="w-full px-4 py-3.5 rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm">
              <option value="Belum tahu, mohon dibantu">Belum tahu, mohon dibantu</option>
              <?php foreach ($packageOptions as $k => $label): ?><option value="<?= $h($label) ?>" data-pkg="<?= $k ?>"><?= $h($label) ?></option><?php endforeach; ?>
            </select>
          </div>
          <button type="submit" id="regSubmitBtn" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-base py-4 rounded-xl shadow-lg">
            <i class="fa-brands fa-whatsapp text-xl"></i><span id="regSubmitBtnText">Lanjut ke WhatsApp</span>
          </button>
          <p class="text-[11px] text-slate-400 text-center">Data hanya dipakai untuk informasi pelatihan · <a href="../kebijakan-privasi/" class="underline">Kebijakan Privasi</a></p>
        </form>

        <div id="regSuccessView" class="hidden mt-5 text-center">
          <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-3xl"><i class="fa-solid fa-circle-check"></i></div>
          <h4 class="mt-3 text-xl font-extrabold text-brand-navy">Data kamu sudah kami terima!</h4>
          <p class="mt-1 text-sm text-slate-500">Mengarahkan ke WhatsApp admin untuk konfirmasi jadwal &amp; kuota…</p>
          <a id="directWaBtn" href="https://wa.me/<?= $waNumber ?>" target="_blank" rel="noopener" class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 rounded-xl"><i class="fa-brands fa-whatsapp text-xl"></i>Buka WhatsApp Admin</a>
          <button type="button" onclick="resetRegistrationForm()" class="mt-3 text-xs text-slate-500 underline">Daftarkan peserta lain</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FAQ ============ -->
  <section id="faq" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 scroll-mt-20">
    <div class="flex flex-wrap items-end justify-between gap-3">
      <div>
        <p class="eyebrow">FAQ</p>
        <h2 class="section-title">Pertanyaan Umum</h2>
      </div>
      <button type="button" onclick="toggleAllFaqs()" class="text-sm font-bold text-brand-blue border border-blue-200 rounded-full px-4 py-2 hover:bg-brand-subtle"><span id="toggleAllFaqText">Buka semua jawaban</span></button>
    </div>
    <div class="mt-6 space-y-3">
      <?php foreach ($faqs as $i => [$q, $a]): $n = $i + 1; ?>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
          <button type="button" onclick="toggleFaq(<?= $n ?>)" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left">
            <span class="font-bold text-brand-navy text-sm sm:text-base"><?= $q ?></span>
            <i id="faq-icon-<?= $n ?>" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform shrink-0"></i>
          </button>
          <div id="faq-ans-<?= $n ?>" class="hidden px-5 pb-4 text-sm text-slate-600 leading-relaxed"><?= $a ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-8 rounded-2xl bg-brand-subtle p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="font-bold text-brand-navy text-center sm:text-left">Masih ada pertanyaan? Konsultasi gratis dengan advisor K3 kami.</p>
      <button type="button" onclick="openModal('advisorModal')" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl"><i class="fa-brands fa-whatsapp"></i>Chat Advisor</button>
    </div>
  </section>

  <!-- ============ FOOTER ============ -->
  <footer class="bg-[#FAF6EE] border-t border-[#EDE4D3] pt-12 pb-8 text-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-8 text-sm">
      <div>
        <img src="<?= $assets ?>/logo-artha.svg" alt="Artha Safety Indonesia" class="h-10 w-auto" loading="lazy" width="160" height="40">
        <p class="mt-3 text-xs text-slate-600 leading-relaxed max-w-xs">PJK3 resmi Kementerian Ketenagakerjaan RI, berkomitmen mencetak profesional K3 berdaya saing global.</p>
      </div>
      <div class="space-y-2 text-xs">
        <p class="font-extrabold text-brand-navy uppercase tracking-wider">Kontak</p>
        <p><i class="fa-solid fa-location-dot text-brand-blue mr-2"></i>Ruko De Mercy, Jl. Kapten Haryadi No. 8, Sardonoharjo, Ngaglik, Sleman, DIY 55581</p>
        <p><i class="fa-solid fa-envelope text-brand-blue mr-2"></i>marketing.arthasafetyindonesia@gmail.com</p>
        <p><i class="fa-solid fa-phone text-brand-blue mr-2"></i>(0274) 5063025</p>
      </div>
      <div class="text-xs">
        <p class="font-extrabold text-brand-navy uppercase tracking-wider mb-2">Ikuti kami</p>
        <div class="flex gap-2">
          <?php foreach ([['instagram', 'https://www.instagram.com/arthasafety.id/'], ['tiktok', 'https://www.tiktok.com/@arthasafety'], ['youtube', 'https://www.youtube.com/@arthasafetyindonesia'], ['linkedin-in', 'https://www.linkedin.com/company/pt-artha-safety-indonesia/'], ['facebook-f', 'https://www.facebook.com/people/Artha-Safety-Indonesia/61552793712859/']] as [$i, $u]): ?>
            <a href="<?= $u ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-brand-navy hover:bg-brand-blue text-white flex items-center justify-center" aria-label="<?= $i ?>"><i class="fa-brands fa-<?= $i ?>"></i></a>
          <?php endforeach; ?>
        </div>
        <p class="mt-3 text-slate-500">36 rb pengikut @arthasafety.id · 12,5 rb pengikut @arthasafety</p>
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-[#E5DAC6] flex flex-col sm:flex-row justify-between gap-2 text-[11px] text-slate-500">
      <span>&copy; <?= date('Y') ?> PT Artha Safety Indonesia</span>
      <a href="../kebijakan-privasi/" class="hover:text-brand-blue">Kebijakan Privasi</a>
    </div>
  </footer>

  <!-- ============ BAR BAWAH (HP) ============ -->
  <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-slate-200 shadow-[0_-4px_20px_rgba(7,28,110,0.12)] px-4 py-2.5 flex items-center justify-between gap-3">
    <div class="leading-tight">
      <p class="text-[11px] text-slate-500">Mulai <span class="line-through text-red-500"><?= $jt($fromNormal) ?></span><?= $hasQuota && !$quotaFull ? ' · <span class="font-bold text-red-600">sisa ' . $quotaLeft . ' kursi</span>' : '' ?></p>
      <p class="text-lg font-extrabold text-brand-navy"><?= $jt($fromPromo) ?></p>
    </div>
    <a href="#daftar" class="inline-flex items-center gap-2 bg-brand-accent text-brand-dark font-extrabold text-sm px-5 py-3 rounded-xl shadow-lime"><i class="fa-solid fa-lock text-xs"></i>Amankan Kursi</a>
  </div>

  <!-- Tombol chat (desktop) -->
  <button type="button" onclick="openModal('advisorModal')" class="hidden lg:inline-flex fixed bottom-6 right-6 z-40 items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3.5 rounded-full shadow-xl"><i class="fa-brands fa-whatsapp text-xl"></i>Chat Admin</button>

  <!-- ============ MODAL VIDEO ============ -->
  <div id="videoModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4" onclick="closeVideoModal()" role="dialog" aria-modal="true" aria-label="Video pelatihan">
    <div class="relative w-full max-w-4xl bg-white rounded-2xl p-3" onclick="event.stopPropagation()">
      <button type="button" onclick="closeVideoModal()" class="absolute -top-3 -right-3 w-9 h-9 rounded-full bg-white shadow-lg text-brand-navy font-bold" aria-label="Tutup">✕</button>
      <div class="aspect-video rounded-xl overflow-hidden bg-black"><iframe id="videoIframe" class="w-full h-full" src="" title="Video Pelatihan Ahli K3 Umum" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe></div>
    </div>
  </div>

  <!-- ============ MODAL KONSULTASI ============ -->
  <div id="advisorModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-brand-navy/80 p-4" onclick="if(event.target===this) closeModal('advisorModal')" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl">
      <button type="button" onclick="closeModal('advisorModal')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 text-lg" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
      <h3 class="text-lg font-extrabold text-brand-navy"><i class="fa-solid fa-headset text-brand-blue mr-2"></i>Konsultasi Gratis</h3>
      <p class="text-sm text-slate-500 mt-1 mb-4">Tanya jadwal, persyaratan, atau biaya. Advisor kami balas lewat WhatsApp.</p>
      <form onsubmit="handleAdvisorSubmit(event)" class="space-y-3 text-sm">
        <input id="advName" required placeholder="Nama kamu" class="w-full px-4 py-3 rounded-xl border border-slate-300">
        <select id="advStatus" class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white">
          <option>Umum (fresh graduate / belum bekerja)</option>
          <option>Utusan perusahaan (SKP & Lisensi)</option>
          <option>Belum tahu, mohon dibantu</option>
        </select>
        <textarea id="advNotes" rows="2" placeholder="Pertanyaan kamu (opsional)" class="w-full px-4 py-3 rounded-xl border border-slate-300"></textarea>
        <button class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl"><i class="fa-brands fa-whatsapp"></i>Chat via WhatsApp</button>
      </form>
    </div>
  </div>

  <script>
    var LANDING_PROGRAM = <?= json_encode($PROGRAM) ?>;
    var SUBMIT_URL = '../api/submit.php';
    var WA_NUMBER = <?= json_encode($waNumber) ?>;
    var BATCH_LABEL = <?= json_encode($batchLong) ?>;

    // ---------- Sumber kunjungan (parameter utm_* & ad_id di link iklan) ----------
    var trafficSource = (function () {
      var keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'ad_id'];
      var src = {}; keys.forEach(function (k) { src[k] = ''; });
      try {
        var params = new URLSearchParams(location.search);
        var saved = JSON.parse(sessionStorage.getItem('artha_utm') || '{}');
        var fromUrl = keys.some(function (k) { return params.get(k); });
        keys.forEach(function (k) { src[k] = (fromUrl ? params.get(k) : saved[k]) || ''; });
        sessionStorage.setItem('artha_utm', JSON.stringify(src));
      } catch (e) {}
      return src;
    })();

    // ---------- Catat kunjungan untuk dashboard ----------
    (function () {
      try {
        var vid = localStorage.getItem('artha_vid');
        if (!vid) { vid = Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 12); localStorage.setItem('artha_vid', vid); }
        fetch('../api/track.php', { method: 'POST', headers: { 'Content-Type': 'text/plain;charset=utf-8' }, keepalive: true,
          body: JSON.stringify({ program: LANDING_PROGRAM, vid: vid, ref: document.referrer, utm_source: trafficSource.utm_source, utm_campaign: trafficSource.utm_campaign, utm_content: trafficSource.utm_content, ad_id: trafficSource.ad_id }) }).catch(function () {});
      } catch (e) {}
    })();

    // ---------- Hitung mundur ke hari pertama pelatihan ----------
    (function () {
      var box = document.querySelector('[data-countdown]');
      if (!box) return;
      var target = +box.getAttribute('data-countdown');
      function tick() {
        var s = Math.max(0, Math.floor((target - Date.now()) / 1000));
        var v = { d: Math.floor(s / 86400), h: Math.floor(s % 86400 / 3600), m: Math.floor(s % 3600 / 60), s: s % 60 };
        Object.keys(v).forEach(function (k) { var el = box.querySelector('[data-cd="' + k + '"]'); if (el) el.textContent = String(v[k]).padStart(2, '0'); });
      }
      tick(); setInterval(tick, 1000);
    })();

    // ---------- Accordion & modal ----------
    function toggleCurriculum(n) {
      var c = document.getElementById('curr-content-' + n), i = document.getElementById('curr-icon-' + n);
      if (!c) return; c.classList.toggle('hidden'); if (i) i.classList.toggle('rotate-180');
    }
    function toggleFaq(n) {
      var a = document.getElementById('faq-ans-' + n), i = document.getElementById('faq-icon-' + n);
      if (!a) return; a.classList.toggle('hidden'); if (i) i.classList.toggle('rotate-180');
    }
    var allFaqOpen = false;
    function toggleAllFaqs() {
      allFaqOpen = !allFaqOpen;
      document.querySelectorAll('[id^="faq-ans-"]').forEach(function (a) { a.classList.toggle('hidden', !allFaqOpen); });
      document.querySelectorAll('[id^="faq-icon-"]').forEach(function (i) { i.classList.toggle('rotate-180', allFaqOpen); });
      document.getElementById('toggleAllFaqText').textContent = allFaqOpen ? 'Tutup semua jawaban' : 'Buka semua jawaban';
    }
    function openModal(id) { var m = document.getElementById(id); if (m) { m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow = 'hidden'; } }
    function closeModal(id) { var m = document.getElementById(id); if (m) { m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow = ''; } }
    function openVideoModal() { document.getElementById('videoIframe').src = 'https://www.youtube.com/embed/T6Y7vA2ks1g?autoplay=1&rel=0'; openModal('videoModal'); }
    function closeVideoModal() { document.getElementById('videoIframe').src = ''; closeModal('videoModal'); }
    window.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeVideoModal(); closeModal('advisorModal'); } });

    // ---------- Pilih paket dari kartu harga ----------
    function pickPackage(key) {
      var sel = document.getElementById('regSelectedScheme');
      for (var i = 0; i < sel.options.length; i++) if (sel.options[i].getAttribute('data-pkg') === key) sel.selectedIndex = i;
    }

    // ---------- Form pendaftaran ----------
    function handleRegistrationSubmit(event) {
      if (event) event.preventDefault();
      var name = document.getElementById('regFullName').value.trim();
      var phone = document.getElementById('regWhatsApp').value.trim();
      var scheme = document.getElementById('regSelectedScheme').value;
      if (!name || !phone) return false;

      var message = 'Halo Artha Safety Indonesia, saya ingin daftar Pelatihan Ahli K3 Umum Kemnaker RI (Blended Yogyakarta).\n\n' +
        '*Nama:* ' + name + '\n*No. WhatsApp:* ' + phone + '\n*Paket:* ' + scheme + '\n*Batch:* ' + BATCH_LABEL + '\n\n' +
        'Mohon info kuota dan langkah selanjutnya. Terima kasih!';
      var waUrl = 'https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(message);

      try { if (typeof fbq === 'function') fbq('track', 'Lead'); } catch (e) {}

      var payload = { program: LANDING_PROGRAM, name: name, phone: phone, email: '', scheme: scheme,
        utm_source: trafficSource.utm_source, utm_medium: trafficSource.utm_medium, utm_campaign: trafficSource.utm_campaign,
        utm_term: trafficSource.utm_term, utm_content: trafficSource.utm_content, ad_id: trafficSource.ad_id };
      try {
        fetch(SUBMIT_URL, { method: 'POST', keepalive: true, headers: { 'Content-Type': 'text/plain;charset=utf-8' }, body: JSON.stringify(payload) }).catch(function () {});
      } catch (e) {}

      document.getElementById('regMainForm').classList.add('hidden');
      document.getElementById('regSuccessView').classList.remove('hidden');
      document.getElementById('directWaBtn').href = waUrl;
      // Langsung buka WhatsApp; tombol tetap tersedia jika browser memblokir.
      setTimeout(function () { window.location.href = waUrl; }, 1200);
      return false;
    }
    function resetRegistrationForm() {
      document.getElementById('regMainForm').reset();
      document.getElementById('regMainForm').classList.remove('hidden');
      document.getElementById('regSuccessView').classList.add('hidden');
    }

    function handleAdvisorSubmit(event) {
      event.preventDefault();
      var msg = 'Halo Artha Safety Indonesia, saya ingin konsultasi Pelatihan Ahli K3 Umum Kemnaker RI.\n\n*Nama:* ' + document.getElementById('advName').value.trim() +
        '\n*Kategori:* ' + document.getElementById('advStatus').value + '\n*Pertanyaan:* ' + (document.getElementById('advNotes').value.trim() || '-');
      window.open('https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(msg), '_blank');
      closeModal('advisorModal');
    }

    // ---------- Galeri dokumentasi bergantian ----------
    (function () {
      var photos = [
        ['https://lh3.googleusercontent.com/d/1O6en3WBWh66rMpWbURaQWMVTnkcGgoEg', 'Graduation', 'Ahli K3 Umum Kemnaker', 'Ujian Seminar'],
        ['https://lh3.googleusercontent.com/d/1Qh7w7vsbv0r1I0eSX-0q5D7GqVUM3h9M', 'Observasi Industri', 'Praktek Kerja Lapangan', 'Kunjungan Pabrik'],
        ['https://lh3.googleusercontent.com/d/1cPfwDuq-o69oVElPewiEvSlNkluisc7G', 'Simulasi Fire Fighting', 'Basic Safety Training', 'Instruktur Damkar'],
        ['https://lh3.googleusercontent.com/d/1hiVin3LLW3V4pdgBIVPTlkedVJUGabfV', 'Praktek P3K', 'Basic Safety Training', 'Instruktur PMI'],
        ['https://lh3.googleusercontent.com/d/173bK5ROrOkpYi7ZXLEL_5EhcgDuTc1b-', 'Ujian Teori', 'AK3U Kemnaker RI', 'Tatap Muka'],
        ['https://lh3.googleusercontent.com/d/13CH9-GSPUPf-fj6P-un2VxIyTWzuJnrp', 'Materi Kelas', 'Bimbingan Mentor Praktisi', 'Kelas Interaktif']
      ];
      var next = 0, slot = 0, timer = null;
      function swap() {
        var el = document.getElementById('docSlot' + slot); if (!el) return;
        var p = photos[next]; next = (next + 1) % photos.length;
        var layer = document.createElement('div');
        layer.className = 'doc-card-layer absolute inset-0 animate-slide-in-up z-10';
        layer.innerHTML = '<img src="' + p[0] + '" alt="' + p[1] + '" class="w-full h-full object-cover">' +
          '<div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/50 to-transparent"></div>' +
          '<div class="absolute bottom-0 inset-x-0 p-3 sm:p-5"><span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider">' + p[3] + '</span>' +
          '<h3 class="mt-1.5 text-sm sm:text-lg font-extrabold text-white leading-tight">' + p[1] + '</h3><p class="text-[11px] sm:text-xs text-slate-300">' + p[2] + '</p></div>';
        var old = el.querySelector('.doc-card-layer');
        el.appendChild(layer);
        if (old) { old.classList.add('animate-slide-out-up'); setTimeout(function () { if (old.parentNode) old.parentNode.removeChild(old); layer.classList.remove('animate-slide-in-up', 'z-10'); }, 460); }
        slot = (slot + 1) % 4;
      }
      var box = document.getElementById('docGalleryContainer');
      function start() { stop(); timer = setInterval(swap, 2200); }
      function stop() { if (timer) clearInterval(timer); }
      if (box && 'IntersectionObserver' in window) {
        new IntersectionObserver(function (en) { en[0].isIntersecting ? start() : stop(); }).observe(box);
        box.addEventListener('mouseenter', stop); box.addEventListener('mouseleave', start);
      }
    })();
  </script>
</body>
</html>

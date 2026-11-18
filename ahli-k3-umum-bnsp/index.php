<?php
/**
 * Landing page Pelatihan Ahli K3 Umum BNSP — kelas online via Zoom.
 * Tanggal batch, batas promo, dan kuota diatur dari dashboard (admin → Pengaturan).
 */
$PROGRAM = 'ahli-k3-umum-bnsp';

// Nilai bawaan jika database belum siap: halaman tetap tampil.
$batchLong = '1 - 4 Desember 2026';
$batchShort = '1-4 Des 2026';
$batchStart = '2026-12-01';
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
$jt = fn($n) => rtrim(rtrim(number_format($n / 1000000, 2, ',', '.'), '0'), ',') . ' jt';

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
$quotaText = !$hasQuota ? '' : ($quotaFull ? 'Kuota batch ini penuh' : 'Sisa ' . $quotaLeft . ($quotaTotal ? ' dari ' . $quotaTotal : '') . ' kursi');
$batchStartTs = strtotime($batchStart . ' 08:30:00');
$batchStarted = time() >= $batchStartTs;

// ---------- Harga ----------
// Satu harga untuk semua peserta: [harga normal, harga sekarang].
// Paket: [harga normal (null = tanpa harga coret), harga sekarang].
// Basic dan Full sama, bedanya hanya training kit (khusus Full).
$packages = [
    'basic' => [null, 2750000],
    'full'  => [null, 3000000],
];
$fromNormal = $packages['basic'][0];
$fromPromo = $packages['basic'][1];
$bonusValue = 500000;
$trainingKit = ['Goodie bag', 'Kemeja safety', 'Blocknote', 'Pulpen'];
// Jenis peserta di form (disimpan ke dashboard sebagai "paket").
$participantTypes = [
    'pribadi' => 'Peserta pribadi',
    'utusan'  => 'Utusan perusahaan',
];
$packageLabel = fn($tier, $who) => 'BNSP Online · ' . ucfirst($tier) . ' · ' . $participantTypes[$who] . ' (' . $rp($packages[$tier][1]) . ')';
$groupLabel = 'BNSP Online · Rombongan perusahaan (3+ orang)';

$faqs = [
    ['Apa beda sertifikasi Ahli K3 Umum BNSP dengan Kemnaker RI?', 'Sertifikasi <strong>BNSP</strong> mengakui kompetensi seseorang dalam menjalankan tugas sesuai standar SKKNI, diakui secara nasional dan di kawasan ASEAN melalui kerangka MRA. Sertifikasi <strong>Kemnaker RI</strong> berkaitan dengan penunjukan Ahli K3 Umum di perusahaan (SKP &amp; Lisensi).'],
    ['Saya lulusan SMA/SMK, apakah bisa ikut?', 'Bisa. Syarat pendidikan sertifikasi Ahli K3 Umum BNSP cukup <strong>ijazah minimal SMA/SLTA sederajat</strong>, jadi cocok untuk memulai karier di bidang K3/HSE.'],
    ['Berapa lama sertifikat BNSP berlaku?', 'Sertifikat kompetensi BNSP berlaku <strong>3 tahun</strong>. Setelah itu dapat diperbarui melalui re-sertifikasi (uji kompetensi ulang).'],
    ['Bagaimana metode pelatihan dan ujiannya?', 'Materi disampaikan sesuai unit kompetensi SKKNI via Zoom. Setelah itu peserta mengikuti uji kompetensi (asesmen) oleh asesor LSP melalui <strong>presentasi tugas DIT, wawancara, dan verifikasi portofolio</strong> secara daring.'],
    ['Apa itu tugas DIT?', 'Tugas yang dikerjakan peserta di hari ke-3 sebagai bahan presentasi saat asesmen. Instruktur membimbing pengerjaannya supaya kamu siap menghadapi uji kompetensi di hari ke-4.'],
    ['Apa yang perlu disiapkan untuk kelas online?', 'Laptop atau tablet, koneksi internet yang stabil, dan bisa mengoperasikan Zoom. Kelas berlangsung pukul <strong>08.30–16.00 WIB</strong>.'],
    ['Apa beda kelas BNSP online dan offline?', 'Kelas online menghemat waktu dan biaya karena bisa diikuti dari mana saja. Ada tambahan 1 hari khusus untuk mengerjakan tugas DIT sebagai bahan presentasi asesmen.'],
    ['Apa beda paket Basic dan Full?', 'Materi, uji kompetensi, sertifikat, dan bonusnya <strong>sama</strong>. Bedanya hanya training kit: paket <strong>Full</strong> mendapat goodie bag, kemeja safety, blocknote, dan pulpen. Paket <strong>Basic</strong> tanpa training kit.'],
    ['Kelasnya online, bagaimana dengan training kit?', 'Training kit paket Full <strong>dikirim ke alamat peserta</strong> setelah pelunasan.'],
    ['Apakah harus bayar lunas di awal?', 'Tidak. Kamu bisa <strong>membayar DP untuk mengamankan kuota</strong>, lalu melunasi sesuai arahan admin.'],
    ['Apakah perusahaan bisa mendaftarkan karyawan?', 'Bisa. Kami melayani utusan perusahaan maupun <strong>rombongan (3+ orang)</strong>. Pilih paket atau "Rombongan perusahaan" di form pendaftaran, admin akan membantu administrasi dan invoice perusahaan.'],
    ['Apakah bisa ikut jadwal batch berikutnya?', 'Bisa. Konsultasikan jadwal yang cocok dengan admin, lalu amankan kursi kamu cukup dengan DP.'],
];

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
$siteBase = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'pelatihan.arthasafetyindonesia.com');
$pageUrl = $siteBase . '/' . $PROGRAM . '/';
$waNumber = '6281337261261';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <title>Pelatihan Ahli K3 Umum BNSP Online (Sertifikat Kompetensi) - PT Artha Safety Indonesia</title>
  <meta name="description" content="Pelatihan & sertifikasi Ahli K3 Umum BNSP 100% online via Zoom, 4 hari. Uji kompetensi oleh asesor LSP, cukup ijazah minimal SMA/SLTA. Batch <?= $h($batchLong) ?>. Mulai <?= $rp($fromPromo) ?>, cukup DP.">
  <link rel="canonical" href="<?= $h($pageUrl) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="PT Artha Safety Indonesia">
  <meta property="og:title" content="Pelatihan Ahli K3 Umum BNSP Online · Batch <?= $h($batchShort) ?>">
  <meta property="og:description" content="Sertifikat Kompetensi BNSP berlaku 3 tahun. 2 hari materi via Zoom + 1 hari tugas DIT + asesmen daring. Gratis 40 e-sertifikat HSE. Mulai Rp <?= $jt($fromPromo) ?>, cukup DP.">
  <meta property="og:url" content="<?= $h($pageUrl) ?>">
  <meta property="og:image" content="<?= $h($siteBase) ?>/assets/img/dok-materi-kelas.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="theme-color" content="#071C6E">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link rel="apple-touch-icon" href="../assets/img/apple-touch-icon.png">

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
  <noscript>
    <img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1399743914287047&ev=PageView&noscript=1" />
  </noscript>
  <!-- End Meta Pixel Code -->

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"></noscript>

  <!-- Ikon (Font Awesome Free, hanya ikon yang dipakai; lihat tools/build-icons.py) -->
  <?php $iconsCss = '../assets/css/icons.css?v=' . (@filemtime(dirname(__DIR__) . '/assets/css/icons.css') ?: 1); ?>
  <link rel="stylesheet" href="<?= $iconsCss ?>" media="print" onload="this.onload=null;this.media='all'">
  <noscript><link rel="stylesheet" href="<?= $iconsCss ?>"></noscript>

  <!-- Tampilan: CSS hasil build Tailwind (lihat tools/build-css.sh) -->
  <link rel="stylesheet" href="../assets/css/ahli-k3-umum.css?v=<?= @filemtime(dirname(__DIR__) . '/assets/css/ahli-k3-umum.css') ?: 1 ?>">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #1e293b;
      background-color: #ffffff;
    }
    .hero-title {
      letter-spacing: -0.035em;
      line-height: 1.08;
      font-weight: 900;
    }
    .pill-on { background: #071C6E; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.08); }
    .pill-off { background: transparent; color: #475569; }
    .stay-on { background: #A6F634; color: #041040; border-color: #A6F634; }
    .scheme-on { background: #071C6E; color: #fff; border-color: #071C6E; box-shadow: 0 4px 14px 0 rgba(13, 59, 232, 0.25); }
    .scheme-off { background: #fff; color: #071C6E; border-color: #e2e8f0; }
    .stay-off { background: #fff; color: #475569; border-color: #e2e8f0; }
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    /* Animasi Marquee Dua Arah Ulasan Alumni */
    @keyframes marqueeLeft {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    @keyframes marqueeRight {
      0% { transform: translateX(-50%); }
      100% { transform: translateX(0); }
    }

    .animate-marquee-left {
      display: flex;
      width: max-content;
      animation: marqueeLeft 42s linear infinite;
    }
    .animate-marquee-right {
      display: flex;
      width: max-content;
      animation: marqueeRight 42s linear infinite;
    }

    .marquee-container:hover .animate-marquee-left,
    .marquee-container:hover .animate-marquee-right {
      animation-play-state: paused;
    }

    /* Animasi Cepat Slide Up Bergantian Satu per Satu untuk Kartu Dokumentasi */
    @keyframes docSlideOutUp {
      0% {
        transform: translateY(0);
        opacity: 1;
      }
      100% {
        transform: translateY(-100%);
        opacity: 0.6;
      }
    }

    @keyframes docSlideInUp {
      0% {
        transform: translateY(100%);
        opacity: 0.6;
      }
      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-slide-out-up {
      animation: docSlideOutUp 0.46s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .animate-slide-in-up {
      animation: docSlideInUp 0.46s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
  </style>

  <script>
    function toggleCurriculum(dayNum) {
      var content = document.getElementById('curr-content-' + dayNum);
      var icon = document.getElementById('curr-icon-' + dayNum);
      if (!content || !icon) return;

      if (content.classList.contains('hidden') || content.style.display === 'none') {
        content.classList.remove('hidden');
        content.style.setProperty('display', 'block', 'important');
        icon.innerHTML = '&minus;';
      } else {
        content.classList.add('hidden');
        content.style.setProperty('display', 'none', 'important');
        icon.innerHTML = '&#43;';
      }
    }

    function openModal(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        modal.style.setProperty('display', 'flex', 'important');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeModal(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        modal.style.setProperty('display', 'none', 'important');
        document.body.style.overflow = '';
      }
    }

    function openVideoModal() {
      var modal = document.getElementById('videoModal');
      var iframe = document.getElementById('videoIframe');
      if (modal && iframe) {
        iframe.src = "https://www.youtube.com/embed/T6Y7vA2ks1g?autoplay=1&rel=0";
        modal.style.setProperty('display', 'flex', 'important');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeVideoModal() {
      var modal = document.getElementById('videoModal');
      var iframe = document.getElementById('videoIframe');
      if (modal && iframe) {
        iframe.src = "";
        modal.style.setProperty('display', 'none', 'important');
        document.body.style.overflow = '';
      }
    }

    function toggleFaq(num) {
      var ans = document.getElementById('faq-ans-' + num);
      var icon = document.getElementById('faq-icon-' + num);
      if (!ans || !icon) return;

      if (ans.classList.contains('hidden') || ans.style.display === 'none') {
        ans.classList.remove('hidden');
        ans.style.setProperty('display', 'block', 'important');
        icon.classList.add('rotate-180');
      } else {
        ans.classList.add('hidden');
        ans.style.setProperty('display', 'none', 'important');
        icon.classList.remove('rotate-180');
      }
    }

    var allFaqOpen = false;
    function toggleAllFaqs() {
      allFaqOpen = !allFaqOpen;
      var btnText = document.getElementById('toggleAllFaqText');
      var btnIcon = document.getElementById('toggleAllFaqIcon');

      for (var i = 1; i <= 40; i++) {
        var ans = document.getElementById('faq-ans-' + i);
        var icon = document.getElementById('faq-icon-' + i);
        if (ans && icon) {
          if (allFaqOpen) {
            ans.classList.remove('hidden');
            ans.style.setProperty('display', 'block', 'important');
            icon.classList.add('rotate-180');
          } else {
            ans.classList.add('hidden');
            ans.style.setProperty('display', 'none', 'important');
            icon.classList.remove('rotate-180');
          }
        }
      }

      if (btnText) {
        btnText.innerText = allFaqOpen ? 'Tutup Semua Jawaban' : 'Buka Semua Jawaban';
      }
      if (btnIcon) {
        btnIcon.className = allFaqOpen ? 'fa-solid fa-chevron-up text-xs transition-transform duration-200' : 'fa-solid fa-chevron-down text-xs transition-transform duration-200';
      }
    }

    window.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeVideoModal();
        closeModal('advisorModal');
      }
    });
  </script>
</head>
<body class="bg-white text-slate-800 antialiased selection:bg-brand-accent selection:text-brand-dark pb-16 lg:pb-0">

  <aside class="bg-brand-navy text-white text-xs sm:text-sm py-2 sm:py-2.5 px-4 shadow-sm border-b border-brand-blue/30 relative z-30">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center gap-x-3 gap-y-1.5 text-center">
      <span class="bg-brand-accent text-brand-dark font-black px-2.5 py-0.5 rounded-full text-[11px] tracking-wider uppercase shadow-sm">
        Batch <?= $h($batchShort) ?>
      </span>
      <?php if ($hasQuota): ?>
        <span class="font-bold <?= $quotaFull ? 'text-red-300' : 'text-white' ?>"><i class="fa-solid fa-user-group text-brand-accent mr-1"></i><?= $h($quotaText) ?></span>
      <?php endif; ?>
      <?php if ($promoActive): ?>
        <span class="font-bold text-brand-accent"><i class="fa-solid fa-fire mr-1"></i><?= $h($promoText) ?></span>
      <?php endif; ?>
      <?php if (!$hasQuota && !$promoActive): ?>
        <span class="font-medium text-slate-200">Sertifikasi Ahli K3 Umum BNSP · 100% online via Zoom · cukup DP untuk amankan kursi</span>
      <?php endif; ?>
      <a href="#registration" class="hidden sm:inline-flex underline hover:text-brand-accent font-bold items-center gap-1 transition">
        <span>Amankan Kursi</span>
        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
      </a>
    </div>
  </aside>

  <header class="border-b border-slate-100 bg-white/95 backdrop-blur-md sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 sm:h-20 gap-4 lg:gap-8">

        <!-- Brand Logo Resmi PT Artha Safety Indonesia -->
        <a href="#" class="flex items-center flex-shrink-0 group py-1">
          <img
            src="../assets/img/logo-artha.png"
            alt="PT Artha Safety Indonesia"
            class="h-8 sm:h-11 w-auto max-w-[180px] sm:max-w-[220px] object-contain transition-transform duration-200 group-hover:scale-105"
            onerror="this.onerror=null; this.src='https://placehold.co/200x50/071C6E/ffffff?text=ARTHA+SAFETY';"
          >
        </a>

        <!-- Desktop Navigation Menu -->
        <nav class="hidden lg:flex items-center gap-6 xl:gap-8 text-xs xl:text-sm font-bold text-slate-600 whitespace-nowrap">
          <a href="#curriculum" class="hover:text-brand-blue transition py-2">Silabus</a>
          <a href="#portfolio" class="hover:text-brand-blue transition py-2">Legalitas</a>
          <a href="#facilities" class="hover:text-brand-blue transition py-2">Fasilitas</a>
          <a href="#reviews" class="hover:text-brand-blue transition py-2">Ulasan</a>
          <a href="#documentation" class="hover:text-brand-blue transition py-2">Dokumentasi</a>
          <a href="#pricing" class="hover:text-brand-blue transition py-2">Biaya</a>
          <a href="#faq" class="hover:text-brand-blue transition py-2">FAQ</a>
        </nav>

        <!-- CTA Buttons Desktop -->
        <div class="hidden sm:flex items-center gap-2.5 xl:gap-3 flex-shrink-0">
          <button type="button" onclick="openModal('advisorModal')" class="bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black text-xs xl:text-sm px-3.5 xl:px-4 py-2.5 rounded-xl transition shadow-lime flex items-center gap-2 cursor-pointer whitespace-nowrap">
            <span>Konsultasi</span>
            <i class="fa-solid fa-chevron-down text-[10px]"></i>
          </button>
          <a href="#registration" class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-xs xl:text-sm px-4 xl:px-5 py-2.5 rounded-xl transition shadow-btn flex items-center gap-2 whitespace-nowrap">
            <span>Daftar Batch</span>
            <i class="fa-solid fa-arrow-right text-[11px]"></i>
          </a>
        </div>

        <!-- Mobile Menu Hamburger Button -->
        <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition focus:outline-none cursor-pointer" aria-label="Buka Menu Mobile">
          <i class="fa-solid fa-bars text-xl text-brand-navy"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Nav -->
    <div id="mobileMenu" class="hidden lg:hidden px-4 pt-2 pb-6 border-t border-slate-100 bg-white shadow-lg">
      <div class="flex flex-col gap-2 font-semibold text-slate-700 py-3 text-sm">
        <a href="#curriculum" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Silabus</a>
        <a href="#portfolio" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Legalitas</a>
        <a href="#facilities" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Fasilitas</a>
        <a href="#reviews" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Ulasan</a>
        <a href="#documentation" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Dokumentasi</a>
        <a href="#pricing" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Biaya</a>
        <a href="#faq" onclick="toggleMobileMenu()" class="py-2.5 hover:text-brand-blue border-b border-slate-50">Pertanyaan Umum (FAQ)</a>
        <a href="#registration" onclick="toggleMobileMenu()" class="py-2.5 text-brand-blue font-bold">Form Pendaftaran</a>
        <div class="pt-3 flex flex-col gap-2.5">
          <button type="button" onclick="openModal('advisorModal'); toggleMobileMenu();" class="w-full bg-brand-accent text-brand-dark font-black py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm">
            <i class="fa-solid fa-headset text-sm"></i>
            <span>Tanya Advisor K3</span>
          </button>
          <a href="#registration" onclick="toggleMobileMenu()" class="w-full bg-brand-navy text-white text-center font-bold py-3 rounded-xl flex items-center justify-center gap-2 shadow-btn">
            <span>Daftar Batch Terdekat</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-12 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 relative items-start">

      <!-- KOLOM KIRI (7 Kolom) -->
      <div class="lg:col-span-7 flex flex-col space-y-12 sm:space-y-14">

        <!-- SECTION 1: HERO & PROGRAM SNAPSHOT -->
        <div>
          <div class="flex items-center gap-2.5 mb-5">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs md:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
              TUK LSP BNSP &bull; Kelas Online via Zoom
            </span>
          </div>

          <h1 class="hero-title text-4xl sm:text-5xl lg:text-[4rem] text-brand-navy mb-6">
            Pelatihan & Sertifikasi Ahli K3 Umum BNSP
          </h1>

          <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal mb-8 max-w-2xl">
            Belajar dari mana saja, langsung uji kompetensi. 2 hari materi via Zoom, 1 hari mengerjakan tugas DIT, lalu asesmen daring oleh asesor LSP. Cukup ijazah minimal SMA/SLTA.
          </p>

          <div class="grid grid-cols-2 gap-3 mb-8 max-w-2xl">
            <?php foreach ([
                ['fa-award', 'Sertifikat Kompetensi BNSP', 'Berlaku 3 tahun', 'border-blue-100 bg-brand-subtle/60', 'text-brand-blue'],
                ['fa-gift', 'GRATIS 40 E-Sertifikat HSE', 'Senilai ' . $rp($bonusValue), 'border-lime-200/70 bg-lime-50/70', 'text-lime-700'],
                ['fa-user-graduate', 'Minimal lulusan SMA/SLTA', 'Fresh graduate bisa ikut', 'border-lime-200/70 bg-lime-50/70', 'text-lime-700'],
                ['fa-laptop', '100% online via Zoom', 'Durasi 4 hari', 'border-blue-100 bg-brand-subtle/60', 'text-brand-blue'],
            ] as [$i, $t, $d, $box, $ic]): ?>
              <div class="rounded-2xl border <?= $box ?> p-3.5 sm:p-4 flex items-start gap-3">
                <i class="fa-solid <?= $i ?> <?= $ic ?> mt-0.5"></i>
                <div>
                  <p class="text-xs sm:text-sm font-extrabold text-brand-navy leading-snug"><?= $t ?></p>
                  <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5"><?= $d ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 mb-8">
            <a href="#registration" class="inline-flex items-center justify-center gap-2.5 bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black text-base px-7 py-4 rounded-2xl transition shadow-lime">
              <i class="fa-solid fa-lock text-sm"></i>
              <span>Amankan Kursi, Cukup DP</span>
            </a>
            <a href="#pricing" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-brand-navy font-bold text-base px-6 py-4 rounded-2xl border-2 border-brand-navy/15 transition">
              <span>Lihat Biaya</span>
              <i class="fa-solid fa-arrow-down text-xs"></i>
            </a>
          </div>

          <!-- Social Proof / Badge Alumni Asli Resmi -->
          <div class="flex flex-wrap items-center gap-4 mb-10 pb-2 border-b border-slate-100">
            <div class="flex -space-x-2.5 overflow-hidden">
              <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-sm" src="../assets/img/alumni-1.jpg" alt="Alumni K3 1">
              <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-sm" src="../assets/img/alumni-2.jpg" alt="Alumni K3 2">
              <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-sm" src="../assets/img/alumni-3.jpg" alt="Alumni K3 3">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full ring-2 ring-white bg-brand-navy text-[11px] font-bold text-white shadow-sm">
                4K+
              </div>
            </div>

            <div class="flex flex-col text-sm">
              <div class="flex items-center gap-1 text-brand-navy font-bold">
                <span>4.000+ Ahli K3 Tersertifikasi</span>
                <div class="flex text-amber-400 text-xs ml-1">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
              </div>
              <a href="https://maps.google.com/?cid=4002387960243989151" target="_blank" rel="noopener" class="text-xs font-medium text-slate-500 hover:text-brand-blue"><i class="fa-brands fa-google text-brand-blue mr-1"></i>5,0 ★ dari 1.139 ulasan Google · TUK LSP BNSP</a>
            </div>
          </div>

          <!-- Box Snapshot Program -->
          <div class="w-full rounded-2xl border border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="bg-brand-subtle/80 px-5 py-3 flex items-center justify-between border-b border-blue-100/70">
              <span class="text-xs font-bold text-brand-navy uppercase tracking-wider">Ringkasan Program</span>
              <span class="text-xs font-semibold text-brand-blue flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Kelas Online via Zoom
              </span>
            </div>

            <div class="p-5 sm:p-6">
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                <div class="sm:col-span-4 border-b sm:border-b-0 sm:border-r border-slate-100 pb-4 sm:pb-0">
                  <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">DURASI SERTIFIKASI</p>
                  <p class="text-3xl font-black text-brand-navy tracking-tight">4 Hari</p>
                  <p class="text-xs text-slate-500 font-medium mt-0.5">Online via Zoom · 08.30–16.00 WIB</p>
                </div>

                <div class="sm:col-span-5 bg-lime-50/70 rounded-xl p-3.5 border border-lime-200/60">
                  <div class="flex items-center gap-1.5 text-lime-700 font-bold text-[11px] uppercase tracking-wider mb-0.5">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>BATCH TERDEKAT</span>
                  </div>
                  <h4 class="font-bold text-brand-navy text-sm leading-snug"><?= $h($batchLong) ?></h4>
                  <p class="text-[11px] mt-0.5 font-semibold <?= $quotaFull ? 'text-red-600' : 'text-slate-600' ?>"><?= $quotaFull ? 'Kuota batch ini penuh' : 'Kuota terbatas · cukup DP' ?></p>
                </div>

                <div class="sm:col-span-3 sm:text-right">
                  <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mulai</p>
                  <?php if ($fromNormal): ?><p class="text-xs text-red-500 line-through font-semibold"><?= $rp($fromNormal) ?></p><?php endif; ?>
                  <p class="text-xl font-black text-brand-blue tracking-tight leading-tight"><?= $rp($fromPromo) ?></p>
                </div>
              </div>

              <?php if (!$batchStarted || $hasQuota || $promoActive): ?>
              <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                <?php if (!$batchStarted): ?>
                  <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Pelatihan dimulai dalam</p>
                    <div class="flex gap-1.5" data-countdown="<?= $batchStartTs * 1000 ?>">
                      <?php foreach (['d' => 'hari', 'h' => 'jam', 'm' => 'mnt', 's' => 'dtk'] as $k => $l): ?>
                        <div class="bg-brand-navy text-white rounded-lg px-2.5 py-1.5 text-center min-w-[52px]"><span class="block text-lg font-black leading-none" data-cd="<?= $k ?>">--</span><span class="text-[10px] text-slate-300"><?= $l ?></span></div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>
                <div>
                  <?php if ($hasQuota): ?>
                    <div class="rounded-xl border <?= $quotaFull ? 'border-red-200 bg-red-50' : 'border-red-100 bg-red-50/60' ?> p-3.5">
                      <div class="flex items-center justify-between gap-3">
                        <div>
                          <p class="text-[11px] font-bold uppercase tracking-wider text-red-600 mb-0.5"><i class="fa-solid fa-user-group mr-1"></i>Sisa kursi batch ini</p>
                          <?php if ($quotaFull): ?>
                            <p class="text-lg font-black text-red-600 leading-tight">Kuota penuh</p>
                          <?php else: ?>
                            <p class="font-black text-brand-navy leading-none"><span class="text-3xl text-red-600"><?= (int) $quotaLeft ?></span><?php if ($quotaTotal): ?><span class="text-sm text-slate-500 font-bold"> / <?= (int) $quotaTotal ?> kursi</span><?php else: ?><span class="text-sm text-slate-500 font-bold"> kursi lagi</span><?php endif; ?></p>
                          <?php endif; ?>
                        </div>
                        <?php if (!$quotaFull): ?><a href="#registration" class="flex-shrink-0 text-[11px] font-extrabold bg-brand-navy text-white px-3 py-2 rounded-lg hover:bg-brand-blue transition">Amankan Kursi</a><?php endif; ?>
                      </div>
                      <?php if ($quotaTotal): ?>
                        <div class="w-full bg-white h-2 rounded-full overflow-hidden mt-2.5"><div class="bg-gradient-to-r from-amber-400 to-red-500 h-full rounded-full" style="width: <?= $quotaPct ?>%"></div></div>
                        <p class="text-[10px] text-slate-500 font-semibold mt-1"><?= $quotaPct ?>% kursi sudah terisi</p>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($promoActive): ?>
                    <p class="text-[11px] font-bold text-red-600 mt-2"><i class="fa-solid fa-fire mr-1"></i><?= $h($promoText) ?>, setelah itu kembali ke harga normal.</p>
                  <?php endif; ?>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="border-b border-slate-200/90 pt-2 sticky top-16 sm:top-20 bg-white/95 backdrop-blur-md z-20">
          <nav class="flex items-center gap-6 sm:gap-8 overflow-x-auto no-scrollbar text-sm font-bold pb-3.5">
            <a href="#description" class="text-brand-navy hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Deskripsi</a>
            <a href="#untuk-siapa" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Untuk Siapa</a>
            <a href="#curriculum" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Materi</a>
            <a href="#portfolio" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Legalitas</a>
            <a href="#certification" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Sertifikat</a>
            <a href="#bonus" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Bonus</a>
            <a href="#reviews" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Ulasan</a>
            <a href="#pricing" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Biaya</a>
            <a href="#requirements" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Persyaratan</a>
            <a href="#faq" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">FAQ</a>
            <a href="#registration" class="text-brand-blue hover:text-brand-navy pb-3.5 whitespace-nowrap transition">Pendaftaran</a>
          </nav>
        </div>

        <section id="description" class="w-full rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-9 shadow-card">
          <h2 class="text-3xl sm:text-4xl font-black text-brand-navy tracking-tight mb-4">
            Deskripsi Program
          </h2>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-6">
            Sertifikat kompetensi BNSP adalah bukti resmi bahwa kamu mampu menjalankan tugas Ahli K3 Umum sesuai standar kerja nasional. Program dari PT Artha Safety Indonesia ini membekali kamu sesuai unit kompetensi SKKNI dalam 4 hari, 100% online, lalu langsung ditutup uji kompetensi oleh asesor LSP.
          </p>

          <div class="w-full border-t border-slate-100 my-6"></div>

          <p class="text-sm sm:text-[15px] text-slate-700 font-semibold mb-6">
            Mengapa Sertifikasi Ahli K3 Umum BNSP?
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <?php foreach ([
                ['Berbasis SKKNI', 'Kompetensi diuji sesuai Standar Kompetensi Kerja Nasional Indonesia bidang K3.'],
                ['Diakui nasional', 'Diterbitkan BNSP dan diakui hingga kawasan ASEAN melalui kerangka MRA.'],
                ['Cepat &amp; terukur', 'Pembekalan singkat lalu langsung uji kompetensi oleh asesor LSP.'],
                ['Terbuka lulusan SMA', 'Cukup ijazah minimal SMA/SLTA sederajat, cocok untuk memulai karier K3.'],
            ] as [$t, $d]): ?>
            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                <i class="fa-solid fa-check text-xs"></i>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-brand-navy uppercase tracking-wide"><?= $t ?></h3>
                <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1"><?= $d ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="w-full border-t border-slate-100 my-6"></div>
          <p class="text-[11px] font-extrabold uppercase tracking-wider text-brand-blue mb-3">Dasar Sertifikasi</p>
          <div class="flex flex-wrap gap-2">
            <?php foreach ([['UU No. 13 Tahun 2003', 'Ketenagakerjaan'], ['PP No. 10 Tahun 2018', 'Badan Nasional Sertifikasi Profesi'], ['SKKNI bidang K3', 'Standar kompetensi yang diujikan'], ['Berlaku 3 tahun', 'Diperbarui lewat re-sertifikasi']] as [$a, $b]): ?>
              <span class="bg-brand-subtle text-brand-navy text-[11px] font-semibold px-3 py-1.5 rounded-full border border-blue-100"><strong><?= $a ?></strong> · <?= $b ?></span>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="untuk-siapa" class="w-full rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-9 shadow-card">
          <div class="flex items-center gap-2.5 mb-3">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">UNTUK SIAPA</span>
          </div>
          <h2 class="text-3xl sm:text-4xl font-black text-brand-navy tracking-tight mb-3">Program Ini Cocok Untuk</h2>
          <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">Terbuka bagi lulusan minimal SMA/SLTA sederajat, baik yang baru memulai karier maupun profesional yang ingin membuktikan kompetensinya.</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([
                ['fa-user-graduate', 'Lulusan Baru SMA/D3/S1', 'Memulai karier K3/HSE dengan sertifikat kompetensi BNSP, cukup berbekal ijazah minimal SMA/SLTA.'],
                ['fa-building', 'Utusan Perusahaan', 'Membuktikan kompetensi personel K3 perusahaan dengan sertifikat BNSP berbasis SKKNI.'],
                ['fa-helmet-safety', 'Safety Officer & Praktisi HSE', 'Mendapat pengakuan kompetensi nasional atas pengalaman kerja K3 yang sudah dimiliki.'],
                ['fa-user-tie', 'Supervisor & Manajer', 'Memimpin penerapan K3 dan budaya kerja aman di unit yang kamu kelola.'],
            ] as [$icon, $title, $desc]): ?>
              <div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
                <div class="w-10 h-10 rounded-xl bg-brand-navy text-brand-accent flex items-center justify-center flex-shrink-0"><i class="fa-solid <?= $icon ?>"></i></div>
                <div>
                  <h3 class="text-sm font-extrabold text-brand-navy"><?= $title ?></h3>
                  <p class="text-xs text-slate-500 leading-relaxed mt-1"><?= $desc ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mt-6 mb-2.5">Dibutuhkan di berbagai sektor industri</p>
          <div class="flex flex-wrap gap-2">
            <?php foreach ([['fa-industry', 'Manufaktur'], ['fa-person-digging', 'Konstruksi'], ['fa-oil-well', 'Migas & Energi'], ['fa-mountain', 'Pertambangan'], ['fa-hospital', 'Rumah Sakit'], ['fa-warehouse', 'Logistik & Gudang']] as [$i, $l]): ?>
              <span class="inline-flex items-center gap-1.5 bg-lime-50/70 border border-lime-200/60 text-brand-navy text-xs font-bold px-3 py-1.5 rounded-full"><i class="fa-solid <?= $i ?> text-lime-700"></i><?= $l ?></span>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="curriculum" class="w-full rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-9 shadow-card">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
              <h2 class="hero-title text-3xl sm:text-4xl text-brand-navy">
                Materi &amp; Unit Kompetensi
              </h2>
              <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                Pembekalan sesuai unit kompetensi SKKNI online via Zoom, pukul 08.30–16.00 WIB, ditutup uji kompetensi oleh asesor LSP.
              </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              <span class="bg-brand-subtle text-brand-blue font-bold text-xs px-3.5 py-1.5 rounded-full border border-blue-100">4 hari</span>
              <span class="bg-brand-subtle text-brand-blue font-bold text-xs px-3.5 py-1.5 rounded-full border border-blue-100">Online via Zoom</span>
              <span class="bg-brand-accent/20 text-brand-dark font-extrabold text-xs px-3.5 py-1.5 rounded-full border border-brand-accent/40 flex items-center gap-1">
                <i class="fa-solid fa-award text-brand-navy text-[11px]"></i>
                <span>Uji Kompetensi</span>
              </span>
            </div>
          </div>

          <?php
          $days = [
              ['Hari 1', 'Materi online via Zoom', 'Pembekalan dasar-dasar K3 hingga manajemen risiko, investigasi kecelakaan, dan dokumentasi K3.', ['Dasar-Dasar K3', 'Pengelolaan APD', 'Komunikasi &amp; Izin Kerja K3', 'Manajemen Risiko K3', 'Investigasi Kecelakaan Kerja', 'Pelayanan Kesehatan Kerja', 'Dokumentasi &amp; Prosedur K3'], 'Materi Online'],
              ['Hari 2', 'Materi online via Zoom', 'Kesiapsiagaan darurat, P3K, pengukuran faktor bahaya, dan pembekalan menjelang ujian.', ['Sistem Tanggap Darurat', 'P3K di Tempat Kerja', 'Pengukuran Faktor Bahaya', 'Pembekalan Ujian'], 'Materi Online'],
              ['Hari 3', 'Tugas DIT &amp; persiapan', 'Satu hari khusus mengerjakan tugas DIT sebagai bahan presentasi asesmen, didampingi instruktur.', ['Pengerjaan Tugas DIT', 'Persiapan Ujian'], 'Tugas &amp; Persiapan'],
              ['Hari 4', 'Asesmen uji kompetensi (daring)', 'Uji kompetensi oleh asesor LSP secara daring. Peserta yang dinyatakan kompeten mendapat Sertifikat Kompetensi BNSP.', ['Presentasi tugas DIT', 'Wawancara asesor', 'Verifikasi portofolio'], 'Uji Kompetensi'],
          ];
          ?>
          <div class="divide-y divide-slate-100">
            <?php foreach ($days as $n => [$day, $title, $desc, $items, $tag]): $id = $n + 1; $open = $n === 0; ?>
            <div class="py-5" id="curr-item-<?= $id ?>">
              <button type="button" onclick="toggleCurriculum(<?= $id ?>)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black <?= $id === 4 ? 'text-brand-blue' : 'text-brand-navy' ?> group-hover:text-brand-blue transition">0<?= $id ?></span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition flex items-center gap-2 flex-wrap">
                    <span><?= $title ?></span>
                    <?php if ($id === 4): ?><span class="bg-brand-accent text-brand-dark text-[10px] sm:text-[11px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">Asesor LSP</span><?php endif; ?>
                  </span>
                </div>
                <span id="curr-icon-<?= $id ?>" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200"><?= $open ? '&minus;' : '&#43;' ?></span>
              </button>
              <div id="curr-content-<?= $id ?>" class="<?= $open ? '' : 'hidden ' ?>pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3"><?= $desc ?></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <?php foreach ($items as $it): ?>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span><?= $it ?></span>
                  </div>
                  <?php endforeach; ?>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md"><?= $day ?></span>
                  <span class="inline-block bg-brand-accent/25 text-brand-dark text-[11px] font-black px-3 py-1 rounded-md"><?= $tag ?></span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="portfolio" class="w-full rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-9 shadow-card">
          <div class="flex items-center gap-2.5 mb-3">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
              AKREDITASI &amp; LEGALITAS
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl text-brand-navy mb-2">
            TUK Resmi LSP BNSP dan PJK3 Kemnaker RI
          </h2>
          <p class="text-xs sm:text-sm font-semibold text-slate-500 mb-6">
            Uji kompetensi diselenggarakan di Tempat Uji Kompetensi (TUK) resmi, dengan asesor dari Lembaga Sertifikasi Profesi (LSP) berlisensi BNSP.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([
                ['fa-certificate', 'Resmi sebagai TUK LSP BNSP', 'SKT.02/122/LSP-K3N/X/2023'],
                ['fa-landmark', 'Resmi terdaftar di Kementerian', 'AHU-0072648.AH.01.01.Tahun 2023'],
                ['fa-file-shield', 'SKP Bidang Keahlian K3 Umum', '5/000666/AS.01.02.4103.6.15/V/2026'],
                ['fa-building-circle-check', 'NIB Perusahaan', '2609230190968'],
            ] as [$i, $t, $no]): ?>
              <div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
                <div class="w-10 h-10 rounded-xl bg-brand-navy text-brand-accent flex items-center justify-center flex-shrink-0"><i class="fa-solid <?= $i ?>"></i></div>
                <div class="min-w-0">
                  <h3 class="text-sm font-extrabold text-brand-navy"><?= $t ?></h3>
                  <p class="text-xs text-slate-500 font-semibold mt-1 break-words"><?= $no ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="mt-6 rounded-2xl bg-brand-navy text-white p-5 flex items-center gap-4">
            <i class="fa-solid fa-shield-halved text-brand-accent text-2xl flex-shrink-0"></i>
            <div>
              <p class="text-sm font-extrabold">Provider K3 terpercaya</p>
              <p class="text-xs text-slate-300 mt-0.5">4.000+ alumni dan 100+ perusahaan telah bekerja sama dengan PT Artha Safety Indonesia.</p>
            </div>
          </div>
        </section>
      </div>

      <div class="lg:col-span-5 relative w-full flex justify-center lg:justify-end items-start h-full">
        <div class="sticky-card-container lg:sticky lg:top-24 w-full max-w-[340px] sm:max-w-[350px] bg-white rounded-3xl border border-slate-200/80 p-4 sm:p-5 shadow-card transition-all hover:shadow-xl hover:border-blue-200 duration-300">

          <!-- Video Showcase Thumbnail -->
          <div
            class="relative w-full aspect-[16/10] rounded-xl sm:rounded-2xl overflow-hidden mb-4 bg-slate-900 group cursor-pointer"
            onclick="openVideoModal()"
            role="button"
            tabindex="0"
            aria-label="Putar Video Resmi Pelatihan Ahli K3 Umum PT Artha Safety Indonesia"
          >
            <img
              src="https://img.youtube.com/vi/T6Y7vA2ks1g/hqdefault.jpg" width="480" height="360" loading="lazy" decoding="async"
              alt="Video Pelatihan Ahli K3 Umum PT Artha Safety Indonesia"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-95"
              onerror="this.onerror=null; this.src='https://img.youtube.com/vi/T6Y7vA2ks1g/mqdefault.jpg';"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy/90 via-black/25 to-transparent"></div>

            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-12 h-12 rounded-full bg-white/95 text-brand-navy flex items-center justify-center pl-0.5 shadow-xl group-hover:scale-110 group-hover:bg-brand-accent group-hover:text-brand-dark transition-all duration-300">
                <i class="fa-solid fa-play text-sm"></i>
              </div>
            </div>

            <div class="absolute bottom-2.5 left-2.5 right-2.5 flex items-center justify-between text-white text-[10px] sm:text-[11px] font-semibold">
              <span class="bg-black/60 backdrop-blur-md px-2 py-0.5 rounded flex items-center gap-1">
                <i class="fa-solid fa-laptop text-brand-accent"></i> Kelas Online
              </span>
              <span class="bg-brand-navy/90 backdrop-blur-md px-2 py-0.5 rounded font-black text-brand-accent">
                Artha Safety
              </span>
            </div>
          </div>

          <!-- Primary Action Buttons -->
          <div class="space-y-2.5 mb-4">
            <button type="button" onclick="openModal('advisorModal')" class="w-full bg-brand-navy hover:bg-brand-blue text-white font-bold py-2.5 sm:py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-btn text-xs sm:text-sm group cursor-pointer">
              <span>Konsultasi dengan Advisor K3</span>
              <i class="fa-solid fa-arrow-up-right-from-square text-[11px] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
            </button>

            <a href="#registration" class="w-full bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black py-2.5 sm:py-3 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 text-xs sm:text-sm shadow-lime">
              <i class="fa-solid fa-lock text-[11px]"></i>
              <span>Amankan Kursi, Cukup DP</span>
            </a>
          </div>

          <!-- Program Details List -->
          <div class="divide-y divide-slate-100 text-xs sm:text-[13px]">
            <?php foreach ([
                ['Durasi', '4 hari · 08.30–16.00 WIB'],
                ['Batch Terdekat', $h($batchShort)],
                ['Metode', '100% online via Zoom'],
                ['Uji kompetensi', 'Asesor LSP, daring'],
                ['Persyaratan', 'Ijazah min. SMA/SLTA'],
            ] as [$k, $v]): ?>
            <div class="py-2 sm:py-2.5 flex items-center justify-between gap-3">
              <span class="text-slate-500 font-medium"><?= $k ?></span>
              <span class="font-extrabold text-brand-navy text-right"><?= $v ?></span>
            </div>
            <?php endforeach; ?>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Biaya mulai</span>
              <span class="text-right"><?php if ($fromNormal): ?><span class="block text-[11px] text-red-500 line-through font-semibold leading-none"><?= $rp($fromNormal) ?></span><?php endif; ?><span class="font-extrabold text-brand-blue"><?= $rp($fromPromo) ?></span></span>
            </div>
            <?php if ($hasQuota): ?>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Kuota</span>
              <span class="font-extrabold text-right text-red-600"><?= $h($quotaText) ?></span>
            </div>
            <?php endif; ?>
          </div>
          <?php if ($promoActive): ?>
            <p class="mt-2 rounded-lg bg-red-50 border border-red-100 text-red-700 text-[11px] font-bold px-3 py-2"><i class="fa-solid fa-fire mr-1"></i><?= $h($promoText) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <section id="certification" class="w-full bg-slate-50/80 border-t border-b border-slate-200/70 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

        <div class="lg:col-span-5 flex flex-col items-center justify-center">
          <div class="relative w-full max-w-[400px] rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200/90 bg-white group flex items-center justify-center p-2 sm:p-2.5 transition-all duration-300 hover:shadow-[0_25px_50px_-12px_rgba(7,28,110,0.18)]">
            <img
              src="../assets/img/sertifikat-bnsp.jpg"
              alt="Contoh Sertifikat Kompetensi Ahli K3 Umum BNSP"
              width="800" height="1150" loading="lazy"
              class="w-full h-auto object-contain rounded-xl sm:rounded-2xl transform transition-transform duration-500 group-hover:scale-[1.015]"
            />
            <div class="absolute inset-0 rounded-2xl sm:rounded-3xl ring-1 ring-inset ring-black/5 pointer-events-none"></div>
          </div>

          <!-- Floating Accreditation Badge -->
          <div class="w-[90%] sm:w-[86%] max-w-[380px] -mt-5 sm:-mt-6 z-10 bg-brand-navy rounded-2xl p-4 sm:p-5 shadow-2xl border border-brand-blue/50 text-white flex items-center gap-4">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-brand-blue border-2 border-brand-accent text-brand-accent flex items-center justify-center font-black text-[10px] sm:text-xs flex-shrink-0 shadow-inner">
              <span>BNSP</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-brand-accent leading-none mb-1">CONTOH SERTIFIKAT</p>
              <h4 class="text-sm sm:text-base font-extrabold text-white truncate">Sertifikat Kompetensi BNSP</h4>
              <p class="text-xs text-slate-300 leading-snug mt-0.5">Berlaku 3 tahun, diterbitkan melalui LSP K3 Nasional.</p>
            </div>
          </div>
        </div>

        <div class="lg:col-span-7 flex flex-col justify-center">
          <div class="flex items-center gap-2.5 mb-4">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
              Benefit
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl lg:text-[2.75rem] text-brand-navy mb-5 leading-tight">
            Benefit yang Kamu Dapatkan
          </h2>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-8 max-w-xl">
            Bukan sekadar pelatihan: kamu dibekali sampai siap uji kompetensi dan pulang membawa pengakuan kompetensi nasional.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([
                ['01', 'Sertifikat Kompetensi BNSP', 'Diterbitkan BNSP melalui LSP bagi peserta yang dinyatakan kompeten. Berlaku 3 tahun.', 'Peserta kompeten'],
                ['02', 'Uji kompetensi oleh asesor LSP', 'Presentasi tugas DIT, wawancara, dan verifikasi portofolio secara daring.', 'Hari ke-4'],
                ['03', 'Belajar dari mana saja', '100% online via Zoom, ditambah 1 hari khusus mengerjakan tugas DIT sebagai bahan asesmen.', 'Hari ke-3'],
                ['04', 'Instruktur berpengalaman', 'Didampingi instruktur Ahli K3 hingga siap menghadapi uji kompetensi.', 'Setiap sesi'],
            ] as [$n, $t, $d, $tag]): ?>
              <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-black text-brand-blue"><?= $n ?></span>
                  <span class="text-[10px] font-extrabold uppercase tracking-wider bg-brand-subtle text-brand-blue px-2 py-0.5 rounded-full"><?= $tag ?></span>
                </div>
                <h3 class="text-sm sm:text-base font-extrabold text-brand-navy"><?= $t ?></h3>
                <p class="text-xs text-slate-500 leading-relaxed mt-1"><?= $d ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="bonus" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-20">
    <div class="w-full bg-brand-navy text-white rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden border border-brand-blue/60">
      <div class="absolute top-0 right-0">
        <div class="bg-brand-accent text-brand-dark font-black text-[10px] sm:text-xs px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow-md">FREE BONUS</div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <div>
          <div class="flex items-center gap-2.5 mb-3">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-accent">BONUS PESERTA</span>
          </div>
          <h2 class="hero-title text-3xl sm:text-4xl text-white mb-2">
            40 E-Sertifikat HSE Senilai <span class="line-through decoration-red-400 decoration-[3px] text-slate-300"><?= $rp($bonusValue) ?></span> <span class="text-brand-accent">GRATIS</span>
          </h2>
          <p class="text-sm sm:text-base text-slate-300 max-w-xl">Sertifikat elektronik pelatihan HSE untuk melengkapi portofolio dan CV kamu di bidang K3.</p>
          <div class="mt-6">
            <a href="#registration" class="inline-flex items-center gap-2 bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black text-sm px-6 py-3.5 rounded-2xl shadow-lime transition"><span>Ambil Bonusnya, Daftar Sekarang</span><i class="fa-solid fa-arrow-right text-xs"></i></a>
          </div>
        </div>
        <div class="bg-white text-slate-800 rounded-2xl p-5 sm:p-6">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div>
              <h3 class="text-lg font-black text-brand-navy">40 E-Sertifikat HSE</h3>
              <p class="text-xs text-slate-500">Untuk setiap peserta Ahli K3 Umum BNSP</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-xs text-red-500 line-through font-semibold"><?= $rp($bonusValue) ?></p>
              <p class="font-black text-emerald-600">GRATIS</p>
            </div>
          </div>
          <div class="space-y-2">
            <?php foreach (['40 topik pelatihan HSE dalam satu program', 'E-sertifikat untuk setiap topik yang diikuti', 'Menambah nilai CV &amp; portofolio K3/HSE'] as $b): ?>
              <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-700"><i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i><span><?= $b ?></span></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="facilities" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
    <div class="flex items-center gap-2.5 mb-3">
      <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
      <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
        Fasilitas
      </span>
    </div>

    <h2 class="hero-title text-3xl sm:text-4xl text-brand-navy mb-8">
      Fasilitas Pelatihan yang Anda Dapatkan
    </h2>

    <div class="w-full bg-[#FAF8F2] rounded-3xl border border-[#EDE5D3] p-6 sm:p-10 shadow-sm">
      <?php
      $facGroups = [
          ['Semua paket (Basic &amp; Full)', 'bg-brand-subtle text-brand-blue', 'fa-book', ['Modul pelatihan', 'Materi pelatihan', 'Free 40 e-sertifikat HSE', 'Sertifikat Kompetensi BNSP (peserta kompeten)']],
          ['Training kit · khusus Full Package', 'bg-brand-accent/30 text-brand-dark', 'fa-box-open', $trainingKit],
      ];
      ?>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <?php foreach ($facGroups as [$title, $badge, $icon, $items]): ?>
          <div class="bg-white rounded-2xl border border-slate-200/80 p-5">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full <?= $badge ?>"><i class="fa-solid <?= $icon ?>"></i><?= $title ?></span>
            <ul class="mt-4 grid grid-cols-2 gap-x-5 gap-y-2.5">
              <?php foreach ($items as $it): ?>
                <li class="flex items-start gap-2.5 text-xs sm:text-sm font-bold text-slate-800"><i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i><span><?= $it ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="text-xs text-slate-500 font-semibold mt-4 px-1"><i class="fa-solid fa-truck-fast text-brand-blue mr-1.5"></i>Training kit dikirim ke alamat peserta setelah pelunasan.</p>
    </div>
  </section>

<?php include dirname(__DIR__) . '/includes/reviews_section.php'; ?>

  <section id="documentation" class="w-full bg-white py-16 sm:py-24 border-t border-slate-100 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
        <div class="flex items-center justify-center gap-2 mb-3">
          <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
          <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
            DOKUMENTASI KEGIATAN
          </span>
        </div>

        <h2 class="hero-title text-3xl sm:text-4xl lg:text-5xl text-brand-navy mb-4 tracking-tight">
          Dokumentasi Kegiatan Artha Safety
        </h2>

        <p class="text-xs sm:text-sm md:text-base text-slate-600 font-normal leading-relaxed max-w-2xl mx-auto">
          Potret nyata kegiatan pelatihan dan sertifikasi K3 bersama PT Artha Safety Indonesia: suasana kelas, ujian, observasi industri, hingga praktik keselamatan bersama mentor praktisi.
        </p>
      </div>

      <!-- Container 4 Kartu Dokumentasi Sejajar Bergaya Referensi dengan Animasi Slide Up Satu per Satu -->
      <div id="docGalleryContainer" class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 select-none">

        <!-- SLOT 0 -->
        <div id="docSlot0" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img loading="lazy" decoding="async" src="../assets/img/dok-graduation.jpg" alt="Graduation" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Ujian Seminar
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Graduation
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Ahli K3 Umum Kemnaker
              </p>
            </div>
          </div>
        </div>

        <!-- SLOT 1 -->
        <div id="docSlot1" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img loading="lazy" decoding="async" src="../assets/img/dok-observasi-industri.jpg" alt="Observasi Industri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Kunjungan Pabrik AK3U Yogyakarta
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Observasi Industri
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Praktek Kerja Lapangan (PKL)
              </p>
            </div>
          </div>
        </div>

        <!-- SLOT 2 -->
        <div id="docSlot2" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img loading="lazy" decoding="async" src="../assets/img/dok-fire-fighting.jpg" alt="Simulasi Evakuasi &amp; Fire Fighting" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Instruktur Damkar Kota
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Simulasi Evakuasi &amp; Fire Fighting
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Inhouse Training
              </p>
            </div>
          </div>
        </div>

        <!-- SLOT 3 -->
        <div id="docSlot3" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img loading="lazy" decoding="async" src="../assets/img/dok-p3k.jpg" alt="Praktek P3K" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Instruktur PMI
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Praktek P3K
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Inhouse Training
              </p>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>

  <section id="pricing" class="w-full bg-slate-50/70 py-16 sm:py-24 border-t border-slate-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="w-full bg-white rounded-3xl sm:rounded-[2.5rem] border border-slate-200/90 p-6 sm:p-12 lg:p-16 shadow-card">

        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
          <div class="flex items-center justify-center gap-2 mb-3">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
              Biaya
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl lg:text-5xl text-brand-navy mb-4">
            Biaya Sertifikasi Ahli K3 Umum BNSP 2026
          </h2>
          <p class="text-xs sm:text-sm md:text-base text-slate-500 font-medium">
            Dua pilihan paket untuk peserta pribadi maupun utusan perusahaan, keduanya sudah termasuk pembekalan dan uji kompetensi. Bedanya hanya training kit. <strong class="text-brand-navy">Cukup bayar DP untuk mengamankan kuota.</strong>
          </p>

          <?php if ($promoActive || $hasQuota): ?>
            <div class="mt-5 flex flex-wrap justify-center gap-2">
              <?php if ($promoActive): ?><span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200 text-xs font-bold px-3 py-1.5 rounded-full"><i class="fa-solid fa-fire"></i><?= $h($promoText) ?></span><?php endif; ?>
              <?php if ($hasQuota): ?><span class="inline-flex items-center gap-1.5 bg-lime-50 text-lime-800 border border-lime-200 text-xs font-bold px-3 py-1.5 rounded-full"><i class="fa-solid fa-user-group"></i><?= $h($quotaText) ?> · Batch <?= $h($batchShort) ?></span><?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <?php
        $common = ['Pembekalan materi sesuai unit kompetensi SKKNI', 'Uji kompetensi oleh asesor LSP', '<strong>Sertifikat Kompetensi BNSP</strong> bagi peserta kompeten (berlaku 3 tahun)', 'Kelas online via Zoom, 4 hari', 'Modul &amp; materi pelatihan', 'Free 40 e-sertifikat HSE'];
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 items-stretch max-w-4xl mx-auto">
          <?php foreach ($packages as $tier => [$normal, $promo]): $isFull = $tier === 'full'; ?>
            <div class="w-full rounded-3xl p-7 sm:p-9 flex flex-col justify-between relative overflow-hidden <?= $isFull ? 'bg-brand-navy text-white shadow-2xl border border-brand-blue/60' : 'bg-white border-2 border-slate-200 shadow-sm transition-all hover:border-brand-blue/50 hover:shadow-md' ?>">
              <?php if ($isFull): ?>
                <div class="absolute top-0 right-0"><div class="bg-brand-accent text-brand-dark font-black text-[10px] sm:text-xs px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow-md">BEST VALUE</div></div>
              <?php endif; ?>
              <div>
                <span class="text-xs font-black uppercase tracking-wider <?= $isFull ? 'text-brand-accent' : 'text-slate-400' ?>"><?= $isFull ? 'FULL PACKAGE' : 'BASIC PACKAGE' ?></span>
                <h3 class="text-2xl font-black mt-3 mb-1 tracking-tight <?= $isFull ? 'text-white' : 'text-brand-navy' ?>">BNSP Online · <?= ucfirst($tier) ?></h3>
                <p class="text-xs font-semibold <?= $isFull ? 'text-slate-300' : 'text-slate-500' ?>"><?= $isFull ? 'Termasuk training kit' : 'Tanpa training kit' ?> · peserta pribadi &amp; utusan perusahaan</p>
                <div class="my-5">
                  <?php if ($normal): ?><span class="block text-sm font-bold line-through <?= $isFull ? 'text-red-300' : 'text-red-500' ?>"><?= $rp($normal) ?></span><?php endif; ?>
                  <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight <?= $isFull ? 'text-brand-accent' : 'text-brand-navy' ?>"><?= $rp($promo) ?></span>
                  <p class="text-xs font-bold mt-1.5 <?= $isFull ? 'text-brand-accent' : 'text-emerald-600' ?>"><?= $normal ? 'Hemat ' . $rp($normal - $promo) . ' · ' : '' ?>cukup DP untuk amankan kuota</p>
                </div>
                <div class="w-full border-t my-6 <?= $isFull ? 'border-white/15' : 'border-slate-100' ?>"></div>
                <div class="space-y-3.5 text-xs sm:text-sm <?= $isFull ? 'text-slate-200' : 'text-slate-700' ?>">
                  <?php foreach ($common as $f): ?>
                    <div class="flex items-start gap-3"><i class="fa-solid fa-check text-xs mt-1 flex-shrink-0 <?= $isFull ? 'text-brand-accent' : 'text-brand-blue' ?>"></i><span><?= $f ?></span></div>
                  <?php endforeach; ?>
                  <?php if ($isFull): ?>
                    <div class="flex items-start gap-3"><i class="fa-solid fa-check text-xs mt-1 flex-shrink-0 text-brand-accent"></i><span><strong>Training kit</strong>: <?= strtolower(implode(', ', $trainingKit)) ?> (dikirim ke rumah)</span></div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="mt-8 pt-4">
                <a href="#registration" onclick="pickPackage('<?= $tier ?>-pribadi')" class="w-full font-black text-sm py-3.5 px-5 rounded-2xl transition duration-200 flex items-center justify-center gap-2 cursor-pointer <?= $isFull ? 'bg-brand-accent hover:bg-brand-accentHover text-brand-dark shadow-lime' : 'bg-white hover:bg-slate-50 text-brand-navy border-2 border-brand-navy hover:border-brand-blue hover:text-brand-blue' ?>">
                  <span>Pilih Paket <?= ucfirst($tier) ?></span><i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="max-w-4xl mx-auto text-center text-xs sm:text-sm text-slate-500 mt-6"><i class="fa-solid fa-people-group text-brand-blue mr-1.5"></i>Mendaftarkan 3 orang atau lebih dari perusahaan? <a href="#registration" onclick="pickPackage('rombongan')" class="font-bold text-brand-blue underline hover:text-brand-navy">Pilih "Rombongan perusahaan" di form</a>, admin bantu invoice perusahaan.</p>

        <div class="max-w-4xl mx-auto mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div class="rounded-2xl bg-brand-subtle border border-blue-100 p-4"><p class="font-extrabold text-brand-navy mb-1"><i class="fa-solid fa-laptop text-brand-blue mr-1.5"></i>Belajar dari mana saja</p><p class="text-slate-600">Tidak perlu biaya perjalanan dan penginapan. Cukup laptop/tablet dan internet stabil.</p></div>
          <div class="rounded-2xl bg-lime-50/70 border border-lime-200/60 p-4"><p class="font-extrabold text-brand-navy mb-1"><i class="fa-solid fa-wallet text-lime-700 mr-1.5"></i>Bayar bertahap</p><p class="text-slate-600">Cukup DP untuk mengunci kuota. Biaya dapat berubah, admin mengonfirmasi biaya final sebelum invoice terbit.</p></div>
        </div>
      </div>
    </div>
  </section>

  <section id="requirements" class="w-full bg-white py-16 sm:py-24 border-t border-slate-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
        <div class="flex items-center justify-center gap-2 mb-3">
          <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
          <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">PERSYARATAN &amp; ALUR DAFTAR</span>
        </div>
        <h2 class="hero-title text-3xl sm:text-4xl lg:text-5xl text-brand-navy mb-4">Siapkan Dokumen, Lalu Daftar</h2>
        <p class="text-xs sm:text-sm md:text-base text-slate-500 font-medium">Cukup 7 persyaratan dan 4 langkah mudah. Semua dokumen dikirim lewat WhatsApp.</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-card">
          <h3 class="text-lg font-extrabold text-brand-navy mb-5"><span class="inline-flex w-7 h-7 rounded-lg bg-brand-accent text-brand-dark items-center justify-center text-sm font-black mr-2">A</span>Dokumen persyaratan</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([['fa-graduation-cap', 'Ijazah minimal SLTA', 'SMA/SMK atau sederajat'], ['fa-id-card', 'Scan KTP', 'Hasil scan jelas dan terbaca'], ['fa-image-portrait', 'Pas foto', 'Latar belakang merah'], ['fa-file-lines', 'CV', 'Riwayat pendidikan &amp; pengalaman kerja'], ['fa-file-signature', 'APL 01', 'Formulir permohonan sertifikasi (dibantu admin)'], ['fa-clipboard-check', 'APL 02', 'Formulir asesmen mandiri (dibantu admin)'], ['fa-laptop', 'Laptop/tablet', 'Koneksi stabil &amp; bisa mengoperasikan Zoom']] as [$i, $t, $d]): ?>
              <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-subtle text-brand-blue flex items-center justify-center flex-shrink-0"><i class="fa-solid <?= $i ?> text-sm"></i></div>
                <div><p class="text-sm font-extrabold text-brand-navy"><?= $t ?></p><p class="text-xs text-slate-500"><?= $d ?></p></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-card">
          <h3 class="text-lg font-extrabold text-brand-navy mb-5"><span class="inline-flex w-7 h-7 rounded-lg bg-brand-accent text-brand-dark items-center justify-center text-sm font-black mr-2">B</span>4 langkah mendaftar</h3>
          <div class="space-y-4">
            <?php foreach ([['Isi form &amp; chat admin', 'Sebutkan jadwal dan jenis peserta kamu.'], ['Kirim dokumen', 'Admin memeriksa kelengkapan persyaratan.'], ['Bayar DP', 'Admin mengirim invoice, cukup DP dulu.'], ['Siap pelatihan', 'Masuk grup WhatsApp peserta untuk info teknis.']] as $n => [$t, $d]): ?>
              <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-brand-navy text-white font-black text-sm flex items-center justify-center flex-shrink-0"><?= $n + 1 ?></div>
                <div><p class="text-sm font-extrabold text-brand-navy"><?= $t ?></p><p class="text-xs sm:text-sm text-slate-500"><?= $d ?></p></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="registration" class="w-full bg-white py-16 sm:py-24 border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">

        <!-- KOLOM KIRI -->
        <div class="lg:col-span-5 flex flex-col justify-start">
          <div class="flex items-center gap-2.5 mb-5">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-navy">
              PENDAFTARAN & KONSULTASI JADWAL
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl lg:text-5xl text-brand-navy mb-4 leading-tight">
            Sudah siap daftar hari ini ?
          </h2>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-8 max-w-lg">
            Amankan kursi di batch <strong class="text-brand-navy"><?= $h($batchLong) ?></strong> cukup dengan DP. Isi data singkat, lalu langsung chat admin di WhatsApp.
          </p>

          <?php if ($promoActive || $hasQuota): ?>
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-100 px-4 py-3 text-sm font-bold text-red-700 space-y-1">
              <?php if ($promoActive): ?><p><i class="fa-solid fa-fire mr-1.5"></i><?= $h($promoText) ?>, setelah itu kembali ke harga normal.</p><?php endif; ?>
              <?php if ($hasQuota): ?><p><i class="fa-solid fa-user-group mr-1.5"></i><?= $h($quotaText) ?> untuk batch ini.</p><?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="w-full bg-brand-navy rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-blue-900/60 mb-6 relative overflow-hidden">
            <div class="flex items-center justify-between pb-5 border-b border-white/10 mb-6">
              <div class="flex items-center gap-2 text-xs sm:text-sm font-extrabold text-brand-accent">
                <i class="fa-solid fa-award text-brand-accent"></i>
                <span class="uppercase tracking-wider">PENCAPAIAN ARTHA SAFETY</span>
              </div>
              <span class="text-[11px] text-slate-400 font-medium">Rekam Jejak Resmi</span>
            </div>

            <div class="grid grid-cols-2 gap-y-7 gap-x-6">
              <div>
                <p class="text-3xl sm:text-4xl font-black text-white tracking-tight">4.000+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Alumni</p>
              </div>
              <div>
                <p class="text-3xl sm:text-4xl font-black text-white tracking-tight">100+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Perusahaan</p>
              </div>
              <div>
                <p class="text-3xl sm:text-4xl font-black text-white tracking-tight">50+</p>
                <p class="text-xs text-slate-300 font-medium mt-1">Perguruan Tinggi</p>
              </div>
              <div>
                <p class="text-3xl sm:text-4xl font-black text-white tracking-tight flex items-center gap-1.5">
                  <span>5,0</span>
                  <i class="fa-solid fa-star text-amber-400 text-xl sm:text-2xl"></i>
                </p>
                <p class="text-xs text-slate-300 font-medium mt-1">1.139 ulasan Google</p>
              </div>
            </div>
          </div>

          <div class="flex items-start gap-3.5 p-1">
            <div class="w-6 h-6 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
              <i class="fa-solid fa-check text-xs"></i>
            </div>
            <div>
              <h4 class="text-xs sm:text-sm font-bold text-slate-900">Kerahasiaan Data Terjamin</h4>
              <p class="text-xs text-slate-500 leading-relaxed mt-0.5">
                Informasi Anda terlindungi dan hanya digunakan oleh tim kami untuk konfirmasi ketersediaan kuota pelatihan.
              </p>
            </div>
          </div>
        </div>

        <!-- KOLOM KANAN: Stepper Form -->
        <div class="lg:col-span-7">
          <div class="w-full bg-white rounded-3xl sm:rounded-[2.5rem] border border-slate-200/90 p-6 sm:p-10 shadow-card">

            <div class="w-full pb-6 border-b border-slate-100">
              <div class="relative flex items-center justify-between max-w-xl mx-auto px-2">
                <div class="absolute left-6 right-6 top-4 sm:top-5 h-[2px] bg-slate-200 -z-0"></div>

                <div class="relative z-10 flex flex-col items-center text-center" id="stepIndicator1">
                  <div id="stepBadge1" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-accent text-brand-dark font-black text-xs sm:text-sm flex items-center justify-center shadow-md">1</div>
                  <span id="stepLabel1" class="text-[11px] sm:text-xs font-extrabold text-brand-navy mt-2 whitespace-nowrap">Isi Form</span>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center" id="stepIndicator2">
                  <div id="stepBadge2" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border-2 border-slate-300 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center transition-all duration-300">2</div>
                  <span id="stepLabel2" class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-2 whitespace-nowrap transition-all duration-300">Konfirmasi Admin</span>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border-2 border-slate-300 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center">3</div>
                  <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-2 whitespace-nowrap">Invoicing</span>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border-2 border-slate-300 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center">4</div>
                  <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-2 whitespace-nowrap">Grup Pelatihan</span>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border-2 border-slate-300 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center">5</div>
                  <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-2 whitespace-nowrap">Sertifikasi</span>
                </div>
              </div>

              <p id="stepCounterText" class="text-center text-xs font-extrabold uppercase tracking-widest text-brand-blue mt-6">
                STEP 1 OF 5
              </p>
            </div>

            <!-- Form Body: Input View (Step 1) -->
            <form id="regMainForm" onsubmit="handleRegistrationSubmit(event); return false;" class="mt-8 space-y-6">
              <div>
                <label for="regFullName" class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  NAMA LENGKAP <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-user text-sm"></i>
                  </div>
                  <input type="text" id="regFullName" required autocomplete="name" placeholder="Nama lengkap sesuai KTP" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm font-medium text-slate-800 transition">
                </div>
              </div>

              <div>
                <label for="regWhatsApp" class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  NOMOR WHATSAPP <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                  </div>
                  <input type="tel" id="regWhatsApp" required inputmode="tel" autocomplete="tel" placeholder="Contoh: 081234567890" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm font-medium text-slate-800 transition">
                </div>
              </div>

              <div>
                <label for="regSelectedScheme" class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  PAKET YANG DIMINATI <span class="text-slate-400 font-semibold normal-case tracking-normal">(opsional)</span>
                </label>
                <select id="regSelectedScheme" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-xs sm:text-sm font-semibold text-slate-800 bg-white">
                  <option value="Belum tahu, mohon dibantu">Belum tahu, mohon dibantu</option>
                  <?php foreach ($packages as $tier => [$_n, $price]): ?>
                  <optgroup label="<?= ucfirst($tier) ?> Package (<?= $rp($price) ?>)<?= $tier === 'full' ? ' · dengan training kit' : '' ?>">
                    <?php foreach ($participantTypes as $who => $whoLabel): $lbl = $packageLabel($tier, $who); ?>
                      <option value="<?= $h($lbl) ?>" data-pkg="<?= $tier ?>-<?= $who ?>"><?= ucfirst($tier) ?> · <?= $h($whoLabel) ?> (<?= $rp($price) ?>)</option>
                    <?php endforeach; ?>
                  </optgroup>
                  <?php endforeach; ?>
                  <option value="<?= $h($groupLabel) ?>" data-pkg="rombongan">Rombongan perusahaan (3+ orang)</option>
                </select>
              </div>

              <div class="pt-2">
                <button type="submit" id="regSubmitBtn" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl transition duration-200 flex items-center justify-center gap-3 shadow-lg cursor-pointer">
                  <i class="fa-brands fa-whatsapp text-xl"></i>
                  <span id="regSubmitBtnText">Lanjut ke WhatsApp</span>
                </button>
                <p class="text-[11px] text-slate-400 text-center mt-3">Data hanya dipakai untuk informasi pelatihan · <a href="../kebijakan-privasi/" class="underline hover:text-brand-blue">Kebijakan Privasi</a></p>
              </div>
            </form>

            <!-- Success View: Tampil Otomatis Setelah Submit -->
            <div id="regSuccessView" style="display: none;" class="mt-8 text-center animate-fade-in">
              <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-5 shadow-inner">
                <i class="fa-solid fa-circle-check text-3xl sm:text-4xl text-emerald-500"></i>
              </div>

              <span class="inline-block bg-brand-accent/30 text-brand-dark font-extrabold text-[11px] uppercase tracking-wider px-3.5 py-1 rounded-full mb-2">
                DATA BERHASIL TERCATAT
              </span>

              <h3 class="text-2xl sm:text-3xl font-black text-brand-navy tracking-tight mb-3">
                Pendaftaran Berhasil Disimpan!
              </h3>

              <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto mb-6 leading-relaxed">
                Terima kasih, data Anda sudah kami terima. Kami arahkan ke WhatsApp admin untuk konfirmasi kuota, jadwal batch, dan persyaratan. Jika tidak terbuka otomatis, klik tombol di bawah.
              </p>

              <!-- Ringkasan Bersih: Hanya Nama, WhatsApp, dan Skema -->
              <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 sm:p-5 text-left text-xs sm:text-sm mb-6 max-w-lg mx-auto space-y-2.5">
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                  <span class="text-slate-500 font-medium">Nama Peserta:</span>
                  <span id="summaryName" class="font-extrabold text-brand-navy text-right"></span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                  <span class="text-slate-500 font-medium">WhatsApp:</span>
                  <span id="summaryPhone" class="font-extrabold text-brand-navy text-right"></span>
                </div>
                <div class="flex items-center justify-between pt-0.5">
                  <span class="text-slate-500 font-medium">Paket Dipilih:</span>
                  <span id="summaryScheme" class="font-extrabold text-brand-blue text-right"></span>
                </div>
              </div>

              <!-- Tombol Utama: CHAT WA ADMIN RESMI 081337261261 -->
              <div class="max-w-lg mx-auto space-y-3">
                <a
                  id="directWaBtn"
                  href="https://wa.me/6281337261261"
                  target="_blank"
                  class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm sm:text-base py-4 px-6 rounded-2xl transition duration-200 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl cursor-pointer group"
                >
                  <i class="fa-brands fa-whatsapp text-2xl group-hover:scale-110 transition-transform"></i>
                  <span>Chat WhatsApp Admin Sekarang</span>
                  <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>

                <button
                  type="button"
                  onclick="resetRegistrationForm()"
                  class="text-xs text-slate-500 hover:text-brand-navy font-semibold underline transition py-2 cursor-pointer"
                >
                  Daftarkan Peserta Lain / Isi Ulang Formulir
                </button>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="faq" class="w-full bg-slate-50/70 py-16 sm:py-24 border-t border-slate-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
        <div class="flex items-center justify-center gap-2 mb-3">
          <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
          <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
            FREQUENTLY ASKED QUESTIONS
          </span>
        </div>

        <h2 class="hero-title text-3xl sm:text-4xl lg:text-5xl text-brand-navy mb-4 tracking-tight">
          Pertanyaan yang Sering Diajukan
        </h2>

        <p class="text-xs sm:text-sm md:text-base text-slate-600 font-normal leading-relaxed max-w-2xl mx-auto mb-6">
          Pahami seluruh informasi terkait syarat, sertifikat kompetensi BNSP, alur pelatihan dan uji kompetensi, hingga kemudahan konsultasi bersama <strong class="text-brand-navy font-extrabold">Artha Safety Indonesia</strong>.
        </p>

        <button
          type="button"
          onclick="toggleAllFaqs()"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-blue-50/70 text-brand-blue border border-blue-200 text-xs sm:text-sm font-bold shadow-xs hover:shadow-sm transition-all duration-200 cursor-pointer"
        >
          <i id="toggleAllFaqIcon" class="fa-solid fa-chevron-down text-xs transition-transform duration-200"></i>
          <span id="toggleAllFaqText">Buka Semua Jawaban</span>
        </button>
      </div>

      <!-- Grid 2 Kolom FAQ -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 max-w-6xl mx-auto">
        <?php foreach (array_chunk($faqs, (int) ceil(count($faqs) / 2), true) as $col): ?>
        <div class="space-y-4">
          <?php foreach ($col as $i => [$q, $a]): $n = $i + 1; ?>
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(<?= $n ?>)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100"><?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition"><?= $q ?></span>
              </div>
              <i id="faq-icon-<?= $n ?>" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-<?= $n ?>" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              <?= $a ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <section class="w-full bg-white border-t border-slate-100 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row items-center lg:items-end justify-between gap-0 lg:gap-10">

        <!-- Sisi Kiri: Teks CTA -->
        <div class="max-w-xl lg:max-w-2xl text-left pt-12 pb-4 lg:py-16 w-full">
          <div class="flex items-center gap-2.5 mb-4">
            <span class="w-7 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-widest text-brand-blue">
              JOIN THE COMMUNITY
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl lg:text-5xl text-brand-navy mb-4 tracking-tight leading-tight">
            Bukan Sekadar Pelatihan, Tapi Juga Jaringan Karier.
          </h2>

          <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed mb-6 lg:mb-0">Bergabung dengan 4.000+ alumni pelatihan K3 dari 100+ perusahaan dan 50+ perguruan tinggi di seluruh Indonesia. Di komunitas alumni, Anda bisa berbagi info lowongan, bertukar pengalaman, dan terus belajar bersama praktisi K3
          </p>

          <!-- Tombol Mobile -->
          <div class="lg:hidden pt-1 pb-4">
            <button
              type="button"
              onclick="openModal('advisorModal')"
              class="inline-flex items-center gap-2.5 bg-brand-navy hover:bg-brand-blue text-white font-extrabold text-sm px-7 py-3.5 rounded-2xl shadow-xl transition-all duration-200 cursor-pointer"
            >
              <span>Chat with us</span>
              <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </button>
          </div>
        </div>

        <!-- Sisi Kanan: Ilustrasi cta.png Menempel di Dasar & Tombol Desktop -->
        <div class="flex items-end justify-center lg:justify-end gap-6 sm:gap-8 shrink-0 w-full lg:w-auto self-end">

          <div class="flex items-end justify-center shrink-0 -mb-[1px]">
            <img loading="lazy" decoding="async"
              src="../assets/img/komunitas.jpg"
              alt="Safety Champions Community Artha Safety"
              class="h-72 sm:h-84 md:h-96 lg:h-[390px] xl:h-[440px] w-auto object-contain object-bottom block drop-shadow-sm transition-transform duration-300 hover:scale-[1.02]"
              onerror="this.onerror=null; this.src='https://placehold.co/320x440/071C6E/ffffff?text=Community+Artha+Safety';"
            >
          </div>

          <div class="hidden lg:flex pb-10 lg:pb-16 shrink-0 items-center">
            <button
              type="button"
              onclick="openModal('advisorModal')"
              class="inline-flex items-center gap-2.5 bg-brand-navy hover:bg-brand-blue text-white font-extrabold text-sm sm:text-base px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer whitespace-nowrap shrink-0"
            >
              <span>Chat with us</span>
              <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </button>
          </div>
        </div>

      </div>
    </div>
  </section>

  <footer class="w-full bg-[#FAF6EE] border-t border-[#EDE4D3] pt-16 sm:pt-20 pb-20 sm:pb-12 text-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-12 pb-14 border-b border-[#E5DAC6]">

        <!-- KOLOM 1: Logo, Deskripsi & Dilisensikan Oleh (Tanpa Box Putih) -->
        <div class="lg:col-span-4 flex flex-col justify-between space-y-6">
          <div class="space-y-4">
            <a href="#" class="inline-block">
              <img
                src="../assets/img/logo-artha.png"
                alt="PT Artha Safety Indonesia"
                class="h-10 sm:h-11 w-auto object-contain"
                onerror="this.onerror=null; this.src='https://placehold.co/200x50/071C6E/ffffff?text=ARTHA+SAFETY';"
              >
            </a>

            <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal max-w-sm">
              PT Artha Safety Indonesia adalah PJK3 resmi Kementerian Ketenagakerjaan RI dan Tempat Uji Kompetensi (TUK) LSP BNSP, berkomitmen mencetak profesional K3 berdaya saing global di industri.
            </p>

            <div class="pt-3">
              <h5 class="text-[11px] font-black uppercase tracking-wider text-brand-navy mb-3 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-brand-accent"></span>
                <span>DILISENSIKAN OLEH:</span>
              </h5>

              <!-- LOGO RESMI KEMNAKER RI & BNSP TANPA BACKGROUND PUTIH -->
              <div class="flex flex-wrap items-center gap-5">
                <img loading="lazy" decoding="async"
                  src="../assets/img/logo-kemnaker.png" width="205" height="84" alt="Kementerian Ketenagakerjaan RI"
                  class="h-8 sm:h-9 w-auto max-w-[150px] object-contain brightness-0 opacity-80 hover:opacity-100 transition"
                  
                >

                <img loading="lazy" decoding="async"
                  src="../assets/img/logo-bnsp.png" width="300" height="114" alt="BNSP - Badan Nasional Sertifikasi Profesi"
                  class="h-7 sm:h-8 w-auto max-w-[110px] object-contain opacity-85 hover:opacity-100 transition"
                  
                >
              </div>
            </div>
          </div>

          <p class="text-[11px] sm:text-xs text-slate-500 font-medium">
            &copy; 2026 PT Artha Safety Indonesia. All rights reserved.
          </p>
        </div>

        <!-- KOLOM 2: PROGRAM PELATIHAN -->
        <div class="lg:col-span-3">
          <h4 class="text-xs sm:text-sm font-black text-brand-navy tracking-wider uppercase mb-5">
            PROGRAM PELATIHAN
          </h4>
          <ul class="space-y-3 text-xs sm:text-[13px] font-semibold text-slate-600">
            <li><a href="#pricing" class="hover:text-brand-blue transition">Ahli K3 Umum BNSP (Online)</a></li>
            <li><a href="../ahli-k3-umum/" class="hover:text-brand-blue transition">Ahli K3 Umum Kemnaker RI</a></li>
            <li><a href="#registration" onclick="pickPackage('full-utusan')" class="hover:text-brand-blue transition">Utusan &amp; Rombongan Perusahaan</a></li>
            <li><button type="button" onclick="openModal('advisorModal')" class="hover:text-brand-blue transition text-left cursor-pointer">Corporate In-House</button></li>
          </ul>
        </div>

        <!-- KOLOM 3: QUICK LINKS -->
        <div class="lg:col-span-2">
          <h4 class="text-xs sm:text-sm font-black text-brand-navy tracking-wider uppercase mb-5">
            QUICK LINKS
          </h4>
          <ul class="space-y-3 text-xs sm:text-[13px] font-semibold text-slate-600">
            <li><a href="#description" class="hover:text-brand-blue transition">Description</a></li>
            <li><a href="#curriculum" class="hover:text-brand-blue transition">Curriculum</a></li>
            <li><a href="#portfolio" class="hover:text-brand-blue transition">Legality</a></li>
            <li><a href="#certification" class="hover:text-brand-blue transition">Certification</a></li>
            <li><a href="#facilities" class="hover:text-brand-blue transition">Facility</a></li>
            <li><a href="#reviews" class="hover:text-brand-blue transition">Review Clients</a></li>
            <li><a href="#documentation" class="hover:text-brand-blue transition">Dokumentasi</a></li>
            <li><a href="#pricing" class="hover:text-brand-blue transition">Pricing</a></li>
            <li><a href="#faq" class="hover:text-brand-blue transition">FAQ</a></li>
          </ul>
        </div>

        <!-- KOLOM 4: LOKASI & KONTAK KAMI -->
        <div class="lg:col-span-3 space-y-5">
          <h4 class="text-xs sm:text-sm font-black text-brand-navy tracking-wider uppercase mb-4">
            LOKASI & KONTAK KAMI
          </h4>

          <div class="space-y-3.5 text-xs sm:text-[13px] text-slate-600">
            <div class="flex items-start gap-3">
              <i class="fa-solid fa-location-dot text-brand-blue text-sm mt-0.5 shrink-0"></i>
              <span class="leading-relaxed">
                Ruko De Mercy Jl. Kapten Haryadi No.8, Dayakan, Sardonoharjo, Kec. Ngaglik, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55581
              </span>
            </div>

            <div class="flex items-center gap-3">
              <i class="fa-solid fa-envelope text-brand-blue text-sm shrink-0"></i>
              <a href="mailto:marketing.arthasafetyindonesia@gmail.com" class="hover:text-brand-blue transition break-all font-semibold">
                marketing.arthasafetyindonesia@gmail.com
              </a>
            </div>

            <div class="flex items-center gap-3">
              <i class="fa-solid fa-phone text-brand-blue text-sm shrink-0"></i>
              <a href="tel:02745063025" class="hover:text-brand-blue transition font-bold text-slate-800">
                (0274) 5063025
              </a>
            </div>
          </div>

          <!-- IKUTI MEDIA SOSIAL KAMI DENGAN URL LENGKAP -->
          <div class="pt-2">
            <h5 class="text-[11px] font-black uppercase tracking-wider text-brand-navy mb-3">
              IKUTI MEDIA SOSIAL KAMI:
            </h5>

            <div class="flex items-center gap-2.5">
              <a href="https://www.facebook.com/people/Artha-Safety-Indonesia/61552793712859/?mibextid=eQY6cl&rdid=3Ab11hUrQkXNnRlw&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2FFpW7Z8JZJki9bFUk%2F%3Fmibextid%3DeQY6cl" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-brand-navy hover:bg-brand-blue text-white flex items-center justify-center text-xs transition duration-200 shadow-xs" aria-label="Facebook Artha Safety">
                <i class="fa-brands fa-facebook-f"></i>
              </a>

              <a href="https://www.linkedin.com/company/pt-artha-safety-indonesia/" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-brand-navy hover:bg-brand-blue text-white flex items-center justify-center text-xs transition duration-200 shadow-xs" aria-label="LinkedIn Artha Safety">
                <i class="fa-brands fa-linkedin-in"></i>
              </a>

              <a href="https://www.instagram.com/arthasafety.id/" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-brand-navy hover:bg-brand-blue text-white flex items-center justify-center text-xs transition duration-200 shadow-xs" aria-label="Instagram Artha Safety">
                <i class="fa-brands fa-instagram"></i>
              </a>

              <a href="https://www.tiktok.com/@arthasafety?_t=8klv13nR4QD&_r=1" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-brand-navy hover:bg-brand-blue text-white flex items-center justify-center text-xs transition duration-200 shadow-xs" aria-label="TikTok Artha Safety">
                <i class="fa-brands fa-tiktok"></i>
              </a>

              <a href="https://www.youtube.com/@arthasafetyindonesia" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-brand-navy hover:bg-brand-blue text-white flex items-center justify-center text-xs transition duration-200 shadow-xs" aria-label="YouTube Artha Safety">
                <i class="fa-brands fa-youtube"></i>
              </a>
            </div>
          </div>
        </div>

      </div>

      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] sm:text-xs text-slate-500">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-brand-accent animate-pulse"></span>
          <span>TUK Resmi LSP BNSP · PJK3 Resmi Kementerian Ketenagakerjaan RI</span>
        </div>
        <div class="flex items-center gap-5 font-semibold">
          <a href="../kebijakan-privasi/" class="hover:text-brand-blue transition">Kebijakan Privasi</a>
        </div>
      </div>

    </div>
  </footer>

  <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_25px_rgba(7,28,110,0.12)] px-4 py-3 flex items-center justify-between">
    <div class="flex flex-col">
      <span class="text-xs sm:text-sm font-black text-brand-navy leading-tight">Batch <?= $h($batchShort) ?><?= $hasQuota && !$quotaFull ? ' · <span class="text-red-600">sisa ' . $quotaLeft . ' kursi</span>' : '' ?></span>
      <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Mulai <?php if ($fromNormal): ?><span class="line-through text-red-500"><?= $jt($fromNormal) ?></span> <?php endif; ?><span class="text-brand-navy font-extrabold">Rp <?= $jt($fromPromo) ?></span></span>
    </div>

    <a href="#registration" class="bg-brand-accent hover:bg-brand-accentHover active:scale-95 text-brand-dark font-black text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-lime flex items-center gap-2 transition duration-150">
      <i class="fa-solid fa-lock text-[10px]"></i>
      <span>Amankan Kursi</span>
    </a>
  </div>

  <div
    id="videoModal"
    style="display: none;"
    class="fixed inset-0 z-50 items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-6"
    onclick="closeVideoModal()"
    role="dialog"
    aria-modal="true"
    aria-label="Pemutar Video Pelatihan Ahli K3 Umum Artha Safety"
  >
    <div class="relative w-full max-w-4xl bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 shadow-2xl transition-all" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-2.5 px-1">
        <div class="flex items-center gap-2 text-brand-navy text-xs sm:text-sm font-extrabold truncate">
          <span class="w-2 h-2 rounded-full bg-brand-accent"></span>
          <span class="truncate">PT Artha Safety Indonesia - Pelatihan Ahli K3 Umum</span>
        </div>

        <button
          type="button"
          onclick="closeVideoModal()"
          class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white shadow-md border border-slate-200/90 flex items-center justify-center text-brand-navy hover:text-brand-blue hover:bg-slate-50 transition font-black text-xs sm:text-sm cursor-pointer ml-2 flex-shrink-0"
          aria-label="Tutup Video"
        >
          ✕
        </button>
      </div>

      <div class="w-full aspect-video rounded-xl sm:rounded-2xl overflow-hidden bg-black shadow-inner">
        <iframe
          id="videoIframe"
          class="w-full h-full"
          src=""
          title="Video Pelatihan Ahli K3 Umum - PT Artha Safety Indonesia"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen
        ></iframe>
      </div>
    </div>
  </div>

  <div
    id="advisorModal"
    style="display: none;"
    class="fixed inset-0 z-50 items-center justify-center bg-brand-navy/80 backdrop-blur-sm p-4"
    onclick="handleAdvisorBackdropClick(event)"
    role="dialog"
    aria-modal="true"
  >
    <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100" onclick="event.stopPropagation()">
      <button type="button" onclick="closeModal('advisorModal')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
        <i class="fa-solid fa-xmark"></i>
      </button>

      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-brand-subtle text-brand-blue flex items-center justify-center text-xl font-bold">
          <i class="fa-solid fa-headset"></i>
        </div>
        <div>
          <h3 class="text-lg font-extrabold text-brand-navy">Konsultasi Advisor K3</h3>
          <p class="text-xs text-slate-500 font-medium">Layanan Informasi Sertifikasi BNSP</p>
        </div>
      </div>

      <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
        Silakan isi data singkat berikut untuk terhubung langsung dengan Tim Advisor Ahli K3 Umum PT Artha Safety Indonesia via WhatsApp.
      </p>

      <form onsubmit="handleAdvisorSubmit(event)" class="space-y-4 text-xs sm:text-sm">
        <div>
          <label class="block text-slate-700 font-bold mb-1">Nama Lengkap *</label>
          <input type="text" id="advName" required placeholder="Nama Anda" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy">
        </div>
        <div>
          <label class="block text-slate-700 font-bold mb-1">Jenis Peserta *</label>
          <select id="advStatus" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy bg-white">
            <option value="Peserta pribadi">Peserta pribadi</option>
            <option value="Utusan perusahaan">Utusan perusahaan</option>
            <option value="Rombongan perusahaan (3+ orang)">Rombongan perusahaan (3+ orang)</option>
            <option value="Belum tahu">Belum tahu, mohon dibantu</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-700 font-bold mb-1">Pertanyaan / Topik Konsultasi</label>
          <textarea id="advNotes" rows="2" placeholder="Tanyakan info jadwal batch, persyaratan, APL 01/02, atau sistem invoice..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy"></textarea>
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-md cursor-pointer">
          <i class="fa-brands fa-whatsapp text-base"></i>
          <span>Hubungi via WhatsApp</span>
        </button>
      </form>
    </div>
  </div>

  <script>
    // Endpoint penyimpanan pendaftaran di hosting sendiri (lihat dashboard di /admin)
    var SPREADSHEET_WEBHOOK_URL = '../api/submit.php';
    var LANDING_PROGRAM = <?= json_encode($PROGRAM) ?>;

    // Sumber kunjungan dari iklan/link (parameter utm_* & ad_id di link iklan), disimpan selama sesi
    var trafficSource = (function() {
      var keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'ad_id'];
      var src = {};
      keys.forEach(function(k) { src[k] = ''; });
      try {
        var params = new URLSearchParams(window.location.search);
        var saved = JSON.parse(sessionStorage.getItem('artha_utm') || '{}');
        // Jika datang dari link iklan baru, pakai semua parameter dari link itu.
        var fromUrl = keys.some(function(k) { return params.get(k); });
        keys.forEach(function(k) { src[k] = (fromUrl ? params.get(k) : saved[k]) || ''; });
        sessionStorage.setItem('artha_utm', JSON.stringify(src));
      } catch (err) {}
      return src;
    })();

    // Catat kunjungan landing page untuk statistik di dashboard
    (function() {
      try {
        var vid = localStorage.getItem('artha_vid');
        if (!vid) {
          vid = Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 12);
          localStorage.setItem('artha_vid', vid);
        }
        var body = JSON.stringify({ program: LANDING_PROGRAM, vid: vid, ref: document.referrer, utm_source: trafficSource.utm_source, utm_campaign: trafficSource.utm_campaign, utm_content: trafficSource.utm_content, ad_id: trafficSource.ad_id });
        fetch('../api/track.php', { method: 'POST', headers: { 'Content-Type': 'text/plain;charset=utf-8' }, body: body, keepalive: true }).catch(function(){});
      } catch (err) {}
    })();

    // 10 Foto Dokumentasi Resmi K3 PT Artha Safety Indonesia
    var docPhotosList = [
      {
        url: "../assets/img/dok-graduation.jpg",
        title: "Graduation",
        category: "Ahli K3 Umum Kemnaker",
        badge: "Ujian Seminar"
      },
      {
        url: "../assets/img/dok-observasi-industri.jpg",
        title: "Observasi Industri",
        category: "Praktek Kerja Lapangan (PKL)",
        badge: "Kunjungan Pabrik AK3U Yogyakarta"
      },
      {
        url: "../assets/img/dok-fire-fighting.jpg",
        title: "Simulasi Evakuasi & Fire Fighting",
        category: "Inhouse Training",
        badge: "Instruktur Damkar Kota"
      },
      {
        url: "../assets/img/dok-p3k.jpg",
        title: "Praktek P3K",
        category: "Inhouse Training",
        badge: "Instruktur PMI"
      },
      {
        url: "../assets/img/dok-observasi-adi-satria.jpg",
        title: "Observasi Industri",
        category: "PRaktek Kerja Lapangan (PKL)",
        badge: "PT Adi Satria Abadi"
      },
      {
        url: "../assets/img/dok-ujian-teori-1.jpg",
        title: "Ujian Teori",
        category: "AK3U Kemnaker RI",
        badge: "Offline TemanK3"
      },
      {
        url: "../assets/img/dok-ujian-teori-2.jpg",
        title: "Ujian Teori",
        category: "AK3U Kemnaker RI",
        badge: "Offline TemanK3"
      },
      {
        url: "../assets/img/dok-pertamina.jpg",
        title: "PT Pertamina",
        category: "Sertifikasi K3 Migas",
        badge: "Inhouse Training"
      },
      {
        url: "../assets/img/dok-materi-kelas.jpg",
        title: "Materi Kelas",
        category: "Bimbingan Mentor Praktisi",
        badge: "Offline Training"
      }
    ];

    // Status Rotator Slide Up Cepat Bergantian Satu per Satu
    var currentNextPhotoIndex = 4;
    var currentSlotToSlide = 0;
    var docRotatorTimer = null;

    function slideUpNextDocCard() {
      var slotEl = document.getElementById('docSlot' + currentSlotToSlide);
      if (!slotEl) return;

      var nextItem = docPhotosList[currentNextPhotoIndex];
      currentNextPhotoIndex = (currentNextPhotoIndex + 1) % docPhotosList.length;

      // Buat Layer Kartu Baru yang Meluncur Masuk dari Bawah
      var newLayer = document.createElement('div');
      newLayer.className = 'doc-card-layer absolute inset-0 w-full h-full animate-slide-in-up z-10';
      newLayer.innerHTML =
        '<img loading="lazy" decoding="async" src="' + nextItem.url + '" alt="' + nextItem.title + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src=\'https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3\';">' +
        '<div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>' +
        '<div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">' +
          '<span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">' +
            nextItem.badge +
          '</span>' +
          '<h3 class="text-base sm:text-lg font-black text-white leading-tight">' +
            nextItem.title +
          '</h3>' +
          '<p class="text-xs text-slate-300 font-medium mt-1 leading-snug">' +
            nextItem.category +
          '</p>' +
        '</div>';

      var oldLayer = slotEl.querySelector('.doc-card-layer');
      slotEl.appendChild(newLayer);

      if (oldLayer) {
        oldLayer.classList.add('animate-slide-out-up');
        setTimeout(function() {
          if (oldLayer && oldLayer.parentNode === slotEl) {
            slotEl.removeChild(oldLayer);
          }
          newLayer.classList.remove('animate-slide-in-up', 'z-10');
        }, 460);
      } else {
        newLayer.classList.remove('animate-slide-in-up', 'z-10');
      }

      // Berganti satu per satu ke slot berikutnya (0 -> 1 -> 2 -> 3 -> 0)
      currentSlotToSlide = (currentSlotToSlide + 1) % 4;
    }

    function startDocRotator() {
      if (docRotatorTimer) clearInterval(docRotatorTimer);
      // Berganti cepat satu per satu setiap 1.4 detik
      docRotatorTimer = setInterval(slideUpNextDocCard, 1400);
    }

    function pauseDocRotator() {
      if (docRotatorTimer) clearInterval(docRotatorTimer);
    }

    document.addEventListener('DOMContentLoaded', function() {
      var galleryBox = document.getElementById('docGalleryContainer');
      if (galleryBox) {
        galleryBox.addEventListener('mouseenter', pauseDocRotator);
        galleryBox.addEventListener('mouseleave', startDocRotator);
      }
      startDocRotator();
    });

    // Pilih paket dari kartu harga → otomatis terpilih di form
    function pickPackage(key) {
      var sel = document.getElementById('regSelectedScheme');
      if (!sel) return;
      for (var i = 0; i < sel.options.length; i++) {
        if (sel.options[i].getAttribute('data-pkg') === key) sel.selectedIndex = i;
      }
    }

    // Hitung mundur ke hari pertama pelatihan
    (function () {
      var box = document.querySelector('[data-countdown]');
      if (!box) return;
      var target = +box.getAttribute('data-countdown');
      function tick() {
        var sec = Math.max(0, Math.floor((target - Date.now()) / 1000));
        var v = { d: Math.floor(sec / 86400), h: Math.floor(sec % 86400 / 3600), m: Math.floor(sec % 3600 / 60), s: sec % 60 };
        Object.keys(v).forEach(function (k) { var el = box.querySelector('[data-cd="' + k + '"]'); if (el) el.textContent = String(v[k]).padStart(2, '0'); });
      }
      tick(); setInterval(tick, 1000);
    })();

    function handleRegistrationSubmit(event) {
      if (event) { event.preventDefault(); event.stopPropagation(); }
      var name = document.getElementById('regFullName').value.trim();
      var phone = document.getElementById('regWhatsApp').value.trim();
      var scheme = document.getElementById('regSelectedScheme').value;
      if (!name || !phone) return false;

      var message = 'Halo PT Artha Safety Indonesia, saya ingin daftar Pelatihan Ahli K3 Umum BNSP (Online via Zoom).\n\n' +
        '*Nama:* ' + name + '\n' +
        '*No. WhatsApp:* ' + phone + '\n' +
        '*Paket:* ' + scheme + '\n' +
        '*Batch:* ' + <?= json_encode($batchLong) ?> + '\n\n' +
        'Mohon info kuota dan langkah selanjutnya. Terima kasih!';
      var waUrl = 'https://wa.me/<?= $waNumber ?>?text=' + encodeURIComponent(message);

      try { if (typeof fbq === 'function') fbq('track', 'Lead'); } catch (pixelErr) {}

      var payload = {
        program: LANDING_PROGRAM, name: name, phone: phone, email: '', scheme: scheme,
        utm_source: trafficSource.utm_source, utm_medium: trafficSource.utm_medium, utm_campaign: trafficSource.utm_campaign,
        utm_term: trafficSource.utm_term, utm_content: trafficSource.utm_content, ad_id: trafficSource.ad_id
      };
      try {
        fetch(SPREADSHEET_WEBHOOK_URL, { method: 'POST', keepalive: true, headers: { 'Content-Type': 'text/plain;charset=utf-8' }, body: JSON.stringify(payload) }).catch(function () {});
      } catch (e) {}

      var formEl = document.getElementById('regMainForm');
      var successView = document.getElementById('regSuccessView');
      if (formEl) formEl.style.setProperty('display', 'none', 'important');
      if (successView) successView.style.setProperty('display', 'block', 'important');
      var sumName = document.getElementById('summaryName'); if (sumName) sumName.innerText = name;
      var sumPhone = document.getElementById('summaryPhone'); if (sumPhone) sumPhone.innerText = phone;
      var sumScheme = document.getElementById('summaryScheme'); if (sumScheme) sumScheme.innerText = scheme;
      var directWaBtn = document.getElementById('directWaBtn'); if (directWaBtn) directWaBtn.href = waUrl;

      var stepBadge1 = document.getElementById('stepBadge1');
      if (stepBadge1) {
        stepBadge1.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-500 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md';
        stepBadge1.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
      }
      var stepBadge2 = document.getElementById('stepBadge2');
      if (stepBadge2) stepBadge2.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-accent text-brand-dark font-black text-xs sm:text-sm flex items-center justify-center shadow-md';
      var stepLabel2 = document.getElementById('stepLabel2');
      if (stepLabel2) stepLabel2.className = 'text-[11px] sm:text-xs font-extrabold text-brand-navy mt-2 whitespace-nowrap';
      var stepCounter = document.getElementById('stepCounterText');
      if (stepCounter) stepCounter.innerText = 'STEP 2 OF 5: KONFIRMASI ADMIN';
      if (successView) successView.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

      // Langsung buka WhatsApp admin; tombol tetap tersedia jika browser memblokir.
      setTimeout(function () { window.location.href = waUrl; }, 1500);
      return false;
    }

    function resetRegistrationForm() {
      var formEl = document.getElementById('regMainForm');
      var successView = document.getElementById('regSuccessView');
      if (formEl) { formEl.reset(); formEl.style.setProperty('display', 'block', 'important'); }
      if (successView) successView.style.setProperty('display', 'none', 'important');
      var stepBadge1 = document.getElementById('stepBadge1');
      if (stepBadge1) { stepBadge1.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-accent text-brand-dark font-black text-xs sm:text-sm flex items-center justify-center shadow-md'; stepBadge1.innerText = '1'; }
      var stepBadge2 = document.getElementById('stepBadge2');
      if (stepBadge2) { stepBadge2.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border-2 border-slate-300 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center'; stepBadge2.innerText = '2'; }
      var stepLabel2 = document.getElementById('stepLabel2');
      if (stepLabel2) stepLabel2.className = 'text-[11px] sm:text-xs font-semibold text-slate-400 mt-2 whitespace-nowrap';
      var stepCounter = document.getElementById('stepCounterText');
      if (stepCounter) stepCounter.innerText = 'STEP 1 OF 5';
    }

    function handleAdvisorBackdropClick(event) {
      var modal = document.getElementById('advisorModal');
      if (event.target === modal) {
        closeModal('advisorModal');
      }
    }

    function handleAdvisorSubmit(event) {
      event.preventDefault();
      var name = document.getElementById('advName').value.trim();
      var status = document.getElementById('advStatus').value;
      var notes = document.getElementById('advNotes').value.trim();

      var message = 'Halo PT Artha Safety Indonesia, saya ingin konsultasi mengenai Pelatihan Ahli K3 Umum BNSP Online.\n\n*Nama:* ' + name + '\n*Jenis Peserta:* ' + status + '\n*Pertanyaan/Catatan:* ' + (notes || '-');
      // Direct Link ke WhatsApp Admin Resmi: 081337261261
      var waUrl = 'https://wa.me/6281337261261?text=' + encodeURIComponent(message);

      window.open(waUrl, '_blank');
      closeModal('advisorModal');
    }

    function toggleMobileMenu() {
      var menu = document.getElementById('mobileMenu');
      if (menu) {
        menu.classList.toggle('hidden');
      }
    }
  </script>
</body>
</html>

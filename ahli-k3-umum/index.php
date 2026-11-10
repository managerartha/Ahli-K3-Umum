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
$batchStartTs = strtotime($batchStart . ' 08:00:00');
$batchStarted = time() >= $batchStartTs;

// ---------- Harga ----------
// Blended: jalur × penginapan × paket, [harga normal, harga sekarang].
$prices = [
    'umum'   => ['non' => ['basic' => [4850000, 3850000], 'full' => [5200000, 4200000]], 'inap' => ['basic' => [5350000, 4350000], 'full' => [5700000, 4700000]]],
    'utusan' => ['non' => ['basic' => [6550000, 5550000], 'full' => [7000000, 6000000]], 'inap' => ['basic' => [7000000, 6000000], 'full' => [7450000, 6450000]]],
];
// Full Offline 12 hari: [harga normal (null = tanpa harga coret), harga sekarang].
$offlinePrices = [
    'umum'   => [null, 5000000],
    'utusan' => [null, 6500000],
];
$packageLabel = function ($cat, $stay, $tier) use ($prices, $rp) {
    return 'Blended · ' . ($cat === 'umum' ? 'Umum' : 'Perusahaan') . ' · ' . ($stay === 'non' ? 'Non penginapan' : 'Penginapan') . ' · ' . ucfirst($tier) . ' (' . $rp($prices[$cat][$stay][$tier][1]) . ')';
};
$offlineLabel = function ($cat) use ($offlinePrices, $rp) {
    return 'Full Offline 12 hari · ' . ($cat === 'umum' ? 'Umum' : 'Perusahaan') . ' (' . $rp($offlinePrices[$cat][1]) . ')';
};
$fromNormal = 4850000;
$fromPromo = 3850000;
$bonusValue = 1250000;

$faqs = [
    ['Saya bukan lulusan K3, apakah bisa ikut?', 'Sangat bisa! Syarat kepesertaan Ahli K3 Umum terbuka untuk minimal D3/D4/S1 dari <strong>semua jurusan</strong> (Teknik, MIPA, Ekonomi, Sosial, Kesehatan, dll.). Seluruh modul disusun dari dasar dan dipandu sampai siap ujian.'],
    ['Saya baru lulus dan ijazah belum keluar. Bisa daftar?', 'Belum bisa. Sesuai regulasi Kemnaker RI, peserta wajib memiliki ijazah asli atau legalisir cap basah, minimal D3 atau S1 semua jurusan. Kamu bisa konsultasi dulu dan mengamankan kursi untuk batch berikutnya.'],
    ['Apa beda skema Blended dan Full Offline?', 'Materi, ujian, sertifikat, dan bonusnya <strong>sama</strong>. Bedanya hanya cara belajar: <strong>Blended</strong> = 9 hari online via Zoom + 3 hari tatap muka di Yogyakarta (hemat waktu & biaya perjalanan). <strong>Full Offline</strong> = 12 hari tatap muka penuh di Hotel Emersia Malioboro Yogyakarta, lebih fokus dan bisa berjejaring langsung dengan peserta lain.'],
    ['Bagaimana kelas online di skema Blended?', 'Hari 1–9 lewat <strong>Live Zoom</strong>, dua sesi setiap hari (08.00–12.00 dan 13.00–16.00 WIB). Siapkan laptop/tablet dan internet yang stabil. Hari 10–12 tatap muka untuk PKL, seminar, dan ujian.'],
    ['Saya tinggal di luar Jogja, bagaimana?', 'Mayoritas peserta kami dari luar kota. Dengan skema <strong>Blended</strong>, kamu cukup 3 hari ke Yogyakarta dan tersedia paket <strong>dengan penginapan</strong>. Untuk <strong>Full Offline</strong>, tim kami siap membantu koordinasi penginapan selama 12 hari.'],
    ['Apakah harus bayar lunas di awal?', 'Tidak. Cukup <strong>bayar DP untuk mengamankan kursi</strong>, pelunasan bisa di hari pertama pelatihan.'],
    ['Apa beda jalur Umum dan Utusan Perusahaan?', 'Jalur Umum mendapat Sertifikat Calon Ahli K3 Umum Kemnaker RI. Jalur Utusan Perusahaan mendapat Sertifikat, <strong>SKP, dan Lisensi</strong> Ahli K3 Umum Kemnaker RI (wajib surat keterangan bekerja dari perusahaan).'],
    ['Apa beda paket Basic dan Full (skema Blended)?', 'Keduanya mendapat sertifikat resmi dan semua bonus. Paket <strong>Full</strong> ditambah modul cetak, himpunan perundang-undangan K3, dan training kit (tumbler, kemeja & t-shirt safety, ransel, blocknote, pulpen).'],
    ['Apakah sertifikatnya resmi?', '<strong>100% resmi dan sah secara hukum.</strong> Sertifikat, SKP, dan Lisensi diterbitkan Direktorat Bina Kelembagaan K3, Ditjen Binwasnaker & K3 Kemnaker RI, dengan nomor registrasi yang bisa dicek di TemanK3.'],
    ['Berapa lama sertifikatnya berlaku?', 'Sertifikat Ahli K3 Umum berlaku <strong>seumur hidup</strong>. SKP dan Lisensi (utusan perusahaan) berlaku 3 tahun dan dapat diperpanjang melalui PJK3 resmi.'],
    ['Bagaimana kalau nilai ujian teori belum lulus?', 'Peserta mendapat satu kali kesempatan remedial. Mentor juga membimbing persiapan ujian sejak sesi online.'],
    ['Apakah bisa mengikuti jadwal beberapa bulan lagi?', 'Bisa sekali! Batch dibuka setiap bulan. Kamu bisa mengunci kursi untuk batch berikutnya cukup dengan DP.'],
    ['Apakah perusahaan bisa mendaftarkan karyawan?', 'Sangat bisa. Kami melayani jalur <strong>Utusan Perusahaan</strong> lengkap dengan SKP & Lisensi, faktur pajak resmi, invoice korporat, hingga program <em>In-House Training</em> di lokasi perusahaan.'],
    ['Bagaimana kalau saya masih harus meminta izin orang tua?', 'Sangat kami dukung. Hubungkan orang tua dengan Advisor kami untuk penjelasan lengkap terkait legalitas Kemnaker RI, fasilitas training, dan prospek karier HSE.'],
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
  <title>Pelatihan Ahli K3 Umum Kemnaker RI Yogyakarta (Blended &amp; Full Offline) - PT Artha Safety Indonesia</title>
  <meta name="description" content="Pelatihan & sertifikasi Ahli K3 Umum Kemnaker RI di Yogyakarta. Pilih Blended (9 hari online + 3 hari tatap muka) atau Full Offline 12 hari. Batch <?= $h($batchLong) ?>. Mulai <?= $rp($fromPromo) ?>, cukup DP.">
  <link rel="canonical" href="<?= $h($pageUrl) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="PT Artha Safety Indonesia">
  <meta property="og:title" content="Pelatihan Ahli K3 Umum Kemnaker RI · Batch <?= $h($batchShort) ?>">
  <meta property="og:description" content="Pilih Blended (9 hari online + 3 tatap muka) atau Full Offline 12 hari di Yogyakarta. Sertifikat resmi Kemnaker RI, gratis Basic Safety Training & 43 e-sertifikat HSE. Mulai Rp <?= $jt($fromPromo) ?>, cukup DP.">
  <meta property="og:url" content="<?= $h($pageUrl) ?>">
  <meta property="og:image" content="<?= $h($siteBase) ?>/assets/img/dok-graduation.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="theme-color" content="#071C6E">
  <link rel="icon" href="../assets/img/logo-artha.png">

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
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    // Pilihan harga: skema (blended/offline) × jalur (umum/utusan) × penginapan (non/inap, khusus Blended)
    var pricingState = { scheme: 'blended', cat: 'umum', stay: 'non' };
    function setPricing(scheme, cat, stay) {
      if (scheme) pricingState.scheme = scheme;
      if (cat) pricingState.cat = cat;
      if (stay) pricingState.stay = stay;
      var key = pricingState.scheme === 'offline'
        ? 'offline-' + pricingState.cat
        : 'blended-' + pricingState.cat + '-' + pricingState.stay;
      document.querySelectorAll('[data-price-grid]').forEach(function (g) {
        var on = g.getAttribute('data-price-grid') === key;
        g.classList.toggle('hidden', !on); g.classList.toggle('grid', on);
      });
      document.querySelectorAll('[data-pick-scheme]').forEach(function (b) {
        var on = b.getAttribute('data-pick-scheme') === pricingState.scheme;
        b.classList.toggle('scheme-on', on); b.classList.toggle('scheme-off', !on);
      });
      document.querySelectorAll('[data-pick-cat]').forEach(function (b) {
        var on = b.getAttribute('data-pick-cat') === pricingState.cat;
        b.classList.toggle('pill-on', on); b.classList.toggle('pill-off', !on);
      });
      document.querySelectorAll('[data-pick-stay]').forEach(function (b) {
        var on = b.getAttribute('data-pick-stay') === pricingState.stay;
        b.classList.toggle('stay-on', on); b.classList.toggle('stay-off', !on);
      });
      var sp = document.getElementById('stayPicker');
      if (sp) sp.style.display = pricingState.scheme === 'offline' ? 'none' : '';
    }

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
        <span class="font-medium text-slate-200">Sertifikasi Ahli K3 Umum Resmi Kemnaker RI · cukup DP untuk amankan kursi</span>
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
              PJK3 Resmi Kemnaker RI &bull; Terdaftar di TemanK3
            </span>
          </div>

          <h1 class="hero-title text-4xl sm:text-5xl lg:text-[4rem] text-brand-navy mb-6">
            Pelatihan & Sertifikasi Ahli K3 Umum Kemnaker RI
          </h1>

          <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal mb-8 max-w-2xl">
            Jadi Ahli K3 Umum bersertifikat resmi Kemnaker RI dalam 12 hari. Terbuka untuk lulusan D3, D4, dan S1 semua jurusan, termasuk fresh graduate yang belum punya pengalaman K3.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8 max-w-2xl">
            <a href="#pricing" onclick="setPricing('blended')" class="rounded-2xl border border-blue-100 bg-brand-subtle/60 hover:border-brand-blue p-4 transition">
              <p class="text-[11px] font-extrabold uppercase tracking-wider text-brand-blue mb-1"><i class="fa-solid fa-laptop-house mr-1"></i>Skema Blended</p>
              <p class="text-sm font-bold text-brand-navy leading-snug">9 hari online via Zoom + 3 hari tatap muka di Yogyakarta</p>
              <p class="text-xs text-slate-500 mt-1">Mulai <span class="line-through text-red-500"><?= $jt($fromNormal) ?></span> <strong class="text-brand-navy">Rp <?= $jt($fromPromo) ?></strong></p>
            </a>
            <a href="#pricing" onclick="setPricing('offline')" class="rounded-2xl border border-lime-200/70 bg-lime-50/70 hover:border-lime-500 p-4 transition">
              <p class="text-[11px] font-extrabold uppercase tracking-wider text-lime-700 mb-1"><i class="fa-solid fa-people-group mr-1"></i>Skema Full Offline</p>
              <p class="text-sm font-bold text-brand-navy leading-snug">12 hari tatap muka penuh di Hotel Emersia Malioboro</p>
              <p class="text-xs text-slate-500 mt-1">Mulai <strong class="text-brand-navy">Rp <?= $jt($offlinePrices['umum'][1]) ?></strong></p>
            </a>
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
              <a href="https://maps.google.com/?cid=4002387960243989151" target="_blank" rel="noopener" class="text-xs font-medium text-slate-500 hover:text-brand-blue"><i class="fa-brands fa-google text-brand-blue mr-1"></i>5,0 ★ dari 1.139 ulasan Google · PJK3 Resmi Kemnaker RI</a>
            </div>
          </div>

          <!-- Box Snapshot Program -->
          <div class="w-full rounded-2xl border border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="bg-brand-subtle/80 px-5 py-3 flex items-center justify-between border-b border-blue-100/70">
              <span class="text-xs font-bold text-brand-navy uppercase tracking-wider">Ringkasan Program</span>
              <span class="text-xs font-semibold text-brand-blue flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Kelas Blended &amp; Full Offline
              </span>
            </div>

            <div class="p-5 sm:p-6">
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                <div class="sm:col-span-4 border-b sm:border-b-0 sm:border-r border-slate-100 pb-4 sm:pb-0">
                  <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">DURASI SERTIFIKASI</p>
                  <p class="text-3xl font-black text-brand-navy tracking-tight">12 Hari</p>
                  <p class="text-xs text-slate-500 font-medium mt-0.5">Blended atau Full Offline</p>
                </div>

                <div class="sm:col-span-5 bg-lime-50/70 rounded-xl p-3.5 border border-lime-200/60">
                  <div class="flex items-center gap-1.5 text-lime-700 font-bold text-[11px] uppercase tracking-wider mb-0.5">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>BATCH TERDEKAT</span>
                  </div>
                  <h4 class="font-bold text-brand-navy text-sm leading-snug"><?= $h($batchLong) ?></h4>
                  <p class="text-[11px] mt-0.5 font-semibold <?= $quotaFull ? 'text-red-600' : 'text-slate-600' ?>"><?= $hasQuota ? $h($quotaText) : 'Kuota Terbatas' ?></p>
                </div>

                <div class="sm:col-span-3 sm:text-right">
                  <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mulai</p>
                  <p class="text-xs text-red-500 line-through font-semibold"><?= $rp($fromNormal) ?></p>
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
                  <?php if ($hasQuota && $quotaTotal): ?>
                    <div class="flex justify-between text-[11px] font-bold mb-1"><span class="<?= $quotaFull ? 'text-red-600' : 'text-brand-navy' ?>"><?= $h($quotaText) ?></span><span class="text-slate-400"><?= $quotaPct ?>% terisi</span></div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-gradient-to-r from-brand-accent to-lime-500 h-full rounded-full" style="width: <?= $quotaPct ?>%"></div></div>
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
            <a href="#portfolio" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Legalitas PJK3</a>
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
            Banyak lowongan HSE dan K3 di sektor manufaktur, konstruksi, pertambangan, dan migas mensyaratkan sertifikat Ahli K3 Umum Kemnaker RI. Program pelatihan Ahli K3 Umum dari PT Artha Safety Indonesia membantu Anda memenuhi syarat itu dalam 12 hari, dengan materi yang disusun dari dasar sehingga mudah diikuti meski Anda bukan lulusan K3.
Program ini cocok untuk fresh graduate yang ingin masuk industri, karyawan yang ditugaskan perusahaan, dan praktisi yang ingin pindah karier ke bidang K3.
          </p>

          <div class="w-full border-t border-slate-100 my-6"></div>

          <p class="text-sm sm:text-[15px] text-slate-700 font-semibold mb-6">
            Kenapa Sertifikasi Ahli K3 Umum Bisa Membuka Jalan Karier Anda?
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                <i class="fa-solid fa-check text-xs"></i>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-brand-navy uppercase tracking-wide">
                  Diwajibkan regulasi
                </h3>
                <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
                  Perusahaan dengan 100+ pekerja atau berisiko tinggi wajib membentuk P2K3, dengan Ahli K3 sebagai sekretarisnya. Artinya, permintaan tenaga Ahli K3 Umum selalu ada.
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                <i class="fa-solid fa-check text-xs"></i>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-brand-navy uppercase tracking-wide">
                  Kewenangan resmi
                </h3>
                <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
                  Ahli K3 Umum ditunjuk Menteri Ketenagakerjaan untuk mengawasi penerapan K3 di tempat kerja. Ini peran yang diakui hukum, bukan sekadar jabatan.
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                <i class="fa-solid fa-check text-xs"></i>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-brand-navy uppercase tracking-wide">
                  Nilai tambah karier
                </h3>
                <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
                  Sertifikat Kemnaker RI diakui secara nasional dan sering menjadi syarat lowongan K3/HSE.
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                <i class="fa-solid fa-check text-xs"></i>
              </div>
              <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-brand-navy uppercase tracking-wide">
                  Tempat kerja lebih aman
                </h3>
                <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
                  Anda belajar mengenali bahaya, menilai risiko, dan mencegah kecelakaan kerja sejak dini.
                </p>
              </div>
            </div>
          </div>

          <div class="w-full border-t border-slate-100 my-6"></div>
          <p class="text-[11px] font-extrabold uppercase tracking-wider text-brand-blue mb-3">Dasar Hukum</p>
          <div class="flex flex-wrap gap-2">
            <?php foreach ([['UU No. 1 Tahun 1970', 'Keselamatan Kerja'], ['Permenaker No. PER.02/MEN/1992', 'Penunjukan Ahli K3'], ['Permenaker No. PER.04/MEN/1987', 'P2K3'], ['PP No. 50 Tahun 2012', 'SMK3']] as [$a, $b]): ?>
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
          <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">Terbuka bagi lulusan D3/S1 semua jurusan, baik yang baru memulai karier maupun profesional yang sudah bekerja.</p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([
                ['fa-user-graduate', 'Lulusan Baru D3/S1', 'Membangun karier sebagai profesional K3/HSE dengan sertifikasi resmi sejak awal.'],
                ['fa-building', 'Utusan Perusahaan', 'Memenuhi kewajiban penunjukan Ahli K3 Umum sesuai regulasi Kemnaker RI.'],
                ['fa-helmet-safety', 'Safety Officer & Praktisi HSE', 'Memperkuat kompetensi dan legalitas profesi melalui sertifikasi Kemnaker RI.'],
                ['fa-user-tie', 'Supervisor & Manajer', 'Memimpin penerapan K3 dan budaya kerja aman di unit yang Anda kelola.'],
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
                Curriculum
              </h2>
              <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                Blended: hari 1–9 online via Zoom, hari 10–12 tatap muka. Full Offline: seluruh 12 hari tatap muka di Emersia Malioboro Yogyakarta.
              </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              <span class="bg-brand-subtle text-brand-blue font-bold text-xs px-3.5 py-1.5 rounded-full border border-blue-100">
                12 hari
              </span>
              <span class="bg-brand-subtle text-brand-blue font-bold text-xs px-3.5 py-1.5 rounded-full border border-blue-100">
                120 JP
              </span>
              <span class="bg-brand-accent/20 text-brand-dark font-extrabold text-xs px-3.5 py-1.5 rounded-full border border-brand-accent/40 flex items-center gap-1">
                <i class="fa-solid fa-gift text-brand-navy text-[11px]"></i>
                <span>+ Bonus Praktek</span>
              </span>
            </div>
          </div>

          <!-- Accordion List 12 Hari Pelatihan + Hari 13 Bonus BST -->
          <div class="divide-y divide-slate-100">
            <!-- HARI 01 -->
            <div class="py-5" id="curr-item-1">
              <button type="button" onclick="toggleCurriculum(1)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="true">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">01</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Kebijakan K3 & Dasar-dasar K3
                  </span>
                </div>
                <span id="curr-icon-1" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&minus;</span>
              </button>
              <div id="curr-content-1" class="pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pengenalan filosofi dasar Keselamatan dan Kesehatan Kerja, landasan kebijakan K3 nasional, serta arah pembinaan perlindungan tenaga kerja di lingkungan industri Indonesia.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Kebijakan K3</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Dasar-dasar K3</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Dasar & Regulasi</span>
                </div>
              </div>
            </div>

            <!-- HARI 02 -->
            <div class="py-5" id="curr-item-2">
              <button type="button" onclick="toggleCurriculum(2)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">02</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Undang-undang No. 01 Tahun 1970 & Kelembagaan K3
                  </span>
                </div>
                <span id="curr-icon-2" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-2" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pendalaman ketentuan pokok UU No. 1 Tahun 1970 sebagai payung hukum keselamatan kerja, pembentukan dan tata kelola Panitia Pembina K3 (P2K3), serta kewenangan Ahli K3 Umum.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Undang-undang No.01 Tahun 1970</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Kelembagaan dan keahlian K3</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Kelembagaan & Keahlian K3</span>
                </div>
              </div>
            </div>

            <!-- HARI 03 -->
            <div class="py-5" id="curr-item-3">
              <button type="button" onclick="toggleCurriculum(3)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">03</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Pengawasan Norma Listrik & Penanggulangan Kebakaran
                  </span>
                </div>
                <span id="curr-icon-3" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-3" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pengawasan teknis sistem instalasi kelistrikan tempat kerja, penangkal petir, serta manajemen proteksi kebakaran aktif, pasif, dan kesiapsiagaan tanggap darurat api.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Keselamatan Kerja Listrik</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Penanggulangan Kebakaran</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Listrik & Kebakaran</span>
                </div>
              </div>
            </div>

            <!-- HARI 04 -->
            <div class="py-5" id="curr-item-4">
              <button type="button" onclick="toggleCurriculum(4)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">04</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Pengawasan Norma Konstruksi Bangunan & Norma Mekanik
                  </span>
                </div>
                <span id="curr-icon-4" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-4" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pengendalian risiko pada pekerjaan konstruksi, pekerjaan di ketinggian (scaffolding), serta norma keselamatan pengoperasian pesawat tenaga produksi, angkat, dan angkut.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Keselamatan Kerja Konstruksi dan Bangunan</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Keselamatan Kerja Mekanik</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Konstruksi & Mekanik</span>
                </div>
              </div>
            </div>

            <!-- HARI 05 -->
            <div class="py-5" id="curr-item-5">
              <button type="button" onclick="toggleCurriculum(5)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">05</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Pengawasan Norma Pesawat Uap & Bejana Tekan
                  </span>
                </div>
                <span id="curr-icon-5" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-5" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Standar ketat instalasi, pengujian, pemeliharaan, dan inspeksi berkala pada boiler/ketel uap, bejana bertekanan, dan tangki timbun sesuai peraturan Kemnaker RI.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Keselamatan Kerja Pesawat Uap dan Bejana Tekan</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">PUBT & Tangki Timbun</span>
                </div>
              </div>
            </div>

            <!-- HARI 06 -->
            <div class="py-5" id="curr-item-6">
              <button type="button" onclick="toggleCurriculum(6)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">06</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Pengawasan Norma Kesehatan Kerja & Bahan Berbahaya
                  </span>
                </div>
                <span id="curr-icon-6" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-6" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pencegahan Penyakit Akibat Kerja (PAK), pelayanan kesehatan kerja, ergonomi kerja, serta tata kelola penyimpanan dan penanganan Bahan Kimia Berbahaya (B3).
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Kesehatan Kerja</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Bahan berbahaya</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Kesehatan & Toksikologi</span>
                </div>
              </div>
            </div>

            <!-- HARI 07 -->
            <div class="py-5" id="curr-item-7">
              <button type="button" onclick="toggleCurriculum(7)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">07</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Pengawasan Norma Lingkungan Kerja
                  </span>
                </div>
                <span id="curr-icon-7" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-7" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pengukuran Nilai Ambang Batas (NAB) faktor fisika, kimia, biologi, ergonomi, dan psikologi kerja sesuai Permenaker No. 5 Tahun 2018 untuk menciptakan lingkungan kerja yang aman dan produktif.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma Lingkungan Kerja</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Higiene Industri</span>
                </div>
              </div>
            </div>

            <!-- HARI 08 -->
            <div class="py-5" id="curr-item-8">
              <button type="button" onclick="toggleCurriculum(8)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">08</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Pengawasan Norma Sistem Manajemen K3 (SMK3)
                  </span>
                </div>
                <span id="curr-icon-8" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-8" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Struktur PP No. 50 Tahun 2012, 5 prinsip dasar SMK3, integrasi klausul audit SMK3 (64, 122, dan 166 kriteria), serta penyusunan dokumen kebijakan dan sasaran K3 perusahaan.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pengawasan Norma SMK3</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Sistem Manajemen (SMK3)</span>
                </div>
              </div>
            </div>

            <!-- HARI 09 -->
            <div class="py-5" id="curr-item-9">
              <button type="button" onclick="toggleCurriculum(9)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">09</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Manajemen Risiko & Analisa Kecelakaan Kerja
                  </span>
                </div>
                <span id="curr-icon-9" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-9" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Praktek identifikasi bahaya dan penilaian risiko (HIRADC/IBPR), metode investigasi akar penyebab insiden, serta mekanisme pelaporan kecelakaan kerja resmi ke instansi ketenagakerjaan.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Manajemen Risiko</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Analisa Kecelakaan</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Risk & Incident Analysis</span>
                </div>
              </div>
            </div>

            <!-- HARI 10 -->
            <div class="py-5" id="curr-item-10">
              <button type="button" onclick="toggleCurriculum(10)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">10</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Praktek Kerja Lapangan (PKL) Observasi K3
                  </span>
                </div>
                <span id="curr-icon-10" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-10" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pelaksanaan observasi lapangan secara mendalam terhadap objek pembinaan K3 di industri terpilih. Peserta mengidentifikasi temuan positif dan negatif serta menyusun draft laporan PKL komprehensif.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Observasi Objek Norma K3 di Industri</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Penyusunan Kertas Kerja & Laporan PKL</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Praktek Lapangan (PKL)</span>
                </div>
              </div>
            </div>

            <!-- HARI 11 -->
            <div class="py-5" id="curr-item-11">
              <button type="button" onclick="toggleCurriculum(11)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">11</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Seminar & Presentasi Laporan PKL
                  </span>
                </div>
                <span id="curr-icon-11" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-11" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pemaparan hasil observasi dan rekomendasi perbaikan K3 oleh masing-masing kelompok peserta dalam forum seminar resmi yang diuji dan dinilai langsung oleh Pengawas Ketenagakerjaan Kemnaker RI.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Seminar Hasil Observasi K3</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Tanya Jawab & Pengujian Teknis Tim Evaluator</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Seminar & Uji Kasus</span>
                </div>
              </div>
            </div>

            <!-- HARI 12 -->
            <div class="py-5" id="curr-item-12">
              <button type="button" onclick="toggleCurriculum(12)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-4">
                  <span class="text-base sm:text-lg font-black text-brand-navy group-hover:text-brand-blue transition">12</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition">
                    Evaluasi Akhir / Post Test & Pembekalan Lisensi
                  </span>
                </div>
                <span id="curr-icon-12" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-12" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Pelaksanaan Ujian Akhir (Post Test) Kemnaker RI berbasis sistem resmi, verifikasi kelengkapan berkas administrasi SKP (Surat Keputusan Penunjukan) dan Lisensi Ahli K3 Umum, serta penutupan resmi.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Evaluasi/Post test Uji Kompetensi</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Pembekalan Pengurusan SKP & Lisensi Kemnaker</span>
                  </div>
                </div>
                <div>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Sertifikasi & Lisensi</span>
                </div>
              </div>
            </div>

            <!-- HARI 13 (FREE BONUS BASIC SAFETY TRAINING) -->
            <div class="py-5" id="curr-item-13">
              <button type="button" onclick="toggleCurriculum(13)" class="w-full flex items-center justify-between text-left group gap-4 transition focus:outline-none cursor-pointer" aria-expanded="false">
                <div class="flex items-center gap-3 sm:gap-4 flex-wrap sm:flex-nowrap">
                  <span class="text-base sm:text-lg font-black text-brand-blue group-hover:text-brand-navy transition">13</span>
                  <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-brand-blue transition flex items-center gap-2 flex-wrap">
                    <span>Bonus Praktek Basic Safety Training (BST)</span>
                    <span class="bg-brand-accent text-brand-dark text-[10px] sm:text-[11px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                      FREE BONUS
                    </span>
                  </span>
                </div>
                <span id="curr-icon-13" class="text-xl sm:text-2xl font-bold text-brand-blue transition-transform duration-200">&#43;</span>
              </button>
              <div id="curr-content-13" class="hidden pt-4 pl-8 sm:pl-9 pr-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3">
                  Program simulasi dan praktik keselamatan kerja langsung di lapangan sebagai bonus eksklusif dari PT Artha Safety Indonesia. Menghadirkan materi dan praktik pemadaman api (Damkar & APAR), penanganan pertolongan pertama (P3K / First Aid), serta teknik penggunaan alat bantu pernapasan mandiri (SCBA) dengan pemateri dan instruktur bersertifikat langsung dari Dinas Pemadam Kebakaran (Damkar) dan Palang Merah Indonesia (PMI).
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Praktek Pemadaman Kebakaran & Fire Drill (Instruktur Damkar)</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Praktek Pertolongan Pertama / P3K & CPR (Instruktur Resmi PMI)</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Simulasi & Praktek Penggunaan SCBA di Ruang Asap / Ruang Terbatas</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i>
                    <span>Sertifikat Tambahan Khusus Basic Safety Training (BST)</span>
                  </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="inline-block bg-brand-accent/25 text-brand-dark text-[11px] font-black px-3 py-1 rounded-md">Free Bonus Praktek</span>
                  <span class="inline-block bg-brand-subtle text-brand-navy text-[11px] font-bold px-3 py-1 rounded-md">Damkar, P3K & SCBA</span>
                </div>
              </div>
            </div>

          </div>
        </section>

        <section id="portfolio" class="w-full rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-9 shadow-card">
          <div class="flex items-center gap-2.5 mb-3">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
              LEGALITAS PJK3
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl text-brand-navy mb-2">
            PJK3 Terakreditasi dan Terdaftar Resmi di TemanK3
          </h2>
          <p class="text-xs sm:text-sm font-semibold text-slate-500 mb-6">
            PT Artha Safety Indonesia terverifikasi resmi dan dapat dicari langsung di fitur Pencarian PJK3 portal TemanK3 Kemnaker RI.
          </p>

          <div class="w-full mb-8">
            <div class="relative rounded-2xl overflow-hidden border border-slate-200/90 shadow-md bg-slate-50 group">
              <img
                src="../assets/img/bukti-temank3.jpg"
                alt="Bukti Legalitas PT Artha Safety Indonesia di Portal TemanK3 Kemnaker RI"
                class="w-full h-auto object-contain transition-transform duration-500 group-hover:scale-[1.01]"
                onerror="this.onerror=null; this.src='https://placehold.co/800x450/071C6E/ffffff?text=Bukti+Legalitas+TemanK3';"
              />
              <div class="bg-brand-navy/95 backdrop-blur-md text-white px-4 py-2.5 text-xs font-bold flex items-center justify-between border-t border-brand-blue/30 shadow-md">
                <span class="flex items-center gap-2">
                  <i class="fa-solid fa-circle-check text-brand-accent"></i>
                  <span>Terverifikasi di Database Resmi TemanK3 Kemnaker RI</span>
                </span>
                <span class="text-brand-accent font-extrabold uppercase tracking-wider text-[11px] sm:text-xs">PJK3 RESMI</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
              PT Artha Safety Indonesia adalah Perusahaan Jasa Keselamatan dan Kesehatan Kerja (PJK3) resmi yang ditunjuk langsung oleh Kementerian Ketenagakerjaan Republik Indonesia (Kemnaker RI) untuk menyelenggarakan pembinaan dan sertifikasi Ahli K3 Umum.
            </p>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
              Sebelum mendaftar pelatihan K3, pastikan penyelenggaranya terdaftar sebagai PJK3 (Perusahaan Jasa K3) di Kemnaker RI. PT Artha Safety Indonesia terdaftar resmi dan bisa Anda cek langsung di portal TemanK3 melalui menu Pencarian PJK3.
            </p>

            <div class="space-y-3.5 pt-4 border-t border-slate-100">
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                  <i class="fa-solid fa-check text-[11px]"></i>
                </div>
                <span class="text-xs sm:text-sm font-bold text-slate-800">
                  No SKP Keahlian K3 Umum : 5/000666/AS.01.02.4103.6.15/V/2026
                </span>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                  <i class="fa-solid fa-check text-[11px]"></i>
                </div>
                <span class="text-xs sm:text-sm font-bold text-slate-800">
                  SKT Aktif BNSP : SKT.02/122/LSP-K3N/X/2023
                </span>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                  <i class="fa-solid fa-check text-[11px]"></i>
                </div>
                <span class="text-xs sm:text-sm font-bold text-slate-800">
                  NIB Perusahaan : 2609230190968
                </span>
              </div>

              <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-brand-accent/25 text-brand-dark flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                  <i class="fa-solid fa-check text-[11px]"></i>
                </div>
                <span class="text-xs sm:text-sm font-bold text-slate-800">
                  Terdaftar di Kementerian : AHU-0072648.AH.01.01.Tahun 2023
                </span>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center gap-2">
              <a href="https://temank3.kemnaker.go.id" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:text-brand-navy transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-brand-blue text-xs"></i>
                <span>Kunjungi Portal Resmi TemanK3 Kemnaker RI</span>
              </a>
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
              src="https://img.youtube.com/vi/T6Y7vA2ks1g/maxresdefault.jpg"
              alt="Video Pelatihan Ahli K3 Umum PT Artha Safety Indonesia"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-95"
              onerror="this.onerror=null; this.src='https://img.youtube.com/vi/T6Y7vA2ks1g/hqdefault.jpg';"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy/90 via-black/25 to-transparent"></div>

            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-12 h-12 rounded-full bg-white/95 text-brand-navy flex items-center justify-center pl-0.5 shadow-xl group-hover:scale-110 group-hover:bg-brand-accent group-hover:text-brand-dark transition-all duration-300">
                <i class="fa-solid fa-play text-sm"></i>
              </div>
            </div>

            <div class="absolute bottom-2.5 left-2.5 right-2.5 flex items-center justify-between text-white text-[10px] sm:text-[11px] font-semibold">
              <span class="bg-black/60 backdrop-blur-md px-2 py-0.5 rounded flex items-center gap-1">
                <i class="fa-solid fa-shield-halved text-brand-accent"></i> Standar Kemnaker
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
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Durasi Pelatihan</span>
              <span class="font-extrabold text-brand-navy text-right">12 hari · Blended / Full Offline</span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Batch Terdekat</span>
              <span class="font-extrabold text-brand-navy text-right"><?= htmlspecialchars($batchShort) ?></span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Biaya mulai</span>
              <span class="text-right"><span class="block text-[11px] text-red-500 line-through font-semibold leading-none"><?= $rp($fromNormal) ?></span><span class="font-extrabold text-brand-blue"><?= $rp($fromPromo) ?></span></span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Persyaratan</span>
              <span class="font-extrabold text-brand-navy text-right">Ijazah min. D3</span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Lokasi</span>
              <span class="font-extrabold text-brand-navy text-right">Hotel Emersia Malioboro Yogyakarta</span>
            </div>
            <?php if ($hasQuota): ?>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Kuota</span>
              <span class="font-extrabold text-right <?= $quotaFull ? 'text-red-600' : 'text-red-600' ?>"><?= $h($quotaText) ?></span>
            </div>
            <?php endif; ?>
          </div>
          <?php if ($promoActive): ?>
            <p class="mt-2 rounded-lg bg-red-50 border border-red-100 text-red-700 text-[11px] font-bold px-3 py-2"><i class="fa-solid fa-fire mr-1"></i><?= $h($promoText) ?></p>
          <?php endif; ?>

          <!-- Download Brochure Bar -->
          <div class="mt-3.5 pt-1">
            <a
              href="https://drive.google.com/file/d/1fcUMOjagWOxqHwBtPUHMgKEggvgf9YWw/view?usp=sharing"
              target="_blank"
              rel="noopener noreferrer"
              class="w-full py-2.5 px-3 rounded-xl bg-lime-50/80 hover:bg-lime-100 text-brand-dark font-black text-xs sm:text-[13px] flex items-center justify-center gap-2 transition duration-200 border border-lime-300/80 cursor-pointer shadow-xs"
            >
              <i class="fa-solid fa-arrow-down text-brand-navy text-xs"></i>
              <span>Download Proposal Pelatihan</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>

  <section id="certification" class="w-full bg-slate-50/80 border-t border-b border-slate-200/70 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

        <div class="lg:col-span-6 flex flex-col items-center justify-center">
          <div class="relative w-full max-w-[430px] sm:max-w-[460px] rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200/90 bg-white group flex items-center justify-center p-2 sm:p-2.5 transition-all duration-300 hover:shadow-[0_25px_50px_-12px_rgba(7,28,110,0.18)]">
            <img
              src="../assets/img/sertifikat-ak3u.jpg"
              alt="Sertifikat Ahli K3 Umum Kemnaker RI PT Artha Safety Indonesia"
              class="w-full h-auto object-contain rounded-xl sm:rounded-2xl transform transition-transform duration-500 group-hover:scale-[1.015]"
              onerror="this.onerror=null; this.src='https://placehold.co/600x850/071C6E/ffffff?text=Sertifikat+Ahli+K3+Umum+Kemnaker+RI';"
            />
            <div class="absolute inset-0 rounded-2xl sm:rounded-3xl ring-1 ring-inset ring-black/5 pointer-events-none"></div>
          </div>

          <!-- Floating Accreditation Badge -->
          <div class="w-[90%] sm:w-[86%] max-w-[430px] -mt-5 sm:-mt-6 z-10 bg-brand-navy rounded-2xl p-4 sm:p-5 shadow-2xl border border-brand-blue/50 text-white flex items-center gap-4">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-brand-blue border-2 border-brand-accent text-brand-accent flex items-center justify-center font-black text-xs sm:text-sm flex-shrink-0 shadow-inner">
              <span>K3</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-brand-accent leading-none mb-1">
                ACCREDITED CREDENTIAL
              </p>
              <h4 class="text-sm sm:text-base font-extrabold text-white truncate">
                Sertifikat Kemnaker RI
              </h4>
              <p class="text-xs text-slate-300 leading-snug mt-0.5">
                Diterbitkan resmi dengan barcode & validasi online di TemanK3.
              </p>
            </div>
          </div>
        </div>

        <div class="lg:col-span-6 flex flex-col justify-center">
          <div class="flex items-center gap-2.5 mb-4">
            <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
            <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue">
              Sertifikat yang Didapat
            </span>
          </div>

          <h2 class="hero-title text-3xl sm:text-4xl lg:text-[2.75rem] text-brand-navy mb-5 leading-tight">
            Raih Sertifikasi Ahli K3 Umum Kemnaker RI
          </h2>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-8 max-w-xl">
            Setelah menyelesaikan seluruh rangkaian pembinaan 12 hari dan evaluasi bersama PT Artha Safety Indonesia, Anda akan memperoleh beberapa sertifikat berikut:
          </p>

          <div class="space-y-4">
            <div class="flex items-start gap-3.5">
              <i class="fa-solid fa-check text-brand-blue text-base mt-0.5 flex-shrink-0"></i>
              <span class="text-sm sm:text-base font-bold text-slate-800">
                Sertifikat Pembinaan Ahli K3 Umum Kemnaker RI (berlaku seumur hidup)
              </span>
            </div>

            <div class="flex items-start gap-3.5">
              <i class="fa-solid fa-check text-brand-blue text-base mt-0.5 flex-shrink-0"></i>
              <span class="text-sm sm:text-base font-bold text-slate-800">
                SKP & Lisensi Kewenangan Ahli K3 Umum Kemnaker RI (bagi utusan perusahaan) berlaku selama 3 tahun dan dapat diperpanjang
              </span>
            </div>

            <div class="flex items-start gap-3.5">
              <i class="fa-solid fa-check text-brand-blue text-base mt-0.5 flex-shrink-0"></i>
              <span class="text-sm sm:text-base font-bold text-slate-800">
                Sertifikat kompetensi Basic Safety Training
              </span>
            </div>

            <div class="flex items-start gap-3.5">
              <i class="fa-solid fa-check text-brand-blue text-base mt-0.5 flex-shrink-0"></i>
              <span class="text-sm sm:text-base font-bold text-slate-800">
                Sertifikat kompetensi penunjang sebanyak 43 e-sertifikat
              </span>
            </div>
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
      <div class="flex items-center gap-2.5 mb-3">
        <span class="w-6 h-1.5 bg-brand-accent rounded-full inline-block"></span>
        <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-accent">BONUS PESERTA</span>
      </div>
      <h2 class="hero-title text-3xl sm:text-4xl text-white mb-2">
        Bonus Senilai <span class="line-through decoration-red-400 decoration-[3px] text-slate-300"><?= $rp($bonusValue) ?></span> <span class="text-brand-accent">GRATIS</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-300 max-w-2xl mb-8">Tambahan dari PT Artha Safety Indonesia untuk setiap peserta, supaya kamu pulang dengan keterampilan praktis dan portofolio yang lebih kuat.</p>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white text-slate-800 rounded-2xl p-5 sm:p-6">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div>
              <h3 class="text-lg font-black text-brand-navy">Praktik Basic Safety Training</h3>
              <p class="text-xs text-slate-500">Dipandu langsung instruktur Damkar &amp; PMI</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-xs text-red-500 line-through font-semibold"><?= $rp(750000) ?></p>
              <p class="font-black text-emerald-600">GRATIS</p>
            </div>
          </div>
          <div class="space-y-2">
            <?php foreach (['Praktik pemadaman api dengan APAR (Basic Fire Fighting)', 'Praktik memakai SCBA di area berasap', 'Pertolongan pertama, RJP dasar & pembidaian (Basic First Aid)', 'Sertifikat tambahan Basic Safety Training'] as $b): ?>
              <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-700"><i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i><span><?= $b ?></span></div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="bg-white text-slate-800 rounded-2xl p-5 sm:p-6">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div>
              <h3 class="text-lg font-black text-brand-navy">43 E-Sertifikat HSE</h3>
              <p class="text-xs text-slate-500">HSE Bootcamp, khusus peserta Ahli K3 Umum</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-xs text-red-500 line-through font-semibold"><?= $rp(500000) ?></p>
              <p class="font-black text-emerald-600">GRATIS</p>
            </div>
          </div>
          <div class="space-y-2">
            <?php foreach (['43 topik pelatihan HSE dalam satu program', 'E-sertifikat untuk setiap topik', 'Menambah nilai CV & portofolio K3/HSE'] as $b): ?>
              <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-700"><i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i><span><?= $b ?></span></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="mt-6">
        <a href="#registration" class="inline-flex items-center gap-2 bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black text-sm px-6 py-3.5 rounded-2xl shadow-lime transition"><span>Ambil Bonusnya, Daftar Sekarang</span><i class="fa-solid fa-arrow-right text-xs"></i></a>
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
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

        <div class="lg:col-span-5 flex justify-center">
          <div class="relative w-full max-w-[420px] rounded-2xl overflow-hidden shadow-md bg-white border border-blue-200/50">
            <img
              src="../assets/img/poster-fasilitas.jpg"
              alt="Fasilitas Pelatihan Ahli K3 Umum PT Artha Safety Indonesia"
              class="w-full h-auto object-cover hover:scale-105 transition-transform duration-500"
              onerror="this.onerror=null; this.src='https://placehold.co/600x800/071C6E/ffffff?text=Fasilitas+Training+Artha+Safety';"
            />
          </div>
        </div>

        <div class="lg:col-span-7 flex flex-col justify-center">
          <h3 class="text-2xl sm:text-3xl font-extrabold text-brand-navy mb-2 tracking-tight">
            Fasilitas & Training Kit Lengkap
          </h3>

          <p class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-brand-blue mb-4">
            STANDAR RESMI PEMBINAAN KEMNAKER RI · BLENDED &amp; FULL OFFLINE
          </p>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-6">
            Selama mengikuti program pembinaan Ahli K3 Umum bersama PT Artha Safety Indonesia, peserta akan mendapatkan fasilitas komprehensif untuk menunjang kenyamanan, kualitas belajar, serta kesiapan implementasi dokumen di tempat kerja secara optimal.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-4 border-t border-slate-200/80">
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Pin & Lencana Ahli K3 <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-subtle text-brand-blue ml-1 align-middle">Semua paket</span></span>
            </div>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Coffee break 2× & makan siang (hari 10–12) <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-subtle text-brand-blue ml-1 align-middle">Semua paket</span></span>
            </div>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Career support, konsultasi & webinar 1 tahun <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-subtle text-brand-blue ml-1 align-middle">Semua paket</span></span>
            </div>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Modul hardcopy & himpunan perundang-undangan K3 <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-accent/30 text-brand-dark ml-1 align-middle">Blended Full &amp; Offline</span></span>
            </div>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Kemeja & t-shirt safety <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-accent/30 text-brand-dark ml-1 align-middle">Blended Full &amp; Offline</span></span>
            </div>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Training kit: tumbler, ransel, blocknote, pulpen <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-accent/30 text-brand-dark ml-1 align-middle">Blended Full &amp; Offline</span></span>
              <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Wisata Lava Tour Merapi <span class="inline-block text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-brand-navy text-white ml-1 align-middle">Full Offline</span></span>
            </div>
          </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="reviews" class="w-full bg-brand-royal py-16 sm:py-24 overflow-hidden relative text-white">
    <div class="max-w-4xl mx-auto px-4 text-center mb-12 sm:mb-16">
      <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold text-brand-accent mb-4 border border-white/20">
        <i class="fa-brands fa-google text-white text-xs"></i>
        <span>Ulasan Google &bull; 5,0 ★ dari 1.139 ulasan</span>
      </div>

      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-4 leading-tight">
        Kata Alumni Pelatihan<br class="hidden sm:inline"> Artha Safety
      </h2>
      <a href="https://maps.google.com/?cid=4002387960243989151" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-bold text-white underline decoration-brand-accent decoration-2 underline-offset-4 hover:text-brand-accent">
        <span>Lihat semua 1.139 ulasan di Google Maps</span>
        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
      </a>
    </div>

    <!-- Container Marquee Dual-Row -->
    <div class="marquee-container space-y-6 sm:space-y-8 select-none">

      <!-- BARIS ATAS: Bergulir ke Kiri -->
      <div class="overflow-hidden">
        <div id="marqueeRowTop" class="animate-marquee-left flex gap-6">
          <div class="w-[330px] sm:w-[380px] bg-white text-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between flex-shrink-0 border border-slate-100">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex text-amber-400 text-xs gap-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <i class="fa-brands fa-google text-slate-400 text-sm"></i>
              </div>
              <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                "Mengikuti pelatihan bersama PT Artha Safety Indonesia sangat menambah ilmu baik teori maupun praktiknya. Hal ini sangat memperluas wawasan dalam dunia kerja K3. Penyampaian materi menarik dan panitianya super ramah!"
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-brand-navy text-white font-black text-xs flex items-center justify-center flex-shrink-0">IR</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Iqbal Romzy</h4>
                <p class="text-[11px] text-slate-500 mt-1">Ahli K3 Umum &bull; Offline Yogyakarta</p>
              </div>
            </div>
          </div>

          <div class="w-[330px] sm:w-[380px] bg-white text-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between flex-shrink-0 border border-slate-100">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex text-amber-400 text-xs gap-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <i class="fa-brands fa-google text-slate-400 text-sm"></i>
              </div>
              <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                "Seru, panitia membuat suasana pelatihan feels like home. Pemateri yang memberikan ilmu merupakan ahli di bidangnya masing-masing. Terima kasih Artha Safety Indonesia, siap rekomendasikan untuk rekan kantor!"
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-brand-blue text-white font-black text-xs flex items-center justify-center flex-shrink-0">NM</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Nixon Marbun</h4>
                <p class="text-[11px] text-slate-500 mt-1">Utusan Perusahaan &bull; Ahli K3 Umum</p>
              </div>
            </div>
          </div>

          <div class="w-[330px] sm:w-[380px] bg-white text-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between flex-shrink-0 border border-slate-100">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex text-amber-400 text-xs gap-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <i class="fa-brands fa-google text-slate-400 text-sm"></i>
              </div>
              <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                "Seru banget ikut pelatihan Ahli K3 Umum di PT Artha Safety Indonesia ini! Tim panitianya asik dan pematerinya berkompeten langsung dari Kemnaker RI. Sangat berkualitas dan terpercaya."
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-brand-accent text-brand-dark font-black text-xs flex items-center justify-center flex-shrink-0">RB</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Renanda Bayu</h4>
                <p class="text-[11px] text-slate-500 mt-1">Alumni Batch Pembinaan K3</p>
              </div>
            </div>
          </div>

          <div class="w-[330px] sm:w-[380px] bg-white text-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between flex-shrink-0 border border-slate-100">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex text-amber-400 text-xs gap-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <i class="fa-brands fa-google text-slate-400 text-sm"></i>
              </div>
              <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                "Tempat pelatihan K3 KEMNAKER terpercaya, 100% batch saya lulus! Senang banget dapat relasi baru yang profesional dan suportif. Recommended buat yang mau ambil lisensi K3!"
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-emerald-600 text-white font-black text-xs flex items-center justify-center flex-shrink-0">HT</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Herdina Theresia</h4>
                <p class="text-[11px] text-slate-500 mt-1">Ahli K3 Umum Certified</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BARIS BAWAH: Bergulir ke Kanan -->
      <div class="overflow-hidden">
        <div id="marqueeRowBottom" class="animate-marquee-right flex gap-6">
          <div class="w-[330px] sm:w-[380px] bg-white text-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between flex-shrink-0 border border-slate-100">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex text-amber-400 text-xs gap-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <i class="fa-brands fa-google text-slate-400 text-sm"></i>
              </div>
              <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                "Belajar K3 Umum di PT Artha Safety Indonesia seru banget! Pematerinya keren dan materinya mudah dimengerti. Saya juga dapat banyak relasi baru. Pokoknya wajib ambil sertifikasi di sini gengs! ✨🙌🏻"
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-pink-600 text-white font-black text-xs flex items-center justify-center flex-shrink-0">AA</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Anisa Andriyani</h4>
                <p class="text-[11px] text-slate-500 mt-1">Ahli K3 Umum Offline</p>
              </div>
            </div>
          </div>

          <div class="w-[330px] sm:w-[380px] bg-white text-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between flex-shrink-0 border border-slate-100">
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex text-amber-400 text-xs gap-1">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <i class="fa-brands fa-google text-slate-400 text-sm"></i>
              </div>
              <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                "Alhamdulillah lulus 100% dan berkas SKP & Lisensi Kemnaker RI terverifikasi resmi di TemanK3. Pendampingan administrasi tim Artha Safety sangat transparan dan sigap."
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-cyan-700 text-white font-black text-xs flex items-center justify-center flex-shrink-0">DC</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Dwi Cahyono</h4>
                <p class="text-[11px] text-slate-500 mt-1">Utusan Perusahaan EPC</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="max-w-5xl mx-auto px-4 mt-12 sm:mt-16 text-center">
      <p class="text-sm font-bold text-slate-200 mb-4">Dipercaya <span class="text-brand-accent">100+ perusahaan &amp; instansi</span> untuk melatih tenaga K3-nya, di antaranya:</p>
      <div class="flex flex-wrap justify-center gap-2">
        <?php foreach (['Kilang Pertamina Internasional', 'PetroChina', 'KAI', 'Pelindo', 'Adhi Karya', 'Total Bangun Persada', 'Karya Podo Moro', 'Radiant Utama Interinsco', 'Sinarmas CEPSR', 'Eka Hospital', 'Institut Teknologi PLN', 'KOBEX'] as $c): ?>
          <span class="bg-white/10 border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-full"><?= $c ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

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
          Dokumentasi Pelatihan K3
        </h2>

        <p class="text-xs sm:text-sm md:text-base text-slate-600 font-normal leading-relaxed max-w-2xl mx-auto">
          Potret nyata kebersamaan, suasana kelas interaktif, observasi industri (PKL), serta praktik langsung Basic Safety Training (BST) bersama para mentor praktisi ahli.
        </p>
      </div>

      <!-- Container 4 Kartu Dokumentasi Sejajar Bergaya Referensi dengan Animasi Slide Up Satu per Satu -->
      <div id="docGalleryContainer" class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 select-none">

        <!-- SLOT 0 -->
        <div id="docSlot0" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img src="../assets/img/dok-apar.jpg" alt="Praktik Pemadaman Api APAR" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Praktik Lapangan
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Praktik Pemadaman Api APAR
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Basic Safety Training (BST)
              </p>
            </div>
          </div>
        </div>

        <!-- SLOT 1 -->
        <div id="docSlot1" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img src="../assets/img/dok-observasi-norma.jpg" alt="Observasi Objek Norma K3" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Kunjungan Pabrik
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Observasi Objek Norma K3
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
            <img src="../assets/img/dok-fire-drill.jpg" alt="Simulasi Evakuasi & Fire Drill" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Instruktur Damkar
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Simulasi Evakuasi & Fire Drill
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Tanggap Darurat Kebakaran
              </p>
            </div>
          </div>
        </div>

        <!-- SLOT 3 -->
        <div id="docSlot3" class="doc-card-container relative w-full aspect-[3/4] sm:aspect-[4/5] lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-card border border-slate-200/90 bg-brand-navy group">
          <div class="doc-card-layer absolute inset-0 w-full h-full">
            <img src="../assets/img/dok-scba.jpg" alt="Pelatihan Penggunaan SCBA" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/60 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left flex flex-col justify-end">
              <span class="inline-block bg-brand-accent text-brand-dark text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-2 self-start shadow-xs">
                Safety Drill
              </span>
              <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                Pelatihan Penggunaan SCBA
              </h3>
              <p class="text-xs text-slate-300 font-medium mt-1 leading-snug">
                Ruang Terbatas & Masker Asap
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
            Biaya Pelatihan Ahli K3 Umum Kemnaker 2026
          </h2>
          <p class="text-xs sm:text-sm md:text-base text-slate-500 font-medium">
            Pilih skema belajar dan jalur sesuai status kerja. Sertifikat yang didapat sama. <strong class="text-brand-navy">Cukup bayar DP untuk mengamankan kursi</strong>, pelunasan di hari pertama pelatihan.
          </p>

          <?php if ($promoActive || $hasQuota): ?>
            <div class="mt-5 flex flex-wrap justify-center gap-2">
              <?php if ($promoActive): ?><span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200 text-xs font-bold px-3 py-1.5 rounded-full"><i class="fa-solid fa-fire"></i><?= $h($promoText) ?></span><?php endif; ?>
              <?php if ($hasQuota): ?><span class="inline-flex items-center gap-1.5 bg-lime-50 text-lime-800 border border-lime-200 text-xs font-bold px-3 py-1.5 rounded-full"><i class="fa-solid fa-user-group"></i><?= $h($quotaText) ?> · Batch <?= $h($batchShort) ?></span><?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- Pill Switcher: skema -->
          <div class="mt-8 grid grid-cols-2 gap-2 max-w-md mx-auto">
            <button type="button" data-pick-scheme="blended" onclick="setPricing('blended')" class="scheme-on rounded-2xl border-2 px-3 py-3 text-left transition cursor-pointer">
              <span class="block text-sm font-black">Blended</span>
              <span class="block text-[11px] font-semibold opacity-80">9 hari online + 3 tatap muka</span>
            </button>
            <button type="button" data-pick-scheme="offline" onclick="setPricing('offline')" class="scheme-off rounded-2xl border-2 px-3 py-3 text-left transition cursor-pointer">
              <span class="block text-sm font-black">Full Offline</span>
              <span class="block text-[11px] font-semibold opacity-80">12 hari tatap muka penuh</span>
            </button>
          </div>

          <!-- Pill Switcher: jalur -->
          <div class="mt-4 inline-flex p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80 shadow-inner">
            <button type="button" data-pick-cat="umum" onclick="setPricing(null, 'umum')" class="pill-on px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-user-graduate text-xs"></i>
              <span>Peserta Umum</span>
            </button>
            <button type="button" data-pick-cat="utusan" onclick="setPricing(null, 'utusan')" class="pill-off px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-building text-xs"></i>
              <span>Utusan Perusahaan</span>
            </button>
          </div>
          <!-- Pill Switcher: penginapan (khusus Blended) -->
          <div id="stayPicker" class="mt-3 flex justify-center gap-2 text-xs font-bold">
            <button type="button" data-pick-stay="non" onclick="setPricing(null, null, 'non')" class="stay-on px-4 py-2 rounded-full border transition cursor-pointer"><i class="fa-solid fa-house-laptop mr-1"></i>Tanpa penginapan</button>
            <button type="button" data-pick-stay="inap" onclick="setPricing(null, null, 'inap')" class="stay-off px-4 py-2 rounded-full border transition cursor-pointer"><i class="fa-solid fa-hotel mr-1"></i>Dengan penginapan</button>
          </div>
        </div>

        <?php
        $tierFeatures = [
            'umum' => ['Sertifikat Calon Ahli K3 Umum Kemnaker RI'],
            'utusan' => ['Sertifikat, <strong>SKP &amp; Lisensi</strong> Kemnaker RI (berlaku 3 tahun)'],
        ];
        $common = ['<strong>9 hari</strong> materi online via Live Zoom', '<strong>3 hari</strong> PKL, seminar &amp; ujian di Yogyakarta', 'Free Basic Safety Training (Damkar &amp; PMI)', 'Free 43 e-sertifikat HSE Bootcamp', 'Career support, konsultasi &amp; webinar 1 tahun'];
        $fullExtra = ['Modul hardcopy &amp; himpunan perundang-undangan K3', '<strong>Training kit</strong>: tumbler, kemeja &amp; t-shirt safety, ransel, blocknote, pulpen'];
        foreach ($prices as $cat => $stays):
            foreach ($stays as $stay => $tiers): ?>
        <div data-price-grid="blended-<?= $cat ?>-<?= $stay ?>" class="<?= $cat === 'umum' && $stay === 'non' ? 'grid' : 'hidden' ?> grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 items-stretch max-w-4xl mx-auto">
          <?php foreach ($tiers as $tier => [$normal, $promo]): $pkg = "blended-$cat-$stay-$tier"; $isFull = $tier === 'full'; ?>
            <div class="w-full rounded-3xl p-7 sm:p-9 flex flex-col justify-between relative overflow-hidden <?= $isFull ? 'bg-brand-navy text-white shadow-2xl border border-brand-blue/60 group' : 'bg-white border-2 border-slate-200 shadow-sm transition-all hover:border-brand-blue/50 hover:shadow-md' ?>">
              <?php if ($isFull): ?>
                <div class="absolute top-0 right-0"><div class="bg-brand-accent text-brand-dark font-black text-[10px] sm:text-xs px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow-md">BEST VALUE</div></div>
              <?php endif; ?>
              <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                  <span class="text-xs font-black uppercase tracking-wider <?= $isFull ? 'text-brand-accent' : 'text-slate-400' ?>"><?= $isFull ? 'FULL PACKAGE' : 'BASIC PACKAGE' ?></span>
                  <span class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $isFull ? 'bg-white/10 text-white border border-white/20 mr-20 sm:mr-24' : 'bg-blue-50 text-brand-blue border border-blue-200' ?>"><?= $stay === 'non' ? 'Tanpa penginapan' : 'Dengan penginapan' ?></span>
                </div>
                <h3 class="text-2xl font-black mb-1 tracking-tight <?= $isFull ? 'text-white' : 'text-brand-navy' ?>">Blended · <?= ucfirst($tier) ?></h3>
                <p class="text-xs font-semibold <?= $isFull ? 'text-slate-300' : 'text-slate-500' ?>"><?= $cat === 'umum' ? 'Peserta Umum' : 'Utusan Perusahaan' ?></p>
                <div class="my-5">
                  <span class="block text-sm font-bold line-through <?= $isFull ? 'text-red-300' : 'text-red-500' ?>"><?= $rp($normal) ?></span>
                  <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight <?= $isFull ? 'text-brand-accent' : 'text-brand-navy' ?>"><?= $rp($promo) ?></span>
                  <p class="text-xs font-bold mt-1.5 <?= $isFull ? 'text-brand-accent' : 'text-emerald-600' ?>">Hemat Rp <?= $jt($normal - $promo) ?> · cukup DP untuk amankan kursi</p>
                </div>
                <div class="w-full border-t my-6 <?= $isFull ? 'border-white/15' : 'border-slate-100' ?>"></div>
                <div class="space-y-3.5 text-xs sm:text-sm <?= $isFull ? 'text-slate-200' : 'text-slate-700' ?>">
                  <?php foreach (array_merge($tierFeatures[$cat], $common, $isFull ? $fullExtra : []) as $f): ?>
                    <div class="flex items-start gap-3"><i class="fa-solid fa-check text-xs mt-1 flex-shrink-0 <?= $isFull ? 'text-brand-accent' : 'text-brand-blue' ?>"></i><span><?= $f ?></span></div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="mt-8 pt-4">
                <a href="#registration" onclick="pickPackage('<?= $pkg ?>')" class="w-full font-black text-sm py-3.5 px-5 rounded-2xl transition duration-200 flex items-center justify-center gap-2 cursor-pointer <?= $isFull ? 'bg-brand-accent hover:bg-brand-accentHover text-brand-dark shadow-lime' : 'bg-white hover:bg-slate-50 text-brand-navy border-2 border-brand-navy hover:border-brand-blue hover:text-brand-blue' ?>">
                  <span>Pilih Paket <?= ucfirst($tier) ?></span>
                  <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; endforeach; ?>

        <?php
        $offlineFeatures = [
            'umum' => ['Sertifikat Calon Ahli K3 Umum Kemnaker RI'],
            'utusan' => ['Sertifikat, <strong>SKP &amp; Lisensi</strong> Kemnaker RI (berlaku 3 tahun)'],
        ];
        $offlineCommon = ['<strong>12 hari</strong> tatap muka penuh di Hotel Emersia Malioboro', 'Modul hardcopy &amp; himpunan perundang-undangan K3', '<strong>Training kit</strong> eksklusif', 'Free Basic Safety Training (Damkar &amp; PMI)', 'Free 43 e-sertifikat HSE Bootcamp', 'Wisata Lava Tour Merapi', 'Career support, konsultasi &amp; webinar 1 tahun'];
        foreach ($offlinePrices as $cat => [$normal, $promo]): ?>
        <div data-price-grid="offline-<?= $cat ?>" class="hidden grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 items-stretch max-w-4xl mx-auto">
          <div class="w-full bg-white rounded-3xl p-7 sm:p-9 border-2 border-slate-200 shadow-sm flex flex-col">
            <span class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">BLENDED VS FULL OFFLINE</span>
            <h3 class="text-2xl font-black text-brand-navy mb-4 tracking-tight">Sertifikatnya sama, bedanya cara belajar</h3>
            <div class="divide-y divide-slate-100 text-xs sm:text-sm">
              <?php foreach ([['Lama pelatihan', '12 hari', '12 hari'], ['Materi hari 1–9', 'Online via Zoom', 'Tatap muka'], ['PKL, seminar &amp; ujian', 'Tatap muka', 'Tatap muka'], ['Ke Yogyakarta', '3 hari', '12 hari'], ['Sertifikat &amp; bonus', 'Sama', 'Sama'], ['Wisata Lava Tour Merapi', '–', 'Ya']] as [$k, $bl, $of]): ?>
                <div class="grid grid-cols-3 gap-2 py-2.5">
                  <span class="text-slate-500 font-medium"><?= $k ?></span>
                  <span class="font-bold text-brand-navy text-center"><?= $bl ?></span>
                  <span class="font-bold text-brand-blue text-center"><?= $of ?></span>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="grid grid-cols-3 gap-2 pt-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400"><span></span><span class="text-center">Blended</span><span class="text-center">Full Offline</span></div>
            <button type="button" onclick="setPricing('blended')" class="mt-auto pt-6 text-sm font-bold text-brand-blue hover:text-brand-navy text-left cursor-pointer"><i class="fa-solid fa-arrow-left text-xs mr-1"></i>Lihat harga Blended (mulai Rp <?= $jt($fromPromo) ?>)</button>
          </div>

          <div class="w-full bg-brand-navy text-white rounded-3xl p-7 sm:p-9 shadow-2xl relative flex flex-col justify-between border border-brand-blue/60 overflow-hidden">
            <div class="absolute top-0 right-0"><div class="bg-brand-accent text-brand-dark font-black text-[10px] sm:text-xs px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow-md">12 HARI TATAP MUKA</div></div>
            <div>
              <span class="text-xs font-black uppercase tracking-wider text-brand-accent">FULL OFFLINE</span>
              <h3 class="text-2xl font-black text-white mt-3 mb-1 tracking-tight"><?= $cat === 'umum' ? 'Peserta Umum' : 'Utusan Perusahaan' ?></h3>
              <div class="my-5">
                <?php if ($normal): ?><span class="block text-sm font-bold line-through text-red-300"><?= $rp($normal) ?></span><?php endif; ?>
                <span class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-brand-accent"><?= $rp($promo) ?></span>
                <p class="text-xs font-bold mt-1.5 text-brand-accent"><?= $normal ? 'Hemat Rp ' . $jt($normal - $promo) . ' · ' : '' ?>cukup DP untuk amankan kursi</p>
              </div>
              <div class="w-full border-t my-6 border-white/15"></div>
              <div class="space-y-3.5 text-xs sm:text-sm text-slate-200">
                <?php foreach (array_merge($offlineFeatures[$cat], $offlineCommon) as $f): ?>
                  <div class="flex items-start gap-3"><i class="fa-solid fa-check text-xs mt-1 flex-shrink-0 text-brand-accent"></i><span><?= $f ?></span></div>
                <?php endforeach; ?>
              </div>
              <p class="mt-4 text-[11px] text-slate-400">Butuh penginapan selama 12 hari? Admin siap bantu koordinasi hotel.</p>
            </div>
            <div class="mt-8 pt-4">
              <a href="#registration" onclick="pickPackage('offline-<?= $cat ?>')" class="w-full font-black text-sm py-3.5 px-5 rounded-2xl transition duration-200 flex items-center justify-center gap-2 cursor-pointer bg-brand-accent hover:bg-brand-accentHover text-brand-dark shadow-lime">
                <span>Pilih Full Offline</span><i class="fa-solid fa-arrow-right text-xs"></i>
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <div class="max-w-4xl mx-auto mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div class="rounded-2xl bg-brand-subtle border border-blue-100 p-4"><p class="font-extrabold text-brand-navy mb-1"><i class="fa-solid fa-utensils text-brand-blue mr-1.5"></i>Semua paket termasuk</p><p class="text-slate-600">Coffee break &amp; makan siang selama sesi tatap muka, pin &amp; lencana K3.</p></div>
          <div class="rounded-2xl bg-lime-50/70 border border-lime-200/60 p-4"><p class="font-extrabold text-brand-navy mb-1"><i class="fa-solid fa-wallet text-lime-700 mr-1.5"></i>Bayar bertahap</p><p class="text-slate-600">Cukup DP untuk mengunci kursi, pelunasan di hari pertama pelatihan. Admin mengonfirmasi biaya final sebelum invoice terbit.</p></div>
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
        <p class="text-xs sm:text-sm md:text-base text-slate-500 font-medium">Cukup 6 persyaratan dan 4 langkah mudah. Semua dokumen dikirim lewat WhatsApp.</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-card">
          <h3 class="text-lg font-extrabold text-brand-navy mb-5"><span class="inline-flex w-7 h-7 rounded-lg bg-brand-accent text-brand-dark items-center justify-center text-sm font-black mr-2">A</span>Dokumen persyaratan</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ([['fa-graduation-cap', 'Ijazah D3/S1', 'Semua jurusan, asli atau legalisir cap basah'], ['fa-id-card', 'Scan KTP', 'Jelas dan terbaca'], ['fa-image-portrait', 'Pas foto', 'Latar belakang merah'], ['fa-file-signature', 'Surat pernyataan', 'Pernyataan personel & pakta integritas'], ['fa-building', 'Surat keterangan bekerja', 'Khusus utusan perusahaan'], ['fa-laptop', 'Laptop/tablet', 'Khusus Blended, bisa mengoperasikan Zoom']] as [$i, $t, $d]): ?>
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
            <?php foreach ([['Isi form & chat admin', 'Sebutkan jadwal dan paket pilihan kamu.'], ['Kirim dokumen', 'Admin memeriksa kelengkapan persyaratan.'], ['Bayar DP', 'Kursi kamu langsung aman. Pelunasan di hari pertama pelatihan.'], ['Siap pelatihan', 'Masuk grup WhatsApp peserta untuk info teknis.']] as $n => [$t, $d]): ?>
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
            Amankan kursi di batch <strong class="text-brand-navy"><?= $h($batchLong) ?></strong> cukup dengan DP, pelunasan di hari pertama pelatihan. Isi data singkat, lalu langsung chat admin di WhatsApp.
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
                  <input type="text" id="regFullName" required autocomplete="name" placeholder="Nama lengkap sesuai ijazah" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm font-medium text-slate-800 transition">
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
                  <optgroup label="Blended (9 hari online + 3 tatap muka)">
                  <?php foreach ($prices as $cat => $stays): foreach ($stays as $stay => $tiers): foreach ($tiers as $tier => $_): $lbl = $packageLabel($cat, $stay, $tier); ?>
                    <option value="<?= $h($lbl) ?>" data-pkg="<?= "blended-$cat-$stay-$tier" ?>"><?= $h($lbl) ?></option>
                  <?php endforeach; endforeach; endforeach; ?>
                  </optgroup>
                  <optgroup label="Full Offline (12 hari tatap muka)">
                  <?php foreach ($offlinePrices as $cat => $_): $lbl = $offlineLabel($cat); ?>
                    <option value="<?= $h($lbl) ?>" data-pkg="offline-<?= $cat ?>"><?= $h($lbl) ?></option>
                  <?php endforeach; ?>
                  </optgroup>
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
                Terima kasih, data Anda sudah kami terima. Kami arahkan ke WhatsApp admin untuk konfirmasi kuota dan jadwal batch. Jika tidak terbuka otomatis, klik tombol di bawah.
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
                  <span class="text-slate-500 font-medium">Skema Dipilih:</span>
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
          Pahami seluruh informasi terkait syarat, keabsahan lisensi Kemnaker RI, alur pelatihan, hingga kemudahan konsultasi bersama <strong class="text-brand-navy font-extrabold">Artha Safety Indonesia</strong>.
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

          <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed mb-6 lg:mb-0">Bergabung dengan 4.000+ alumni Ahli K3 Umum dari 100+ perusahaan dan 50+ perguruan tinggi di seluruh Indonesia. Di komunitas alumni, Anda bisa berbagi info lowongan, bertukar pengalaman, dan terus belajar bersama praktisi K3
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
            <img
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
              PT Artha Safety Indonesia adalah Perusahaan Jasa Keselamatan dan Kesehatan Kerja (PJK3) resmi yang ditunjuk oleh Kementerian Ketenagakerjaan RI, berkomitmen mencetak profesional K3 berdaya saing global di industri.
            </p>

            <div class="pt-3">
              <h5 class="text-[11px] font-black uppercase tracking-wider text-brand-navy mb-3 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-brand-accent"></span>
                <span>DILISENSIKAN OLEH:</span>
              </h5>

              <!-- LOGO RESMI KEMNAKER RI & BNSP TANPA BACKGROUND PUTIH -->
              <div class="flex flex-wrap items-center gap-5">
                <img
                  src="https://temank3.kemnaker.go.id/public/themes/website/asset/img/logo.png"
                  alt="Kementerian Ketenagakerjaan RI"
                  class="h-8 sm:h-9 w-auto max-w-[150px] object-contain brightness-0 opacity-80 hover:opacity-100 transition"
                  onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/Logo_Kementerian_Ketenagakerjaan_%282016%29.png/320px-Logo_Kementerian_Ketenagakerjaan_%282016%29.png';"
                >

                <img
                  src="https://bnsp.go.id/assets/landing-page/v3/img/bnsp-black.png"
                  alt="BNSP - Badan Nasional Sertifikasi Profesi"
                  class="h-7 sm:h-8 w-auto max-w-[110px] object-contain opacity-85 hover:opacity-100 transition"
                  onerror="this.onerror=null; this.src='https://bnsp.go.id/assets/landing-page/v3/img/bnsp-black.png';"
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
            <li><a href="#curriculum" class="hover:text-brand-blue transition">Sertifikasi KEMNAKER RI</a></li>
            <li><a href="#certification" class="hover:text-brand-blue transition">Sertifikasi BNSP</a></li>
            <li><a href="#pricing" class="hover:text-brand-blue transition">Ahli K3 Umum (Blended)</a></li>
            <li><a href="#pricing" class="hover:text-brand-blue transition">Ahli K3 Umum Utusan Perusahaan</a></li>
            <li><button type="button" onclick="openModal('advisorModal')" class="hover:text-brand-blue transition text-left cursor-pointer">Corporate In-House</button></li>
            <li><a href="#curriculum" class="hover:text-brand-blue transition">Awareness SMK3 PP 50/2012</a></li>
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
          <span>PJK3 Resmi Terakreditasi Kementerian Ketenagakerjaan Republik Indonesia</span>
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
      <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Mulai <span class="line-through text-red-500"><?= $jt($fromNormal) ?></span> <span class="text-brand-navy font-extrabold">Rp <?= $jt($fromPromo) ?></span></span>
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
          <span class="truncate">PT Artha Safety Indonesia - Pelatihan Ahli K3 Umum Kemnaker RI</span>
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
          title="Video Pelatihan Ahli K3 Umum Kemnaker RI - PT Artha Safety Indonesia"
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
          <p class="text-xs text-slate-500 font-medium">Layanan Informasi & Syarat Kemnaker</p>
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
          <label class="block text-slate-700 font-bold mb-1">Kategori Kepesertaan *</label>
          <select id="advStatus" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy bg-white">
            <option value="Peserta Umum">Peserta Umum (fresh graduate / belum bekerja)</option>
            <option value="Utusan Perusahaan">Utusan Perusahaan (SKP & Lisensi)</option>
            <option value="Belum tahu">Belum tahu, mohon dibantu</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-700 font-bold mb-1">Pertanyaan / Topik Konsultasi</label>
          <textarea id="advNotes" rows="2" placeholder="Tanyakan info jadwal batch, persyaratan, atau sistem invoice..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy"></textarea>
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
      },
      {
        url: "../assets/img/dok-lava-tour.jpg",
        title: "Wisata Gratis",
        category: "Lava tour Merapi",
        badge: "Fasilitas Pelatihan"
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
        '<img src="' + nextItem.url + '" alt="' + nextItem.title + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src=\'https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3\';">' +
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

      var message = 'Halo PT Artha Safety Indonesia, saya ingin daftar Pelatihan Ahli K3 Umum Kemnaker RI di Yogyakarta.\n\n' +
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

      var message = 'Halo PT Artha Safety Indonesia, saya ingin konsultasi mengenai Pelatihan Ahli K3 Umum Kemnaker RI.\n\n*Nama:* ' + name + '\n*Skema & Kategori:* ' + status + '\n*Pertanyaan/Catatan:* ' + (notes || '-');
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

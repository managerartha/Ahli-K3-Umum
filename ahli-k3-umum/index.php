<?php
// Kode program ini = nama folder landing page (lihat includes/programs.php).
$PROGRAM = 'ahli-k3-umum';

// Tanggal batch diatur dari dashboard admin (admin/pengaturan.php).
// Jika database belum siap, landing page tetap tampil dengan tanggal bawaan.
$batchLong = '26 Okt - 6 November 2026';
$batchShort = '26 Okt-6 Nov 2026';
try {
    require_once dirname(__DIR__) . '/includes/bootstrap.php';
    $batchLong = batch_long($PROGRAM) ?: $batchLong;
    $batchShort = batch_short($PROGRAM) ?: $batchShort;
} catch (Throwable $e) {
    error_log('Landing page settings: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <title>Ahli K3 Umum Kemnaker RI - PT Artha Safety Indonesia</title>

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

  <!-- Tailwind CSS CDN v3 -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              navy: '#071C6E',        /* Deep Royal Navy */
              blue: '#0D3BE8',        /* Vibrant Electric Royal Blue sesuai branding */
              royal: '#0A2CB7',       /* Hero Blue sesuai branding */
              accent: '#A6F634',      /* Electric Lime Green aksen */
              accentHover: '#92E022',  /* Lime Green hover */
              subtle: '#EEF3FF',      /* Ice Blue tint */
              dark: '#041040'         /* Deepest navy untuk teks di atas lime */
            }
          },
          boxShadow: {
            card: '0 10px 30px -5px rgba(7, 28, 110, 0.07), 0 4px 6px -2px rgba(7, 28, 110, 0.04)',
            btn: '0 4px 14px 0 rgba(13, 59, 232, 0.30)',
            lime: '0 4px 16px 0 rgba(166, 246, 52, 0.40)'
          }
        }
      }
    }
  </script>

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
    window.currentPricingCategory = 'umum';

    function switchPricingCategory(category) {
      window.currentPricingCategory = category;
      var gridUmum = document.getElementById('pricingUmumGrid');
      var gridPerusahaan = document.getElementById('pricingPerusahaanGrid');
      var tabUmum = document.getElementById('tabUmum');
      var tabPerusahaan = document.getElementById('tabPerusahaan');

      if (!gridUmum || !gridPerusahaan) return;

      if (category === 'umum') {
        gridUmum.style.setProperty('display', 'grid', 'important');
        gridPerusahaan.style.setProperty('display', 'none', 'important');
        if (tabUmum) {
          tabUmum.className = 'px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 bg-brand-navy text-white shadow-sm flex items-center gap-2 cursor-pointer';
        }
        if (tabPerusahaan) {
          tabPerusahaan.className = 'px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 bg-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 cursor-pointer';
        }
      } else {
        gridUmum.style.setProperty('display', 'none', 'important');
        gridPerusahaan.style.setProperty('display', 'grid', 'important');
        if (tabPerusahaan) {
          tabPerusahaan.className = 'px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 bg-brand-navy text-white shadow-sm flex items-center gap-2 cursor-pointer';
        }
        if (tabUmum) {
          tabUmum.className = 'px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 bg-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 cursor-pointer';
        }
      }
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

    function selectGoal(goalValue) {
      var radio = document.getElementById('goal_' + goalValue);
      if (radio) radio.checked = true;

      var cards = document.querySelectorAll('.goal-card');
      cards.forEach(function(c) {
        c.classList.remove('border-brand-navy', 'bg-blue-50/50', 'ring-1', 'ring-brand-navy');
        c.classList.add('border-slate-200');
        var dot = c.querySelector('.goal-dot');
        if (dot) {
          dot.classList.remove('bg-brand-navy', 'border-brand-navy');
          dot.classList.add('border-slate-300', 'bg-white');
        }
      });
      var selectedCard = document.getElementById('goal_card_' + goalValue);
      if (selectedCard) {
        selectedCard.classList.remove('border-slate-200');
        selectedCard.classList.add('border-brand-navy', 'bg-blue-50/50', 'ring-1', 'ring-brand-navy');
        var selDot = selectedCard.querySelector('.goal-dot');
        if (selDot) {
          selDot.classList.remove('border-slate-300', 'bg-white');
          selDot.classList.add('bg-brand-navy', 'border-brand-navy');
        }
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

      for (var i = 1; i <= 14; i++) {
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
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center gap-2 sm:gap-3 text-center">
      <span class="bg-brand-accent text-brand-dark font-black px-2.5 py-0.5 rounded-full text-[11px] tracking-wider uppercase shadow-sm">
        12 HARI + BONUS
      </span>
      <span class="font-medium text-slate-200">
        Sertifikasi Ahli K3 Umum Resmi Kemnaker RI Bersama PT Artha Safety Indonesia.
      </span>
      <a href="#registration" class="hidden sm:inline-flex underline hover:text-brand-accent font-bold items-center gap-1 transition">
        <span>Daftar Sekarang</span>
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
            src="https://arthasafetyindonesia.co.id/landing/assets/images/home/logo_artha.png"
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
            Jadi Ahli K3 Umum bersertifikat resmi Kemnaker RI dalam 12 hari. Terbuka untuk lulusan D3, D4, dan S1 dari semua jurusan, termasuk fresh graduate yang belum punya pengalaman K3.
          </p>

          <!-- Social Proof / Badge Alumni Asli Resmi -->
          <div class="flex flex-wrap items-center gap-4 mb-10 pb-2 border-b border-slate-100">
            <div class="flex -space-x-2.5 overflow-hidden">
              <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-sm" src="https://lh3.googleusercontent.com/d/1B9OA62DpfHu6KFeSHlKgU37i5jCkYkzD" alt="Alumni K3 1">
              <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-sm" src="https://lh3.googleusercontent.com/d/1Asd-BiKtyrpoXCXDl2x1zVbqdluuFcla" alt="Alumni K3 2">
              <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white object-cover shadow-sm" src="https://lh3.googleusercontent.com/d/1mcax3BankVmKBh4r9mefzsJvph3NRAbc" alt="Alumni K3 3">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full ring-2 ring-white bg-brand-navy text-[11px] font-bold text-white shadow-sm">
                +4.8k
              </div>
            </div>

            <div class="flex flex-col text-sm">
              <div class="flex items-center gap-1 text-brand-navy font-bold">
                <span>3,500+ Ahli K3 Tersertifikasi</span>
                <div class="flex text-amber-400 text-xs ml-1">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </div>
              </div>
              <span class="text-xs font-medium text-slate-500">PJK3 Resmi Terakreditasi Kemnaker RI</span>
            </div>
          </div>

          <!-- Box Snapshot Program -->
          <div class="w-full rounded-2xl border border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="bg-brand-subtle/80 px-5 py-3 flex items-center justify-between border-b border-blue-100/70">
              <span class="text-xs font-bold text-brand-navy uppercase tracking-wider">Ringkasan Program</span>
              <span class="text-xs font-semibold text-brand-blue flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Kelas Blended & Offline
              </span>
            </div>

            <div class="p-5 sm:p-6">
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                <div class="sm:col-span-4 border-b sm:border-b-0 sm:border-r border-slate-100 pb-4 sm:pb-0">
                  <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">DURASI SERTIFIKASI</p>
                  <p class="text-3xl font-black text-brand-navy tracking-tight">12 Hari</p>
                  <p class="text-xs text-slate-500 font-medium mt-0.5">Sesuai Silabus Regulasi + Bonus</p>
                </div>

                <div class="sm:col-span-5 bg-lime-50/70 rounded-xl p-3.5 border border-lime-200/60">
                  <div class="flex items-center gap-1.5 text-lime-700 font-bold text-[11px] uppercase tracking-wider mb-0.5">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>BATCH TERDEKAT</span>
                  </div>
                  <h4 class="font-bold text-brand-navy text-sm leading-snug"><?= htmlspecialchars($batchLong) ?></h4>
                  <p class="text-[11px] text-slate-600 mt-0.5">Kuota Terbatas</p>
                </div>

                <div class="sm:col-span-3 flex sm:justify-end">
                  <a href="#registration" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-4 py-3 rounded-xl transition duration-200 shadow-md">
                    <span>Daftar</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                  </a>
                </div>
              </div>

              <div class="mt-5 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="w-full sm:w-1/2 bg-slate-100 h-2 rounded-full overflow-hidden">
                  <div class="bg-gradient-to-r from-brand-accent to-lime-500 h-full w-[80%] rounded-full"></div>
                </div>
                <div class="text-[11px] font-medium text-slate-500 flex items-center gap-2">
                  <span>Pendidikan min. D3</span>
                  <span class="text-slate-300">&bull;</span>
                  <span>Semua Jurusan</span>
                  <span class="text-slate-300">&bull;</span>
                  <span>Semua Kalangan</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="border-b border-slate-200/90 pt-2 sticky top-16 sm:top-20 bg-white/95 backdrop-blur-md z-20">
          <nav class="flex items-center gap-6 sm:gap-8 overflow-x-auto no-scrollbar text-sm font-bold pb-3.5">
            <a href="#description" class="text-brand-navy hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Description</a>
            <a href="#curriculum" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Curriculum</a>
            <a href="#portfolio" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Legalitas PJK3</a>
            <a href="#certification" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Certification</a>
            <a href="#facilities" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Fasilitas Kami</a>
            <a href="#reviews" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Ulasan Alumni</a>
            <a href="#documentation" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Dokumentasi</a>
            <a href="#pricing" class="text-slate-500 hover:text-brand-blue pb-3.5 whitespace-nowrap transition">Pricing</a>
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
        </section>

        <section id="curriculum" class="w-full rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-9 shadow-card">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
              <h2 class="hero-title text-3xl sm:text-4xl text-brand-navy">
                Curriculum
              </h2>
              <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                Materi sesuai standar pembinaan Kemnaker RI, disusun bertahap dari dasar hingga siap ujian.
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
                src="https://lh3.googleusercontent.com/d/1NZAFSA0FI4mrz5nG6yJg6AdI0v3nraiR"
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

            <a href="#registration" class="w-full bg-white hover:bg-slate-50 text-brand-navy font-bold py-2.5 sm:py-3 px-4 rounded-xl border border-brand-navy/20 transition duration-200 flex items-center justify-center gap-2 text-xs sm:text-sm">
              <span>Amankan Kuota</span>
              <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
            </a>
          </div>

          <!-- Program Details List -->
          <div class="divide-y divide-slate-100 text-xs sm:text-[13px]">
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Durasi Pelatihan</span>
              <span class="font-extrabold text-brand-navy text-right">12 Hari Pembinaan</span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Batch Terdekat</span>
              <span class="font-extrabold text-brand-navy text-right"><?= htmlspecialchars($batchShort) ?></span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Biaya mulai</span>
              <span class="font-extrabold text-brand-navy text-right">Rp 3.850.000</span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Persyaratan</span>
              <span class="font-extrabold text-brand-navy text-right">Ijazah min. D3</span>
            </div>
            <div class="py-2 sm:py-2.5 flex items-center justify-between">
              <span class="text-slate-500 font-medium">Lokasi Offline</span>
              <span class="font-extrabold text-brand-navy text-right">Hotel Emersia Malioboro Yogyakarta</span>
            </div>
          </div>

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
              src="https://lh3.googleusercontent.com/d/1ydQnc8vwT24SwXUK7HS2JBcynGQSHGCw"
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
                Sertifikat kompetensi penunjang sebanyak 40 e-sertifikat
              </span>
            </div>
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
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

        <div class="lg:col-span-5 flex justify-center">
          <div class="relative w-full max-w-[420px] rounded-2xl overflow-hidden shadow-md bg-white border border-blue-200/50">
            <img
              src="https://arthasafetyindonesia.co.id/storage/training-benefits/posters/ZbuNdRnShBnlbfzt4EtJIxYxHaqIpaTmbvy28lvx.png"
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
            STANDAR RESMI PEMBINAAN KEMNAKER RI
          </p>

          <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-6">
            Selama mengikuti program pembinaan Ahli K3 Umum bersama PT Artha Safety Indonesia, peserta akan mendapatkan fasilitas komprehensif untuk menunjang kenyamanan, kualitas belajar, serta kesiapan implementasi dokumen di tempat kerja secara optimal.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-4 border-t border-slate-200/80">
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Modul hardcopy dan buku himpunan peraturan K3</span>
            </div>

            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Kemeja safety dan kaos</span>
            </div>

            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Training Kit (Ransel, Notebook, LED Tumbler)</span>
            </div>

            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Pin & Lencana Ahli K3</span>
            </div>

            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Makan siang & coffee break selama pelatihan</span>
            </div>

            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-brand-blue text-sm mt-0.5 flex-shrink-0"></i>
              <span class="text-xs sm:text-sm font-bold text-slate-800">Wisata Lava Tour Merapi (kelas offline)</span>
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
        <span>Ulasan Google &bull; Rating 5.0</span>
      </div>

      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-4 leading-tight">
        Kata Alumni Pelatihan<br class="hidden sm:inline"> Artha Safety
      </h2>
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
                "Penyelenggara PJK3 resmi yang sangat profesional. Fasilitas pelatihan lengkap, studi kasus audit SMK3 dan observasi PKL industri benar-benar mengasah kemampuan implementasi di tempat kerja."
              </p>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
              <div class="w-10 h-10 rounded-full bg-brand-navy text-white font-black text-xs flex items-center justify-center flex-shrink-0">BS</div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 leading-none">Budi Santoso</h4>
                <p class="text-[11px] text-slate-500 mt-1">Supervisor HSE Manufaktur</p>
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
            <img src="https://lh3.googleusercontent.com/d/16pSUSYQY_Mkd4he5iUJ1JgbZ1RVHTJm7" alt="Praktik Pemadaman Api APAR" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
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
            <img src="https://lh3.googleusercontent.com/d/1lM46Tm8njbAlHOlrMPSo0MCqrazKAxpc" alt="Observasi Objek Norma K3" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
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
            <img src="https://lh3.googleusercontent.com/d/1NJqo1ick-ssTvCQTD1D0qBDwfsK970Cu" alt="Simulasi Evakuasi & Fire Drill" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
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
            <img src="https://lh3.googleusercontent.com/d/1vLw_oUJ4zRDh7URJi6sHuzfJC_bGKfLZ" alt="Pelatihan Penggunaan SCBA" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.onerror=null; this.src='https://placehold.co/400x500/071C6E/ffffff?text=Dokumentasi+K3';">
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
            Pilih skema pelatihan terbaik sesuai kebutuhan karir atau penugasan resmi perusahaan Anda.
          </p>

          <!-- Pill Switcher -->
          <div class="mt-8 inline-flex p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80 shadow-inner">
            <button
              type="button"
              id="tabUmum"
              onclick="switchPricingCategory('umum')"
              class="px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 bg-brand-navy text-white shadow-sm flex items-center gap-2 cursor-pointer"
            >
              <i class="fa-solid fa-user-graduate text-xs"></i>
              <span>Peserta Umum</span>
            </button>
            <button
              type="button"
              id="tabPerusahaan"
              onclick="switchPricingCategory('perusahaan')"
              class="px-5 sm:px-7 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all duration-200 bg-transparent text-slate-600 hover:text-slate-900 flex items-center gap-2 cursor-pointer"
            >
              <i class="fa-solid fa-building text-xs"></i>
              <span>Utusan Perusahaan</span>
            </button>
          </div>
        </div>

        <!-- 1. GRID KARTU HARGA: PESERTA UMUM -->
        <div
          id="pricingUmumGrid"
          style="display: grid;"
          class="grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 items-stretch max-w-4xl mx-auto"
        >
          <div class="w-full bg-white rounded-3xl p-7 sm:p-9 border-2 border-slate-200 shadow-sm flex flex-col justify-between transition-all hover:border-brand-blue/50 hover:shadow-md">
            <div>
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">BASIC PACKAGE</span>
                <span class="bg-blue-50 text-brand-blue border border-blue-200 text-[11px] font-bold px-2.5 py-1 rounded-full">
                  9 Online + 3 Offline
                </span>
              </div>

              <h3 class="text-2xl font-black text-brand-navy mb-1 tracking-tight">Blended Training</h3>
              <div class="my-5">
                <span class="text-3xl sm:text-4xl lg:text-5xl font-black text-brand-navy tracking-tight">Rp 3.850.000</span>
                <p class="text-xs text-slate-500 font-medium mt-1.5">Pilihan paling fleksible dan hemat biaya</p>
              </div>

              <div class="w-full border-t border-slate-100 my-6"></div>

              <div class="space-y-3.5 text-xs sm:text-sm text-slate-700">
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>9 Hari</strong> Pembinaan Teori Online (Live Zoom Interaktif)</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>3 Hari</strong> PKL dan Ujian Offline</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>Sertifikat</strong> Pembinaan Ahli K3 Umum Kemnaker RI</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span>Free Basic Safety Training (Damkar & PMI)</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span>Free HSE Training 40 e-sertifikat</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span>Akses materi seumur hidup & komunitas Alumni</span>
                </div>
              </div>
            </div>

            <div class="mt-8 pt-4">
              <a
                href="#registration"
                onclick="handlePricingAction('Blended Training', 'Peserta Umum')"
                class="w-full bg-white hover:bg-slate-50 text-brand-navy font-bold text-sm py-3.5 px-5 rounded-2xl border-2 border-brand-navy transition duration-200 flex items-center justify-center gap-2 shadow-sm cursor-pointer hover:border-brand-blue hover:text-brand-blue"
              >
                <span>Pilih Skema Blended</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </a>
            </div>
          </div>

          <div class="w-full bg-brand-navy text-white rounded-3xl p-7 sm:p-9 shadow-2xl relative flex flex-col justify-between border border-brand-blue/60 group overflow-hidden">
            <div class="absolute top-0 right-0">
              <div class="bg-brand-accent text-brand-dark font-black text-[10px] sm:text-xs px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow-md">
                BEST VALUE
              </div>
            </div>

            <div>
              <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-brand-accent">FULL PACKAGE</span>
                <span class="bg-white/10 text-white border border-white/20 text-[11px] font-bold px-2.5 py-1 rounded-full">
                  12 Hari Tatap Muka
                </span>
              </div>

              <h3 class="text-2xl font-black text-white mb-1 tracking-tight">Full Offline Training</h3>
              <div class="my-5">
                <span class="text-3xl sm:text-4xl lg:text-5xl font-black text-brand-accent tracking-tight">Rp 5.000.000</span>
                <p class="text-xs text-slate-300 font-medium mt-1.5">Pilihan terbaik untuk pengalaman langsung</p>
              </div>

              <div class="w-full border-t border-white/15 my-6"></div>

              <div class="space-y-3.5 text-xs sm:text-sm text-slate-200">
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>12 Hari</strong> Tatap Muka Penuh di Hotel Emersia Malioboro</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Sertifikat Pembinaan Ahli K3 Umum Kemnaker RI</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>Training Kit</strong> Eksklusif</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Free Basic Safety Training (Damkar & PMI)</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Free HSE Training 40 e-sertifikat</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Materi hardcopy dan akses komunitas Alumni</span>
                </div>
              </div>
            </div>

            <div class="mt-8 pt-4">
              <a
                href="#registration"
                onclick="handlePricingAction('Full Offline Training', 'Peserta Umum')"
                class="w-full bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black text-sm py-3.5 px-5 rounded-2xl transition duration-200 flex items-center justify-center gap-2 shadow-lime group-hover:scale-[1.01] cursor-pointer"
              >
                <span>Daftar Full Offline</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- 2. GRID KARTU HARGA: UTUSAN PERUSAHAAN -->
        <div
          id="pricingPerusahaanGrid"
          style="display: none;"
          class="grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 items-stretch max-w-4xl mx-auto"
        >
          <div class="w-full bg-white rounded-3xl p-7 sm:p-9 border-2 border-slate-200 shadow-sm flex flex-col justify-between transition-all hover:border-brand-blue/50 hover:shadow-md">
            <div>
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">BASIC PACKAGE</span>
                <span class="bg-blue-50 text-brand-blue border border-blue-200 text-[11px] font-bold px-2.5 py-1 rounded-full">
                  9 Online + 3 Offline
                </span>
              </div>

              <h3 class="text-2xl font-black text-brand-navy mb-1 tracking-tight">Blended Training</h3>
              <div class="my-5">
                <span class="text-3xl sm:text-4xl lg:text-5xl font-black text-brand-navy tracking-tight">Rp 5.550.000</span>
                <p class="text-xs text-slate-500 font-medium mt-1.5">Termasuk Penerbitan SKP & Lisensi Kemnaker RI</p>
              </div>

              <div class="w-full border-t border-slate-100 my-6"></div>

              <div class="space-y-3.5 text-xs sm:text-sm text-slate-700">
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>9 Hari</strong> Pembinaan Teori Online (Live Zoom Interaktif)</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>3 Hari</strong> PKL dan Ujian Offline</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>Sertifikat, SKP dan Lisensi</strong> Kemnaker RI</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span>Free Basic Safety Training (Damkar & PMI)</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span>Free HSE Training 40 e-sertifikat</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-blue text-xs mt-1 flex-shrink-0"></i>
                  <span>Akses materi seumur hidup & komunitas Alumni</span>
                </div>
              </div>
            </div>

            <div class="mt-8 pt-4">
              <a
                href="#registration"
                onclick="handlePricingAction('Blended Training', 'Utusan Perusahaan')"
                class="w-full bg-white hover:bg-slate-50 text-brand-navy font-bold text-sm py-3.5 px-5 rounded-2xl border-2 border-brand-navy transition duration-200 flex items-center justify-center gap-2 shadow-sm cursor-pointer hover:border-brand-blue hover:text-brand-blue"
              >
                <span>Pilih Skema Blended</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </a>
            </div>
          </div>

          <div class="w-full bg-brand-navy text-white rounded-3xl p-7 sm:p-9 shadow-2xl relative flex flex-col justify-between border border-brand-blue/60 group overflow-hidden">
            <div class="absolute top-0 right-0">
              <div class="bg-brand-accent text-brand-dark font-black text-[10px] sm:text-xs px-4 py-1.5 rounded-bl-2xl uppercase tracking-wider shadow-md">
                BEST VALUE
              </div>
            </div>

            <div>
              <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-brand-accent">FULL PACKAGE</span>
                <span class="bg-white/10 text-white border border-white/20 text-[11px] font-bold px-2.5 py-1 rounded-full">
                  12 Hari Tatap Muka
                </span>
              </div>

              <h3 class="text-2xl font-black text-white mb-1 tracking-tight">Full Offline Training</h3>
              <div class="my-5">
                <span class="text-3xl sm:text-4xl lg:text-5xl font-black text-brand-accent tracking-tight">Rp 6.500.000</span>
                <p class="text-xs text-slate-300 font-medium mt-1.5">Termasuk Penerbitan SKP & Lisensi Kemnaker RI</p>
              </div>

              <div class="w-full border-t border-white/15 my-6"></div>

              <div class="space-y-3.5 text-xs sm:text-sm text-slate-200">
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>12 Hari</strong> Tatap Muka Penuh di Hotel Emersia Malioboro</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Training Kit Eksklusif</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span><strong>Sertifikat, SKP & Lisensi</strong> Kemnaker RI</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Free Basic Safety Training (Damkar & PMI)</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Free HSE Training 40 e-sertifikat</span>
                </div>
                <div class="flex items-start gap-3">
                  <i class="fa-solid fa-check text-brand-accent text-xs mt-1 flex-shrink-0"></i>
                  <span>Materi hardcopy dan akses komunitas Alumni</span>
                </div>
              </div>
            </div>

            <div class="mt-8 pt-4">
              <a
                href="#registration"
                onclick="handlePricingAction('Full Offline Training', 'Utusan Perusahaan')"
                class="w-full bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-black text-sm py-3.5 px-5 rounded-2xl transition duration-200 flex items-center justify-center gap-2 shadow-lime group-hover:scale-[1.01] cursor-pointer"
              >
                <span>Daftar Full Offline</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
              </a>
            </div>
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
            Tim Artha Safety akan menghubungi Anda pada jam kerja untuk membantu mengecek kesiapan dan jadwal yang sesuai.
          </p>

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
                <p class="text-3xl sm:text-4xl font-black text-white tracking-tight">3500+</p>
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
                  <span>5+</span>
                  <i class="fa-solid fa-star text-amber-400 text-xl sm:text-2xl"></i>
                </p>
                <p class="text-xs text-slate-300 font-medium mt-1">Google Reviews</p>
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
                <label class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  NAMA LENGKAP <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-user text-sm"></i>
                  </div>
                  <input
                    type="text"
                    id="regFullName"
                    required
                    placeholder="Masukkan nama lengkap beserta gelar (jika ada)"
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm font-medium text-slate-800 transition"
                  >
                </div>
              </div>

              <div>
                <label class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  NOMOR WHATSAPP <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                  </div>
                  <input
                    type="tel"
                    id="regWhatsApp"
                    required
                    placeholder="Contoh: 081234567890"
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm font-medium text-slate-800 transition"
                  >
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">
                  Pastikan nomor aktif dan terhubung dengan WhatsApp untuk pengiriman jadwal.
                </p>
              </div>

              <div>
                <label class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  EMAIL <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-envelope text-sm"></i>
                  </div>
                  <input
                    type="email"
                    id="regEmail"
                    required
                    placeholder="contoh: namaanda@gmail.com"
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm font-medium text-slate-800 transition"
                  >
                </div>
              </div>

              <div>
                <label class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-3">
                  SAYA MENGIKUTI PROGRAM UNTUK: <span class="text-red-500">*</span>
                </label>

                <div class="space-y-3">
                  <div id="goal_card_karir" onclick="selectGoal('karir')" class="goal-card border border-brand-navy bg-blue-50/50 ring-1 ring-brand-navy rounded-2xl p-4 flex items-center gap-3.5 cursor-pointer transition hover:border-brand-navy">
                    <input type="radio" name="programGoal" id="goal_karir" value="Pengembangan karier" checked class="hidden">
                    <div class="goal-dot w-5 h-5 rounded-full border border-brand-navy bg-brand-navy flex items-center justify-center flex-shrink-0 transition">
                      <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Pengembangan karier</span>
                  </div>

                  <div id="goal_card_perusahaan" onclick="selectGoal('perusahaan')" class="goal-card border border-slate-200 rounded-2xl p-4 flex items-center gap-3.5 cursor-pointer transition hover:border-slate-400">
                    <input type="radio" name="programGoal" id="goal_perusahaan" value="Persyaratan perusahaan" class="hidden">
                    <div class="goal-dot w-5 h-5 rounded-full border border-slate-300 bg-white flex items-center justify-center flex-shrink-0 transition">
                      <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Persyaratan perusahaan</span>
                  </div>

                  <div id="goal_card_kerja" onclick="selectGoal('kerja')" class="goal-card border border-slate-200 rounded-2xl p-4 flex items-center gap-3.5 cursor-pointer transition hover:border-slate-400">
                    <input type="radio" name="programGoal" id="goal_kerja" value="Mencari pekerjaan" class="hidden">
                    <div class="goal-dot w-5 h-5 rounded-full border border-slate-300 bg-white flex items-center justify-center flex-shrink-0 transition">
                      <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Mencari pekerjaan</span>
                  </div>

                  <div id="goal_card_switch" onclick="selectGoal('switch')" class="goal-card border border-slate-200 rounded-2xl p-4 flex items-center gap-3.5 cursor-pointer transition hover:border-slate-400">
                    <input type="radio" name="programGoal" id="goal_switch" value="Switch career" class="hidden">
                    <div class="goal-dot w-5 h-5 rounded-full border border-slate-300 bg-white flex items-center justify-center flex-shrink-0 transition">
                      <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Switch career</span>
                  </div>
                </div>
              </div>

              <div class="pt-2">
                <label class="block text-xs sm:text-sm font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                  SKEMA PELATIHAN YANG DIMINATI
                </label>
                <select id="regSelectedScheme" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-xs sm:text-sm font-semibold text-slate-800 bg-white">
                  <option value="Peserta Umum - Blended (Rp 3.850.000)">Peserta Umum - Blended Training (Rp 3.850.000)</option>
                  <option value="Peserta Umum - Full Offline (Rp 5.000.000)">Peserta Umum - Full Offline Training (Rp 5.000.000)</option>
                  <option value="Utusan Perusahaan - Blended (Rp 5.550.000)">Utusan Perusahaan - Blended Training (Rp 5.550.000)</option>
                  <option value="Utusan Perusahaan - Full Offline (Rp 6.500.000)">Utusan Perusahaan - Full Offline Training (Rp 6.500.000)</option>
                </select>
              </div>

              <!-- Submit Button -->
              <div class="pt-4">
                <button
                  type="submit"
                  id="regSubmitBtn"
                  class="w-full bg-brand-navy hover:bg-brand-blue text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl transition duration-200 flex items-center justify-center gap-3 shadow-btn cursor-pointer"
                >
                  <span id="regSubmitBtnText">Kirim Pendaftaran</span>
                  <i id="regSubmitBtnIcon" class="fa-solid fa-arrow-right text-xs"></i>
                </button>
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
                Terima kasih, data pendaftaran Anda telah berhasil disimpan. Silakan klik tombol di bawah untuk langsung menghubungi Admin WhatsApp kami guna konfirmasi kuota dan jadwal batch.
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

      <!-- Grid 2 Kolom FAQ (Item 01 - 07 Kiri, Item 08 - 14 Kanan) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 max-w-6xl mx-auto">

        <!-- KOLOM KIRI (01 - 07) -->
        <div class="space-y-4">
          <!-- FAQ 01 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(1)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">01</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Saya bukan lulusan K3, apakah bisa ikut?</span>
              </div>
              <i id="faq-icon-1" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-1" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Sangat bisa! Sesuai regulasi Kemnaker RI, syarat kepesertaan Ahli K3 Umum terbuka untuk minimal D3/D4/S1 dari <strong>semua jurusan</strong> (Teknik, MIPA, Ekonomi, Sosial, Kesehatan, dll.). Seluruh modul disusun dari dasar dan dipandu secara intensif hingga tuntas.
            </div>
          </div>

          <!-- FAQ 02 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(2)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">02</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Apakah pelatihannya full online?</span>
              </div>
              <i id="faq-icon-2" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-2" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Kami menyediakan dua pilihan skema: <strong>Blended Training</strong> (9 hari teori interaktif via Live Zoom + 3 hari praktik observasi PKL & seminar tatap muka) atau opsi <strong>Full Offline Training</strong> (12 hari tatap muka penuh di hotel & Training Center Yogyakarta).
            </div>
          </div>

          <!-- FAQ 03 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(3)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">03</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Kenapa ada sesi offline?</span>
              </div>
              <i id="faq-icon-3" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-3" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Sesi offline diselenggarakan untuk agenda <strong>Praktek Kerja Lapangan (PKL) observasi objek K3 langsung di industri nyata</strong>, bimbingan penyusunan laporan audit PKL kelompok, serta Ujian seminar dan Teori resmi yang diuji langsung oleh Pengawas Spesialis Kemnaker RI.
            </div>
          </div>

          <!-- FAQ 04 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(4)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">04</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Saya tinggal di luar Jogja, bagaimana?</span>
              </div>
              <i id="faq-icon-4" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-4" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Mayoritas peserta kami berasal dari luar kota, luar pulau, dan berbagai provinsi di Indonesia. Untuk sesi online Anda dapat mengikuti dari domisili masing-masing, dan saat sesi tatap muka di Yogyakarta, tim Artha Safety siap membantu koordinasi akomodasi dan paket penginapan hotel.
            </div>
          </div>

          <!-- FAQ 05 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(5)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">05</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Apakah saya harus menginap?</span>
              </div>
              <i id="faq-icon-5" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-5" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Bagi peserta yang berdomisili di Yogyakarta atau memiliki tempat tinggal sendiri, Anda tidak diwajibkan menginap dan dapat memilih skema Reguler. Bagi peserta luar kota, kami menyediakan opsi paket yang telah mencakup kamar hotel berbintang agar nyaman dan fokus belajar.
            </div>
          </div>

          <!-- FAQ 06 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(6)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">06</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Apakah sertifikatnya resmi?</span>
              </div>
              <i id="faq-icon-6" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-6" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              <strong>100% Resmi dan Sah Secara Hukum</strong>. Sertifikat, Surat Keputusan Penunjukan (SKP), dan Lisensi Kewenangan Ahli K3 Umum diterbitkan resmi oleh Kementerian Ketenagakerjaan Republik Indonesia (Kemnaker RI) dengan nomor registrasi unik yang terverifikasi nasional di TemanK3.
            </div>
          </div>

          <!-- FAQ 07 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(7)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">07</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Siapa yang menerbitkan sertifikat?</span>
              </div>
              <i id="faq-icon-7" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-7" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Sertifikat diterbitkan langsung oleh <strong>Direktorat Bina Kelembagaan Keselamatan dan Kesehatan Kerja, Ditjen Binwasnaker & K3 - Kemnaker RI</strong>. PT Artha Safety Indonesia bertindak sebagai PJK3 resmi penyelenggara pembinaan yang ditunjuk kementerian.
            </div>
          </div>
        </div>

        <!-- KOLOM KANAN (08 - 14) -->
        <div class="space-y-4">
          <!-- FAQ 08 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(8)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">08</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Berapa lama prosesnya?</span>
              </div>
              <i id="faq-icon-8" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-8" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Rangkaian pembinaan berlangsung selama <strong>12 hari kerja intensif</strong> (120 JP) ditambah <strong>Hari ke-13 Bonus Praktek BST</strong>. Pasca ujian evaluasi selesai, Surat Keterangan Lulus (SKL) resmi langsung terbit dan dapat digunakan untuk melamar pekerjaan sementara fisik sertifikat & lisensi resmi Kemnaker RI dicetak kementerian.
            </div>
          </div>

          <!-- FAQ 09 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(9)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">09</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Kalau saya belum punya pengalaman K3 bagaimana?</span>
              </div>
              <i id="faq-icon-9" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-9" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Tidak masalah! Kurikulum pembinaan kami dirancang ramah bagi pemula, fresh graduate, maupun profesional yang ingin switch career ke dunia HSE industri. Instruktur akan membimbing langkah demi langkah mulai dari dasar identifikasi bahaya kerja hingga simulasi audit.
            </div>
          </div>

          <!-- FAQ 10 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(10)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">10</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Apakah bisa mengikuti jadwal beberapa bulan lagi?</span>
              </div>
              <i id="faq-icon-10" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-10" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Bisa sekali! PT Artha Safety Indonesia menyelenggarakan batch pelatihan setiap bulan secara rutin. Anda dapat mendaftar dan mengunci kuota peserta untuk batch bulan mendatang sesuai dengan kesiapan waktu Anda.
            </div>
          </div>

          <!-- FAQ 11 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(11)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">11</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Saya belum punya uang sekarang, apakah bisa konsultasi dulu?</span>
              </div>
              <i id="faq-icon-11" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-11" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Tentu sangat bisa! Layanan konsultasi bersama Tim Advisor K3 kami adalah <strong>100% GRATIS</strong>. Anda bebas bertanya mengenai kelengkapan berkas, silabus, prospek karir, hingga opsi pembayaran bertahap (Down Payment / DP) untuk mengamankan kuota.
            </div>
          </div>

          <!-- FAQ 12 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(12)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">12</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Apakah perusahaan bisa mendaftarkan karyawan?</span>
              </div>
              <i id="faq-icon-12" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-12" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Sangat bisa. Kami melayani skema <strong>Utusan Perusahaan</strong> lengkap dengan penerbitan SKP & Lisensi Kewenangan atas nama perusahaan penunjuk, faktur pajak resmi, invoice korporat, hingga pelaksanaan program <em>In-House Training</em> di lokasi perusahaan Anda.
            </div>
          </div>

          <!-- FAQ 13 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(13)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">13</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Bagaimana kalau saya masih harus meminta izin orang tua?</span>
              </div>
              <i id="faq-icon-13" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-13" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              Sangat kami dukung. Anda dapat mengunduh silabus resmi atau menghubungkan orang tua Anda dengan Advisor kami agar mendapatkan penjelasan komprehensif terkait legalitas Kemnaker RI, keamanan fasilitas training, serta prospek karir HSE di sektor industri.
            </div>
          </div>

          <!-- FAQ 14 -->
          <div class="bg-white rounded-2xl border border-blue-100 hover:border-brand-blue/50 p-4 sm:p-5 shadow-xs transition duration-200 group">
            <button type="button" onclick="toggleFaq(14)" class="w-full flex items-center justify-between text-left gap-3.5 focus:outline-none cursor-pointer">
              <div class="flex items-center gap-3 sm:gap-3.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-brand-blue font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">14</span>
                <span class="text-xs sm:text-sm font-extrabold text-brand-navy group-hover:text-brand-blue transition">Apa yang membedakan Artha Safety dengan penyelenggara lain?</span>
              </div>
              <i id="faq-icon-14" class="fa-solid fa-chevron-down text-xs text-brand-blue transition-transform duration-200 shrink-0"></i>
            </button>
            <div id="faq-ans-14" class="hidden pt-3 mt-3 border-t border-slate-100 text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal">
              PT Artha Safety Indonesia adalah PJK3 resmi Kemnaker RI dengan rekam jejak <strong>3.500+ alumni & kelulusan 100%</strong>. Kami menyediakan fasilitas terlengkap (Training kit eksklusif, kemeja safety, observasi pabrik riil), bimbingan ujian intensif, bonus 40 e-sertifikat HSE, gratis praktik Basic Safety Training (BST), serta komunitas <em>Safety Champions Talent</em> untuk peluang karir.
            </div>
          </div>
        </div>

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

          <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed mb-6 lg:mb-0">Bergabung dengan 3.500+ alumni Ahli K3 Umum dari 100+ perusahaan dan 50+ perguruan tinggi di seluruh Indonesia. Di komunitas alumni, Anda bisa berbagi info lowongan, bertukar pengalaman, dan terus belajar bersama praktisi K3
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
              src="https://arthasafetyindonesia.co.id/landing/assets/images/inhouse/cta.png"
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
                src="https://arthasafetyindonesia.co.id/landing/assets/images/home/logo_artha.png"
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
            <li><a href="#pricing" class="hover:text-brand-blue transition">Ahli K3 Umum (Offline)</a></li>
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
          <a href="#description" class="hover:text-brand-blue transition">Privacy Policy</a>
          <span class="text-slate-300">&bull;</span>
          <a href="#description" class="hover:text-brand-blue transition">Terms of Service</a>
        </div>
      </div>

    </div>
  </footer>

  <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_25px_rgba(7,28,110,0.12)] px-4 py-3 flex items-center justify-between">
    <div class="flex flex-col">
      <span class="text-xs sm:text-sm font-black text-brand-navy leading-tight">Batch <?= htmlspecialchars($batchShort) ?></span>
      <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Mulai 3.850.000</span>
    </div>

    <a href="#registration" class="bg-brand-navy hover:bg-brand-blue active:scale-95 text-white font-extrabold text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-btn flex items-center gap-2 transition duration-150">
      <span>Daftar Batch</span>
      <i class="fa-solid fa-arrow-right text-[10px]"></i>
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
          <input type="text" id="advName" required placeholder="Contoh: Budi Santoso" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy">
        </div>
        <div>
          <label class="block text-slate-700 font-bold mb-1">Kategori Kepesertaan *</label>
          <select id="advStatus" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy bg-white">
            <option value="Peserta Umum - Blended">Peserta Umum - Blended (9 Online, 3 Offline)</option>
            <option value="Peserta Umum - Full Offline">Peserta Umum - Full Offline (12 Hari)</option>
            <option value="Utusan Perusahaan - Blended">Utusan Perusahaan - Blended (SKP & Lisensi)</option>
            <option value="Utusan Perusahaan - Full Offline">Utusan Perusahaan - Full Offline (SKP & Lisensi)</option>
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

    // Sumber kunjungan dari iklan/link (utm_source & utm_campaign), disimpan selama sesi
    var trafficSource = (function() {
      var src = { utm_source: '', utm_campaign: '' };
      try {
        var params = new URLSearchParams(window.location.search);
        var saved = JSON.parse(sessionStorage.getItem('artha_utm') || '{}');
        src.utm_source = params.get('utm_source') || saved.utm_source || '';
        src.utm_campaign = params.get('utm_campaign') || saved.utm_campaign || '';
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
        var body = JSON.stringify({ program: LANDING_PROGRAM, vid: vid, ref: document.referrer, utm_source: trafficSource.utm_source, utm_campaign: trafficSource.utm_campaign });
        fetch('../api/track.php', { method: 'POST', headers: { 'Content-Type': 'text/plain;charset=utf-8' }, body: body, keepalive: true }).catch(function(){});
      } catch (err) {}
    })();

    // 10 Foto Dokumentasi Resmi K3 PT Artha Safety Indonesia
    var docPhotosList = [
      {
        url: "https://lh3.googleusercontent.com/d/1O6en3WBWh66rMpWbURaQWMVTnkcGgoEg",
        title: "Graduation",
        category: "Ahli K3 Umum Kemnaker",
        badge: "Ujian Seminar"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1Qh7w7vsbv0r1I0eSX-0q5D7GqVUM3h9M",
        title: "Observasi Industri",
        category: "Praktek Kerja Lapangan (PKL)",
        badge: "Kunjungan Pabrik AK3U Yogyakarta"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1cPfwDuq-o69oVElPewiEvSlNkluisc7G",
        title: "Simulasi Evakuasi & Fire Fighting",
        category: "Inhouse Training",
        badge: "Instruktur Damkar Kota"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1hiVin3LLW3V4pdgBIVPTlkedVJUGabfV",
        title: "Praktek P3K",
        category: "Inhouse Training",
        badge: "Instruktur PMI"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1rqnH9cfwxXPIiw8LtobMTHJh0_-F-sYy",
        title: "Observasi Industri",
        category: "PRaktek Kerja Lapangan (PKL)",
        badge: "PT Adi Satria Abadi"
      },
      {
        url: "https://lh3.googleusercontent.com/d/173bK5ROrOkpYi7ZXLEL_5EhcgDuTc1b-",
        title: "Ujian Teori",
        category: "AK3U Kemnaker RI",
        badge: "Offline TemanK3"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1SjnE0IT9IjvECTlXBVKjG6erSlrdzk2s",
        title: "Ujian Teori",
        category: "AK3U Kemnaker RI",
        badge: "Offline TemanK3"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1iHDiu1ExjJMmNDPi6BARbfK--iqgRapD",
        title: "PT Pertamina",
        category: "Sertifikasi K3 Migas",
        badge: "Inhouse Training"
      },
      {
        url: "https://lh3.googleusercontent.com/d/13CH9-GSPUPf-fj6P-un2VxIyTWzuJnrp",
        title: "Materi Kelas",
        category: "Bimbingan Mentor Praktisi",
        badge: "Offline Training"
      },
      {
        url: "https://lh3.googleusercontent.com/d/1ULTlKlDln-qtSpnZfXvDc3DXcl_zF1t3",
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

    function handlePricingAction(schemeName, categoryLabel) {
      var selectedCategory = categoryLabel || (window.currentPricingCategory === 'umum' ? 'Peserta Umum' : 'Utusan Perusahaan');
      var schemeSelect = document.getElementById('regSelectedScheme');

      if (schemeSelect) {
        if (selectedCategory === 'Peserta Umum') {
          schemeSelect.value = schemeName.includes('Blended')
            ? 'Peserta Umum - Blended (Rp 3.850.000)'
            : 'Peserta Umum - Full Offline (Rp 5.000.000)';
        } else {
          schemeSelect.value = schemeName.includes('Blended')
            ? 'Utusan Perusahaan - Blended (Rp 5.550.000)'
            : 'Utusan Perusahaan - Full Offline (Rp 6.500.000)';
        }
      }

      var regElem = document.getElementById('registration');
      if (regElem) {
        regElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

    function handleRegistrationSubmit(event) {
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }

      var nameInput = document.getElementById('regFullName');
      var phoneInput = document.getElementById('regWhatsApp');
      var emailInput = document.getElementById('regEmail');

      if (!nameInput || !phoneInput || !emailInput) return false;

      var name = nameInput.value.trim();
      var phone = phoneInput.value.trim();
      var email = emailInput.value.trim();

      if (!name || !phone || !email) return false;

      var schemeElem = document.getElementById('regSelectedScheme');
      var scheme = schemeElem ? schemeElem.value : 'Peserta Umum - Blended (Rp 3.850.000)';

      var selectedGoalInput = document.querySelector('input[name="programGoal"]:checked');
      var goal = selectedGoalInput ? selectedGoalInput.value : 'Pengembangan karier';

      var now = new Date();
      var formattedDate = ('0' + now.getDate()).slice(-2) + '/' +
                          ('0' + (now.getMonth() + 1)).slice(-2) + '/' +
                          now.getFullYear() + ' ' +
                          ('0' + now.getHours()).slice(-2) + ':' +
                          ('0' + now.getMinutes()).slice(-2) + ':' +
                          ('0' + now.getSeconds()).slice(-2) + ' WIB';

      var message = 'Halo PT Artha Safety Indonesia, saya ingin melakukan registrasi Pelatihan Ahli K3 Umum Kemnaker RI.\n\n' +
        '*DATA PENDAFTAR:*\n' +
        '• *Waktu Pendaftaran:* ' + formattedDate + '\n' +
        '• *Nama Lengkap:* ' + name + '\n' +
        '• *No. WhatsApp:* ' + phone + '\n' +
        '• *Email:* ' + email + '\n' +
        '• *Tujuan Mengikuti:* ' + goal + '\n' +
        '• *Skema Diminati:* ' + scheme + '\n\n' +
        'Mohon konfirmasi ketersediaan kuota batch terdekat & petunjuk langkah selanjutnya. Terima kasih!';

      // Direct Link ke WhatsApp Admin Resmi: 081337261261
      var waUrl = 'https://wa.me/6281337261261?text=' + encodeURIComponent(message);

      var payload = {
        program: LANDING_PROGRAM,
        timestamp: formattedDate,
        name: name,
        phone: phone,
        email: email,
        goal: goal,
        scheme: scheme,
        utm_source: trafficSource.utm_source,
        utm_campaign: trafficSource.utm_campaign
      };

      // 1. Meta Pixel Event Tracking (Lead)
      try {
        if (typeof fbq === 'function') {
          fbq('track', 'Lead');
          fbq('track', 'lead');
        }
      } catch (pixelErr) {
        console.warn('Meta Pixel tracking error:', pixelErr);
      }

      // 2. Simpan cadangan data ke browser (LocalStorage)
      try {
        var existingList = JSON.parse(localStorage.getItem('artha_safety_registrations') || '[]');
        existingList.push(payload);
        localStorage.setItem('artha_safety_registrations', JSON.stringify(existingList));
      } catch (err) {
        console.warn('LocalStorage save bypassed:', err);
      }

      // 3. Kirim otomatis ke database dashboard (../api/submit.php)
      if (SPREADSHEET_WEBHOOK_URL && SPREADSHEET_WEBHOOK_URL.length > 5) {
        try {
          fetch(SPREADSHEET_WEBHOOK_URL, {
            method: 'POST',
            mode: 'no-cors',
            headers: { 'Content-Type': 'text/plain;charset=utf-8' },
            body: JSON.stringify(payload)
          }).catch(function(e){ console.log('Spreadsheet webhook push info:', e); });
        } catch(e) {
          console.warn('Webhook connection error:', e);
        }
      }

      // 4. Tampilkan Tampilan Berhasil (Thank You Page)
      var formEl = document.getElementById('regMainForm');
      var successView = document.getElementById('regSuccessView');

      if (formEl) formEl.style.setProperty('display', 'none', 'important');
      if (successView) successView.style.setProperty('display', 'block', 'important');

      var sumName = document.getElementById('summaryName');
      var sumPhone = document.getElementById('summaryPhone');
      var sumScheme = document.getElementById('summaryScheme');
      var directWaBtn = document.getElementById('directWaBtn');

      if (sumName) sumName.innerText = name;
      if (sumPhone) sumPhone.innerText = phone;
      if (sumScheme) sumScheme.innerText = scheme;
      if (directWaBtn) directWaBtn.href = waUrl;

      // 5. Perbarui Indikator Stepper ke Step 2
      var stepBadge1 = document.getElementById('stepBadge1');
      if (stepBadge1) {
        stepBadge1.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-500 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md';
        stepBadge1.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
      }

      var stepBadge2 = document.getElementById('stepBadge2');
      if (stepBadge2) {
        stepBadge2.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-accent text-brand-dark font-black text-xs sm:text-sm flex items-center justify-center shadow-md';
      }

      var stepLabel2 = document.getElementById('stepLabel2');
      if (stepLabel2) {
        stepLabel2.className = 'text-[11px] sm:text-xs font-extrabold text-brand-navy mt-2 whitespace-nowrap';
      }

      var stepCounter = document.getElementById('stepCounterText');
      if (stepCounter) {
        stepCounter.innerText = 'STEP 2 OF 5: KONFIRMASI ADMIN';
      }

      if (successView) {
        successView.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }

      return false;
    }

    function resetRegistrationForm() {
      var formEl = document.getElementById('regMainForm');
      var successView = document.getElementById('regSuccessView');

      if (formEl) {
        formEl.reset();
        formEl.style.setProperty('display', 'block', 'important');
      }
      if (successView) {
        successView.style.setProperty('display', 'none', 'important');
      }

      var stepBadge1 = document.getElementById('stepBadge1');
      if (stepBadge1) {
        stepBadge1.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-brand-accent text-brand-dark font-black text-xs sm:text-sm flex items-center justify-center shadow-md';
        stepBadge1.innerText = '1';
      }

      var stepBadge2 = document.getElementById('stepBadge2');
      if (stepBadge2) {
        stepBadge2.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white border-2 border-slate-300 text-slate-500 font-bold text-xs sm:text-sm flex items-center justify-center';
        stepBadge2.innerText = '2';
      }

      var stepLabel2 = document.getElementById('stepLabel2');
      if (stepLabel2) {
        stepLabel2.className = 'text-[11px] sm:text-xs font-semibold text-slate-400 mt-2 whitespace-nowrap';
      }

      var stepCounter = document.getElementById('stepCounterText');
      if (stepCounter) {
        stepCounter.innerText = 'STEP 1 OF 5';
      }
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

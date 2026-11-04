<?php
/**
 * Kerangka tampilan dashboard admin (head, navigasi, footer).
 */

function admin_head(string $title): void
{
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?> · Dashboard Artha Safety</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: { brand: { navy: '#071C6E', blue: '#0D3BE8', accent: '#A6F634', accentHover: '#92E022', subtle: '#EEF3FF', dark: '#041040' } }
      } }
    }
  </script>
  <style>body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#F6F8FC;color:#1e293b}</style>
</head>
<body class="min-h-screen antialiased">
    <?php
}

function admin_nav(string $active, array $user): void
{
    $items = [
        'index.php'      => ['Ringkasan', 'fa-chart-simple'],
        'pendaftar.php'  => ['Pendaftar', 'fa-users'],
        'pengaturan.php' => ['Pengaturan', 'fa-gear'],
    ];
    ?>
  <header class="bg-brand-navy text-white sticky top-0 z-30 shadow">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between gap-3">
      <a href="index.php" class="flex items-center gap-2 font-extrabold text-sm sm:text-base min-w-0">
        <span class="w-7 h-7 rounded-lg bg-brand-accent text-brand-dark flex items-center justify-center text-xs font-black shrink-0">K3</span>
        <span class="truncate">Dashboard Ahli K3 Umum</span>
      </a>
      <div class="flex items-center gap-3 text-xs shrink-0">
        <a href="../" target="_blank" rel="noopener" class="hidden sm:inline text-slate-300 hover:text-white"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Lihat landing page</a>
        <span class="hidden sm:inline text-slate-400">|</span>
        <span class="text-slate-300 hidden sm:inline"><i class="fa-regular fa-user mr-1"></i><?= e($user['username']) ?></span>
        <a href="logout.php" class="bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg font-bold">Keluar</a>
      </div>
    </div>
    <nav class="max-w-6xl mx-auto px-4 flex gap-1 overflow-x-auto">
      <?php foreach ($items as $file => [$label, $icon]): $on = $file === $active; ?>
        <a href="<?= $file ?>" class="px-3 sm:px-4 py-2.5 text-xs sm:text-sm font-bold whitespace-nowrap border-b-2 <?= $on ? 'border-brand-accent text-white' : 'border-transparent text-slate-300 hover:text-white' ?>">
          <i class="fa-solid <?= $icon ?> mr-1.5"></i><?= $label ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </header>
  <main class="max-w-6xl mx-auto px-4 py-6 sm:py-8">
    <?php
    $f = flash();
    if ($f): ?>
      <div class="mb-5 rounded-xl px-4 py-3 text-sm font-semibold <?= $f['type'] === 'ok' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
        <?= e($f['msg']) ?>
      </div>
    <?php endif;
}

function admin_foot(): void
{
    ?>
  </main>
</body>
</html>
    <?php
}

/** Halaman sederhana di tengah layar untuk login & setup. */
function auth_card_open(string $title, string $subtitle): void
{
    admin_head($title);
    ?>
  <div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-xl border border-slate-200 p-7">
      <div class="flex items-center gap-3 mb-6">
        <span class="w-10 h-10 rounded-xl bg-brand-navy text-brand-accent flex items-center justify-center font-black">K3</span>
        <div>
          <h1 class="text-lg font-extrabold text-brand-navy leading-tight"><?= e($title) ?></h1>
          <p class="text-xs text-slate-500"><?= e($subtitle) ?></p>
        </div>
      </div>
    <?php
}

function auth_card_close(): void
{
    ?>
    </div>
  </div>
</body>
</html>
    <?php
}

function input_class(): string
{
    return 'w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-navy text-sm';
}

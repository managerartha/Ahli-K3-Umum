<?php
/**
 * Ringkasan: jumlah pengunjung landing page, jumlah pendaftar, konversi,
 * grafik 14 hari terakhir, sumber trafik, dan pendaftar terbaru.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';
require_once dirname(__DIR__) . '/includes/registrant_card.php';

$user = require_login();
$pdo = db();

function count_since(PDO $pdo, string $sql, string $since): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$since]);
    return (int) $stmt->fetchColumn();
}

$today = date('Y-m-d');
$periods = [
    'Hari ini' => $today,
    '7 hari'   => date('Y-m-d', strtotime('-6 days')),
    '30 hari'  => date('Y-m-d', strtotime('-29 days')),
];

$stats = [];
foreach ($periods as $label => $since) {
    $visitors = count_since($pdo, 'SELECT COUNT(DISTINCT visitor_id) FROM visits WHERE day >= ?', $since);
    $regs     = count_since($pdo, 'SELECT COUNT(*) FROM registrations WHERE created_at >= ?', $since . ' 00:00:00');
    $stats[$label] = [
        'visitors' => $visitors,
        'visits'   => count_since($pdo, 'SELECT COUNT(*) FROM visits WHERE day >= ?', $since),
        'regs'     => $regs,
        'rate'     => $visitors > 0 ? round($regs / $visitors * 100, 1) : 0,
    ];
}
$totalRegs = (int) $pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$newRegs   = (int) $pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'baru'")->fetchColumn();

// Data grafik 14 hari
$days = [];
for ($i = 13; $i >= 0; $i--) $days[date('Y-m-d', strtotime("-$i days"))] = ['v' => 0, 'r' => 0];
$since14 = array_key_first($days);
$stmt = $pdo->prepare('SELECT day, COUNT(DISTINCT visitor_id) c FROM visits WHERE day >= ? GROUP BY day');
$stmt->execute([$since14]);
foreach ($stmt as $row) if (isset($days[$row['day']])) $days[$row['day']]['v'] = (int) $row['c'];
$stmt = $pdo->prepare('SELECT substr(created_at, 1, 10) d, COUNT(*) c FROM registrations WHERE created_at >= ? GROUP BY d');
$stmt->execute([$since14 . ' 00:00:00']);
foreach ($stmt as $row) if (isset($days[$row['d']])) $days[$row['d']]['r'] = (int) $row['c'];
$maxV = max(1, max(array_column($days, 'v')));

// Sumber trafik 30 hari (utm_source, jika tidak ada pakai domain referrer)
$stmt = $pdo->prepare('SELECT utm_source, referrer FROM visits WHERE day >= ?');
$stmt->execute([$periods['30 hari']]);
$sources = [];
foreach ($stmt as $row) {
    $src = $row['utm_source'] ?: ($row['referrer'] ? (parse_url($row['referrer'], PHP_URL_HOST) ?: 'Lainnya') : 'Langsung / tidak diketahui');
    $src = preg_replace('/^(www\.|m\.|l\.|lm\.)/', '', strtolower($src));
    $sources[$src] = ($sources[$src] ?? 0) + 1;
}
arsort($sources);
$sources = array_slice($sources, 0, 6, true);
$sourceTotal = max(1, array_sum($sources));

$latest = $pdo->query('SELECT * FROM registrations ORDER BY id DESC LIMIT 5')->fetchAll();

admin_head('Ringkasan');
admin_nav('index.php', $user);
?>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 mb-6">
      <div>
        <h1 class="text-2xl font-extrabold text-brand-navy">Ringkasan</h1>
        <p class="text-sm text-slate-500">Batch aktif di landing page: <strong class="text-brand-navy"><?= e(batch_long()) ?></strong></p>
      </div>
      <?php if ($newRegs > 0): ?>
        <a href="pendaftar.php?status=baru" class="inline-flex items-center gap-2 bg-brand-accent text-brand-dark font-extrabold text-xs px-3.5 py-2 rounded-xl self-start sm:self-auto">
          <i class="fa-solid fa-bell"></i> <?= $newRegs ?> pendaftar belum dihubungi
        </a>
      <?php endif; ?>
    </div>

    <!-- Kartu statistik per periode -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <?php foreach ($stats as $label => $s): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
          <p class="text-[11px] font-extrabold uppercase tracking-wider text-brand-blue mb-3"><?= e($label) ?></p>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <p class="text-2xl font-black text-brand-navy"><?= number_format($s['visitors'], 0, ',', '.') ?></p>
              <p class="text-[11px] text-slate-500 font-semibold">Pengunjung</p>
            </div>
            <div>
              <p class="text-2xl font-black text-brand-navy"><?= number_format($s['regs'], 0, ',', '.') ?></p>
              <p class="text-[11px] text-slate-500 font-semibold">Pendaftar</p>
            </div>
            <div>
              <p class="text-2xl font-black text-emerald-600"><?= str_replace('.', ',', (string) $s['rate']) ?>%</p>
              <p class="text-[11px] text-slate-500 font-semibold">Konversi</p>
            </div>
          </div>
          <p class="text-[11px] text-slate-400 mt-3"><?= number_format($s['visits'], 0, ',', '.') ?> kunjungan</p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
      <!-- Grafik 14 hari -->
      <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-sm font-extrabold text-brand-navy">14 hari terakhir</h2>
          <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-brand-blue/80"></span>Pengunjung</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span>Pendaftar</span>
          </div>
        </div>
        <div class="flex items-end gap-1 sm:gap-1.5 h-44">
          <?php foreach ($days as $d => $c):
              $h = $c['v'] / $maxV * 100;
              $hr = $c['r'] / $maxV * 100; ?>
            <div class="flex-1 h-full flex flex-col justify-end items-center group relative" title="<?= e(date('d/m', strtotime($d))) ?>: <?= $c['v'] ?> pengunjung, <?= $c['r'] ?> pendaftar">
              <div class="w-full flex items-end justify-center gap-[2px] h-full">
                <div class="w-1/2 bg-brand-blue/80 rounded-t" style="height: <?= max($c['v'] ? 2 : 0, $h) ?>%"></div>
                <div class="w-1/3 bg-emerald-500 rounded-t" style="height: <?= max($c['r'] ? 2 : 0, $hr) ?>%"></div>
              </div>
              <span class="absolute -top-6 hidden group-hover:block bg-brand-navy text-white text-[10px] font-bold px-1.5 py-0.5 rounded whitespace-nowrap z-10"><?= $c['v'] ?> / <?= $c['r'] ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="flex gap-1 sm:gap-1.5 mt-1.5">
          <?php foreach ($days as $d => $c): ?>
            <span class="flex-1 text-center text-[9px] sm:text-[10px] text-slate-400"><?= date('j', strtotime($d)) ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Sumber trafik -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <h2 class="text-sm font-extrabold text-brand-navy mb-1">Sumber kunjungan</h2>
        <p class="text-[11px] text-slate-500 mb-4">30 hari · dari <code>utm_source</code> atau situs perujuk</p>
        <?php if (!$sources): ?>
          <p class="text-xs text-slate-400">Belum ada data kunjungan.</p>
        <?php endif; ?>
        <div class="space-y-3">
          <?php foreach ($sources as $src => $n): $pct = round($n / $sourceTotal * 100); ?>
            <div>
              <div class="flex justify-between text-xs font-semibold mb-1">
                <span class="truncate pr-2"><?= e($src) ?></span><span class="text-slate-500"><?= $n ?></span>
              </div>
              <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-brand-blue rounded-full" style="width: <?= $pct ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Pendaftar terbaru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-extrabold text-brand-navy">Pendaftar terbaru <span class="text-slate-400 font-semibold">· total <?= $totalRegs ?></span></h2>
        <a href="pendaftar.php" class="text-xs font-bold text-brand-blue hover:text-brand-navy">Lihat semua <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
      </div>
      <?php if (!$latest): ?>
        <p class="px-5 py-8 text-center text-sm text-slate-400">Belum ada pendaftar.</p>
      <?php endif; ?>
      <div class="divide-y divide-slate-100">
        <?php foreach ($latest as $r) registrant_row($r, false); ?>
      </div>
    </div>
<?php admin_foot();

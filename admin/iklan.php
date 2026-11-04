<?php
/**
 * Iklan & Closing: biaya iklan Meta, CPL, CPA, closing rate, kualitas lead (cold/warm/hot),
 * dan performa per iklan (konten mana yang menghasilkan lead dan closing).
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';
require_once dirname(__DIR__) . '/includes/meta.php';

$user = require_login();
$program = admin_program();
meta_auto_sync();

/* ---------- Periode ---------- */
$presets = [
    '7'          => ['7 hari terakhir', date('Y-m-d', strtotime('-6 days')), date('Y-m-d')],
    '30'         => ['30 hari terakhir', date('Y-m-d', strtotime('-29 days')), date('Y-m-d')],
    'bulan-ini'  => ['Bulan ini', date('Y-m-01'), date('Y-m-d')],
    'bulan-lalu' => ['Bulan lalu', date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))],
];
$range = $_GET['range'] ?? '30';
$validDate = fn($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
if ($range === 'custom' && $validDate($_GET['from'] ?? '') && $validDate($_GET['to'] ?? '')) {
    [$from, $to] = [$_GET['from'], $_GET['to']];
    if ($to < $from) [$from, $to] = [$to, $from];
    $rangeLabel = date('d/m/Y', strtotime($from)) . ' – ' . date('d/m/Y', strtotime($to));
} else {
    if (!isset($presets[$range])) $range = '30';
    [$rangeLabel, $from, $to] = $presets[$range];
}

$pdo = db();
$keywords = $program ? campaign_keywords($program) : [];

/* ---------- Biaya iklan per iklan ---------- */
$ads = [];
$adInfo = []; // nama & kampanye semua iklan (termasuk di luar filter program), untuk label
foreach ($pdo->query('SELECT ad_id, ad_name, campaign_name FROM ad_insights GROUP BY ad_id') as $a) {
    $adInfo[$a['ad_id']] = ['name' => $a['ad_name'], 'campaign' => $a['campaign_name']];
}
$stmt = $pdo->prepare('SELECT ad_id, ad_name, adset_name, campaign_name, SUM(spend) spend, SUM(clicks) clicks, SUM(impressions) impressions
                       FROM ad_insights WHERE day >= ? AND day <= ? GROUP BY ad_id');
$stmt->execute([$from, $to]);
foreach ($stmt as $a) {
    if (!campaign_matches($a['campaign_name'], $keywords)) continue;
    $ads[$a['ad_id']] = [
        'name' => $a['ad_name'], 'campaign' => $a['campaign_name'], 'adset' => $a['adset_name'],
        'spend' => (float) $a['spend'], 'clicks' => (int) $a['clicks'], 'impressions' => (int) $a['impressions'],
    ];
}
$adIdByName = [];
foreach ($ads as $id => $a) if ($a['name'] !== null) $adIdByName[mb_strtolower($a['name'])] = $id;

/* ---------- Lead ---------- */
$pw = $program ? ' AND program = ?' : '';
$stmt = $pdo->prepare("SELECT temperature, closing, ad_id, utm_content, utm_campaign, utm_source FROM registrations WHERE created_at >= ? AND created_at <= ?$pw");
$stmt->execute(array_merge([$from . ' 00:00:00', $to . ' 23:59:59'], $program ? [$program] : []));
$leads = $stmt->fetchAll();

$emptyRow = fn() => ['leads' => 0, 'hot' => 0, 'warm' => 0, 'cold' => 0, 'closing' => 0, 'visitors' => 0];
$rows = [];   // per iklan
$tot = ['leads' => 0, 'hot' => 0, 'warm' => 0, 'cold' => 0, 'unrated' => 0, 'closing' => 0, 'not_closing' => 0, 'from_ads' => 0];

foreach ($leads as $l) {
    $key = 'none';
    if ($l['ad_id'] && isset($ads[$l['ad_id']])) $key = $l['ad_id'];
    elseif ($l['ad_id']) $key = $l['ad_id'];
    elseif ($l['utm_content'] && isset($adIdByName[mb_strtolower($l['utm_content'])])) $key = $adIdByName[mb_strtolower($l['utm_content'])];
    elseif ($l['utm_content']) $key = 'name:' . $l['utm_content'];

    $rows[$key] = $rows[$key] ?? $emptyRow();
    if (!isset($rows[$key]['label']) && $l['utm_content']) $rows[$key]['label'] = $l['utm_content'];
    $rows[$key]['leads']++;
    if ($l['temperature']) $rows[$key][$l['temperature']]++;
    if ($l['closing'] === 'ya') $rows[$key]['closing']++;

    $tot['leads']++;
    $l['temperature'] ? $tot[$l['temperature']]++ : $tot['unrated']++;
    if ($l['closing'] === 'ya') $tot['closing']++;
    if ($l['closing'] === 'tidak') $tot['not_closing']++;
    if ($key !== 'none') $tot['from_ads']++;
}

// Pengunjung landing page per iklan
$stmt = $pdo->prepare("SELECT ad_id, utm_content, COUNT(DISTINCT visitor_id) c FROM visits WHERE day >= ? AND day <= ?$pw AND (ad_id IS NOT NULL OR utm_content IS NOT NULL) GROUP BY ad_id, utm_content");
$stmt->execute(array_merge([$from, $to], $program ? [$program] : []));
foreach ($stmt as $v) {
    $key = $v['ad_id'] ?: ($v['utm_content'] && isset($adIdByName[mb_strtolower($v['utm_content'])]) ? $adIdByName[mb_strtolower($v['utm_content'])] : 'name:' . $v['utm_content']);
    $rows[$key] = $rows[$key] ?? $emptyRow();
    $rows[$key]['visitors'] += (int) $v['c'];
}

// Gabungkan dengan iklan yang punya biaya tapi belum menghasilkan lead
foreach ($ads as $id => $a) $rows[$id] = $rows[$id] ?? $emptyRow();

$table = [];
foreach ($rows as $key => $r) {
    if ($key === 'none') continue;
    $ad = $ads[$key] ?? null;
    $info = $adInfo[$key] ?? null;
    $table[] = $r + [
        'key'      => $key,
        'name'     => $ad['name'] ?? $info['name'] ?? $r['label'] ?? (str_starts_with_compat($key, 'name:') ? substr($key, 5) : 'Iklan ID ' . $key),
        'campaign' => $ad['campaign'] ?? $info['campaign'] ?? null,
        'spend'    => $ad['spend'] ?? null,
        'clicks'   => $ad['clicks'] ?? null,
    ];
}
usort($table, fn($a, $b) => [($b['spend'] ?? -1), $b['leads']] <=> [($a['spend'] ?? -1), $a['leads']]);
$noneRow = $rows['none'] ?? null;

$spend = array_sum(array_column($ads, 'spend'));
$clicks = array_sum(array_column($ads, 'clicks'));

function str_starts_with_compat(string $h, string $n): bool
{
    return substr($h, 0, strlen($n)) === $n;
}
function rp(?float $n): string
{
    return $n === null ? '–' : 'Rp ' . number_format(round($n), 0, ',', '.');
}
function pct(int $a, int $b): string
{
    return $b > 0 ? str_replace('.', ',', (string) round($a / $b * 100, 1)) . '%' : '–';
}

$metaOn = meta_configured();
$lastSync = get_setting('meta_last_sync');
$lastError = get_setting('meta_last_error');

admin_head('Iklan & Closing');
admin_nav('iklan.php', $user);
?>
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-3 mb-5">
      <div>
        <h1 class="text-2xl font-extrabold text-brand-navy">Iklan &amp; Closing<?= $program ? ': ' . e(program_name($program)) : '' ?></h1>
        <p class="text-sm text-slate-500">Periode: <strong class="text-brand-navy"><?= e($rangeLabel) ?></strong>
          <?php if ($program && $keywords): ?> · biaya iklan dari kampanye sesuai kata kunci: "<?= e(implode('", "', $keywords)) ?>"<?php endif; ?>
          <?php if ($program && !$keywords && $metaOn): ?> · <span class="text-amber-700">biaya iklan = semua kampanye (atur kata kunci kampanye di Pengaturan)</span><?php endif; ?>
        </p>
      </div>
      <form method="get" class="flex flex-wrap items-end gap-2">
        <select name="range" onchange="document.getElementById('customRange').classList.toggle('hidden', this.value !== 'custom'); if (this.value !== 'custom') this.form.submit();" class="px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white font-semibold">
          <?php foreach ($presets as $k => [$label]): ?><option value="<?= $k ?>" <?= (string) $range === (string) $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
          <option value="custom" <?= $range === 'custom' ? 'selected' : '' ?>>Pilih tanggal…</option>
        </select>
        <span id="customRange" class="<?= $range === 'custom' ? '' : 'hidden' ?> flex items-center gap-2">
          <input type="date" name="from" value="<?= e($from) ?>" class="px-3 py-2 rounded-xl border border-slate-300 text-sm">
          <input type="date" name="to" value="<?= e($to) ?>" class="px-3 py-2 rounded-xl border border-slate-300 text-sm">
          <button class="bg-brand-navy text-white font-bold text-sm px-4 py-2 rounded-xl">Terapkan</button>
        </span>
      </form>
    </div>

    <!-- Status koneksi Meta -->
    <?php if (!$metaOn): ?>
      <div class="mb-5 rounded-2xl p-4 bg-amber-50 border border-amber-200 text-sm text-amber-900">
        <strong><i class="fa-brands fa-meta mr-1"></i>Biaya iklan belum terhubung.</strong>
        CPL dan CPA baru bisa dihitung setelah akun Meta Ads dihubungkan di
        <a href="pengaturan.php#meta" class="font-bold underline">Pengaturan → Koneksi Meta Ads</a>.
        Data lead dan closing di bawah tetap bisa dipakai.
      </div>
    <?php else: ?>
      <div class="mb-5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
        <span><i class="fa-brands fa-meta mr-1 text-brand-blue"></i>Biaya iklan diperbarui: <strong><?= $lastSync ? e(date('d/m/Y H:i', strtotime($lastSync))) : 'belum pernah' ?></strong></span>
        <form method="post" action="action.php" class="inline">
          <?= csrf_field() ?><input type="hidden" name="action" value="meta_sync"><input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI'] ?? '') ?>">
          <button class="font-bold text-brand-blue hover:text-brand-navy"><i class="fa-solid fa-rotate mr-0.5"></i>Perbarui sekarang</button>
        </form>
        <?php if ($lastError): ?><span class="w-full text-red-600 font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i><?= e($lastError) ?></span><?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Kartu ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
      <?php
      $cards = [
          ['Biaya iklan', $metaOn ? rp($spend) : '–', $metaOn ? number_format($clicks, 0, ',', '.') . ' klik link' : 'belum terhubung', 'text-brand-navy'],
          ['Lead', number_format($tot['leads'], 0, ',', '.'), $tot['from_ads'] . ' tercatat dari iklan', 'text-brand-navy'],
          ['CPL', $metaOn && $tot['leads'] ? rp($spend / $tot['leads']) : '–', 'biaya per lead', 'text-brand-blue'],
          ['Closing', number_format($tot['closing'], 0, ',', '.'), $tot['not_closing'] . ' tidak closing', 'text-emerald-600'],
          ['CPA', $metaOn && $tot['closing'] ? rp($spend / $tot['closing']) : '–', 'biaya per closing', 'text-brand-blue'],
          ['Closing rate', pct($tot['closing'], $tot['leads']), 'closing ÷ lead', 'text-emerald-600'],
      ];
      foreach ($cards as [$label, $value, $sub, $color]): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
          <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500"><?= $label ?></p>
          <p class="text-xl sm:text-2xl font-black <?= $color ?> mt-1 whitespace-nowrap"><?= $value ?></p>
          <p class="text-[11px] text-slate-400 mt-0.5"><?= $sub ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Kualitas lead -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm mb-6">
      <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
        <h2 class="text-sm font-extrabold text-brand-navy">Kualitas lead</h2>
        <a href="pendaftar.php?lead=belum" class="text-xs font-bold text-brand-blue hover:text-brand-navy"><?= $tot['unrated'] ?> lead belum dinilai →</a>
      </div>
      <?php $segments = [['hot', 'Hot', 'bg-red-500'], ['warm', 'Warm', 'bg-amber-500'], ['cold', 'Cold', 'bg-sky-500'], ['unrated', 'Belum dinilai', 'bg-slate-200']]; ?>
      <div class="flex h-3 rounded-full overflow-hidden bg-slate-100 mb-3">
        <?php foreach ($segments as [$k, , $c]): if ($tot[$k]): ?><div class="<?= $c ?>" style="width: <?= $tot[$k] / max(1, $tot['leads']) * 100 ?>%"></div><?php endif; endforeach; ?>
      </div>
      <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs font-semibold text-slate-600">
        <?php foreach ($segments as [$k, $label, $c]): ?>
          <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm <?= $c ?>"></span><?= $label ?>: <strong class="text-brand-navy"><?= $tot[$k] ?></strong> (<?= pct($tot[$k], $tot['leads']) ?>)</span>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Per iklan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
      <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-extrabold text-brand-navy">Performa per iklan (konten)</h2>
        <p class="text-[11px] text-slate-500 mt-0.5">Lead dikenali dari parameter URL di link iklan. Lihat <a href="pengaturan.php#meta" class="font-bold text-brand-blue">Pengaturan</a> untuk teks parameter yang perlu dipasang di Ads Manager.</p>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs sm:text-sm">
          <thead>
            <tr class="text-left text-[10px] sm:text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
              <th class="px-4 py-2.5 font-bold min-w-[220px]">Iklan</th>
              <th class="px-3 py-2.5 font-bold text-right">Biaya</th>
              <th class="px-3 py-2.5 font-bold text-right">Klik</th>
              <th class="px-3 py-2.5 font-bold text-right" title="Pengunjung landing page dari iklan ini">Pengunjung</th>
              <th class="px-3 py-2.5 font-bold text-right">Lead</th>
              <th class="px-3 py-2.5 font-bold text-center">Hot / Warm / Cold</th>
              <th class="px-3 py-2.5 font-bold text-right">Closing</th>
              <th class="px-3 py-2.5 font-bold text-right">CPL</th>
              <th class="px-4 py-2.5 font-bold text-right">CPA</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (!$table && !$noneRow): ?>
              <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">Belum ada data pada periode ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($table as $r): ?>
              <tr class="hover:bg-slate-50/70">
                <td class="px-4 py-2.5">
                  <p class="font-bold text-brand-navy break-words"><?= e($r['name']) ?></p>
                  <?php if ($r['campaign']): ?><p class="text-[11px] text-slate-400 break-words"><?= e($r['campaign']) ?></p><?php endif; ?>
                </td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap"><?= rp($r['spend']) ?></td>
                <td class="px-3 py-2.5 text-right"><?= $r['clicks'] ?? '–' ?></td>
                <td class="px-3 py-2.5 text-right"><?= $r['visitors'] ?: '–' ?></td>
                <td class="px-3 py-2.5 text-right font-bold"><?= $r['leads'] ?: '–' ?></td>
                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                  <span class="text-red-600 font-bold"><?= $r['hot'] ?></span> / <span class="text-amber-600 font-bold"><?= $r['warm'] ?></span> / <span class="text-sky-600 font-bold"><?= $r['cold'] ?></span>
                </td>
                <td class="px-3 py-2.5 text-right font-bold text-emerald-600"><?= $r['closing'] ?: '–' ?></td>
                <td class="px-3 py-2.5 text-right whitespace-nowrap"><?= $r['spend'] !== null && $r['leads'] ? rp($r['spend'] / $r['leads']) : '–' ?></td>
                <td class="px-4 py-2.5 text-right whitespace-nowrap"><?= $r['spend'] !== null && $r['closing'] ? rp($r['spend'] / $r['closing']) : '–' ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if ($noneRow && $noneRow['leads']): ?>
              <tr class="bg-slate-50/60">
                <td class="px-4 py-2.5">
                  <p class="font-bold text-slate-600">Tanpa data iklan</p>
                  <p class="text-[11px] text-slate-400">Organik, link tanpa parameter, data impor, atau iklan sebelum parameter dipasang</p>
                </td>
                <td class="px-3 py-2.5 text-right">–</td>
                <td class="px-3 py-2.5 text-right">–</td>
                <td class="px-3 py-2.5 text-right">–</td>
                <td class="px-3 py-2.5 text-right font-bold"><?= $noneRow['leads'] ?></td>
                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                  <span class="text-red-600 font-bold"><?= $noneRow['hot'] ?></span> / <span class="text-amber-600 font-bold"><?= $noneRow['warm'] ?></span> / <span class="text-sky-600 font-bold"><?= $noneRow['cold'] ?></span>
                </td>
                <td class="px-3 py-2.5 text-right font-bold text-emerald-600"><?= $noneRow['closing'] ?: '–' ?></td>
                <td class="px-3 py-2.5 text-right">–</td>
                <td class="px-4 py-2.5 text-right">–</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
<?php admin_foot();

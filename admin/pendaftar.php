<?php
/**
 * Daftar semua pendaftar dari form landing page, dengan pencarian,
 * filter status & tanggal, tombol WhatsApp, dan ekspor CSV.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';
require_once dirname(__DIR__) . '/includes/registrant_query.php';
require_once dirname(__DIR__) . '/includes/registrant_card.php';

$user = require_login();
$program = admin_program();
$f = registrant_filters($_GET, $program);
[$where, $params] = registrant_where($f);

$perPage = 25;
$stmt = db()->prepare("SELECT COUNT(*) FROM registrations $where");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page = min(max(1, (int) ($_GET['page'] ?? 1)), $pages);

$stmt = db()->prepare("SELECT * FROM registrations $where ORDER BY id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage));
$stmt->execute($params);
$rows = $stmt->fetchAll();

$qs = fn(array $extra) => http_build_query(array_filter(array_merge(array_diff_key($f, ['program' => 1]), $extra), fn($v) => $v !== '' && $v !== null));
$hasFilter = $f['q'] !== '' || $f['status'] !== '' || $f['from'] !== '' || $f['to'] !== '' || $f['lead'] !== '' || $f['hasil'] !== '';

admin_head('Pendaftar');
admin_nav('pendaftar.php', $user);
?>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-5">
      <div>
        <h1 class="text-2xl font-extrabold text-brand-navy">Pendaftar<?= $program ? ': ' . e(program_name($program)) : '' ?></h1>
        <p class="text-sm text-slate-500"><?= $total ?> data<?= $hasFilter ? ' sesuai filter' : '' ?><?= $program ? '' : ' dari semua program' ?>. Klik tombol hijau untuk chat WhatsApp langsung.</p>
      </div>
      <div class="flex gap-2 self-start sm:self-auto">
        <a href="impor.php" class="inline-flex items-center gap-2 bg-white border border-slate-300 hover:border-brand-navy text-brand-navy font-bold text-xs px-3.5 py-2 rounded-xl">
          <i class="fa-solid fa-file-import"></i> Impor CSV
        </a>
        <a href="export.php?<?= e($qs([])) ?>" class="inline-flex items-center gap-2 bg-white border border-slate-300 hover:border-brand-navy text-brand-navy font-bold text-xs px-3.5 py-2 rounded-xl">
          <i class="fa-solid fa-file-csv"></i> Unduh CSV / Excel
        </a>
      </div>
    </div>

    <form method="get" class="bg-white rounded-2xl border border-slate-200 p-4 mb-4 grid grid-cols-2 md:grid-cols-12 gap-3 items-end shadow-sm">
      <div class="col-span-2 md:col-span-4">
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari nama / WA / email / catatan</label>
        <input name="q" value="<?= e($f['q']) ?>" placeholder="Ketik untuk mencari..." class="<?= input_class() ?>">
      </div>
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Status</label>
        <select name="status" class="<?= input_class() ?> bg-white">
          <option value="">Semua</option>
          <option value="baru" <?= $f['status'] === 'baru' ? 'selected' : '' ?>>Baru</option>
          <option value="dihubungi" <?= $f['status'] === 'dihubungi' ? 'selected' : '' ?>>Sudah dihubungi</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Lead</label>
        <select name="lead" class="<?= input_class() ?> bg-white">
          <option value="">Semua</option>
          <?php foreach (['hot' => 'Hot', 'warm' => 'Warm', 'cold' => 'Cold', 'belum' => 'Belum dinilai'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= $f['lead'] === $v ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Hasil</label>
        <select name="hasil" class="<?= input_class() ?> bg-white">
          <option value="">Semua</option>
          <?php foreach (['ya' => 'Closing', 'tidak' => 'Tidak closing', 'belum' => 'Belum ada hasil'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= $f['hasil'] === $v ? 'selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Dari tanggal</label>
        <input type="date" name="from" value="<?= e($f['from']) ?>" class="<?= input_class() ?>">
      </div>
      <div class="md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Sampai tanggal</label>
        <input type="date" name="to" value="<?= e($f['to']) ?>" class="<?= input_class() ?>">
      </div>
      <div class="col-span-2 md:col-span-2 flex gap-2">
        <button class="flex-1 bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm py-2.5 rounded-xl">Filter</button>
        <?php if ($hasFilter): ?><a href="pendaftar.php" class="px-3 py-2.5 rounded-xl border border-slate-300 text-slate-500 text-sm" title="Reset filter"><i class="fa-solid fa-xmark"></i></a><?php endif; ?>
      </div>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
      <?php if (!$rows): ?>
        <p class="px-5 py-10 text-center text-sm text-slate-400">Tidak ada pendaftar<?= $hasFilter ? ' yang sesuai filter' : '' ?>.</p>
      <?php endif; ?>
      <div class="divide-y divide-slate-100">
        <?php foreach ($rows as $r) registrant_row($r, true, !$program); ?>
      </div>
    </div>

    <?php if ($pages > 1): ?>
      <div class="flex items-center justify-center gap-2 mt-5 text-sm">
        <?php if ($page > 1): ?><a href="?<?= e($qs(['page' => $page - 1])) ?>" class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 font-bold text-brand-navy">&larr; Sebelumnya</a><?php endif; ?>
        <span class="text-slate-500 font-semibold px-2">Halaman <?= $page ?> / <?= $pages ?></span>
        <?php if ($page < $pages): ?><a href="?<?= e($qs(['page' => $page + 1])) ?>" class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 font-bold text-brand-navy">Berikutnya &rarr;</a><?php endif; ?>
      </div>
    <?php endif; ?>
<?php admin_foot();

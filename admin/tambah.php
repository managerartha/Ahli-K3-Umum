<?php
/**
 * Tambah lead secara manual, terutama dari iklan klik-ke-WhatsApp (CTWA) yang tidak lewat
 * landing page. Lead bisa dikaitkan ke iklan Meta supaya CPL & CPA per iklan terhitung.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';
require_once dirname(__DIR__) . '/includes/meta.php';

$user = require_login();

const LEAD_CHANNELS = ['whatsapp' => 'WhatsApp', 'instagram' => 'DM Instagram', 'facebook' => 'Messenger Facebook', 'telepon' => 'Telepon', 'lainnya' => 'Lainnya'];

// Iklan yang aktif 60 hari terakhir, untuk dipilih (iklan dengan chat WA di atas).
$adOptions = db()->query("SELECT ad_id, ad_name, campaign_name, SUM(messaging) chats, MAX(day) last_day
                          FROM ad_insights WHERE day >= '" . date('Y-m-d', strtotime('-60 days')) . "'
                          GROUP BY ad_id ORDER BY (SUM(messaging) > 0) DESC, MAX(day) DESC, ad_name")->fetchAll();
$adById = array_column($adOptions, null, 'ad_id');

$error = '';
$duplicate = null;
$old = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $program = (string) ($_POST['program'] ?? '');
    $name    = clean_text($_POST['name'] ?? '', 150);
    $phone   = clean_text($_POST['phone'] ?? '', 30);
    $email   = clean_text($_POST['email'] ?? '', 150);
    $channel = (string) ($_POST['channel'] ?? 'whatsapp');
    $adId    = preg_replace('/\D+/', '', (string) ($_POST['ad_id'] ?? ''));
    $scheme  = clean_text($_POST['scheme'] ?? '', 150);
    $goal    = clean_text($_POST['goal'] ?? '', 100);
    $notes   = trim(mb_substr(str_replace("\r\n", "\n", (string) ($_POST['notes'] ?? '')), 0, 2000));
    $date    = (string) ($_POST['date'] ?? '');
    $temp    = (string) ($_POST['temperature'] ?? '');
    $closing = (string) ($_POST['closing'] ?? '');

    if (!is_program($program)) {
        $error = 'Pilih program.';
    } elseif (!preg_match('/\d{6,}/', preg_replace('/\D+/', '', $phone))) {
        $error = 'Nomor WhatsApp wajib diisi (minimal 6 angka).';
    } elseif (!isset(LEAD_CHANNELS[$channel])) {
        $error = 'Pilih asal lead.';
    } else {
        if (preg_match('/^8\d{7,}$/', $phone)) $phone = '0' . $phone;
        // Nama & email boleh kosong (lead WA sering belum membalas / tidak memberi email).
        if ($name === '') $name = 'Lead WA (belum ada nama)';
        if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) $email = '';
        $created = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date . ' ' . date('H:i:s') : now();

        // Cegah input dobel: nomor yang sama di program yang sama
        $digits = wa_number($phone);
        $stmt = db()->prepare('SELECT id, name, created_at, phone FROM registrations WHERE program = ?');
        $stmt->execute([$program]);
        foreach ($stmt as $row) {
            if (wa_number($row['phone']) === $digits) { $duplicate = $row; break; }
        }

        if ($duplicate && empty($_POST['allow_duplicate'])) {
            $error = 'Nomor ini sudah pernah terdaftar atas nama "' . $duplicate['name'] . '" (' . date('d/m/Y', strtotime($duplicate['created_at'])) . '). Centang "tetap simpan" di bawah jika memang ingin menambah lagi.';
        } else {
            $ad = $adById[$adId] ?? null;
            $temperature = isset(LEAD_TEMPERATURES[$temp]) ? $temp : null;
            $closingVal = isset(LEAD_CLOSING[$closing]) ? $closing : null;
            db()->prepare('INSERT INTO registrations (program, created_at, name, phone, email, goal, scheme, status, contacted_at,
                               utm_source, utm_medium, utm_campaign, utm_content, ad_id, notes, source, temperature, closing)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute([
                    $program, $created, $name, $phone, $email !== '' ? $email : '-', $goal ?: null, $scheme ?: null,
                    'dihubungi', $created,
                    $channel, $ad ? 'paid' : null, $ad['campaign_name'] ?? null, $ad['ad_name'] ?? null, $ad ? $adId : null,
                    $notes !== '' ? $notes : null, 'manual', $temperature, $closingVal,
                ]);
            flash('Lead "' . $name . '" ditambahkan' . ($ad ? ' dari iklan "' . $ad['ad_name'] . '"' : '') . '.');
            header('Location: ' . (!empty($_POST['add_another']) ? 'tambah.php?program=' . urlencode($program) . '&last_ad=' . urlencode($adId) : 'pendaftar.php?program=' . urlencode($program)));
            exit;
        }
    }
}

$program = $old['program'] ?? (admin_program() ?: default_program());
$selectedAd = $old['ad_id'] ?? ($_GET['last_ad'] ?? '');
$fmtDate = fn($d) => date('d/m', strtotime($d));

admin_head('Tambah Lead');
admin_nav('pendaftar.php', $user, false);
?>
    <a href="pendaftar.php" class="text-xs font-bold text-brand-blue hover:text-brand-navy"><i class="fa-solid fa-arrow-left mr-1"></i>Kembali ke Pendaftar</a>
    <h1 class="text-2xl font-extrabold text-brand-navy mt-2 mb-1">Tambah Lead WhatsApp / Manual</h1>
    <p class="text-sm text-slate-500 mb-5 max-w-2xl">Untuk lead yang masuk langsung ke WhatsApp dari iklan (tanpa lewat landing page), atau dari DM/telepon. Pilih iklannya supaya CPL &amp; CPA per iklan ikut terhitung.</p>

    <?php if ($error): ?>
      <div class="mb-5 rounded-xl px-4 py-3 text-sm font-semibold bg-red-50 text-red-700 border border-red-200 max-w-2xl"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm max-w-2xl space-y-4">
      <?= csrf_field() ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Program <span class="text-red-500">*</span></label>
          <select name="program" class="<?= input_class() ?> bg-white">
            <?php foreach (PROGRAMS as $slug => $prog): ?>
              <option value="<?= e($slug) ?>" <?= $slug === $program ? 'selected' : '' ?>><?= e($prog['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Asal lead <span class="text-red-500">*</span></label>
          <select name="channel" class="<?= input_class() ?> bg-white">
            <?php foreach (LEAD_CHANNELS as $k => $label): ?>
              <option value="<?= $k ?>" <?= ($old['channel'] ?? 'whatsapp') === $k ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Dari iklan</label>
        <select name="ad_id" class="<?= input_class() ?> bg-white">
          <option value="">— Tidak dari iklan / tidak tahu —</option>
          <?php foreach ($adOptions as $a): ?>
            <option value="<?= e($a['ad_id']) ?>" <?= (string) $selectedAd === (string) $a['ad_id'] ? 'selected' : '' ?>>
              <?= e($a['ad_name']) ?> — <?= e($a['campaign_name']) ?><?= $a['chats'] ? ' · ' . (int) $a['chats'] . ' chat WA' : '' ?> · aktif s/d <?= $fmtDate($a['last_day']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="text-[11px] text-slate-500 mt-1">
          <?php if (!$adOptions): ?>Daftar iklan muncul setelah Meta Ads terhubung (Pengaturan → Koneksi Meta Ads).
          <?php else: ?>Iklan yang punya chat WA ditampilkan paling atas. Chat dari iklan biasanya menampilkan pratinjau iklannya di pesan pertama WhatsApp, jadi cocokkan dengan nama iklan di sini.<?php endif; ?>
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama <span class="text-slate-400 font-semibold">(opsional)</span></label>
          <input name="name" placeholder="Nama di profil WA, boleh kosong" value="<?= e($old['name'] ?? '') ?>" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
          <input name="phone" required inputmode="tel" placeholder="08..." value="<?= e($old['phone'] ?? '') ?>" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Email <span class="text-slate-400 font-semibold">(opsional)</span></label>
          <input name="email" type="text" inputmode="email" autocomplete="off" placeholder="Boleh kosong" value="<?= e($old['email'] ?? '') ?>" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal chat masuk</label>
          <input name="date" type="date" value="<?= e($old['date'] ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Skema diminati</label>
          <input name="scheme" list="schemeList" value="<?= e($old['scheme'] ?? '') ?>" placeholder="Pilih atau ketik" class="<?= input_class() ?>">
          <datalist id="schemeList">
            <option value="Blended · Umum · Non penginapan · Basic (Rp 3.850.000)">
            <option value="Blended · Umum · Non penginapan · Full (Rp 4.200.000)">
            <option value="Blended · Umum · Penginapan · Basic (Rp 4.350.000)">
            <option value="Blended · Umum · Penginapan · Full (Rp 4.700.000)">
            <option value="Blended · Perusahaan · Non penginapan · Basic (Rp 5.550.000)">
            <option value="Blended · Perusahaan · Non penginapan · Full (Rp 6.000.000)">
            <option value="Blended · Perusahaan · Penginapan · Basic (Rp 6.000.000)">
            <option value="Blended · Perusahaan · Penginapan · Full (Rp 6.450.000)">
            <option value="Full Offline 12 hari · Umum (Rp 5.000.000)">
            <option value="Full Offline 12 hari · Perusahaan (Rp 6.500.000)">
          </datalist>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tujuan</label>
          <select name="goal" class="<?= input_class() ?> bg-white">
            <option value="">—</option>
            <?php foreach (['Pengembangan karier', 'Persyaratan perusahaan', 'Mencari pekerjaan', 'Switch career'] as $g): ?>
              <option <?= ($old['goal'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Lead</label>
          <select name="temperature" class="<?= input_class() ?> bg-white">
            <option value="">Belum dinilai</option>
            <?php foreach (LEAD_TEMPERATURES as $k => $l): ?><option value="<?= $k ?>" <?= ($old['temperature'] ?? '') === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Hasil</label>
          <select name="closing" class="<?= input_class() ?> bg-white">
            <option value="">Belum ada hasil</option>
            <?php foreach (LEAD_CLOSING as $k => $l): ?><option value="<?= $k ?>" <?= ($old['closing'] ?? '') === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
        <textarea name="notes" rows="2" maxlength="2000" placeholder="Contoh: tanya jadwal Desember, minta invoice perusahaan" class="<?= input_class() ?>"><?= e($old['notes'] ?? '') ?></textarea>
      </div>

      <?php if ($duplicate): ?>
        <label class="flex items-center gap-2 text-sm font-semibold text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-3">
          <input type="checkbox" name="allow_duplicate" value="1"> Tetap simpan walaupun nomor sudah ada
        </label>
      <?php endif; ?>

      <div class="flex flex-wrap gap-2 pt-1">
        <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl"><i class="fa-solid fa-plus mr-1.5"></i>Simpan Lead</button>
        <button name="add_another" value="1" class="bg-white border border-slate-300 hover:border-brand-navy text-brand-navy font-bold text-sm px-5 py-2.5 rounded-xl">Simpan &amp; Tambah Lagi</button>
      </div>
    </form>
<?php admin_foot();

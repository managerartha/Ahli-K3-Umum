<?php
/**
 * Impor data pendaftar dari file CSV (misalnya hasil unduhan Google Spreadsheet lama).
 *
 * Kolom yang dikenali otomatis: Waktu Submit, Nama Lengkap, Nomor WhatsApp, Email,
 * Tujuan Mengikuti, Skema Pelatihan. Kolom lain (misal status, isi chat, follow up)
 * disimpan ke Catatan pendaftar. Data yang sudah pernah diimpor tidak akan dobel.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';

$user = require_login();

const IMPORT_FIELDS = [
    'created' => ['waktu submit', 'timestamp', 'waktu daftar', 'waktu pendaftaran', 'tanggal'],
    'name'    => ['nama lengkap', 'nama', 'name', 'nama peserta'],
    'phone'   => ['nomor whatsapp', 'no whatsapp', 'no. whatsapp', 'whatsapp', 'no wa', 'nomor wa', 'no hp', 'nomor hp', 'phone'],
    'email'   => ['email', 'e-mail', 'alamat email'],
    'goal'    => ['tujuan mengikuti', 'tujuan'],
    'scheme'  => ['skema pelatihan', 'skema', 'skema diminati'],
];

function parse_import_date(string $v): ?string
{
    $v = trim(preg_replace('/\s*WI[BTT]A?$/i', '', trim($v)));
    foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d', 'd-m-Y H:i:s', 'd-m-Y'] as $fmt) {
        $dt = DateTime::createFromFormat('!' . $fmt, $v);
        $err = DateTime::getLastErrors();
        if ($dt && (!$err || ($err['warning_count'] === 0 && $err['error_count'] === 0))) {
            return $dt->format('Y-m-d H:i:s');
        }
    }
    return null;
}

/** Baca CSV: deteksi pemisah (koma / titik koma) dan buang BOM. */
function read_csv_rows(string $path): array
{
    $raw = file_get_contents($path);
    $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
    if (!mb_check_encoding($raw, 'UTF-8')) $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
    $firstLine = strtok($raw, "\n");
    $delim = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

    $fh = fopen('php://temp', 'r+');
    fwrite($fh, $raw);
    rewind($fh);
    $rows = [];
    while (($r = fgetcsv($fh, 0, $delim, '"', '\\')) !== false) {
        if ($r === [null] || implode('', array_map('trim', $r)) === '') continue;
        $rows[] = $r;
    }
    fclose($fh);
    return $rows;
}

$result = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $program = (string) ($_POST['program'] ?? '');
    $markAll = !empty($_POST['mark_contacted']);
    $file = $_FILES['csv'] ?? null;

    if (!is_program($program)) {
        $error = 'Pilih program tujuan.';
    } elseif (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'File belum dipilih atau gagal diunggah.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $error = 'Ukuran file maksimal 5 MB.';
    } else {
        $rows = read_csv_rows($file['tmp_name']);
        $header = array_map(fn($h) => mb_strtolower(trim((string) $h)), array_shift($rows) ?? []);

        // Petakan judul kolom ke field
        $map = [];
        foreach ($header as $i => $h) {
            foreach (IMPORT_FIELDS as $field => $aliases) {
                if (!isset($map[$field]) && in_array($h, $aliases, true)) {
                    $map[$field] = $i;
                    continue 2;
                }
            }
        }

        if (!isset($map['name'], $map['phone'])) {
            $error = 'Kolom "Nama Lengkap" dan "Nomor WhatsApp" tidak ditemukan di baris pertama file. Pastikan baris pertama berisi judul kolom.';
        } else {
            $known = array_flip($map);
            $pdo = db();
            $exists = $pdo->prepare('SELECT 1 FROM registrations WHERE program = ? AND phone = ? AND created_at = ? LIMIT 1');
            $insert = $pdo->prepare('INSERT INTO registrations (program, created_at, name, phone, email, goal, scheme, status, contacted_at, notes, source, temperature, closing)
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $result = ['imported' => 0, 'duplicate' => 0, 'skipped' => []];

            $pdo->beginTransaction();
            foreach ($rows as $n => $r) {
                $line = $n + 2; // nomor baris di file (baris 1 = judul)
                $get = fn($f) => isset($map[$f]) ? trim((string) ($r[$map[$f]] ?? '')) : '';

                $name = clean_text($get('name'), 150);
                $phone = clean_text($get('phone'), 30);
                if ($name === '' || $phone === '') {
                    $result['skipped'][] = "Baris $line: nama atau nomor WhatsApp kosong";
                    continue;
                }
                // Nomor yang kehilangan angka 0 di depan (dibaca angka oleh spreadsheet)
                if (preg_match('/^8\d{7,}$/', $phone)) $phone = '0' . $phone;

                $created = isset($map['created']) ? parse_import_date($get('created')) : null;
                if (isset($map['created']) && !$created) {
                    $result['skipped'][] = "Baris $line ($name): format waktu tidak dikenali \"" . $get('created') . '"';
                    continue;
                }
                $created = $created ?: now();

                // Kolom lain → catatan
                $notes = [];
                foreach ($r as $i => $val) {
                    $val = trim((string) $val);
                    if ($val === '' || isset($known[$i])) continue;
                    $label = trim((string) ($header[$i] ?? ''));
                    $notes[] = $label !== '' ? ucfirst($label) . ': ' . $val : $val;
                }
                $notesText = $notes ? mb_substr(implode("\n", $notes), 0, 2000) : null;

                $exists->execute([$program, $phone, $created]);
                if ($exists->fetchColumn()) {
                    $result['duplicate']++;
                    continue;
                }

                $contacted = $markAll || $notesText !== null;
                $insert->execute([
                    $program, $created, $name, $phone,
                    clean_text($get('email'), 150) ?: '-',
                    clean_text($get('goal'), 100) ?: null,
                    clean_text($get('scheme'), 150) ?: null,
                    $contacted ? 'dihubungi' : 'baru',
                    $contacted ? $created : null,
                    $notesText,
                    'impor',
                    ...lead_fields_from_notes((string) $notesText),
                ]);
                $result['imported']++;
            }
            $pdo->commit();
        }
    }
}

$program = admin_program() ?: default_program();

admin_head('Impor CSV');
admin_nav('pendaftar.php', $user, false);
?>
    <a href="pendaftar.php" class="text-xs font-bold text-brand-blue hover:text-brand-navy"><i class="fa-solid fa-arrow-left mr-1"></i>Kembali ke Pendaftar</a>
    <h1 class="text-2xl font-extrabold text-brand-navy mt-2 mb-1">Impor Data Pendaftar (CSV)</h1>
    <p class="text-sm text-slate-500 mb-5 max-w-2xl">Untuk memindahkan data lama, misalnya dari Google Spreadsheet: buka spreadsheet → <strong>File → Download → Comma-separated values (.csv)</strong>, lalu unggah file itu di sini.</p>

    <?php if ($error): ?>
      <div class="mb-5 rounded-xl px-4 py-3 text-sm font-semibold bg-red-50 text-red-700 border border-red-200 max-w-2xl"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($result): ?>
      <div class="mb-5 rounded-2xl p-5 bg-emerald-50 border border-emerald-200 max-w-2xl">
        <p class="text-base font-extrabold text-emerald-800 mb-2"><i class="fa-solid fa-circle-check mr-1"></i>Impor selesai</p>
        <ul class="text-sm text-emerald-900 space-y-1">
          <li><strong><?= $result['imported'] ?></strong> pendaftar berhasil ditambahkan.</li>
          <?php if ($result['duplicate']): ?><li><strong><?= $result['duplicate'] ?></strong> data dilewati karena sudah ada (tidak dobel).</li><?php endif; ?>
          <?php if ($result['skipped']): ?><li><strong><?= count($result['skipped']) ?></strong> baris dilewati:</li><?php endif; ?>
        </ul>
        <?php if ($result['skipped']): ?>
          <ul class="mt-1 ml-4 list-disc text-xs text-amber-800 space-y-0.5">
            <?php foreach (array_slice($result['skipped'], 0, 30) as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <a href="pendaftar.php?program=<?= e($_POST['program'] ?? '') ?>" class="inline-block mt-4 bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-4 py-2 rounded-xl">Lihat Pendaftar</a>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm max-w-2xl space-y-5">
      <?= csrf_field() ?>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">1. Data ini untuk program</label>
        <select name="program" class="<?= input_class() ?> bg-white">
          <?php foreach (PROGRAMS as $slug => $prog): ?>
            <option value="<?= e($slug) ?>" <?= $slug === $program ? 'selected' : '' ?>><?= e($prog['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">2. Pilih file CSV</label>
        <input type="file" name="csv" accept=".csv,text/csv" required class="block w-full text-sm text-slate-600 file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-brand-subtle file:text-brand-navy file:font-bold hover:file:bg-blue-100">
      </div>
      <label class="flex items-start gap-2.5 text-sm text-slate-700 cursor-pointer">
        <input type="checkbox" name="mark_contacted" value="1" class="mt-1">
        <span>Tandai <strong>semua</strong> data impor sebagai <em>Sudah dihubungi</em>.<br>
          <span class="text-xs text-slate-500">Jika tidak dicentang: baris yang punya catatan follow-up ditandai "Sudah dihubungi", sisanya "Baru".</span></span>
      </label>
      <div class="text-xs text-slate-500 bg-slate-50 rounded-xl p-3 leading-relaxed">
        Kolom yang dibaca otomatis: <strong>Waktu Submit, Nama Lengkap, Nomor WhatsApp, Email, Tujuan Mengikuti, Skema Pelatihan</strong>.
        Kolom lain (misalnya status hot/warm/cold, isi chat, follow up) masuk ke <strong>Catatan</strong> tiap pendaftar.
        Aman diimpor ulang: data yang sudah ada tidak akan dobel.
      </div>
      <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl"><i class="fa-solid fa-file-import mr-1.5"></i>Impor Sekarang</button>
    </form>
<?php admin_foot();

<?php
/**
 * Semua aksi yang mengubah data di dashboard (wajib login + token CSRF).
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/meta.php';

$user = require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
check_csrf();

$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$back = (string) ($_POST['back'] ?? '');
$program = (string) ($_POST['program'] ?? '');
// Hanya izinkan kembali ke halaman di dalam folder admin.
if (!preg_match('#^[^:]*/admin/[a-z]+\.php(\?[^\s]*)?$#', $back)) $back = '';

function go(string $to): void
{
    header('Location: ' . $to);
    exit;
}

switch ($action) {
    case 'mark_contacted':
        db()->prepare("UPDATE registrations SET status = 'dihubungi', contacted_at = COALESCE(contacted_at, ?) WHERE id = ?")
            ->execute([now(), $id]);
        if (!empty($_POST['ajax'])) json_response(['ok' => true]);
        go($back ?: 'pendaftar.php');

    case 'toggle_status':
        db()->prepare("UPDATE registrations SET status = CASE status WHEN 'baru' THEN 'dihubungi' ELSE 'baru' END,
                       contacted_at = CASE status WHEN 'baru' THEN ? ELSE NULL END WHERE id = ?")
            ->execute([now(), $id]);
        go($back ?: 'pendaftar.php');

    case 'set_lead':
        $field = (string) ($_POST['field'] ?? '');
        $value = (string) ($_POST['value'] ?? '');
        $allowed = ['temperature' => LEAD_TEMPERATURES, 'closing' => LEAD_CLOSING];
        if (!isset($allowed[$field]) || !isset($allowed[$field][$value])) {
            if (!empty($_POST['ajax'])) json_response(['ok' => false], 400);
            go($back ?: 'pendaftar.php');
        }
        $stmt = db()->prepare("SELECT $field FROM registrations WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        // Klik tombol yang sudah aktif = batalkan pilihan.
        $new = $current === $value ? null : $value;
        db()->prepare("UPDATE registrations SET $field = ?,
                       status = CASE WHEN ? IS NOT NULL THEN 'dihubungi' ELSE status END,
                       contacted_at = CASE WHEN ? IS NOT NULL THEN COALESCE(contacted_at, ?) ELSE contacted_at END
                       WHERE id = ?")
            ->execute([$new, $new, $new, now(), $id]);
        if (!empty($_POST['ajax'])) json_response(['ok' => true, 'field' => $field, 'value' => $new]);
        go($back ?: 'pendaftar.php');

    case 'save_campaign_keywords':
        if (!is_program($program)) go('pengaturan.php');
        $kw = implode(', ', array_filter(array_map('trim', explode(',', mb_substr((string) ($_POST['campaign_keywords'] ?? ''), 0, 300))), 'strlen'));
        set_setting('campaign_keywords', $program, $kw);
        flash(program_name($program) . ': kata kunci kampanye disimpan.');
        go('pengaturan.php#' . $program);

    case 'save_meta':
        if (!empty($_POST['disconnect'])) {
            set_setting('meta_token', '', '');
            set_setting('meta_last_error', '', '');
            flash('Koneksi Meta Ads diputus.');
            go('pengaturan.php#meta');
        }
        set_setting('meta_account_id', '', preg_replace('/\D+/', '', (string) ($_POST['meta_account_id'] ?? '')));
        $token = trim((string) ($_POST['meta_token'] ?? ''));
        if ($token !== '') set_setting('meta_token', '', $token);
        if (!meta_configured()) {
            flash('Isi ID akun iklan dan access token.', 'error');
            go('pengaturan.php#meta');
        }
        try {
            $n = meta_sync();
            flash("Koneksi Meta Ads berhasil. $n baris data biaya iklan diambil.");
        } catch (Throwable $e) {
            flash('Koneksi Meta Ads gagal: ' . $e->getMessage(), 'error');
        }
        go('pengaturan.php#meta');

    case 'meta_sync':
        try {
            $n = meta_sync(!empty($_POST['full']));
            flash("Biaya iklan diperbarui ($n baris data).");
        } catch (Throwable $e) {
            flash('Gagal memperbarui biaya iklan: ' . $e->getMessage(), 'error');
        }
        go($back ?: 'iklan.php');

    case 'save_details':
        $name = clean_text($_POST['name'] ?? '', 150);
        $phone = clean_text($_POST['phone'] ?? '', 30);
        $email = clean_text($_POST['email'] ?? '', 150);
        if ($name === '' || !preg_match('/\d{6,}/', preg_replace('/\D+/', '', $phone))) {
            flash('Nama dan nomor WhatsApp tidak boleh kosong.', 'error');
            go($back ?: 'pendaftar.php');
        }
        db()->prepare('UPDATE registrations SET name = ?, phone = ?, email = ?, scheme = ? WHERE id = ?')
            ->execute([$name, $phone, $email !== '' ? $email : '-', clean_text($_POST['scheme'] ?? '', 150) ?: null, $id]);
        flash('Data "' . $name . '" disimpan.');
        go($back ?: 'pendaftar.php');

    case 'save_notes':
        $notes = trim(mb_substr(str_replace("\r\n", "\n", (string) ($_POST['notes'] ?? '')), 0, 2000));
        db()->prepare('UPDATE registrations SET notes = ? WHERE id = ?')->execute([$notes !== '' ? $notes : null, $id]);
        flash('Catatan disimpan.');
        go($back ?: 'pendaftar.php');

    case 'delete':
        $stmt = db()->prepare('DELETE FROM registrations WHERE id = ?');
        $stmt->execute([$id]);
        flash($stmt->rowCount() ? 'Pendaftar dihapus.' : 'Data tidak ditemukan.', $stmt->rowCount() ? 'ok' : 'error');
        go($back ?: 'pendaftar.php');

    case 'save_batch':
        if (!is_program($program)) go('pengaturan.php');
        $start = (string) ($_POST['batch_start'] ?? '');
        $end = (string) ($_POST['batch_end'] ?? '');
        $valid = fn($d) => ($dt = DateTime::createFromFormat('Y-m-d', $d)) && $dt->format('Y-m-d') === $d;
        if (!$valid($start) || !$valid($end)) {
            flash('Format tanggal tidak valid.', 'error');
        } elseif ($end < $start) {
            flash('Tanggal selesai tidak boleh sebelum tanggal mulai.', 'error');
        } else {
            set_setting('batch_start', $program, $start);
            set_setting('batch_end', $program, $end);
            flash(program_name($program) . ': tanggal batch disimpan (' . batch_long($program) . '). Landing page sudah otomatis berubah.');
        }
        go('pengaturan.php#' . $program);

    case 'save_wa_template':
        if (!is_program($program)) go('pengaturan.php');
        $tpl = trim(mb_substr((string) ($_POST['wa_template'] ?? ''), 0, 1000));
        set_setting('wa_template', $program, $tpl !== '' ? $tpl : DEFAULT_WA_TEMPLATE);
        flash(program_name($program) . ': template pesan WhatsApp disimpan.');
        go('pengaturan.php#' . $program);

    case 'change_password':
        $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $new = (string) ($_POST['new'] ?? '');
        if (!password_verify((string) ($_POST['current'] ?? ''), (string) $stmt->fetchColumn())) {
            flash('Password saat ini salah.', 'error');
        } elseif (strlen($new) < 8) {
            flash('Password baru minimal 8 karakter.', 'error');
        } elseif ($new !== ($_POST['confirm'] ?? '')) {
            flash('Konfirmasi password baru tidak sama.', 'error');
        } else {
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
            session_regenerate_id(true);
            flash('Password berhasil diganti.');
        }
        go('pengaturan.php');
}

go('index.php');

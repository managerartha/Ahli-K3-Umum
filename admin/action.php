<?php
/**
 * Semua aksi yang mengubah data di dashboard (wajib login + token CSRF).
 */
require_once dirname(__DIR__) . '/includes/auth.php';

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

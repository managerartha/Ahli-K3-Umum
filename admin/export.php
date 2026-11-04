<?php
/**
 * Unduh data pendaftar sebagai CSV (bisa dibuka di Excel / Google Sheets).
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/registrant_query.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';

require_login();
$program = admin_program();
[$where, $params] = registrant_where(registrant_filters($_GET, $program));
$stmt = db()->prepare("SELECT * FROM registrations $where ORDER BY id DESC");
$stmt->execute($params);

// Cegah isian diawali = + - @ dibaca sebagai rumus saat dibuka di Excel.
$safe = fn($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : (string) $v;

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="pendaftar-' . ($program ?: 'semua-program') . '-' . date('Ymd-His') . '.csv"');
header('Cache-Control: no-store');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
fputcsv($out, ['Program', 'Waktu Submit', 'Nama Lengkap', 'Nomor WhatsApp', 'Email', 'Tujuan Mengikuti', 'Skema Pelatihan', 'Status', 'Sumber (utm_source)', 'Kampanye (utm_campaign)']);
foreach ($stmt as $r) {
    fputcsv($out, array_map($safe, [
        program_name($r['program']),
        date('d/m/Y H:i:s', strtotime($r['created_at'])),
        $r['name'], $r['phone'], $r['email'], $r['goal'], $r['scheme'],
        $r['status'] === 'dihubungi' ? 'Sudah dihubungi' : 'Baru',
        $r['utm_source'], $r['utm_campaign'],
    ]));
}
fclose($out);

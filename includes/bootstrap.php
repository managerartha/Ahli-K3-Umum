<?php
/**
 * Inti aplikasi: koneksi database (SQLite), pengaturan, dan fungsi bantu.
 * Dipakai oleh landing page (index.php), API, dan dashboard admin.
 */

date_default_timezone_set('Asia/Jakarta');

// Lokasi database. File diawali ".ht" otomatis diblokir Apache dari akses publik,
// dan folder data/ juga diblokir lewat data/.htaccess.
define('DB_PATH', dirname(__DIR__) . '/data/.ht-artha.sqlite');

const MONTHS_LONG  = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const MONTHS_SHORT = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

// Nilai bawaan pengaturan (sama dengan isi landing page sebelumnya).
const DEFAULT_SETTINGS = [
    'batch_start' => '2026-10-26',
    'batch_end'   => '2026-11-06',
    'wa_template' => "Halo {nama}, kami dari PT Artha Safety Indonesia. Terima kasih sudah mendaftar Pelatihan Ahli K3 Umum Kemnaker RI ({skema}). Kami ingin mengonfirmasi jadwal dan kuota batch Anda.",
];

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0750, true);

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA journal_mode = WAL; PRAGMA busy_timeout = 5000; PRAGMA foreign_keys = ON;');
    migrate($pdo);
    return $pdo;
}

function migrate(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS registrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL,
            name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT NOT NULL,
            goal TEXT,
            scheme TEXT,
            status TEXT NOT NULL DEFAULT 'baru',
            contacted_at TEXT,
            utm_source TEXT,
            utm_campaign TEXT,
            ip_hash TEXT
        );
        CREATE INDEX IF NOT EXISTS idx_reg_created ON registrations(created_at);
        CREATE TABLE IF NOT EXISTS visits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL,
            day TEXT NOT NULL,
            visitor_id TEXT NOT NULL,
            referrer TEXT,
            utm_source TEXT,
            utm_campaign TEXT
        );
        CREATE INDEX IF NOT EXISTS idx_visits_day ON visits(day);
        CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS login_attempts (
            ip_hash TEXT NOT NULL,
            attempted_at INTEGER NOT NULL
        );
    ");
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function ip_hash(): string
{
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|artha');
}

function get_setting(string $key): string
{
    $stmt = db()->prepare('SELECT value FROM settings WHERE key = ?');
    $stmt->execute([$key]);
    $v = $stmt->fetchColumn();
    return $v === false ? (DEFAULT_SETTINGS[$key] ?? '') : $v;
}

function set_setting(string $key, string $value): void
{
    db()->prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value')
        ->execute([$key, $value]);
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * Format tanggal batch seperti di landing page.
 * $long  : "26 Okt - 6 November 2026"
 * $short : "26 Okt-6 Nov 2026"
 */
function batch_label(string $start, string $end, bool $long): string
{
    $s = DateTime::createFromFormat('Y-m-d', $start);
    $t = DateTime::createFromFormat('Y-m-d', $end);
    if (!$s || !$t) return '';

    [$sd, $sm, $sy] = [(int) $s->format('j'), (int) $s->format('n'), (int) $s->format('Y')];
    [$td, $tm, $ty] = [(int) $t->format('j'), (int) $t->format('n'), (int) $t->format('Y')];
    $endMonth = $long ? MONTHS_LONG[$tm] : MONTHS_SHORT[$tm];
    $sep = $long ? ' - ' : '-';

    if ($sy !== $ty) {
        return "$sd " . MONTHS_SHORT[$sm] . " $sy$sep$td $endMonth $ty";
    }
    if ($sm === $tm) {
        return "$sd$sep$td $endMonth $ty";
    }
    return "$sd " . MONTHS_SHORT[$sm] . "$sep$td $endMonth $ty";
}

function batch_long(): string
{
    return batch_label(get_setting('batch_start'), get_setting('batch_end'), true);
}

function batch_short(): string
{
    return batch_label(get_setting('batch_start'), get_setting('batch_end'), false);
}

/** Ubah nomor lokal (0812...) menjadi format internasional WhatsApp (62812...). */
function wa_number(string $phone): string
{
    $d = preg_replace('/\D+/', '', $phone);
    if (substr($d, 0, 1) === '0') return '62' . substr($d, 1);
    if (substr($d, 0, 1) === '8') return '62' . $d;
    return $d;
}

function wa_link(array $reg): string
{
    $msg = strtr(get_setting('wa_template'), [
        '{nama}'  => $reg['name'],
        '{skema}' => $reg['scheme'] ?? '',
    ]);
    return 'https://wa.me/' . wa_number($reg['phone']) . '?text=' . rawurlencode($msg);
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data);
    exit;
}

function clean_text($v, int $max = 200): string
{
    $v = trim(preg_replace('/\s+/u', ' ', (string) $v));
    return mb_substr($v, 0, $max);
}

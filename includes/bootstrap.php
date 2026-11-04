<?php
/**
 * Inti aplikasi: koneksi database (SQLite), pengaturan per program, dan fungsi bantu.
 * Dipakai oleh semua landing page, API, dan dashboard admin.
 */

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/programs.php';

// Lokasi database. File diawali ".ht" otomatis diblokir Apache dari akses publik,
// dan folder data/ juga diblokir lewat data/.htaccess.
define('DB_PATH', dirname(__DIR__) . '/data/.ht-artha.sqlite');

const MONTHS_LONG  = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const MONTHS_SHORT = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

const DEFAULT_WA_TEMPLATE = "Halo {nama}, kami dari PT Artha Safety Indonesia. Terima kasih sudah mendaftar Pelatihan {program} ({skema}). Kami ingin mengonfirmasi jadwal dan kuota batch Anda.";

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
    $default = $pdo->quote(default_program());
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS registrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            program TEXT NOT NULL DEFAULT $default,
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
            ip_hash TEXT,
            notes TEXT,
            source TEXT NOT NULL DEFAULT 'form',
            temperature TEXT,
            closing TEXT,
            utm_medium TEXT,
            utm_content TEXT,
            utm_term TEXT,
            ad_id TEXT
        );
        CREATE TABLE IF NOT EXISTS visits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            program TEXT NOT NULL DEFAULT $default,
            created_at TEXT NOT NULL,
            day TEXT NOT NULL,
            visitor_id TEXT NOT NULL,
            referrer TEXT,
            utm_source TEXT,
            utm_campaign TEXT,
            utm_content TEXT,
            ad_id TEXT
        );
        CREATE TABLE IF NOT EXISTS ad_insights (
            day TEXT NOT NULL,
            ad_id TEXT NOT NULL,
            ad_name TEXT,
            adset_name TEXT,
            campaign_name TEXT,
            spend REAL NOT NULL DEFAULT 0,
            impressions INTEGER NOT NULL DEFAULT 0,
            clicks INTEGER NOT NULL DEFAULT 0,
            PRIMARY KEY (day, ad_id)
        );
        CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT NOT NULL
        );
        CREATE TABLE IF NOT EXISTS login_attempts (
            ip_hash TEXT NOT NULL,
            attempted_at INTEGER NOT NULL
        );
    ");

    // Tambahkan kolom baru ke database dari versi sebelumnya.
    $addColumns = [
        'registrations' => [
            'program' => "TEXT NOT NULL DEFAULT $default", 'notes' => 'TEXT', 'source' => "TEXT NOT NULL DEFAULT 'form'",
            'temperature' => 'TEXT', 'closing' => 'TEXT',
            'utm_medium' => 'TEXT', 'utm_content' => 'TEXT', 'utm_term' => 'TEXT', 'ad_id' => 'TEXT',
        ],
        'visits' => ['program' => "TEXT NOT NULL DEFAULT $default", 'utm_content' => 'TEXT', 'ad_id' => 'TEXT'],
    ];
    foreach ($addColumns as $table => $columns) {
        $existing = array_column($pdo->query("PRAGMA table_info($table)")->fetchAll(), 'name');
        foreach ($columns as $col => $def) {
            if (!in_array($col, $existing, true)) {
                $pdo->exec("ALTER TABLE $table ADD COLUMN $col $def");
                // Data yang sudah diimpor sebelum kolom "source" ada: hanya data impor
                // yang tidak punya ip_hash (form landing page selalu mengisinya).
                if ($table === 'registrations' && $col === 'source') {
                    $pdo->exec("UPDATE registrations SET source = 'impor' WHERE ip_hash IS NULL");
                }
                // Isi Cold/Warm/Hot & Closing dari catatan hasil impor spreadsheet lama.
                if ($table === 'registrations' && $col === 'closing') {
                    $upd = $pdo->prepare('UPDATE registrations SET temperature = COALESCE(temperature, ?), closing = COALESCE(closing, ?) WHERE id = ?');
                    foreach ($pdo->query("SELECT id, notes FROM registrations WHERE notes IS NOT NULL AND notes != ''")->fetchAll() as $row) {
                        [$temp, $closing] = lead_fields_from_notes($row['notes']);
                        if ($temp || $closing) $upd->execute([$temp, $closing, $row['id']]);
                    }
                }
            }
        }
    }

    $pdo->exec("
        CREATE INDEX IF NOT EXISTS idx_reg_program_created ON registrations(program, created_at);
        CREATE INDEX IF NOT EXISTS idx_visits_program_day ON visits(program, day);
    ");
}

/* ---------- Program ---------- */

function default_program(): string
{
    return array_key_first(PROGRAMS);
}

function is_program(?string $slug): bool
{
    return is_string($slug) && isset(PROGRAMS[$slug]);
}

function program_name(string $slug): string
{
    return PROGRAMS[$slug]['name'] ?? $slug;
}

/* ---------- Pengaturan (disimpan per program) ---------- */

function setting_key(string $key, string $program): string
{
    return $program === '' ? $key : $key . ':' . $program;
}

/** Pengaturan per program; $program = '' untuk pengaturan umum (misal koneksi Meta). */
function get_setting(string $key, string $program = ''): string
{
    $stmt = db()->prepare('SELECT value FROM settings WHERE key = ?');
    $stmt->execute([setting_key($key, $program)]);
    $v = $stmt->fetchColumn();
    if ($v !== false) return $v;
    if ($key === 'wa_template') return DEFAULT_WA_TEMPLATE;
    return (string) (PROGRAMS[$program][$key] ?? '');
}

function set_setting(string $key, string $program, string $value): void
{
    db()->prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value')
        ->execute([setting_key($key, $program), $value]);
}

/* ---------- Status lead ---------- */

const LEAD_TEMPERATURES = ['cold' => 'Cold', 'warm' => 'Warm', 'hot' => 'Hot'];
const LEAD_CLOSING = ['ya' => 'Closing', 'tidak' => 'Tidak closing'];

/** Ambil Cold/Warm/Hot dan Closing dari teks catatan (format spreadsheet lama). */
function lead_fields_from_notes(string $notes): array
{
    $temp = preg_match('/\bstatus\s*:\s*(hot|warm|cold)\b/i', $notes, $m) ? strtolower($m[1]) : null;
    $closing = preg_match('/\bfinal\s*:\s*closing\b/i', $notes) ? 'ya' : null;
    return [$temp, $closing];
}

/* ---------- Tanggal batch ---------- */

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

function batch_long(string $program): string
{
    return batch_label(get_setting('batch_start', $program), get_setting('batch_end', $program), true);
}

function batch_short(string $program): string
{
    return batch_label(get_setting('batch_start', $program), get_setting('batch_end', $program), false);
}

/* ---------- WhatsApp ---------- */

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
    $msg = strtr(get_setting('wa_template', $reg['program']), [
        '{nama}'    => $reg['name'],
        '{skema}'   => $reg['scheme'] ?? '',
        '{program}' => program_name($reg['program']),
    ]);
    return 'https://wa.me/' . wa_number($reg['phone']) . '?text=' . rawurlencode($msg);
}

/* ---------- Lain-lain ---------- */

function now(): string
{
    return date('Y-m-d H:i:s');
}

function ip_hash(): string
{
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|artha');
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
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

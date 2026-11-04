<?php
/**
 * Sesi login, proteksi CSRF, dan pembatasan percobaan login untuk dashboard.
 */
require_once __DIR__ . '/bootstrap.php';

const MAX_LOGIN_ATTEMPTS = 5;      // percobaan gagal
const LOGIN_LOCK_SECONDS = 900;    // dikunci 15 menit
const SESSION_IDLE_SECONDS = 28800; // otomatis logout setelah 8 jam tidak aktif

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('artha_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    if (!empty($_SESSION['uid']) && time() - ($_SESSION['last_seen'] ?? 0) > SESSION_IDLE_SECONDS) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['last_seen'] = time();
}

function has_users(): bool
{
    return (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
}

function current_user(): ?array
{
    start_session();
    if (empty($_SESSION['uid'])) return null;
    $stmt = db()->prepare('SELECT id, username FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['uid']]);
    return $stmt->fetch() ?: null;
}

function require_login(): array
{
    $u = current_user();
    if (!$u) {
        header('Location: ' . (has_users() ? 'login.php' : 'setup.php'));
        exit;
    }
    return $u;
}

function login_user(int $uid): void
{
    start_session();
    session_regenerate_id(true);
    $_SESSION['uid'] = $uid;
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function check_csrf(): void
{
    start_session();
    $t = $_POST['csrf'] ?? '';
    if (!is_string($t) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) {
        http_response_code(400);
        exit('Sesi tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

function login_locked(): bool
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip_hash = ? AND attempted_at > ?');
    $stmt->execute([ip_hash(), time() - LOGIN_LOCK_SECONDS]);
    return (int) $stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS;
}

function record_failed_login(): void
{
    db()->prepare('INSERT INTO login_attempts (ip_hash, attempted_at) VALUES (?, ?)')->execute([ip_hash(), time()]);
    db()->prepare('DELETE FROM login_attempts WHERE attempted_at < ?')->execute([time() - 86400]);
}

function clear_failed_logins(): void
{
    db()->prepare('DELETE FROM login_attempts WHERE ip_hash = ?')->execute([ip_hash()]);
}

function flash(?string $msg = null, string $type = 'ok'): ?array
{
    start_session();
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

<?php
/**
 * Mencatat kunjungan ke landing page. Dipanggil oleh JavaScript di halaman,
 * sehingga bot tanpa JavaScript tidak ikut terhitung.
 */
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) json_response(['ok' => false], 400);

$visitor = (string) ($data['vid'] ?? '');
if (!preg_match('/^[a-zA-Z0-9-]{8,64}$/', $visitor)) json_response(['ok' => false], 400);

$program = (string) ($data['program'] ?? '');
if (!is_program($program)) json_response(['ok' => false], 400);

$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
if ($ua === '' || preg_match('/bot|crawl|spider|slurp|preview|headless|lighthouse/', $ua)) {
    json_response(['ok' => true]);
}

// Abaikan kunjungan ulang dari pengunjung yang sama dalam 30 menit (refresh halaman).
$stmt = db()->prepare('SELECT 1 FROM visits WHERE program = ? AND visitor_id = ? AND created_at > ? LIMIT 1');
$stmt->execute([$program, $visitor, date('Y-m-d H:i:s', time() - 1800)]);
if (!$stmt->fetchColumn()) {
    $ref = clean_text($data['ref'] ?? '', 300);
    $host = parse_url($ref, PHP_URL_HOST);
    if ($host && $host === ($_SERVER['HTTP_HOST'] ?? '')) $ref = '';

    $adId = preg_replace('/\D+/', '', (string) ($data['ad_id'] ?? ''));
    db()->prepare('INSERT INTO visits (program, created_at, day, visitor_id, referrer, utm_source, utm_campaign, utm_content, ad_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            $program, now(), date('Y-m-d'), $visitor, $ref ?: null,
            clean_text($data['utm_source'] ?? '', 100) ?: null,
            clean_text($data['utm_campaign'] ?? '', 200) ?: null,
            clean_text($data['utm_content'] ?? '', 200) ?: null,
            $adId !== '' ? substr($adId, 0, 30) : null,
        ]);
}

json_response(['ok' => true]);

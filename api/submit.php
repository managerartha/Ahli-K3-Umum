<?php
/**
 * Menerima data form pendaftaran dari landing page dan menyimpannya ke database.
 * Pengganti webhook Google Apps Script.
 */
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['result' => 'error', 'message' => 'Metode tidak diizinkan'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    json_response(['result' => 'error', 'message' => 'Data tidak valid'], 400);
}

$program = (string) ($data['program'] ?? '');
if (!is_program($program)) {
    json_response(['result' => 'error', 'message' => 'Program tidak dikenal'], 422);
}

$name   = clean_text($data['name'] ?? '', 150);
$phone  = clean_text($data['phone'] ?? '', 30);
$email  = clean_text($data['email'] ?? '', 150);
$goal   = clean_text($data['goal'] ?? '', 100);
$scheme = clean_text($data['scheme'] ?? '', 150);

if ($name === '' || $phone === '' || $email === '' || !preg_match('/\d{6,}/', preg_replace('/\D+/', '', $phone))) {
    json_response(['result' => 'error', 'message' => 'Nama, WhatsApp, dan email wajib diisi'], 422);
}

// Batasi spam: maksimal 10 pendaftaran per jam dari alamat IP yang sama.
$stmt = db()->prepare('SELECT COUNT(*) FROM registrations WHERE ip_hash = ? AND created_at > ?');
$stmt->execute([ip_hash(), date('Y-m-d H:i:s', time() - 3600)]);
if ((int) $stmt->fetchColumn() >= 10) {
    json_response(['result' => 'error', 'message' => 'Terlalu banyak pendaftaran, coba lagi nanti'], 429);
}

$adId = preg_replace('/\D+/', '', (string) ($data['ad_id'] ?? ''));

db()->prepare('INSERT INTO registrations (program, created_at, name, phone, email, goal, scheme,
                   utm_source, utm_medium, utm_campaign, utm_term, utm_content, ad_id, ip_hash)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
    ->execute([
        $program, now(), $name, $phone, $email, $goal, $scheme,
        clean_text($data['utm_source'] ?? '', 100) ?: null,
        clean_text($data['utm_medium'] ?? '', 100) ?: null,
        clean_text($data['utm_campaign'] ?? '', 200) ?: null,
        clean_text($data['utm_term'] ?? '', 200) ?: null,
        clean_text($data['utm_content'] ?? '', 200) ?: null,
        $adId !== '' ? substr($adId, 0, 30) : null,
        ip_hash(),
    ]);

json_response(['result' => 'success', 'message' => 'Data berhasil dicatat']);

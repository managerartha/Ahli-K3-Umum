<?php
/**
 * Halaman utama subdomain: arahkan ke landing page program pertama.
 * Ubah tujuan di sini jika nanti ingin halaman daftar semua pelatihan.
 */
require_once __DIR__ . '/includes/programs.php';
header('Location: ' . array_key_first(PROGRAMS) . '/', true, 302);
exit;

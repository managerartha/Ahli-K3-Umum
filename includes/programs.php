<?php
/**
 * Daftar program pelatihan (satu program = satu landing page).
 *
 * Kunci (misal 'ahli-k3-umum') = nama folder landing page di hosting.
 * Untuk menambah pelatihan baru: buat folder landing page baru dengan
 * nama yang sama, lalu tambahkan satu baris di bawah.
 */
const PROGRAMS = [
    'ahli-k3-umum' => [
        'name'        => 'Ahli K3 Umum Kemnaker RI',
        'batch_start' => '2026-10-26',
        'batch_end'   => '2026-11-06',
        // Urgensi (diatur dari dashboard → Pengaturan). Kosong = tidak ditampilkan.
        'promo_end'   => '',
        'quota_total' => '',
        'quota_left'  => '',
    ],
];

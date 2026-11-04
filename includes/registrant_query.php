<?php
/**
 * Filter pendaftar yang dipakai bersama oleh halaman daftar dan ekspor CSV.
 */

function registrant_filters(array $in, string $program = ''): array
{
    $date = fn($v) => is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
    $status = $in['status'] ?? '';
    return [
        'program' => $program,
        'q'      => mb_substr(trim((string) ($in['q'] ?? '')), 0, 100),
        'status' => in_array($status, ['baru', 'dihubungi'], true) ? $status : '',
        'from'   => $date($in['from'] ?? ''),
        'to'     => $date($in['to'] ?? ''),
    ];
}

function registrant_where(array $f): array
{
    $cond = [];
    $params = [];
    if ($f['program'] !== '') {
        $cond[] = 'program = ?';
        $params[] = $f['program'];
    }
    if ($f['q'] !== '') {
        $cond[] = '(name LIKE ? OR phone LIKE ? OR email LIKE ? OR notes LIKE ?)';
        $like = '%' . $f['q'] . '%';
        array_push($params, $like, $like, $like, $like);
    }
    if ($f['status'] !== '') {
        $cond[] = 'status = ?';
        $params[] = $f['status'];
    }
    if ($f['from'] !== '') {
        $cond[] = 'created_at >= ?';
        $params[] = $f['from'] . ' 00:00:00';
    }
    if ($f['to'] !== '') {
        $cond[] = 'created_at <= ?';
        $params[] = $f['to'] . ' 23:59:59';
    }
    return [$cond ? 'WHERE ' . implode(' AND ', $cond) : '', $params];
}

<?php
/**
 * Ulasan Google Maps PT Artha Safety Indonesia.
 *
 * Sumber: feed widget Google Reviews SociableKIT milik Artha (embed 25721082),
 * yang menyalin ulasan terbaru dari profil Google Maps. Hasilnya disimpan
 * sementara di data/ selama 6 jam supaya landing page tetap cepat.
 */
const REVIEWS_FEED_URL = 'https://data.accentapi.com/feed/25721082.json';
const REVIEWS_CACHE_TTL = 21600;      // 6 jam
const REVIEWS_RETRY_AFTER = 1800;     // jika gagal, coba lagi 30 menit kemudian
const REVIEWS_MIN_RATING = 5;
const REVIEWS_MIN_CHARS = 20;         // lewati ulasan yang terlalu pendek ("mantap")

function google_reviews(): ?array
{
    $cacheFile = dirname(__DIR__) . '/data/.ht-reviews.json';
    $cached = is_file($cacheFile) ? json_decode((string) @file_get_contents($cacheFile), true) : null;
    if (is_array($cached) && time() - (int) ($cached['fetched_at'] ?? 0) < REVIEWS_CACHE_TTL) {
        return $cached;
    }

    $raw = reviews_http_get(REVIEWS_FEED_URL);
    $fresh = $raw !== null ? reviews_parse($raw) : null;
    if ($fresh !== null) {
        $fresh['fetched_at'] = time();
        @file_put_contents($cacheFile, json_encode($fresh, JSON_UNESCAPED_UNICODE), LOCK_EX);
        return $fresh;
    }

    // Gagal mengambil: pakai data lama (atau kosong) dan coba lagi nanti.
    $keep = is_array($cached) ? $cached : ['rating' => null, 'count' => null, 'items' => []];
    $keep['fetched_at'] = time() - REVIEWS_CACHE_TTL + REVIEWS_RETRY_AFTER;
    @file_put_contents($cacheFile, json_encode($keep, JSON_UNESCAPED_UNICODE), LOCK_EX);
    return $keep;
}

function reviews_http_get(string $url): ?string
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (landing page reviews)',
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($body !== false && $code === 200) ? $body : null;
    }
    $ctx = stream_context_create(['http' => ['timeout' => 5, 'header' => "User-Agent: Mozilla/5.0\r\n"]]);
    $body = @file_get_contents($url, false, $ctx);
    return $body === false ? null : $body;
}

function reviews_parse(string $raw): ?array
{
    $d = json_decode($raw, true);
    if (!is_array($d) || !isset($d['reviews']) || !is_array($d['reviews'])) return null;

    $items = [];
    foreach ($d['reviews'] as $r) {
        $text = trim(preg_replace('/\s+/u', ' ', (string) ($r['review_text'] ?? '')));
        if ((int) ($r['rating'] ?? 0) < REVIEWS_MIN_RATING || (function_exists('mb_strlen') ? mb_strlen($text) : strlen($text)) < REVIEWS_MIN_CHARS) continue;
        $photo = (string) ($r['reviewer_photo_link'] ?? '');
        $items[] = [
            'name'  => trim((string) ($r['reviewer_name'] ?? 'Alumni')),
            'text'  => $text,
            'date'  => substr((string) ($r['review_date_time'] ?? ''), 0, 10),
            'photo' => strpos($photo, 'https://') === 0 ? preg_replace('/=s\d+/', '=s80', $photo) : '',
        ];
    }
    return [
        'rating' => isset($d['bio']['overall_star_rating']) ? (float) $d['bio']['overall_star_rating'] : null,
        'count'  => isset($d['bio']['rating_count']) ? (int) $d['bio']['rating_count'] : null,
        'items'  => $items,
    ];
}

/** "3 minggu lalu", "2 bulan lalu", dst. */
function reviews_ago(string $date): string
{
    $t = strtotime($date);
    if (!$t) return '';
    $days = max(0, (int) floor((time() - $t) / 86400));
    if ($days < 1) return 'hari ini';
    if ($days < 7) return $days . ' hari lalu';
    if ($days < 30) return floor($days / 7) . ' minggu lalu';
    if ($days < 365) return floor($days / 30) . ' bulan lalu';
    return floor($days / 365) . ' tahun lalu';
}

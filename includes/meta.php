<?php
/**
 * Mengambil biaya iklan per iklan per hari dari Meta (Facebook/Instagram) Marketing API,
 * lalu menyimpannya di tabel ad_insights untuk dihitung CPL, CPA, dan closing rate.
 *
 * Butuh dua pengaturan (diisi di dashboard → Pengaturan → Koneksi Meta Ads):
 *  - meta_account_id : ID akun iklan (angka, tanpa "act_")
 *  - meta_token      : Access token System User dengan izin ads_read
 */
require_once __DIR__ . '/bootstrap.php';

// Versi Graph API. Jika suatu saat Meta menonaktifkan versi ini, ganti ke versi terbaru.
const META_GRAPH_VERSION = 'v23.0';
const META_SYNC_DAYS = 40;          // ambil data 40 hari terakhir setiap sinkron
const META_FULL_SYNC_MONTHS = 24;   // sinkron penuh: data sejak awal (maksimal 24 bulan ke belakang)
const META_AUTO_SYNC_SECONDS = 10800; // sinkron otomatis jika data lebih tua dari 3 jam

function meta_graph_base(): string
{
    // Bisa diganti lewat environment variable (dipakai saat pengujian).
    return rtrim(getenv('META_GRAPH_URL') ?: 'https://graph.facebook.com/' . META_GRAPH_VERSION, '/');
}

function meta_configured(): bool
{
    return get_setting('meta_account_id') !== '' && get_setting('meta_token') !== '';
}

function meta_http_get(string $url): array
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $body = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($body === false) throw new RuntimeException('Tidak bisa terhubung ke Meta: ' . $err);
    } else {
        $body = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 30, 'ignore_errors' => true]]));
        if ($body === false) throw new RuntimeException('Tidak bisa terhubung ke Meta.');
    }
    $json = json_decode($body, true);
    if (!is_array($json)) throw new RuntimeException('Jawaban Meta tidak bisa dibaca.');
    if (isset($json['error'])) {
        $msg = $json['error']['message'] ?? 'Kesalahan tidak diketahui';
        $code = $json['error']['code'] ?? '';
        if ((int) $code === 190) $msg = 'Access token tidak valid atau sudah kedaluwarsa. Buat token baru. (' . $msg . ')';
        if ((int) $code === 100 || (int) $code === 803) $msg = 'ID akun iklan tidak ditemukan atau token tidak punya akses ke akun ini. (' . $msg . ')';
        throw new RuntimeException($msg);
    }
    return $json;
}

/**
 * Sinkronkan biaya iklan. Mengembalikan jumlah baris (iklan × hari) yang disimpan.
 * Melempar RuntimeException jika gagal; pesan error juga disimpan untuk ditampilkan.
 */
function meta_sync(bool $full = false): int
{
    // Sinkron pertama kali selalu penuh, supaya data sejak awal kampanye ikut tertarik.
    if (get_setting('meta_full_synced') === '') $full = true;
    $account = preg_replace('/\D+/', '', get_setting('meta_account_id'));
    $token = get_setting('meta_token');
    if ($account === '' || $token === '') throw new RuntimeException('Koneksi Meta Ads belum diatur.');

    $since = $full
        ? date('Y-m-d', strtotime('-' . META_FULL_SYNC_MONTHS . ' months'))
        : date('Y-m-d', strtotime('-' . (META_SYNC_DAYS - 1) . ' days'));
    $until = date('Y-m-d');

    $url = meta_graph_base() . '/act_' . $account . '/insights?' . http_build_query([
        'level'          => 'ad',
        'fields'         => 'ad_id,ad_name,adset_name,campaign_name,spend,impressions,inline_link_clicks,actions',
        'time_increment' => 1,
        'time_range'     => json_encode(['since' => $since, 'until' => $until]),
        'limit'          => 500,
        'access_token'   => $token,
    ]);

    try {
        $rows = [];
        $pages = 0;
        while ($url && $pages++ < 50) {
            $res = meta_http_get($url);
            foreach ($res['data'] ?? [] as $r) $rows[] = $r;
            $url = $res['paging']['next'] ?? null;
        }

        $pdo = db();
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM ad_insights WHERE day >= ? AND day <= ?')->execute([$since, $until]);
        $ins = $pdo->prepare('INSERT OR REPLACE INTO ad_insights (day, ad_id, ad_name, adset_name, campaign_name, spend, impressions, clicks, messaging)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($rows as $r) {
            // Jumlah percakapan WhatsApp/Messenger yang dimulai dari iklan (iklan klik-ke-WhatsApp).
            $messaging = 0;
            foreach ($r['actions'] ?? [] as $act) {
                if (($act['action_type'] ?? '') === 'onsite_conversion.messaging_conversation_started_7d') {
                    $messaging = (int) round((float) ($act['value'] ?? 0));
                }
            }
            $ins->execute([
                $r['date_start'] ?? $since,
                (string) ($r['ad_id'] ?? ''),
                $r['ad_name'] ?? null,
                $r['adset_name'] ?? null,
                $r['campaign_name'] ?? null,
                (float) ($r['spend'] ?? 0),
                (int) ($r['impressions'] ?? 0),
                (int) ($r['inline_link_clicks'] ?? 0),
                $messaging,
            ]);
        }
        $pdo->commit();

        set_setting('meta_last_sync', '', now());
        set_setting('meta_last_error', '', '');
        if ($full) set_setting('meta_full_synced', '', now());
        return count($rows);
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        set_setting('meta_last_error', '', $e->getMessage());
        set_setting('meta_last_sync', '', now()); // cegah percobaan ulang terus-menerus
        throw $e instanceof RuntimeException ? $e : new RuntimeException($e->getMessage());
    }
}

/** Sinkron otomatis jika sudah dikonfigurasi dan data sudah lebih dari 3 jam. Tidak melempar error. */
function meta_auto_sync(): void
{
    if (!meta_configured()) return;
    $last = get_setting('meta_last_sync');
    if ($last !== '' && time() - strtotime($last) < META_AUTO_SYNC_SECONDS) return;
    try {
        meta_sync();
    } catch (Throwable $e) {
        // Pesan error sudah disimpan dan ditampilkan di halaman.
    }
}

/** Iklan yang pernah menghasilkan chat WhatsApp = iklan WA (klik ke WhatsApp). */
function wa_ad_ids(): array
{
    $ids = [];
    foreach (db()->query('SELECT ad_id FROM ad_insights GROUP BY ad_id HAVING SUM(messaging) > 0') as $r) $ids[$r['ad_id']] = true;
    return $ids;
}

/** Kata kunci nama kampanye/iklan untuk sebuah program (dipisah koma), dalam huruf kecil. */
function campaign_keywords(string $program): array
{
    $raw = get_setting('campaign_keywords', $program);
    return array_values(array_filter(array_map(fn($k) => mb_strtolower(trim($k)), explode(',', $raw)), 'strlen'));
}

/**
 * Cocokkan nama kampanye dengan kata kunci program.
 * Kata kunci biasa = kampanye harus mengandung salah satunya.
 * Kata kunci diawali "-" = kampanye yang mengandungnya dikecualikan (misal "-testing wa").
 */
function campaign_matches(?string $campaign, array $keywords): bool
{
    if (!$keywords) return true;
    $c = mb_strtolower((string) $campaign);
    $include = [];
    foreach ($keywords as $k) {
        if ($k[0] === '-') {
            $ex = trim(substr($k, 1));
            if ($ex !== '' && strpos($c, $ex) !== false) return false;
        } else {
            $include[] = $k;
        }
    }
    if (!$include) return true;
    foreach ($include as $k) if (strpos($c, $k) !== false) return true;
    return false;
}

/** Parameter URL yang disarankan untuk dipasang di setiap iklan Meta. */
function recommended_url_params(): string
{
    return 'utm_source=meta&utm_medium=paid_social&utm_campaign={{campaign.name}}&utm_term={{adset.name}}&utm_content={{ad.name}}&ad_id={{ad.id}}';
}

<?php
/**
 * Bagian "Kata Alumni" untuk semua landing page.
 * Ulasan Google diambil otomatis (lihat includes/reviews.php); kartu di bawah
 * ($rvFallback) hanya dipakai jika ulasan Google belum bisa diambil.
 * Membutuhkan $h dari landing page.
 */
require_once __DIR__ . '/reviews.php';

$rv = null;
try {
    $rv = google_reviews();
} catch (Throwable $e) {
    error_log('Google reviews: ' . $e->getMessage());
}
$rvRating = $rv['rating'] ?? 5;
$rvCount = $rv['count'] ?? 1139;

$rvFallback = [
    ['Iqbal Romzy', 'Mengikuti pelatihan bersama PT Artha Safety Indonesia sangat menambah ilmu baik teori maupun praktiknya. Hal ini sangat memperluas wawasan dalam dunia kerja K3. Penyampaian materi menarik dan panitianya super ramah!'],
    ['Nixon Marbun', 'Seru, panitia membuat suasana pelatihan feels like home. Pemateri yang memberikan ilmu merupakan ahli di bidangnya masing-masing. Terima kasih Artha Safety Indonesia, siap rekomendasikan untuk rekan kantor!'],
    ['Renanda Bayu', 'Seru banget ikut pelatihan Ahli K3 Umum di PT Artha Safety Indonesia ini! Tim panitianya asik dan pematerinya berkompeten langsung dari Kemnaker RI. Sangat berkualitas dan terpercaya.'],
    ['Herdina Theresia', 'Tempat pelatihan K3 KEMNAKER terpercaya, 100% batch saya lulus! Senang banget dapat relasi baru yang profesional dan suportif. Recommended buat yang mau ambil lisensi K3!'],
    ['Anisa Andriyani', 'Belajar K3 Umum di PT Artha Safety Indonesia seru banget! Pematerinya keren dan materinya mudah dimengerti. Saya juga dapat banyak relasi baru. Pokoknya wajib ambil sertifikasi di sini gengs! ✨🙌🏻'],
    ['Dwi Cahyono', 'Alhamdulillah lulus 100% dan berkas SKP & Lisensi Kemnaker RI terverifikasi resmi di TemanK3. Pendampingan administrasi tim Artha Safety sangat transparan dan sigap.'],
];

$rvItems = [];
foreach ($rv['items'] ?? [] as $it) {
    $rvItems[] = ['name' => $it['name'], 'text' => $it['text'], 'meta' => reviews_ago($it['date']), 'photo' => $it['photo']];
}
if (count($rvItems) < 6) {
    foreach ($rvFallback as [$n, $t]) $rvItems[] = ['name' => $n, 'text' => $t, 'meta' => 'Ulasan Google', 'photo' => ''];
}
// Minimal 8 kartu per baris supaya baris selalu penuh (tidak ada ruang kosong).
$rvPool = $rvItems;
while (count($rvPool) < 16) $rvPool = array_merge($rvPool, $rvItems);
$rvRows = [[], []];
foreach ($rvPool as $i => $it) $rvRows[$i % 2][] = $it;

$rvInitials = function (string $name): string {
    $parts = preg_split('/\s+/', trim(preg_replace('/[^\p{L}\s]/u', '', $name)));
    $ini = '';
    foreach (array_slice(array_filter($parts), 0, 2) as $p) $ini .= function_exists('mb_substr') ? mb_strtoupper(mb_substr($p, 0, 1)) : strtoupper($p[0]);
    return $ini ?: 'A';
};
$rvColors = ['bg-brand-navy text-white', 'bg-brand-blue text-white', 'bg-brand-accent text-brand-dark', 'bg-emerald-600 text-white', 'bg-sky-600 text-white'];
$rvCard = function (array $it, int $i, bool $dup) use ($h, $rvInitials, $rvColors) {
    ?>
          <figure class="w-[250px] sm:w-[290px] bg-white text-slate-800 rounded-2xl p-4 sm:p-5 shadow-lg flex flex-col justify-between flex-shrink-0 border border-slate-100"<?= $dup ? ' aria-hidden="true"' : '' ?>>
            <div>
              <div class="flex items-center justify-between mb-2.5">
                <div class="flex text-amber-400 text-[11px] gap-0.5"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                <i class="fa-brands fa-google text-slate-400 text-xs"></i>
              </div>
              <blockquote class="text-xs text-slate-600 leading-relaxed line-clamp-4">"<?= $h($it['text']) ?>"</blockquote>
            </div>
            <figcaption class="flex items-center gap-2.5 pt-3 mt-3 border-t border-slate-100">
              <span class="relative w-8 h-8 rounded-full <?= $rvColors[$i % count($rvColors)] ?> font-black text-[10px] flex items-center justify-center flex-shrink-0 overflow-hidden">
                <?= $h($rvInitials($it['name'])) ?>
                <?php if ($it['photo']): ?><img src="<?= $h($it['photo']) ?>" alt="" width="32" height="32" loading="lazy" decoding="async" referrerpolicy="no-referrer" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()"><?php endif; ?>
              </span>
              <span class="min-w-0">
                <span class="block text-xs font-bold text-slate-900 truncate"><?= $h($it['name']) ?></span>
                <span class="block text-[10px] text-slate-500"><?= $h($it['meta']) ?></span>
              </span>
            </figcaption>
          </figure>
    <?php
};
?>
  <section id="reviews" class="w-full bg-brand-royal py-14 sm:py-20 overflow-hidden relative text-white">
    <div class="max-w-4xl mx-auto px-4 text-center mb-8 sm:mb-10">
      <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold text-brand-accent mb-4 border border-white/20">
        <i class="fa-brands fa-google text-white text-xs"></i>
        <span>Ulasan Google &bull; <?= number_format($rvRating, 1, ',', '.') ?> ★ dari <?= number_format($rvCount, 0, ',', '.') ?> ulasan</span>
      </div>

      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-4 leading-tight">
        Kata Alumni Pelatihan<br class="hidden sm:inline"> Artha Safety
      </h2>
      <a href="https://maps.google.com/?cid=4002387960243989151" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-bold text-white underline decoration-brand-accent decoration-2 underline-offset-4 hover:text-brand-accent">
        <span>Lihat semua <?= number_format($rvCount, 0, ',', '.') ?> ulasan di Google Maps</span>
        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
      </a>
    </div>

    <!-- Dua baris ulasan bergulir berlawanan arah. Setiap baris digandakan agar berputar tanpa putus. -->
    <div class="marquee-container space-y-4 select-none">
      <?php foreach ($rvRows as $r => $row): ?>
      <div class="overflow-hidden">
        <div class="<?= $r === 0 ? 'animate-marquee-left' : 'animate-marquee-right' ?> flex gap-4 items-stretch" style="animation-duration: <?= count($row) * 4 ?>s">
          <?php foreach ([false, true] as $dup): foreach ($row as $i => $it) $rvCard($it, $i + $r, $dup); endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="max-w-5xl mx-auto px-4 mt-10 sm:mt-12 text-center">
      <p class="text-sm font-bold text-slate-200 mb-4">Dipercaya <span class="text-brand-accent">100+ perusahaan &amp; instansi</span> untuk melatih tenaga K3-nya, di antaranya:</p>
      <div class="flex flex-wrap justify-center gap-2">
        <?php foreach (['Kilang Pertamina Internasional', 'PetroChina', 'KAI', 'Pelindo', 'Adhi Karya', 'Total Bangun Persada', 'Karya Podo Moro', 'Radiant Utama Interinsco', 'Sinarmas CEPSR', 'Eka Hospital', 'Institut Teknologi PLN', 'KOBEX'] as $c): ?>
          <span class="bg-white/10 border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-full"><?= $c ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

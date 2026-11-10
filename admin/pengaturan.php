<?php
/**
 * Pengaturan per program (tanggal batch & template pesan WhatsApp)
 * dan ganti password admin.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';
require_once dirname(__DIR__) . '/includes/meta.php';

$user = require_login();
$selected = admin_program();
// Jika satu program dipilih di header, tampilkan program itu saja.
$programs = $selected ? [$selected => PROGRAMS[$selected]] : PROGRAMS;

admin_head('Pengaturan');
admin_nav('pengaturan.php', $user);
?>
    <h1 class="text-2xl font-extrabold text-brand-navy mb-1">Pengaturan</h1>
    <p class="text-sm text-slate-500 mb-5">Tanggal batch dan pesan WhatsApp diatur terpisah untuk setiap program pelatihan.</p>

    <?php foreach ($programs as $slug => $prog): ?>
      <section id="<?= e($slug) ?>" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-5 scroll-mt-28">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
          <h2 class="text-base font-extrabold text-brand-navy"><?= e($prog['name']) ?></h2>
          <a href="../<?= e($slug) ?>/" target="_blank" rel="noopener" class="text-xs font-bold text-brand-blue hover:text-brand-navy">
            <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Buka landing page
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
          <!-- Tanggal batch -->
          <div class="p-5 sm:p-6">
            <h3 class="text-sm font-extrabold text-brand-navy mb-1"><i class="fa-regular fa-calendar mr-1.5 text-brand-blue"></i>Tanggal Batch Terdekat</h3>
            <p class="text-xs text-slate-500 mb-4">Tampil di kotak <em>Ringkasan Program</em>, kartu samping, dan bar bawah di HP.</p>
            <form method="post" action="action.php" class="space-y-3" data-batch-form>
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="save_batch">
              <input type="hidden" name="program" value="<?= e($slug) ?>">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal mulai</label>
                  <input type="date" name="batch_start" data-start required value="<?= e(get_setting('batch_start', $slug)) ?>" class="<?= input_class() ?>">
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal selesai</label>
                  <input type="date" name="batch_end" data-end required value="<?= e(get_setting('batch_end', $slug)) ?>" class="<?= input_class() ?>">
                </div>
              </div>
              <div class="bg-slate-50 rounded-xl border border-slate-200 px-4 py-2.5 text-xs">
                <p class="text-slate-500 font-semibold">Pratinjau di landing page:</p>
                <p class="font-extrabold text-brand-navy" data-preview-long><?= e(batch_long($slug)) ?></p>
                <p class="font-bold text-slate-600" data-preview-short><?= e(batch_short($slug)) ?></p>
              </div>
              <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl">Simpan Tanggal</button>
            </form>
          </div>

          <!-- Template WA -->
          <div class="p-5 sm:p-6">
            <h3 class="text-sm font-extrabold text-brand-navy mb-1"><i class="fa-brands fa-whatsapp mr-1.5 text-emerald-600"></i>Template Pesan WhatsApp</h3>
            <p class="text-xs text-slate-500 mb-4">Pesan awal saat Anda klik nomor pendaftar. <code class="bg-slate-100 px-1 rounded">{nama}</code>, <code class="bg-slate-100 px-1 rounded">{skema}</code>, dan <code class="bg-slate-100 px-1 rounded">{program}</code> terisi otomatis.</p>
            <form method="post" action="action.php" class="space-y-3">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="save_wa_template">
              <input type="hidden" name="program" value="<?= e($slug) ?>">
              <textarea name="wa_template" rows="5" maxlength="1000" class="<?= input_class() ?>"><?= e(get_setting('wa_template', $slug)) ?></textarea>
              <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl">Simpan Template</button>
            </form>
          </div>
        </div>

        <!-- Promo & kuota (urgensi di landing page) -->
        <div class="px-5 sm:px-6 py-4 border-t border-slate-100">
          <h3 class="text-sm font-extrabold text-brand-navy mb-1"><i class="fa-solid fa-fire mr-1.5 text-red-500"></i>Promo &amp; Kuota (urgensi di landing page)</h3>
          <p class="text-xs text-slate-500 mb-3">Tampil di bar atas, hero, bagian biaya, dan bar bawah HP. Kosongkan kolom yang tidak ingin ditampilkan. <strong>Perbarui sisa kursi setiap ada peserta yang bayar DP</strong> supaya angkanya selalu jujur.</p>
          <form method="post" action="action.php" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_urgency">
            <input type="hidden" name="program" value="<?= e($slug) ?>">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Harga promo berlaku s/d</label>
              <input type="date" name="promo_end" value="<?= e(get_setting('promo_end', $slug)) ?>" class="<?= input_class() ?>">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Total kursi batch</label>
              <input type="number" min="0" max="999" name="quota_total" value="<?= e(get_setting('quota_total', $slug)) ?>" placeholder="contoh: 30" class="<?= input_class() ?>">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Sisa kursi</label>
              <input type="number" min="0" max="999" name="quota_left" value="<?= e(get_setting('quota_left', $slug)) ?>" placeholder="contoh: 8" class="<?= input_class() ?>">
            </div>
            <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl">Simpan</button>
          </form>
          <p class="text-[11px] text-slate-400 mt-2">Hitung mundur ke hari pertama pelatihan otomatis dari <em>Tanggal mulai</em> batch di atas.</p>
        </div>

        <!-- Kata kunci kampanye -->
        <div class="px-5 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/60 rounded-b-2xl">
          <form method="post" action="action.php" class="flex flex-col md:flex-row md:items-end gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_campaign_keywords">
            <input type="hidden" name="program" value="<?= e($slug) ?>">
            <div class="flex-1">
              <label class="block text-xs font-bold text-slate-700 mb-1"><i class="fa-brands fa-meta mr-1 text-brand-blue"></i>Kata kunci nama kampanye / iklan Meta untuk program ini</label>
              <input name="campaign_keywords" value="<?= e(get_setting('campaign_keywords', $slug)) ?>" placeholder="Contoh: AK3U Web, Landing AK3U" class="<?= input_class() ?>">
              <p class="text-[11px] text-slate-500 mt-1">Biaya iklan dihitung dari kampanye atau iklan yang namanya mengandung salah satu kata ini (pisahkan dengan koma). Awali dengan tanda minus untuk <strong>mengecualikan</strong>, misalnya <code class="bg-slate-100 px-1 rounded">-Testing WA</code>. Kosongkan untuk menghitung semua kampanye.</p>
            </div>
            <button class="bg-white border border-slate-300 hover:border-brand-navy text-brand-navy font-bold text-sm px-5 py-2.5 rounded-xl">Simpan</button>
          </form>
        </div>
      </section>
    <?php endforeach; ?>

    <!-- Koneksi Meta Ads -->
    <?php $metaToken = get_setting('meta_token'); ?>
    <section id="meta" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-5 scroll-mt-28">
      <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
        <h2 class="text-base font-extrabold text-brand-navy"><i class="fa-brands fa-meta mr-1.5 text-brand-blue"></i>Koneksi Meta Ads</h2>
        <p class="text-xs text-slate-500 mt-0.5">Untuk mengambil biaya iklan otomatis (CPL & CPA) di halaman <a href="iklan.php" class="font-bold text-brand-blue">Iklan &amp; Closing</a>. Hanya membaca data, tidak bisa mengubah iklan.</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
        <form method="post" action="action.php" class="p-5 sm:p-6 space-y-3">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_meta">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ID Akun Iklan</label>
            <input name="meta_account_id" value="<?= e(get_setting('meta_account_id')) ?>" placeholder="Contoh: 2229317924648301" inputmode="numeric" class="<?= input_class() ?>">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Access Token</label>
            <input type="password" name="meta_token" autocomplete="off" placeholder="<?= $metaToken ? 'Tersimpan (kosongkan jika tidak diganti)' : 'Tempel access token di sini' ?>" class="<?= input_class() ?>">
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl">Simpan &amp; Tes Koneksi</button>
            <?php if ($metaToken): ?>
              <button name="disconnect" value="1" onclick="return confirm('Putuskan koneksi Meta Ads? Data biaya yang sudah tersimpan tetap ada.');" class="text-xs font-bold text-red-600 hover:text-red-700 px-2">Putuskan</button>
            <?php endif; ?>
          </div>
          <?php if (meta_configured()): ?>
            <p class="text-xs <?= get_setting('meta_last_error') ? 'text-red-600' : 'text-emerald-700' ?> font-semibold">
              <?= get_setting('meta_last_error') ? '<i class="fa-solid fa-triangle-exclamation mr-1"></i>' . e(get_setting('meta_last_error')) : '<i class="fa-solid fa-circle-check mr-1"></i>Terhubung. Terakhir diperbarui ' . e(get_setting('meta_last_sync') ? date('d/m/Y H:i', strtotime(get_setting('meta_last_sync'))) : '-') ?>
            </p>
          <?php endif; ?>
        </form>
        <div class="p-5 sm:p-6">
          <h3 class="text-sm font-extrabold text-brand-navy mb-1">Parameter URL untuk setiap iklan</h3>
          <p class="text-xs text-slate-500 mb-3">Tempel teks ini di Ads Manager → edit iklan → bagian <em>Tujuan / Destination</em> → <strong>URL parameters</strong>. Dengan ini dashboard tahu setiap lead datang dari iklan mana.</p>
          <div class="flex gap-2">
            <input id="urlParams" readonly value="<?= e(recommended_url_params()) ?>" class="flex-1 min-w-0 px-3 py-2 rounded-xl border border-slate-300 text-[11px] font-mono bg-slate-50">
            <button type="button" onclick="var i=document.getElementById('urlParams'); i.select(); navigator.clipboard && navigator.clipboard.writeText(i.value); this.textContent='Tersalin ✓';" class="shrink-0 bg-brand-accent hover:bg-brand-accentHover text-brand-dark font-extrabold text-xs px-3 rounded-xl">Salin</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Ganti password -->
    <section class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm max-w-xl">
      <h2 class="text-base font-extrabold text-brand-navy mb-1"><i class="fa-solid fa-key mr-1.5 text-brand-blue"></i>Ganti Password</h2>
      <p class="text-xs text-slate-500 mb-4">Login sebagai <strong><?= e($user['username']) ?></strong>.</p>
      <form method="post" action="action.php" class="space-y-3">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">
        <input type="password" name="current" required placeholder="Password saat ini" autocomplete="current-password" class="<?= input_class() ?>">
        <input type="password" name="new" required minlength="8" placeholder="Password baru (min. 8 karakter)" autocomplete="new-password" class="<?= input_class() ?>">
        <input type="password" name="confirm" required minlength="8" placeholder="Ulangi password baru" autocomplete="new-password" class="<?= input_class() ?>">
        <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl">Ganti Password</button>
      </form>
    </section>

    <script>
      // Pratinjau format tanggal, sama persis dengan fungsi batch_label() di server.
      (function () {
        var L = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        var S = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        function label(a, b, long) {
          var p = a.split('-').map(Number), q = b.split('-').map(Number);
          if (p.length < 3 || q.length < 3 || !p[0] || !q[0]) return '';
          var em = long ? L[q[1]] : S[q[1]], sep = long ? ' - ' : '-';
          if (p[0] !== q[0]) return p[2] + ' ' + S[p[1]] + ' ' + p[0] + sep + q[2] + ' ' + em + ' ' + q[0];
          if (p[1] === q[1]) return p[2] + sep + q[2] + ' ' + em + ' ' + q[0];
          return p[2] + ' ' + S[p[1]] + sep + q[2] + ' ' + em + ' ' + q[0];
        }
        document.querySelectorAll('[data-batch-form]').forEach(function (form) {
          var s = form.querySelector('[data-start]'), t = form.querySelector('[data-end]');
          function upd() {
            form.querySelector('[data-preview-long]').textContent = label(s.value, t.value, true);
            form.querySelector('[data-preview-short]').textContent = label(s.value, t.value, false);
          }
          s.addEventListener('input', upd); t.addEventListener('input', upd);
        });
      })();
    </script>
<?php admin_foot();

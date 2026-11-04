<?php
/**
 * Pengaturan per program (tanggal batch & template pesan WhatsApp)
 * dan ganti password admin.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';

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
      </section>
    <?php endforeach; ?>

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

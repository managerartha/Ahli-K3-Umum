<?php
/**
 * Pengaturan: tanggal batch di landing page, template pesan WhatsApp,
 * dan ganti password admin.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';

$user = require_login();
$start = get_setting('batch_start');
$end = get_setting('batch_end');

admin_head('Pengaturan');
admin_nav('pengaturan.php', $user);
?>
    <h1 class="text-2xl font-extrabold text-brand-navy mb-5">Pengaturan</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Tanggal batch -->
      <section class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm lg:col-span-2">
        <h2 class="text-base font-extrabold text-brand-navy mb-1"><i class="fa-regular fa-calendar mr-1.5 text-brand-blue"></i>Tanggal Batch Terdekat</h2>
        <p class="text-xs text-slate-500 mb-5">Tanggal ini tampil di 3 tempat di landing page: kotak <em>Ringkasan Program</em>, kartu samping (<em>Batch Terdekat</em>), dan bar bawah di HP.</p>
        <form method="post" action="action.php" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_batch">
          <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal mulai</label>
            <input type="date" name="batch_start" id="batchStart" required value="<?= e($start) ?>" class="<?= input_class() ?>">
          </div>
          <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal selesai</label>
            <input type="date" name="batch_end" id="batchEnd" required value="<?= e($end) ?>" class="<?= input_class() ?>">
          </div>
          <div class="md:col-span-4 bg-slate-50 rounded-xl border border-slate-200 px-4 py-2.5 text-xs">
            <p class="text-slate-500 font-semibold">Pratinjau di landing page:</p>
            <p class="font-extrabold text-brand-navy" id="previewLong"><?= e(batch_long()) ?></p>
            <p class="font-bold text-slate-600" id="previewShort"><?= e(batch_short()) ?></p>
          </div>
          <div class="md:col-span-2">
            <button class="w-full bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm py-2.5 rounded-xl">Simpan</button>
          </div>
        </form>
      </section>

      <!-- Template WA -->
      <section class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
        <h2 class="text-base font-extrabold text-brand-navy mb-1"><i class="fa-brands fa-whatsapp mr-1.5 text-emerald-600"></i>Template Pesan WhatsApp</h2>
        <p class="text-xs text-slate-500 mb-4">Pesan awal saat Anda klik nomor pendaftar. Gunakan <code class="bg-slate-100 px-1 rounded">{nama}</code> dan <code class="bg-slate-100 px-1 rounded">{skema}</code> untuk diisi otomatis.</p>
        <form method="post" action="action.php" class="space-y-3">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_wa_template">
          <textarea name="wa_template" rows="5" maxlength="1000" class="<?= input_class() ?>"><?= e(get_setting('wa_template')) ?></textarea>
          <button class="bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm px-5 py-2.5 rounded-xl">Simpan Template</button>
        </form>
      </section>

      <!-- Ganti password -->
      <section class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm">
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
    </div>

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
        var s = document.getElementById('batchStart'), t = document.getElementById('batchEnd');
        function upd() {
          document.getElementById('previewLong').textContent = label(s.value, t.value, true);
          document.getElementById('previewShort').textContent = label(s.value, t.value, false);
        }
        s.addEventListener('input', upd); t.addEventListener('input', upd);
      })();
    </script>
<?php admin_foot();

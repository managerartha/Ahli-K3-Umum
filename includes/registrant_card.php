<?php
/**
 * Satu baris pendaftar di dashboard, lengkap dengan tombol WhatsApp.
 * Klik tombol WhatsApp otomatis menandai pendaftar sebagai "Sudah dihubungi".
 */

function registrant_row(array $r, bool $withActions = true, bool $showProgram = false): void
{
    $contacted = $r['status'] === 'dihubungi';
    $when = date('d/m/Y H:i', strtotime($r['created_at']));
    ?>
    <div class="px-4 sm:px-5 py-4 flex flex-col md:flex-row md:items-center gap-3" id="reg-<?= (int) $r['id'] ?>">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="font-extrabold text-brand-navy text-sm"><?= e($r['name']) ?></span>
          <span data-status-badge class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full <?= $contacted ? 'bg-slate-100 text-slate-500' : 'bg-brand-accent text-brand-dark' ?>">
            <?= $contacted ? 'Sudah dihubungi' : 'Baru' ?>
          </span>
          <?php if ($showProgram): ?>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-subtle text-brand-blue"><?= e(program_name($r['program'])) ?></span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          <i class="fa-regular fa-clock mr-0.5"></i> <?= e($when) ?>
          <span class="text-slate-300 mx-1">•</span><?= e($r['scheme'] ?: '-') ?>
          <span class="text-slate-300 mx-1">•</span><?= e($r['goal'] ?: '-') ?>
        </p>
        <p class="text-xs text-slate-500 mt-0.5 break-all">
          <i class="fa-regular fa-envelope mr-0.5"></i> <a href="mailto:<?= e($r['email']) ?>" class="hover:text-brand-blue"><?= e($r['email']) ?></a>
          <?php if (!empty($r['utm_source'])): ?>
            <span class="text-slate-300 mx-1">•</span><span class="text-slate-400">sumber: <?= e($r['utm_source']) ?><?= !empty($r['utm_campaign']) ? ' / ' . e($r['utm_campaign']) : '' ?></span>
          <?php endif; ?>
        </p>
        <?php if (!empty($r['notes'])): ?>
          <p class="text-xs text-slate-700 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1.5 mt-2 whitespace-pre-line"><i class="fa-regular fa-note-sticky mr-1 text-amber-600"></i><?= e($r['notes']) ?></p>
        <?php endif; ?>
        <?php if ($withActions): ?>
          <details class="mt-2 group/notes">
            <summary class="text-[11px] font-bold text-brand-blue hover:text-brand-navy cursor-pointer select-none list-none">
              <i class="fa-regular fa-pen-to-square mr-0.5"></i><?= empty($r['notes']) ? 'Tambah catatan' : 'Edit catatan' ?>
            </summary>
            <form method="post" action="action.php" class="mt-2 flex flex-col sm:flex-row gap-2">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="save_notes">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI'] ?? 'pendaftar.php') ?>">
              <textarea name="notes" rows="3" maxlength="2000" placeholder="Contoh: hot, sudah tanya biaya, FU 16 Okt" class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-brand-blue/30"><?= e($r['notes'] ?? '') ?></textarea>
              <button class="self-start bg-brand-navy hover:bg-brand-blue text-white font-bold text-xs px-4 py-2 rounded-xl">Simpan</button>
            </form>
          </details>
        <?php endif; ?>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <a href="<?= e(wa_link($r)) ?>" target="_blank" rel="noopener" data-wa-id="<?= (int) $r['id'] ?>"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-3.5 py-2 rounded-xl shadow-sm">
          <i class="fa-brands fa-whatsapp text-base"></i><span><?= e($r['phone']) ?></span>
        </a>
        <?php if ($withActions): ?>
          <form method="post" action="action.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI'] ?? 'pendaftar.php') ?>">
            <button title="<?= $contacted ? 'Tandai belum dihubungi' : 'Tandai sudah dihubungi' ?>" class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-brand-navy hover:border-brand-navy">
              <i class="fa-solid <?= $contacted ? 'fa-rotate-left' : 'fa-check' ?>"></i>
            </button>
          </form>
          <form method="post" action="action.php" onsubmit="return confirm('Hapus pendaftar <?= e(addslashes($r['name'])) ?>? Data tidak bisa dikembalikan.');">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <input type="hidden" name="back" value="<?= e($_SERVER['REQUEST_URI'] ?? 'pendaftar.php') ?>">
            <button title="Hapus" class="w-9 h-9 rounded-xl border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-300">
              <i class="fa-regular fa-trash-can"></i>
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <?php
    static $scriptPrinted = false;
    if (!$scriptPrinted) {
        $scriptPrinted = true;
        registrant_script();
    }
}

function registrant_script(): void
{
    ?>
    <script>
      document.addEventListener('click', function (ev) {
        var a = ev.target.closest('[data-wa-id]');
        if (!a) return;
        var id = a.getAttribute('data-wa-id');
        var body = new URLSearchParams({ action: 'mark_contacted', id: id, csrf: <?= json_encode(csrf_token()) ?>, ajax: '1' });
        fetch('action.php', { method: 'POST', body: body, keepalive: true }).then(function () {
          var row = document.getElementById('reg-' + id);
          var badge = row && row.querySelector('[data-status-badge]');
          if (badge) { badge.textContent = 'Sudah dihubungi'; badge.className = 'text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-500'; }
        }).catch(function () {});
      });
    </script>
    <?php
}

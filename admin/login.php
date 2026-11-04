<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';

if (!has_users()) {
    header('Location: setup.php');
    exit;
}
if (current_user()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (login_locked()) {
        $error = 'Terlalu banyak percobaan gagal. Coba lagi dalam 15 menit.';
    } else {
        $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        $stmt->execute([trim((string) ($_POST['username'] ?? ''))]);
        $u = $stmt->fetch();
        if ($u && password_verify((string) ($_POST['password'] ?? ''), $u['password_hash'])) {
            clear_failed_logins();
            login_user((int) $u['id']);
            header('Location: index.php');
            exit;
        }
        record_failed_login();
        $error = 'Username atau password salah.';
    }
}

auth_card_open('Masuk Dashboard', 'PT Artha Safety Indonesia');
?>
      <?php if ($error): ?>
        <div class="mb-4 rounded-xl px-3 py-2.5 text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
          <input name="username" required autofocus autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
          <input type="password" name="password" required autocomplete="current-password" class="<?= input_class() ?>">
        </div>
        <button class="w-full bg-brand-navy hover:bg-brand-blue text-white font-bold py-3 rounded-xl text-sm">Masuk</button>
      </form>
<?php auth_card_close();

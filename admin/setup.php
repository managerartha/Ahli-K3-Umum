<?php
/**
 * Pembuatan akun admin pertama. Halaman ini otomatis nonaktif
 * setelah akun pertama dibuat.
 */
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/admin_layout.php';

if (has_users()) {
    header('Location: login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm  = (string) ($_POST['confirm'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9._-]{3,40}$/', $username)) {
        $error = 'Username 3–40 karakter, hanya huruf, angka, titik, strip, atau garis bawah.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $pdo = db();
        $pdo->beginTransaction();
        // Cek ulang di dalam transaksi agar tidak ada dua akun "pertama".
        if ((int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
            $pdo->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, ?)')
                ->execute([$username, password_hash($password, PASSWORD_DEFAULT), now()]);
            $uid = (int) $pdo->lastInsertId();
            $pdo->commit();
            login_user($uid);
            flash('Akun admin berhasil dibuat. Selamat datang!');
            header('Location: index.php');
            exit;
        }
        $pdo->rollBack();
        header('Location: login.php');
        exit;
    }
}

auth_card_open('Buat Akun Admin', 'Langkah pertama, hanya sekali');
?>
      <p class="text-xs text-slate-600 bg-brand-subtle rounded-xl p-3 mb-5 leading-relaxed">
        Belum ada akun admin. Buat username dan password untuk masuk ke dashboard. Halaman ini otomatis tertutup setelah akun dibuat.
      </p>
      <?php if ($error): ?>
        <div class="mb-4 rounded-xl px-3 py-2.5 text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
          <input name="username" required autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Password (min. 8 karakter)</label>
          <input type="password" name="password" required minlength="8" autocomplete="new-password" class="<?= input_class() ?>">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Password</label>
          <input type="password" name="confirm" required minlength="8" autocomplete="new-password" class="<?= input_class() ?>">
        </div>
        <button class="w-full bg-brand-navy hover:bg-brand-blue text-white font-bold py-3 rounded-xl text-sm">Buat Akun &amp; Masuk</button>
      </form>
<?php auth_card_close();

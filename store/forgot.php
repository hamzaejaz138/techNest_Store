<?php
require_once 'data.php';
$msg = ''; $link = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $login = trim($_POST['login'] ?? '');
  $st = $pdo->prepare('SELECT id, email FROM admins WHERE username = ? OR email = ?'); $st->execute([$login, $login]);
  if ($a = $st->fetch()) {
    $token = bin2hex(random_bytes(32));
    $pdo->prepare('DELETE FROM password_resets WHERE admin_id = ?')->execute([$a['id']]);
    $pdo->prepare('INSERT INTO password_resets (admin_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))')
        ->execute([$a['id'], hash('sha256', $token)]);
    $url = SITE_URL.'/reset.php?token='.$token;
    if (DEV_MODE) $link = $url;
    elseif ($a['email']) @mail($a['email'], 'Reset your TechNest admin password', "Open this link within 30 minutes:\n$url");
  }
  $msg = 'If that account exists, a reset link has been sent.'; // same message either way
}
include 'header.php'; ?>
<main class="wrap done"><form class="card login" method="post">
  <h2>Forgot password</h2>
  <p class="muted">Enter your admin username or email and we'll send a reset link.</p>
  <?php if ($msg): ?><p class="okmsg"><?= e($msg) ?></p><?php endif; ?>
  <?php if ($link): ?><p class="devbox"><b>Dev mode:</b> no email is sent locally.<br><a href="<?= e($link) ?>">Open reset link</a></p><?php endif; ?>
  <?= csrf_field() ?>
  <label>Username or email<input name="login" required autofocus></label>
  <button class="btn full">Send reset link</button>
  <p style="text-align:center;margin:14px 0 0"><a href="admin.php">Back to login</a></p>
</form></main>
<?php include 'footer.php'; ?>

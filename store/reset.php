<?php
require_once 'data.php';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$st = $pdo->prepare('SELECT admin_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW()');
$st->execute([hash('sha256', (string)$token)]); $row = $st->fetch();
$err = ''; $done = false;
if ($row && $_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $pw = $_POST['password'] ?? '';
  if (strlen($pw) < 8) $err = 'Password must be at least 8 characters.';
  elseif ($pw !== ($_POST['confirm'] ?? '')) $err = 'Passwords do not match.';
  else {
    $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $row['admin_id']]);
    $pdo->prepare('DELETE FROM password_resets WHERE admin_id = ?')->execute([$row['admin_id']]);
    $done = true;
  }
}
include 'header.php'; ?>
<main class="wrap done">
<?php if ($done): ?>
  <div class="card login"><h2>Password updated ✅</h2><p>You can now sign in with your new password.</p><a class="btn full" href="admin.php">Go to login</a></div>
<?php elseif (!$row): ?>
  <div class="card login"><h2>Link expired</h2><p class="muted">This reset link is invalid or has expired.</p><a class="btn full" href="forgot.php">Request a new link</a></div>
<?php else: ?>
  <form class="card login" method="post"><h2>Set a new password</h2>
    <?php if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
    <?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>">
    <label>New password<input type="password" name="password" minlength="8" required autofocus></label>
    <label>Confirm password<input type="password" name="confirm" minlength="8" required></label>
    <button class="btn full">Update password</button></form>
<?php endif; ?></main>
<?php include 'footer.php'; ?>

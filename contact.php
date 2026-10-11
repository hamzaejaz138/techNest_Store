<?php require_once 'data.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(120), email VARCHAR(160), subject VARCHAR(160), message TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
$sent = false; $err = ''; $f = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  foreach ($f as $k => $_) $f[$k] = trim($_POST[$k] ?? '');
  if ($f['name'] === '' || $f['message'] === '' || !filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $err = 'Please enter your name, a valid email, and a message.';
  else { $pdo->prepare('INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)')->execute(array_values($f)); $sent = true; $f = array_map(fn($x) => '', $f); }
}
include 'header.php'; ?>
<main class="wrap">
  <div class="shophero"><p class="crumb" style="margin:0 0 6px"><a href="index.php">Home</a> / Contact</p><h1>Contact us</h1><p class="muted" style="margin:4px 0 0">Questions about an order or a product? We reply within 24 hours.</p></div>
  <div class="cgrid">
    <div class="cinfo">
      <div class="card"><b>📧 Email</b><br><span class="muted">support@technest.example</span></div>
      <div class="card"><b>📞 Phone</b><br><span class="muted">+00 000 000 0000</span></div>
      <div class="card"><b>📍 Address</b><br><span class="muted">123 Sample Street, Your City</span></div>
      <div class="card"><b>🕘 Hours</b><br><span class="muted">Mon–Sat, 9am – 6pm</span></div>
    </div>
    <form class="card cform" method="post">
      <?= csrf_field() ?>
      <?php if ($sent): ?><p class="okmsg">✓ Thanks! Your message was sent.</p><?php endif; ?>
      <?php if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
      <div class="fg"><label>Name<input name="name" required value="<?= e($f['name']) ?>"></label><label>Email<input type="email" name="email" required value="<?= e($f['email']) ?>"></label></div>
      <label>Subject<input name="subject" value="<?= e($f['subject']) ?>"></label>
      <label>Message<textarea name="message" rows="5" required><?= e($f['message']) ?></textarea></label>
      <button class="btn lg2">Send message</button>
    </form>
  </div>
</main>
<?php include 'footer.php'; ?>

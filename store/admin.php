<?php
require_once 'data.php';
$error = ''; $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
if (isset($_GET['logout'])) { $_SESSION = []; session_destroy(); header('Location: admin.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (!empty($_SESSION['admin']) && ($_POST['action'] ?? '') === 'status') {
    $s = $_POST['status'] ?? '';
    if (in_array($s, $statuses, true))
      $pdo->prepare("UPDATE orders SET status = ?, payment_status = IF(? = 'Delivered' AND payment_method = 'cod', 'paid', payment_status) WHERE id = ?")->execute([$s, $s, $_POST['id'] ?? '']);
    header('Location: admin.php?ok=Status+updated'); exit;
  }
  $st = $pdo->prepare('SELECT password_hash FROM admins WHERE username = ?');
  $st->execute([$_POST['user'] ?? '']);
  $hash = $st->fetchColumn();
  if ($hash && password_verify($_POST['pass'] ?? '', $hash)) {
    session_regenerate_id(true); $_SESSION['admin'] = true; header('Location: admin.php'); exit;
  }
  $error = 'Wrong username or password.';
}
include 'header.php';
if (empty($_SESSION['admin'])): ?>
<main class="wrap done"><form class="card login" method="post">
  <h2>Admin login</h2>
  <?= csrf_field() ?>
  <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
  <label>Username<input name="user" required autofocus></label>
  <label>Password<input type="password" name="pass" required></label>
  <button class="btn full">Sign in</button>
  <p class="muted" style="text-align:center;margin:14px 0 0"><a href="forgot.php">Forgot password?</a></p>
</form></main>
<?php else:
  $orders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
  $items = [];
  foreach ($pdo->query('SELECT order_id, name, qty FROM order_items')->fetchAll() as $it) $items[$it['order_id']][] = $it;
  $paidRows = array_filter($orders, fn($o) => $o['payment_status'] === 'paid' && $o['status'] !== 'Cancelled');
  $revenue = array_sum(array_column($paidRows, 'total'));
  $open = count(array_filter($orders, fn($o) => in_array($o['status'], ['Pending', 'Processing']) && ($o['payment_status'] === 'paid' || $o['payment_method'] === 'cod'))); ?>
<main class="wrap">
  <?php $tab = 'orders'; include 'admin_nav.php'; ?>
  <?php if (!empty($_GET['ok'])): ?><p class="okmsg">✓ <?= e($_GET['ok']) ?></p><?php endif; ?>
  <div class="stat3">
    <div class="card"><span class="muted">Total orders</span><b><?= count($orders) ?></b></div>
    <div class="card"><span class="muted">Revenue (paid)</span><b><?= money($revenue) ?></b></div>
    <div class="card"><span class="muted">To fulfil</span><b><?= $open ?></b></div>
  </div>
  <?php if (!$orders): ?><p class="muted">No orders yet. Place a test order from the shop.</p>
  <?php else: ?><div class="tablewrap"><table>
    <tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th></tr>
    <?php foreach ($orders as $o): $pay = $o['payment_status'] === 'paid' ? ['Paid', 'paid'] : ($o['payment_method'] === 'stripe' ? ['Awaiting payment', 'wait'] : ['Pay on delivery', 'wait']); ?><tr>
      <td><b><?= e($o['id']) ?></b><br><small class="muted"><?= e(date('d M Y, H:i', strtotime($o['created_at']))) ?></small></td>
      <td><?= e($o['name']) ?><br><small class="muted"><?= e($o['email']) ?><br><?= nl2br(e($o['address'])) ?></small></td>
      <td><?php foreach ($items[$o['id']] ?? [] as $it) echo e($it['qty'].' × '.$it['name']).'<br>'; ?></td>
      <td><b><?= money($o['total']) ?></b></td>
      <td><span class="st <?= $pay[1] ?>"><?= $pay[0] ?></span></td>
      <td><form method="post" class="stform"><?= csrf_field() ?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= e($o['id']) ?>">
        <select name="status" class="s-<?= strtolower($o['status']) ?>"><?php foreach ($statuses as $s): ?><option <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select>
        <button class="btn sm">Save</button></form></td></tr>
    <?php endforeach; ?></table></div><?php endif; ?>
</main>
<?php endif; include 'footer.php'; ?>

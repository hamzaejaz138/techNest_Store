<?php
require_once 'data.php'; require_once 'stripe.php';
$oid = $_GET['order'] ?? ''; $sid = $_GET['session_id'] ?? ''; $paid = false;
$st = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND stripe_session = ?'); $st->execute([$oid, $sid]); $o = $st->fetch();
if ($o) {
  if ($o['payment_status'] === 'paid') $paid = true;
  else try {
    $s = stripe_request('GET', '/checkout/sessions/'.urlencode($sid));
    if (($s['payment_status'] ?? '') === 'paid' && ($s['client_reference_id'] ?? '') === $oid) {
      $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?")->execute([$oid]); $paid = true;
    }
  } catch (Exception $x) {}
}
if ($paid) $_SESSION['cart'] = [];
include 'header.php'; ?>
<main class="wrap done">
<?php if ($paid): ?>
  <h1>Payment received 🎉</h1><p>Thank you, <?= e($o['name']) ?>! Order <b><?= e($o['id']) ?></b> is confirmed. Total paid: <?= money($o['total']) ?>.</p>
<?php else: ?>
  <h1>We couldn't confirm your payment</h1><p>If you were charged, contact us with your order number.</p>
<?php endif; ?>
  <a class="btn" href="index.php">Continue shopping</a></main>
<?php include 'footer.php'; ?>

<?php
require_once 'data.php';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  if (find_product($id)) {
    switch ($_POST['action'] ?? '') {
      case 'add': $n = max(1, min(99, (int)($_POST['qty'] ?? 1))); $_SESSION['cart'][$id] = min(99, ($_SESSION['cart'][$id] ?? 0) + $n); break;
      case 'update':
        $q = max(0, min(99, (int)($_POST['qty'] ?? 1)));
        if ($q === 0) unset($_SESSION['cart'][$id]); else $_SESSION['cart'][$id] = $q; break;
      case 'remove': unset($_SESSION['cart'][$id]); break;
    }
  }
  $ret = preg_replace('/[?&]added=1/', '', (string)($_POST['return'] ?? ''));
  if (($_POST['action'] ?? '') === 'add') {
    $ok = preg_match('#^/[^/\\\\]#', $ret);
    header('Location: ' . ($ok ? $ret . (strpos($ret, '?') !== false ? '&' : '?') . 'added=1' : 'index.php?added=1'));
  } else header('Location: cart.php');
  exit;
}
include 'header.php';
$total = 0; ?>
<main class="wrap">
<h1 class="pt">Your cart</h1>
<?php if (!$_SESSION['cart']): ?>
  <p class="muted">Your cart is empty.</p><a class="btn" href="index.php">Start shopping</a>
<?php else: ?>
<div class="cols">
 <div>
 <?php foreach ($_SESSION['cart'] as $id => $qty): $p = find_product($id); if (!$p) { unset($_SESSION['cart'][$id]); continue; } $sub = $p['price'] * $qty; $total += $sub; ?>
  <div class="card line">
    <span class="pic sm"><?= pic($p) ?></span>
    <div class="grow"><b><?= e($p['name']) ?></b><br><span class="muted"><?= money($p['price']) ?> each</span></div>
    <form method="post"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $id ?>">
      <input class="qty" type="number" name="qty" value="<?= $qty ?>" min="0" max="99" onchange="this.form.submit()"></form>
    <b><?= money($sub) ?></b>
    <form method="post"><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?= $id ?>"><button class="link">Remove</button></form>
  </div>
 <?php endforeach; ?>
 </div>
 <form class="card" method="post" action="checkout.php">
   <?= csrf_field() ?>
   <h3>Checkout</h3>
   <?php $ship = shipping($total); $left = max(0, FREE_SHIP - $total); ?>
   <div class="ship"><div class="meter"><i style="width:<?= min(100, $total / FREE_SHIP * 100) ?>%"></i></div>
   <small><?= $left > 0 ? 'Add '.money($left).' more for free shipping' : '🎉 You get free shipping!' ?></small></div>
   <p class="sum"><span>Subtotal</span><b><?= money($total) ?></b></p>
   <p class="sum"><span>Shipping</span><b><?= $ship ? money($ship) : 'Free' ?></b></p>
   <p class="sum big"><span>Total</span><b><?= money($total + $ship) ?></b></p>
   <label>Full name<input name="name" required></label>
   <label>Email<input type="email" name="email" required></label>
   <label>Address<textarea name="address" rows="3" required></textarea></label>
   <button class="btn full">Place order</button>
 </form>
</div>
<?php endif; ?></main>
<?php include 'footer.php'; ?>

<?php
require_once 'data.php'; require_once 'stripe.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) { header('Location: cart.php'); exit; }
csrf_check();
$name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $address = trim($_POST['address'] ?? '');
$error = ''; $paidOnline = stripe_enabled();
if ($name === '' || $address === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please fill in all fields with a valid email.';
if (!$error) {
  $subtotal = 0; $lines = [];
  foreach ($_SESSION['cart'] as $id => $qty) {
    if ($p = find_product($id)) { $lines[] = [$p, (int)$qty]; $subtotal += $p['price'] * $qty; }
  }
  if (!$lines) { header('Location: cart.php'); exit; }
  $ship = shipping($subtotal); $total = $subtotal + $ship;
  $orderId = 'ORD-'.strtoupper(bin2hex(random_bytes(3)));
  try {
    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO orders (id,name,email,address,shipping,total,payment_method) VALUES (?,?,?,?,?,?,?)')
        ->execute([$orderId, $name, $email, $address, $ship, $total, $paidOnline ? 'stripe' : 'cod']);
    $ins = $pdo->prepare('INSERT INTO order_items (order_id,product_id,name,qty,price) VALUES (?,?,?,?,?)');
    foreach ($lines as [$p, $qty]) $ins->execute([$orderId, $p['id'], $p['name'], $qty, $p['price']]);
    $pdo->commit();
  } catch (Exception $x) { $pdo->rollBack(); $error = 'We could not save your order. Please try again.'; }
}
if (!$error && $paidOnline) {
  try {
    $params = ['mode'=>'payment', 'customer_email'=>$email, 'client_reference_id'=>$orderId,
      'success_url'=>SITE_URL.'/success.php?order='.$orderId.'&session_id={CHECKOUT_SESSION_ID}', 'cancel_url'=>SITE_URL.'/cart.php'];
    $i = 0;
    foreach ($lines as [$p, $qty]) {
      $params['line_items'][$i++] = ['quantity'=>$qty, 'price_data'=>['currency'=>CURRENCY, 'unit_amount'=>(int)round($p['price'] * 100), 'product_data'=>['name'=>$p['name']]]];
    }
    if ($ship > 0) $params['line_items'][$i] = ['quantity'=>1, 'price_data'=>['currency'=>CURRENCY, 'unit_amount'=>(int)round($ship * 100), 'product_data'=>['name'=>'Shipping']]];
    $s = stripe_request('POST', '/checkout/sessions', $params);
    $pdo->prepare('UPDATE orders SET stripe_session = ? WHERE id = ?')->execute([$s['id'], $orderId]);
    header('Location: '.$s['url']); exit;
  } catch (Exception $x) {
    $pdo->prepare('DELETE FROM orders WHERE id = ?')->execute([$orderId]);
    $error = 'Payment could not be started. Please try again.';
  }
}
if (!$error) $_SESSION['cart'] = [];
include 'header.php'; ?>
<main class="wrap done">
<?php if ($error): ?>
  <h1>Something went wrong</h1><p><?= e($error) ?></p><a class="btn" href="cart.php">Back to cart</a>
<?php else: ?>
  <h1>Thank you, <?= e($name) ?>!</h1>
  <p>Order <b><?= e($orderId) ?></b> was placed. Total: <?= money($total) ?>. You'll pay when it arrives.</p>
  <a class="btn" href="index.php">Continue shopping</a>
<?php endif; ?></main>
<?php include 'footer.php'; ?>

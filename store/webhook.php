<?php
// Stripe webhook: marks orders paid even if the customer closes the browser early.
require_once 'data.php';
$payload = file_get_contents('php://input'); $sig = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
if (STRIPE_WEBHOOK_SECRET === '') { http_response_code(503); exit('Webhook secret not set'); }
$parts = []; foreach (explode(',', $sig) as $kv) { [$k, $v] = array_pad(explode('=', $kv, 2), 2, ''); $parts[$k][] = $v; }
$t = $parts['t'][0] ?? ''; $expected = hash_hmac('sha256', $t.'.'.$payload, STRIPE_WEBHOOK_SECRET);
$valid = $t !== '' && abs(time() - (int)$t) < 300 && array_filter($parts['v1'] ?? [], fn($v) => hash_equals($expected, $v));
if (!$valid) { http_response_code(400); exit('Bad signature'); }
$ev = json_decode($payload, true);
if (($ev['type'] ?? '') === 'checkout.session.completed') {
  $s = $ev['data']['object'] ?? [];
  if (($s['payment_status'] ?? '') === 'paid' && !empty($s['client_reference_id']))
    $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?")->execute([$s['client_reference_id']]);
}
http_response_code(200); echo 'ok';

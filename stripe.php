<?php
// Tiny Stripe REST helper (no Composer needed). Requires the PHP cURL extension.
function stripe_enabled(){ return STRIPE_SECRET_KEY !== ''; }
function stripe_request($method, $path, $params = []) {
  $ch = curl_init('https://api.stripe.com/v1'.$path);
  curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_USERPWD=>STRIPE_SECRET_KEY.':', CURLOPT_TIMEOUT=>20]);
  if ($method === 'POST') { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); }
  $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
  $data = json_decode((string)$res, true);
  if ($res === false || $code >= 400 || !is_array($data)) throw new Exception($data['error']['message'] ?? 'Stripe request failed');
  return $data;
}

<?php
// Never cache pages: a cached form would carry an old security token.
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
// Make sure PHP can save sessions (some local setups have an unwritable session folder).
$sp = session_save_path();
if ($sp === '' || !is_dir($sp) || !is_writable($sp)) {
  $fallback = __DIR__.'/sessions';
  if (!is_dir($fallback)) @mkdir($fallback, 0777, true);
  if (is_dir($fallback) && is_writable($fallback)) session_save_path($fallback);
}
session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();
require_once __DIR__.'/config.php';
try {
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch (PDOException $x) {
  http_response_code(500);
  exit('Database not ready. Check config.php, then open <a href="setup.php">setup.php</a> once.');
}
$products = $pdo->query('SELECT id,name,price,old_price AS old,cat,emoji,image,rating,reviews,badge,bg,description AS `desc` FROM products ORDER BY id')->fetchAll();
const FREE_SHIP = 75;
function find_product($id){ global $products; foreach($products as $p) if($p['id']==$id) return $p; return null; }
function cart_count(){ return array_sum($_SESSION['cart'] ?? []); }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n){ return '$'.number_format($n,2); }
function shipping($sub){ return ($sub <= 0 || $sub >= FREE_SHIP) ? 0 : 5; }
function stars($r){ $f=(int)round($r); return str_repeat('★',$f).str_repeat('☆',5-$f); }

function csrf_token(){ if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field(){ return '<input type="hidden" name="csrf" value="'.csrf_token().'">'; }
function csrf_check(){
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><body style="font:17px/1.6 system-ui,sans-serif;max-width:520px;margin:12vh auto;padding:0 20px">'
      .'<h2>Your session expired</h2><p>The page was open too long or your browser did not keep its session cookie. '
      .'<a href="'.htmlspecialchars(strtok($_SERVER['REQUEST_URI'], '?'), ENT_QUOTES).'">Reload the page</a> and try again.</p></body>');
  }
}
function pic($p){ return !empty($p['image']) ? '<img src="uploads/'.e($p['image']).'" alt="'.e($p['name']).'" loading="lazy">' : e($p['emoji']); }

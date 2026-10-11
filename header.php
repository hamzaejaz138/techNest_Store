<?php require_once 'data.php';
$cur = basename($_SERVER['SCRIPT_NAME']);
$navCats = array_values(array_unique(array_column($products, 'cat'))); ?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>TechNest – Smart gadgets, delivered fast</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head><body>
<div class="promo">🚚 Free shipping on orders over <?= money(FREE_SHIP) ?></div>
<div class="wrap nbar-wrap"><header class="nbar">
  <a class="logo" href="index.php"><span class="lmark">◆</span> TechNest</a>
  <nav class="nlinks" aria-label="Main">
    <a href="index.php" class="<?= $cur === 'index.php' ? 'on' : '' ?>">Home</a>
    <a href="shop.php" class="<?= $cur === 'shop.php' ? 'on' : '' ?>">Shop</a>
    <div class="dd"><a href="index.php#categories">Categories ▾</a>
      <div class="ddm"><a href="shop.php">All products</a><?php foreach ($navCats as $c): ?><a href="shop.php?cat=<?= urlencode($c) ?>"><?= e($c) ?></a><?php endforeach; ?></div></div>
    <a href="contact.php" class="<?= $cur === 'contact.php' ? 'on' : '' ?>">Contact</a>
  </nav>
  <form class="nsearch" action="shop.php" method="get">
    <input name="q" type="search" placeholder="Search products..." aria-label="Search products" value="<?= e($_GET['q'] ?? '') ?>">
    <button aria-label="Search"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
  </form>
  <a class="iconlink" href="cart.php" aria-label="Cart"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8h12l-1 12H7L6 8Z"/><path d="M9 8a3 3 0 0 1 6 0"/></svg><span class="badge"><?= cart_count() ?></span></a>
  <a class="iconlink" href="admin.php" aria-label="Admin login" title="Admin"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></a>
</header></div>

<?php include 'header.php'; require_once 'card.php';
$byReviews = $products; usort($byReviews, fn($a, $b) => $b['reviews'] <=> $a['reviews']);
$best = array_slice($byReviews, 0, 4); $hero = $best[0] ?? null; $bestIds = array_column($best, 'id');
$limited = array_values(array_filter($products, fn($p) => !in_array($p['id'], $bestIds)));
if (count($limited) < 4) $limited = $products;
$limited = array_slice($limited, 0, 8);
$offer = null; $top = 0;
foreach ($products as $p) if ((float)$p['old'] > (float)$p['price']) { $d = 1 - $p['price'] / $p['old']; if ($d > $top) { $top = $d; $offer = $p; } }
if (!$offer) $offer = $best[1] ?? $hero;
$cats = [];
foreach ($products as $p) { $cats[$p['cat']]['n'] = ($cats[$p['cat']]['n'] ?? 0) + 1; $cats[$p['cat']]['icon'] = $cats[$p['cat']]['icon'] ?? $p['emoji']; }
$avg = $products ? round(array_sum(array_column($products, 'rating')) / count($products), 1) : 0;
$end = strtotime('last day of this month 23:59:59'); ?>
<main class="wrap">
<?php if (!$products): ?><p class="muted" style="padding:60px 0">No products yet. Add some in the admin panel.</p><?php else: ?>

<section class="hero2"><div class="h2grid">
  <div>
    <span class="label">⭐ Best seller</span>
    <h1><?= e($hero['name']) ?></h1>
    <p><?= e($hero['desc']) ?></p>
    <div class="herocta"><a class="btn lg2" href="product.php?id=<?= (int)$hero['id'] ?>">Shop now →</a>
      <div class="heroavg"><b>★ <?= $avg ?></b><span>Average customer rating</span></div></div>
  </div>
  <div class="h2art"><div class="pricebubble"><small>Only</small><b><?= money($hero['price']) ?></b></div><?= pic($hero) ?></div>
</div></section>

<section>
  <div class="sechead"><div><span class="eyebrow"><i></i>Our Collection</span><h2>Explore Our Best Sellers</h2></div>
    <a class="btn out" href="shop.php">View All Products →</a></div>
  <div class="grid4"><?php foreach ($best as $p) echo product_card($p); ?></div>
</section>

<section id="categories">
  <div class="sechead"><div><span class="eyebrow"><i></i>Shop by Category</span><h2>Browse by Category</h2></div></div>
  <div class="cats"><?php foreach ($cats as $name => $c): ?>
    <a class="cat" href="shop.php?cat=<?= urlencode($name) ?>"><span class="ci"><?= e($c['icon']) ?></span><b><?= e($name) ?></b><small><?= $c['n'] ?> item<?= $c['n'] > 1 ? 's' : '' ?></small></a>
  <?php endforeach; ?></div>
</section>

<section class="offer">
  <div>
    <span class="eyebrow"><i></i>Special Offer</span>
    <h2><?= $top ? 'Save '.round($top * 100).'% on '.e($offer['name']) : e($offer['name']) ?></h2>
    <p class="muted"><?= e($offer['desc']) ?></p>
    <div class="cd" id="cd" data-end="<?= $end * 1000 ?>">
      <div><b data-d>00</b><span>Days</span></div><div><b data-h>00</b><span>Hours</span></div><div><b data-m>00</b><span>Mins</span></div><div><b data-s>00</b><span>Secs</span></div>
    </div>
    <p class="price offerprice"><b><?= money($offer['price']) ?></b><?php if ((float)$offer['old'] > 0): ?><s><?= money($offer['old']) ?></s><?php endif; ?></p>
    <a class="btn lg2" href="product.php?id=<?= (int)$offer['id'] ?>">Check it out →</a>
  </div>
  <div class="offerart"><?= pic($offer) ?></div>
</section>

<section>
  <div class="sechead"><div><span class="eyebrow"><i></i>Just Landed</span><h2>Limited Products</h2></div>
    <a class="btn out" href="shop.php">View All Products →</a></div>
  <div class="grid4"><?php foreach ($limited as $p) echo product_card($p); ?></div>
  <p class="center"><a class="btn lg2" href="shop.php">View All Products →</a></p>
</section>

<div class="trust2">
  <div><span>🚚</span><b>Free Shipping</b><small>On orders over <?= money(FREE_SHIP) ?></small></div>
  <div><span>↩️</span><b>30-Day Returns</b><small>Money back guarantee</small></div>
  <div><span>🔒</span><b>Secure Payment</b><small>Safe, encrypted checkout</small></div>
  <div><span>💬</span><b>24/7 Support</b><small>Friendly customer support</small></div>
</div>
<?php endif; ?>
</main>
<?php include 'footer.php'; ?>

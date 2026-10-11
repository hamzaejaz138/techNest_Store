<?php include 'header.php';
$p = find_product((int)($_GET['id'] ?? 0));
if (!$p): ?><main class="wrap done"><h1>Product not found</h1><a class="btn" href="index.php">Back to shop</a></main>
<?php else: ?>
<main class="wrap">
  <p class="crumb"><a href="index.php">Shop</a> / <?= e($p['cat']) ?> / <?= e($p['name']) ?></p>
  <div class="detail">
    <div class="bigpic" style="background:<?= $p['bg'] ?>"><?= pic($p) ?></div>
    <form method="post" action="cart.php" class="dinfo">
      <h1><?= e($p['name']) ?></h1>
      <p class="stars"><?= stars($p['rating']) ?> <small><?= $p['rating'] ?> from <?= $p['reviews'] ?> reviews</small></p>
      <p class="price big"><b><?= money($p['price']) ?></b><?php if($p['old']): ?><s><?= money($p['old']) ?></s><?php endif; ?></p>
      <p><?= e($p['desc']) ?></p>
      <ul class="ticks"><li>Free shipping over <?= money(FREE_SHIP) ?></li><li>30-day easy returns</li><li>1-year warranty</li></ul>
      <input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= $p['id'] ?>">
      <label>Quantity <input class="qty" type="number" name="qty" value="1" min="1" max="99"></label>
      <button class="btn lg full">Add to cart</button>
    </form>
  </div>
  <h2 class="rel">You may also like</h2>
  <div class="grid">
  <?php $n=0; foreach($products as $o){ if($o['id']==$p['id'] || $o['cat']!=$p['cat'] && $n>=2) continue; if($n++>=4) break; ?>
    <a class="card product" href="product.php?id=<?= $o['id'] ?>"><div class="pic" style="background:<?= $o['bg'] ?>"><?= pic($o) ?></div><h3><?= e($o['name']) ?></h3><b><?= money($o['price']) ?></b></a>
  <?php } ?>
  </div>
</main>
<?php endif; include 'footer.php'; ?>

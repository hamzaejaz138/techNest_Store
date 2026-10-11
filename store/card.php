<?php
// Reusable product card used on the home page and shop page.
function product_card($p) {
  $disc = ((float)$p['old'] > (float)$p['price'] && (float)$p['old'] > 0) ? round((1 - $p['price'] / $p['old']) * 100) : 0;
  $tag = $disc ? $disc.'% OFF' : ($p['badge'] ?: '');
  $url = 'product.php?id='.(int)$p['id'];
  ob_start(); ?>
  <article class="pcard">
    <a class="pimg" href="<?= $url ?>" style="background:<?= e($p['bg']) ?>" aria-label="<?= e($p['name']) ?>"><?= pic($p) ?>
      <?php if ($tag): ?><span class="pbadge"><?= e($tag) ?></span><?php endif; ?></a>
    <h3><a href="<?= $url ?>"><?= e($p['name']) ?></a></h3>
    <p class="stars"><?= stars($p['rating']) ?> <small>(<?= (int)$p['reviews'] ?>)</small></p>
    <form class="pbottom" method="post" action="cart.php">
      <div class="price"><b><?= money($p['price']) ?></b><?php if ((float)$p['old'] > 0): ?><s><?= money($p['old']) ?></s><?php endif; ?></div>
      <input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <input type="hidden" name="return" value="<?= e($_SERVER['REQUEST_URI'] ?? 'index.php') ?>">
      <button class="cartbtn" aria-label="Add <?= e($p['name']) ?> to cart"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8h12l-1 12H7L6 8Z"/><path d="M9 8a3 3 0 0 1 6 0"/></svg></button>
    </form>
  </article>
<?php return ob_get_clean(); }

<?php require_once 'data.php'; require_once 'card.php';
$cat = $_GET['cat'] ?? ''; $q = trim($_GET['q'] ?? ''); $sort = $_GET['sort'] ?? '';
$counts = []; foreach ($products as $p) $counts[$p['cat']] = ($counts[$p['cat']] ?? 0) + 1;
if ($cat !== '' && !isset($counts[$cat])) $cat = '';
$list = array_values(array_filter($products, fn($p) => ($cat === '' || $p['cat'] === $cat) && ($q === '' || stripos($p['name'].' '.$p['cat'], $q) !== false)));
if ($sort === 'low') usort($list, fn($a, $b) => $a['price'] <=> $b['price']);
if ($sort === 'high') usort($list, fn($a, $b) => $b['price'] <=> $a['price']);
if ($sort === 'rate') usort($list, fn($a, $b) => $b['rating'] <=> $a['rating']);
if ($sort === 'new') usort($list, fn($a, $b) => $b['id'] <=> $a['id']);
include 'header.php'; ?>
<main class="wrap">
  <div class="shophero"><p class="crumb" style="margin:0 0 6px"><a href="index.php">Home</a> / Shop<?= $cat ? ' / '.e($cat) : '' ?></p>
    <h1><?= $cat ? e($cat) : 'All Products' ?></h1><p class="muted" style="margin:4px 0 0"><?= count($list) ?> product<?= count($list) === 1 ? '' : 's' ?><?= $q !== '' ? ' for "'.e($q).'"' : '' ?></p></div>
  <div class="sechead" style="margin-top:26px">
    <div class="chips"><a class="chip <?= $cat === '' ? 'active' : '' ?>" href="shop.php">All <small>(<?= count($products) ?>)</small></a>
      <?php foreach ($counts as $name => $n): ?><a class="chip <?= $cat === $name ? 'active' : '' ?>" href="shop.php?cat=<?= urlencode($name) ?>"><?= e($name) ?> <small>(<?= $n ?>)</small></a><?php endforeach; ?></div>
    <a class="btn out" href="shop.php">View All Products →</a>
  </div>
  <form class="sortbar" method="get">
    <input type="hidden" name="cat" value="<?= e($cat) ?>"><input type="hidden" name="q" value="<?= e($q) ?>">
    <label class="inl">Sort by <select name="sort" onchange="this.form.submit()">
      <?php foreach (['' => 'Featured', 'new' => 'Newest', 'low' => 'Price: low to high', 'high' => 'Price: high to low', 'rate' => 'Top rated'] as $k => $v): ?><option value="<?= $k ?>" <?= $sort === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></label>
  </form>
  <?php if (!$list): ?><p class="muted" style="padding:30px 0">No products found. <a href="shop.php" style="color:var(--brand)">View all products</a></p>
  <?php else: ?><div class="grid4"><?php foreach ($list as $p) echo product_card($p); ?></div><?php endif; ?>
</main>
<?php include 'footer.php'; ?>

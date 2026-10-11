<footer class="foot"><div class="wrap fgrid">
  <div><div class="logo"><span class="lmark">◆</span> TechNest</div><p>Smart gadgets at fair prices.</p></div>
  <div><b>Shop</b><br><?php foreach (array_slice($navCats ?? [], 0, 4) as $c): ?><a href="shop.php?cat=<?= urlencode($c) ?>"><?= e($c) ?></a><br><?php endforeach; ?></div>
  <div><b>Help</b><br><a href="shop.php">All products</a><br><a href="cart.php">Cart</a><br><a href="contact.php">Contact</a></div>
  <div class="small">Sample project built with PHP, MySQL, HTML, CSS and JavaScript.</div>
</div></footer>
<script src="script.js"></script>
</body></html>

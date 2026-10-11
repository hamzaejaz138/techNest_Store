<?php
require_once 'data.php';
if (empty($_SESSION['admin'])) { header('Location: admin.php'); exit; }
$err = ''; $edit = null; $badges = ['', 'Sale', 'New', 'Best seller'];
$dir = __DIR__.'/uploads';
function remove_image($dir, $name) { if ($name && basename($name) === $name) @unlink($dir.'/'.$name); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (($_POST['action'] ?? '') === 'delete') {
    $did = (int)($_POST['id'] ?? 0);
    $q = $pdo->prepare('SELECT image FROM products WHERE id = ?'); $q->execute([$did]); remove_image($dir, $q->fetchColumn());
    $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$did]);
    header('Location: admin_products.php?ok=Product+deleted'); exit;
  }
  $id = (int)($_POST['id'] ?? 0);
  $f = ['name'=>trim($_POST['name'] ?? ''), 'price'=>$_POST['price'] ?? '', 'old_price'=>$_POST['old_price'] ?: '0',
        'cat'=>trim($_POST['cat'] ?? ''), 'emoji'=>trim($_POST['emoji'] ?? ''), 'badge'=>$_POST['badge'] ?? '',
        'bg'=>$_POST['bg'] ?? '', 'description'=>trim($_POST['description'] ?? '')];
  if ($f['name'] === '' || $f['cat'] === '') $err = 'Name and category are required.';
  elseif (!is_numeric($f['price']) || $f['price'] < 0 || !is_numeric($f['old_price']) || $f['old_price'] < 0) $err = 'Enter valid prices.';
  if (!in_array($f['badge'], $badges, true)) $f['badge'] = '';
  if (!preg_match('/^#[0-9a-fA-F]{6}$/', $f['bg'])) $f['bg'] = '#EEF2F7';

  $old = null;
  if ($id) { $q = $pdo->prepare('SELECT image FROM products WHERE id = ?'); $q->execute([$id]); $old = $q->fetchColumn() ?: null; }
  $img = $old;
  if (!$err && !empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) $err = 'Image upload failed. Try a smaller file.';
    elseif ($file['size'] > 2 * 1024 * 1024) $err = 'Image must be under 2 MB.';
    else {
      $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
      $ext = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'][$mime] ?? null;
      if (!$ext || !getimagesize($file['tmp_name'])) $err = 'Only JPG, PNG or WebP images are allowed.';
      else {
        $name = bin2hex(random_bytes(8)).'.'.$ext;
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $dir.'/'.$name)) $img = $name; else $err = 'Could not save the image.';
      }
    }
  } elseif (!$err && !empty($_POST['remove_image'])) $img = null;

  if (!$err) {
    $vals = array_values($f); $vals[] = $img;
    if ($id) { $vals[] = $id; $pdo->prepare('UPDATE products SET name=?,price=?,old_price=?,cat=?,emoji=?,badge=?,bg=?,description=?,image=? WHERE id=?')->execute($vals); }
    else { $pdo->prepare('INSERT INTO products (name,price,old_price,cat,emoji,badge,bg,description,image) VALUES (?,?,?,?,?,?,?,?,?)')->execute($vals); }
    if ($old && $old !== $img) remove_image($dir, $old);
    header('Location: admin_products.php?ok=Product+saved'); exit;
  }
  $edit = $f + ['id'=>$id, 'image'=>$old];
}
if (!$edit && isset($_GET['edit'])) {
  $st = $pdo->prepare('SELECT * FROM products WHERE id = ?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null;
}
$rows = $pdo->query('SELECT * FROM products ORDER BY id')->fetchAll();
include 'header.php'; $tab = 'products'; ?>
<main class="wrap">
  <?php include 'admin_nav.php'; ?>
  <?php if (!empty($_GET['ok'])): ?><p class="okmsg">✓ <?= e($_GET['ok']) ?></p><?php endif; ?>
  <form class="card pform" method="post" enctype="multipart/form-data" id="top">
    <h3><?= $edit && $edit['id'] ? 'Edit product' : 'Add a product' ?></h3>
    <?php if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
    <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
    <div class="fg">
      <label>Name<input name="name" required value="<?= e($edit['name'] ?? '') ?>"></label>
      <label>Category<input name="cat" required value="<?= e($edit['cat'] ?? '') ?>"></label>
      <label>Price ($)<input name="price" type="number" step="0.01" min="0" required value="<?= e($edit['price'] ?? '') ?>"></label>
      <label>Old price ($, optional)<input name="old_price" type="number" step="0.01" min="0" value="<?= e(($edit['old_price'] ?? 0) ?: '') ?>"></label>
      <label>Emoji (used if no photo)<input name="emoji" maxlength="8" value="<?= e($edit['emoji'] ?? '📦') ?>"></label>
      <label>Badge<select name="badge"><?php foreach ($badges as $b): ?><option value="<?= e($b) ?>" <?= ($edit['badge'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?: 'None' ?></option><?php endforeach; ?></select></label>
      <label>Card colour<input name="bg" type="color" value="<?= e($edit['bg'] ?? '#EEF2F7') ?>"></label>
      <label>Photo (JPG, PNG, WebP, max 2 MB)<input name="image" type="file" accept="image/jpeg,image/png,image/webp"></label>
    </div>
    <?php if (!empty($edit['image'])): ?><p class="curimg"><img src="uploads/<?= e($edit['image']) ?>" alt=""> <label class="inl"><input type="checkbox" name="remove_image" value="1"> Remove current photo</label></p><?php endif; ?>
    <label>Description<textarea name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></label>
    <button class="btn"><?= $edit && $edit['id'] ? 'Save changes' : 'Add product' ?></button>
    <?php if ($edit): ?> <a class="link" href="admin_products.php">Cancel</a><?php endif; ?>
  </form>
  <div class="tablewrap"><table>
    <tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr>
    <?php foreach ($rows as $r): ?><tr>
      <td class="thumb"><?= pic($r) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['cat']) ?></td><td><?= money($r['price']) ?></td>
      <td class="acts"><a href="?edit=<?= $r['id'] ?>#top">Edit</a>
        <form method="post" onsubmit="return confirm('Delete this product?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="link">Delete</button></form></td>
    </tr><?php endforeach; ?>
  </table></div>
</main>
<?php include 'footer.php'; ?>

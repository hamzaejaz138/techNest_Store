<?php
// Run once in the browser, then DELETE this file.
require __DIR__.'/config.php';
try {
  $pdo = new PDO('mysql:host='.DB_HOST.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.DB_NAME.'` CHARACTER SET utf8mb4');
  $pdo->exec('USE `'.DB_NAME.'`');
  foreach (array_filter(array_map('trim', explode(";\n", file_get_contents(__DIR__.'/schema.sql')))) as $sql) $pdo->exec($sql);
  foreach (array_map(fn($c) => "ALTER TABLE orders ADD COLUMN $c", ["status VARCHAR(20) NOT NULL DEFAULT 'Pending'", "payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'", "payment_method VARCHAR(20) NOT NULL DEFAULT 'cod'", 'stripe_session VARCHAR(120) DEFAULT NULL']) as $q) { try { $pdo->exec($q); } catch (PDOException $e) {} }
  try { $pdo->exec('ALTER TABLE products ADD COLUMN image VARCHAR(255) DEFAULT NULL'); } catch (PDOException $e) {} // already exists
  try { $pdo->exec('ALTER TABLE admins ADD COLUMN email VARCHAR(160) DEFAULT NULL'); } catch (PDOException $e) {} // already exists
  if (!$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn()) {
    $pdo->prepare('INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)')
        ->execute(['admin', ADMIN_EMAIL, password_hash('change-me-123', PASSWORD_DEFAULT)]);
  }
  $pdo->prepare("UPDATE admins SET email = ? WHERE username = 'admin' AND (email IS NULL OR email = '')")->execute([ADMIN_EMAIL]);
  echo '<h2>Setup complete ✅</h2><p>Tables created and products added. Admin login: <b>admin</b> / <b>change-me-123</b>.</p>
        <p>Now <b>delete setup.php</b> and open <a href="index.php">the store</a>.</p>';
} catch (PDOException $e) {
  echo '<h2>Setup failed</h2><p>'.htmlspecialchars($e->getMessage()).'</p><p>Check that MySQL is running and config.php is correct.</p>';
}

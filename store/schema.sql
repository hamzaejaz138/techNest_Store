CREATE TABLE IF NOT EXISTS products (
  id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(120) NOT NULL, price DECIMAL(8,2) NOT NULL,
  old_price DECIMAL(8,2) NOT NULL DEFAULT 0, cat VARCHAR(60) NOT NULL, emoji VARCHAR(16), image VARCHAR(255) DEFAULT NULL,
  rating DECIMAL(2,1) DEFAULT 4.5, reviews INT DEFAULT 0, badge VARCHAR(30) DEFAULT '',
  bg VARCHAR(9) DEFAULT '#EEF2F7', description TEXT
);
CREATE TABLE IF NOT EXISTS orders (
  id VARCHAR(30) PRIMARY KEY, created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  name VARCHAR(120) NOT NULL, email VARCHAR(160) NOT NULL, address TEXT NOT NULL,
  shipping DECIMAL(8,2) NOT NULL, total DECIMAL(8,2) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Pending', payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid',
  payment_method VARCHAR(20) NOT NULL DEFAULT 'cod', stripe_session VARCHAR(120) DEFAULT NULL
);
CREATE TABLE IF NOT EXISTS order_items (
  id INT PRIMARY KEY AUTO_INCREMENT, order_id VARCHAR(30) NOT NULL, product_id INT NOT NULL,
  name VARCHAR(120) NOT NULL, qty INT NOT NULL, price DECIMAL(8,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS admins (
  id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(60) UNIQUE NOT NULL, email VARCHAR(160) DEFAULT NULL, password_hash VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS password_resets (
  id INT PRIMARY KEY AUTO_INCREMENT, admin_id INT NOT NULL, token_hash CHAR(64) NOT NULL, expires_at DATETIME NOT NULL,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);
INSERT IGNORE INTO products (id,name,price,old_price,cat,emoji,rating,reviews,badge,bg,description) VALUES
(1,'Wireless Earbuds',29.99,49.99,'Audio','🎧',4.7,312,'Sale','#DDF4EE','Crisp sound, deep bass and up to 24 hours of battery with the charging case.'),
(2,'Smart Watch',59.00,0,'Wearables','⌚',4.8,528,'Best seller','#FFEBD0','Tracks steps, sleep and heart rate. Water resistant with a 7-day battery.'),
(3,'Laptop Backpack',39.50,0,'Bags','🎒',4.5,140,'','#E3E9FF','Padded laptop sleeve, hidden anti-theft pocket and a water-resistant shell.'),
(4,'Bluetooth Speaker',34.99,44.99,'Audio','🔊',4.6,207,'Sale','#FDE3E3','Loud, clear 360° sound. 12 hours of playtime and a splash-proof body.'),
(5,'Mechanical Keyboard',49.00,0,'Accessories','⌨️',4.9,391,'New','#EBE3FF','Hot-swappable switches, satisfying clicks and soft backlighting.'),
(6,'Gaming Mouse',24.99,0,'Accessories','🖱️',4.4,96,'','#DDF4EE','Adjustable DPI, six programmable buttons and a comfortable grip.'),
(7,'Phone Stand',9.99,14.99,'Accessories','📱',4.3,64,'Sale','#FFEBD0','Sturdy, foldable stand that fits every phone and small tablet.'),
(8,'Travel Duffel',44.00,0,'Bags','👜',4.6,118,'New','#E3E9FF','Roomy weekend bag with a shoe pocket and a padded shoulder strap.');

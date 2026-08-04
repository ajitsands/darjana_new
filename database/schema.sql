-- Database Schema & Seed Data for Dar Jana Fashion

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    image VARCHAR(255) DEFAULT '',
    description TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    category_id INTEGER NOT NULL,
    product_code VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) DEFAULT NULL,
    image VARCHAR(500) NOT NULL,
    secondary_image VARCHAR(500) DEFAULT '',
    description TEXT,
    sizes VARCHAR(255) DEFAULT '52,54,56,58,60',
    is_featured TINYINT(1) DEFAULT 0,
    stock INTEGER DEFAULT 50,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(100) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    size VARCHAR(20) DEFAULT '54',
    price DECIMAL(10, 2) NOT NULL,
    quantity INTEGER NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE IF NOT EXISTS subscribers (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seed Categories
INSERT IGNORE INTO categories (id, name, slug, image, description) VALUES
(1, 'All Abaya & Dresses', 'all-abaya', 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&auto=format&fit=crop&q=80', 'Explore our full luxury dress & abaya collection'),
(2, 'Black Abaya', 'black-abaya', 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=600&auto=format&fit=crop&q=80', 'Timeless elegance in dark crepe and linen'),
(3, 'Colourful Abaya', 'colorful-abayas', 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80', 'Vibrant shades and handcrafted luxury embroidery'),
(4, 'Matching Sets', 'set', 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&auto=format&fit=crop&q=80', 'Sophisticated two-piece dress and inner sets'),
(5, 'Blazers & Capes', 'blazer', 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=600&auto=format&fit=crop&q=80', 'Modern tailored blazer dresses and outer coats'),
(6, 'Inner Wear', 'inner', 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=600&auto=format&fit=crop&q=80', 'Comfortable satin and silk slip dresses'),
(7, 'Ramadan Collection', 'ramadan-collection', 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=600&auto=format&fit=crop&q=80', 'Exclusive festive couture for special occasions');

-- Seed Products
INSERT IGNORE INTO products (id, category_id, product_code, name, slug, price, sale_price, image, secondary_image, description, sizes, is_featured, stock) VALUES
(1, 2, 'C:6643', 'Black Crepe Blazer Abaya with Beige Striped Linen', 'black-crepe-blazer-abaya-c6643', 38.00, 32.00, 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80', 'Premium black Japanese crepe fabric styled with bespoke beige linen lapels. Soft, lightweight, and tailored for refined everyday luxury.', '52,54,56,58', 1, 35),
(2, 2, 'C:6793', 'Black Crepe Abaya with Gold Thread Embroidery', 'black-crepe-abaya-embroidery-c6793', 45.00, NULL, 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&auto=format&fit=crop&q=80', 'Exquisite black crepe abaya ornamented with intricate gold thread sleeve detailing. Includes matching chiffon scarf.', '54,56,58,60', 1, 28),
(3, 3, 'C:6794', 'Flesh-Colored Linen Blazer Abaya with Embroidery', 'flesh-linen-blazer-abaya-c6794', 42.00, 36.00, 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80', 'Soft nude linen fabric with fine geometric embroidery along the sleeves and back. Elegant button-down front design.', '52,54,56,58', 1, 20),
(4, 5, 'C:6813', 'Beige Natural Linen Blazer Dress Abaya', 'beige-linen-blazer-c6813', 39.00, NULL, 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80', 'Tailored linen jacket dress with structure shoulder pads and minimalist horn buttons. Perfect for work and evening gatherings.', '52,54,56', 1, 15),
(5, 3, 'C:6814', 'Royal Navy Blue Silk Crepe Abaya Dress', 'navy-blue-crepe-abaya-c6814', 48.00, 40.00, 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=800&auto=format&fit=crop&q=80', 'Flowing navy blue silk blend crepe abaya featuring pleated cuff details and a fluid silhouette.', '54,56,58,60', 1, 40),
(6, 4, 'C:6683', 'Linen Abaya in Rich Brown with Lace Embroidery', 'brown-linen-abaya-c6683', 37.00, NULL, 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&auto=format&fit=crop&q=80', 'Earth-toned chocolate brown linen design with handcrafted floral lace accents. Breathable and comfortable.', '52,54,56,58', 1, 18),
(7, 6, 'C:1012', 'Premium Satin Silk Inner Dress in Ivory Cream', 'ivory-satin-inner-dress-c1012', 18.00, 15.00, 'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80', 'Sleeveless full-length A-line inner slip dress made from ultra-smooth ivory silk satin.', '52,54,56,58,60', 0, 50),
(8, 7, 'C:9080', 'Ramadan Edition Embroidered Kaftan Dress', 'ramadan-embroidered-kaftan-c9080', 55.00, 49.00, 'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=800&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&auto=format&fit=crop&q=80', 'Limited Ramadan collection kaftan crafted with gold tilla handwork and sheer organza trim.', '54,56,58', 1, 12);


CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    action_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

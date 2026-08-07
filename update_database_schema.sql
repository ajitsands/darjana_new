-- ====================================================================
-- DAR JANA FASHION - DATABASE UPDATE SCRIPT
-- SQL Script for Product Verification, Share Tracking & View Analytics
-- ====================================================================

-- 1. Add `is_verified` column to `products` table (Defaults to 1 for existing items)
ALTER TABLE products ADD COLUMN is_verified TINYINT(1) DEFAULT 1;

-- 2. Create `product_share_clicks` table for tracking share link clicks & geolocation
CREATE TABLE IF NOT EXISTS product_share_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    source VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    country VARCHAR(100) DEFAULT NULL,
    country_code VARCHAR(10) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    recipient_email VARCHAR(255) DEFAULT NULL,
    clicked_at DATETIME NOT NULL,
    INDEX idx_prod_ip_src (product_id, ip_address, source, clicked_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add recipient_email column for existing product_share_clicks tables
ALTER TABLE product_share_clicks ADD COLUMN recipient_email VARCHAR(255) DEFAULT NULL;

-- 3. Create `product_views` table for tracking detail page views & repeat IP visitors
CREATE TABLE IF NOT EXISTS product_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    country VARCHAR(100) DEFAULT NULL,
    country_code VARCHAR(10) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    viewed_at DATETIME NOT NULL,
    INDEX idx_prod_ip_view (product_id, ip_address, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

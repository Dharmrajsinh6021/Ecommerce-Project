-- ==========================================================
-- Database Schema for ElectroTech E-Commerce Platform
-- Course: Advanced Web Development in PHP (Course Code: 2010043347)
-- Compatible with MySQL / MariaDB (XAMPP / WAMP / LAMP)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `electrotech_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `electrotech_db`;

-- 1. USERS TABLE
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `mobile` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `pincode` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. CATEGORIES TABLE
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. PRODUCTS TABLE
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `discount_price` DECIMAL(10,2) DEFAULT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `rating` DECIMAL(2,1) DEFAULT 4.5,
  `image` VARCHAR(255) DEFAULT NULL,
  `specifications` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. ORDERS TABLE
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_mobile` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(20) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `payment_status` ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
  `order_status` ENUM('Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. ORDER ITEMS TABLE
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. CONTACT MESSAGES TABLE
CREATE TABLE `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(200) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read') DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- SEED DATA
-- ==========================================================

-- Insert Users (Default Admin & Sample Customer)
-- Admin Password: admin123
-- Customer Password: customer123
INSERT INTO `users` (`id`, `name`, `email`, `password`, `mobile`, `address`, `city`, `state`, `pincode`, `role`, `status`) VALUES
(1, 'Administrator', 'admin@techtower.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '1234567891', 'Tech Tower', 'Ahmedabad', 'Gujarat', '380015', 'admin', 'active'),
(2, 'Rahul Sharma', 'customer@gmail.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '9898989898', 'Flat 402, GJ Homes, SG Highway', 'Ahmedabad', 'Gujarat', '38****', 'customer', 'active');

-- Insert Categories
INSERT INTO `categories` (`id`, `name`, `image`, `description`, `status`) VALUES
(1, 'Smartphones & Tablets', 'https://images.unsplash.com/photo-1511707171634-5f897ff02560?w=600&auto=format&fit=crop&q=80', 'Latest 5G smartphones, flagship devices, and high-performance tablets.', 'active'),
(2, 'Laptops & Computers', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&auto=format&fit=crop&q=80', 'Ultra-thin laptops, gaming rigs, and high productivity desktop systems.', 'active'),
(3, 'Audio & Headphones', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80', 'Noise-cancelling wireless headphones, earbuds, and Bluetooth speakers.', 'active'),
(4, 'Smart Wearables', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80', 'Fitness bands, premium smartwatches with AMOLED displays and GPS.', 'active');

-- Insert Products
INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `discount_price`, `stock`, `rating`, `image`, `specifications`, `status`) VALUES
(1, 1, 'ProPhone 15 Pro Max 256GB', 'Experience revolutionary titanium design, A17 Pro chip, and unprecedented camera performance with 5x optical zoom.', 134900.00, 127900.00, 25, 4.9, 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=600&auto=format&fit=crop&q=80', 'Processor: A17 Pro Hexa-core\nRAM: 8GB\nDisplay: 6.7 inch Super Retina XDR\nBattery: 4422 mAh\nOS: iOS 17', 'active'),
(2, 1, 'Galaxy Ultra S24 5G 512GB', 'Built with Galaxy AI, 200MP camera sensor, titanium frame, and built-in S-Pen for creative pros.', 124999.00, 114999.00, 18, 4.8, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=600&auto=format&fit=crop&q=80', 'Processor: Snapdragon 8 Gen 3\nRAM: 12GB\nDisplay: 6.8 inch Dynamic AMOLED 2X 120Hz\nBattery: 5000 mAh\nOS: Android 14', 'active'),
(3, 2, 'MacBook Air M3 15-inch', 'Incredible thinness, blazingly fast M3 chip with up to 18 hours of battery life and Liquid Retina display.', 134900.00, 122900.00, 15, 4.9, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&auto=format&fit=crop&q=80', 'Processor: Apple M3 8-core CPU\nRAM: 16GB Unified\nStorage: 512GB SSD\nDisplay: 15.3 inch Liquid Retina\nWeight: 1.51 kg', 'active'),
(4, 2, 'Legion Pro 7 Gaming Laptop', 'Unleash ultimate gaming power with Intel Core i9 14th Gen, RTX 4080 12GB graphics, and 240Hz display.', 189990.00, 174990.00, 8, 4.7, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&auto=format&fit=crop&q=80', 'Processor: Intel Core i9-14900HX\nGPU: NVIDIA GeForce RTX 4080 12GB\nRAM: 32GB DDR5\nStorage: 1TB NVMe Gen4\nRefresh Rate: 240Hz', 'active'),
(5, 3, 'Sony WH-1000XM5 Wireless ANC', 'Industry-leading noise canceling with two processors, 8 microphones, and remarkable high-resolution audio.', 29990.00, 24990.00, 30, 4.8, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80', 'Battery Life: 30 hours\nNoise Cancellation: Auto NC Optimizer\nDriver: 30mm carbon fiber\nConnectivity: Bluetooth 5.2, Multipoint', 'active'),
(6, 3, 'AirPods Pro 2nd Gen USB-C', 'Up to 2x more active noise cancellation, transparency mode, spatial audio, and MagSafe charging case.', 24900.00, 20900.00, 40, 4.7, 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=600&auto=format&fit=crop&q=80', 'Chip: Apple H2 headphone chip\nWater Resistance: IP54\nBattery: 6 hours listening (30 hrs with case)\nCharging: USB-C & MagSafe', 'active'),
(7, 4, 'Apex Watch Ultra 2 GPS 49mm', 'The most rugged and capable smartwatch with precision dual-frequency GPS, 3000-nit display, and 36-hr battery.', 89900.00, 79900.00, 12, 4.9, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80', 'Case: 49mm Aerospace-grade Titanium\nWater Resistance: 100m\nDisplay: Always-On Retina 3000 nits\nSensors: ECG, Blood Oxygen, Depth Gauge', 'active'),
(8, 4, 'Galaxy Watch 6 Classic 47mm', 'Iconic rotating bezel, advanced sleep coaching, body composition analysis, and sapphire crystal glass.', 36999.00, 29999.00, 20, 4.6, 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=600&auto=format&fit=crop&q=80', 'Bezel: Physical Rotating Stainless Steel\nDisplay: 1.5 inch Super AMOLED Sapphire\nRAM: 2GB | Storage: 16GB\nBattery: 425 mAh with Fast Wireless Charging', 'active');

-- Insert Sample Order
INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_mobile`, `shipping_address`, `city`, `state`, `pincode`, `total_amount`, `payment_method`, `payment_status`, `order_status`) VALUES
(1001, 2, 'Rahul Sharma', 'customer@gmail.com', '9898989898', 'Flat 402, Green Avenue, SG Highway', 'Ahmedabad', 'Gujarat', '380054', 24990.00, 'Cash on Delivery', 'Pending', 'Processing');

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `price`, `quantity`, `subtotal`) VALUES
(1001, 5, 'Sony WH-1000XM5 Wireless ANC', 24990.00, 1, 24990.00);

-- Insert Sample Contact Submission
INSERT INTO `contacts` (`name`, `email`, `phone`, `subject`, `message`) VALUES
('Amit Patel', 'amit.patel@gmail.com', '9825012345', 'Inquiry about Bulk Laptop Purchase', 'Hi ElectroTech team, we are looking to procure 10 units of MacBook Air for our startup. Do you offer institutional discounts?');

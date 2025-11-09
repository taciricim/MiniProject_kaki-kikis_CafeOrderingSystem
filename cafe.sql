-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2025 at 06:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`order_item_id`, `order_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, 8, 2, 6, 93.00),
(2, 9, 13, 1, 9.00),
(3, 9, 12, 1, 12.00),
(4, 9, 2, 1, 15.50);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Paid','Preparing','Ready','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `screenshot` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`, `screenshot`) VALUES
(1, 11, '2025-10-31 20:26:20', 0.00, 'Pending', NULL),
(2, 12, '2025-11-01 03:18:37', 0.00, '', NULL),
(3, 12, '2025-11-01 08:47:10', 0.00, '', NULL),
(4, 12, '2025-11-01 09:20:22', 0.00, '', NULL),
(8, 5, '2025-11-08 15:28:12', 93.00, 'Pending', NULL),
(9, 8, '2025-11-09 04:50:51', 36.50, '', 'proof_9_1762664232.png');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('authorized','captured','refunded','voided') NOT NULL DEFAULT 'captured',
  `reference_number` varchar(64) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preorders`
--

CREATE TABLE `preorders` (
  `preorder_id` int(11) NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `note` varchar(255) DEFAULT NULL,
  `status` enum('new','confirmed','fulfilled','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preorders`
--

INSERT INTO `preorders` (`preorder_id`, `product_id`, `user_id`, `qty`, `note`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 1, '', 'new', '2025-10-30 12:00:12', '2025-10-30 12:00:12'),
(2, 1, 8, 1, '', 'confirmed', '2025-11-01 00:30:13', '2025-11-08 14:20:25');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `availability` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(500) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`, `stock_qty`, `availability`, `description`, `image_path`) VALUES
(1, 'Burger Danish', 27.60, 0, 0, 'Handmade(and drawn) burger by Danish', 'images/products/p_1761815311_9f7dff87.jpg'),
(2, 'Carrot Cake', 15.50, 20, 1, 'Dia pedas sikit. Fav Huda', 'images/products/p_1761817359_0a03b3d3.webp'),
(12, 'Tiramisu Petit Tarte', 12.00, 29, 1, 'Decadent chocolate with hints of espresso', 'images/products/p_1762662380_62f02653.jpeg'),
(13, 'Canele', 9.00, 9, 1, 'The custardy classic. Our specialty.', 'images/products/p_1762662406_279f09c8.jpg'),
(14, 'Raspberry Tart', 11.00, 2, 1, 'The tartness of raspberry.', 'images/products/p_1762665471_fbe85ab2.jpeg'),
(15, 'Raspberry Refresher', 11.20, 0, 0, 'Taste the sour summer breeze.', 'images/products/p_1762665539_e964112b.jpeg'),
(16, 'Banana Bread Latte', 13.20, 3, 1, 'Banana!!!', 'images/products/p_1762665578_40d65f97.jpeg'),
(17, 'RIKU Matcha Latte', 14.00, 15, 1, 'Full-bodied, creamy flavor with a slight hint of sweetness. The finest specialities in the world of tea.', 'images/products/p_1762665774_9b9d18db.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `review` text NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `phone_number` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `phone_number`) VALUES
(5, 'admin', 'admin@ngopi.com', '$2y$10$DYrsxAkNFqUXzcuui9nkGOrOMKIIJCrIB501pv.ZwMqJ8u38KsNwG', 'admin', NULL),
(7, 'huda', 'huda@gmail.com', '$2y$10$DYrsxAkNFqUXzcuui9nkGOrOMKIIJCrIB501pv.ZwMqJ8u38KsNwG', 'customer', '0123456789'),
(8, 'tas', 'tas@gmail.com', '$2y$10$DYrsxAkNFqUXzcuui9nkGOrOMKIIJCrIB501pv.ZwMqJ8u38KsNwG', 'customer', '0123456789'),
(9, 'roy', 'roy@gmail.com', '$2y$10$DYrsxAkNFqUXzcuui9nkGOrOMKIIJCrIB501pv.ZwMqJ8u38KsNwG', 'customer', '0123456789'),
(10, 'danish', 'danish@gmail.com', '$2y$10$DYrsxAkNFqUXzcuui9nkGOrOMKIIJCrIB501pv.ZwMqJ8u38KsNwG', 'customer', '12345678'),
(11, 'sir', 'sir@gmail.com', '$2y$10$tbjHYRYZKpaZlV2nv5pmNOh0ioAMWZAHEo975ebuUZf37zpS4j8.2', 'customer', '1234567'),
(12, 'rayyan', 'rayyan@gmail.com', '$2y$10$TXBNzZTqEkqy4Mu0A3orvep2i1BcPantssz/H0mfPVJZiu.MwD4cu', 'customer', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `idx_oi_order` (`order_id`),
  ADD KEY `idx_oi_product` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_payment_order` (`order_id`),
  ADD KEY `idx_payment_user` (`user_id`);

--
-- Indexes for table `preorders`
--
ALTER TABLE `preorders`
  ADD PRIMARY KEY (`preorder_id`),
  ADD KEY `idx_preorders_product_id` (`product_id`),
  ADD KEY `idx_preorders_user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `order_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `preorders`
--
ALTER TABLE `preorders`
  MODIFY `preorder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderitems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `preorders`
--
ALTER TABLE `preorders`
  ADD CONSTRAINT `fk_preorders_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_preorders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

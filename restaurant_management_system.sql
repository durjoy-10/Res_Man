-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 01, 2025 at 10:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `is_super_admin`, `last_login`, `login_attempts`) VALUES
(1, 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `restaurant_id`, `name`, `description`) VALUES
(62, 32, 'Appetizer', ''),
(63, 32, 'Tacos', ''),
(64, 33, 'sdgafs', ''),
(65, 34, 'sg', 'arsg');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image_path`, `stock`) VALUES
(210, 62, 'cheesy chiken', '', 150.00, 'uploads/menu_items/item_683b36f7d9a62.jpeg', 16),
(211, 62, 'chiken basket', '', 160.00, 'uploads/menu_items/item_683b36f7da48a.jpeg', 21),
(218, 64, 'dsgv', '', 555.00, 'uploads/menu_items/item_683bf1cfa62b3.jpeg', 27),
(219, 65, 'asg', 'sfafg', 777.00, 'uploads/menu_items/item_683c0bee103da.jpeg', 65);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `valid_until` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `restaurant_id`, `description`, `valid_until`) VALUES
(30, 32, '10% off on tacos', '2025-06-05'),
(31, 34, 'fawrng wrfa ', '2025-06-18');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delivery_address` text NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','confirmed','preparing','out_for_delivery','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash_on_delivery','bkash','nagad','card') NOT NULL,
  `payment_number` varchar(20) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `restaurant_id`, `restaurant_name`, `created_at`, `updated_at`, `delivery_address`, `special_instructions`, `status`, `total_amount`, `payment_method`, `payment_number`, `transaction_id`) VALUES
(15, 1, 32, 'Food King', '2025-05-31 16:53:41', '2025-05-31 16:53:41', 'bsl', '', 'pending', 904.00, 'cash_on_delivery', NULL, NULL),
(16, 1, 32, 'Food King', '2025-05-31 17:09:43', '2025-05-31 17:09:43', 'sfljb', '', 'pending', 1280.00, 'bkash', '01797373835', '15161'),
(17, 1, 32, 'Food King', '2025-05-31 18:04:57', '2025-05-31 18:04:57', 'hjg', '', 'pending', 1360.00, 'cash_on_delivery', NULL, NULL),
(18, 1, 32, 'Food King', '2025-06-01 04:23:21', '2025-06-01 19:28:28', 'dsv', '', 'confirmed', 460.00, 'cash_on_delivery', NULL, NULL),
(19, 1, 33, 'Food King Restaurant', '2025-06-01 06:50:02', '2025-06-01 07:03:48', 'dg', '', 'delivered', 2775.00, 'cash_on_delivery', NULL, NULL),
(20, 1, 33, 'Food King Restaurant', '2025-06-01 07:29:30', '2025-06-01 07:34:52', 'afaf', '', 'delivered', 1665.00, 'bkash', '01735568714', '11414'),
(21, 1, 32, 'Food King', '2025-06-01 08:15:00', '2025-06-01 19:29:12', 'fsg', '', 'delivered', 470.00, 'cash_on_delivery', NULL, NULL),
(22, 1, 34, 'Hundy Carai', '2025-06-01 08:15:54', '2025-06-01 08:22:28', 'srrg', '', 'delivered', 3885.00, 'cash_on_delivery', NULL, NULL),
(23, 1, 33, 'Food King Restaurant', '2025-06-01 08:17:07', '2025-06-01 08:19:45', 'g', '', 'confirmed', 2775.00, 'cash_on_delivery', NULL, NULL),
(24, 2, 32, 'Food King', '2025-06-01 19:26:54', '2025-06-01 19:27:28', 'jk', '', 'delivered', 310.00, 'cash_on_delivery', NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_details`
-- (See below for the actual view)
--
CREATE TABLE `order_details` (
`order_id` int(11)
,`user_id` int(11)
,`user_name` varchar(50)
,`restaurant_id` int(11)
,`name` varchar(255)
,`order_date` timestamp
,`delivery_address` text
,`special_instructions` text
,`status` enum('pending','confirmed','preparing','out_for_delivery','delivered','cancelled')
,`total_amount` decimal(10,2)
,`payment_method` enum('cash_on_delivery','bkash','nagad','card')
,`payment_number` varchar(20)
,`transaction_id` varchar(100)
,`items` mediumtext
,`item_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(55, 15, 204, 1, 444.00),
(56, 15, 202, 1, 170.00),
(57, 15, 203, 1, 140.00),
(58, 15, 200, 1, 150.00),
(59, 16, 212, 1, 170.00),
(60, 16, 215, 2, 555.00),
(61, 17, 212, 8, 170.00),
(62, 18, 216, 1, 150.00),
(63, 18, 210, 1, 150.00),
(64, 18, 211, 1, 160.00),
(65, 19, 218, 5, 555.00),
(66, 20, 218, 3, 555.00),
(67, 21, 211, 2, 160.00),
(68, 21, 210, 1, 150.00),
(69, 22, 219, 5, 777.00),
(70, 23, 218, 5, 555.00),
(71, 24, 210, 1, 150.00),
(72, 24, 211, 1, 160.00);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_email` varchar(255) NOT NULL,
  `owner_password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `owner_name`, `owner_email`, `owner_password`, `phone`, `address`, `image_path`, `created_at`, `updated_at`) VALUES
(32, 'Food King', 'Hello food lovers, Welcome to our food palace Enjoy our food in cheapest rate Thank yo', 'MD Shuvo', 'shuvo009@gmail.com', '$2y$10$M/tx9bv8f8dEQ1/Di2OSy.ySkWEGfb9QDyM2eHZMnU62.i7l8Kc9.', '01797373835', 'BotTola, Barishal', 'uploads/restaurants/restaurant_683b2b80b23eb.jpg', '2025-05-31 16:17:04', '2025-06-01 06:24:24'),
(33, 'Food King Restaurant', 'sfgbn', 'fsgndh', 'durjoy11@gmail.com', '$2y$10$i6JdjsEDHO/.JjAr3sDep.55q98RQVYyYEuwRNvDnQzTFvy22pTSC', '01797373835', 'sg', 'uploads/restaurants/restaurant_683bf1cf8cf03.jpeg', '2025-06-01 06:23:11', '2025-06-01 06:26:44'),
(34, 'Hundy Carai', 'fgvas', 'wrwg', 'aaa@gmail.com', '$2y$10$8qajBs45QwKvauRjqO6sZesPh2ShHNsF2uY.YvsXEGonxIkNh0AHa', '01520148321', 'srr', 'uploads/restaurants/restaurant_683c0bedebe59.jpeg', '2025-06-01 08:14:38', '2025-06-01 08:14:38');

-- --------------------------------------------------------

--
-- Stand-in structure for view `restaurant_ratings`
-- (See below for the actual view)
--
CREATE TABLE `restaurant_ratings` (
`id` int(11)
,`name` varchar(255)
,`address` text
,`phone` varchar(20)
,`average_rating` decimal(14,4)
,`review_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_reviews`
--

CREATE TABLE `restaurant_reviews` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_reviews`
--

INSERT INTO `restaurant_reviews` (`id`, `restaurant_id`, `user_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(6, 32, 1, 5, 'bnm', '2025-06-01 19:30:00', '2025-06-01 19:30:00'),
(7, 33, 1, 5, 'bnmn', '2025-06-01 19:30:13', '2025-06-01 19:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `phone`, `address`, `profile_photo`, `password`, `created_at`) VALUES
(1, 'durjoy', 'Durjoy Das', 'durjoy15432@gmail.com', '01797373835', 'Kalaskati,Bakerganj, Barishal', 'static/uploads/profile_photos/user_1_1748019335.jpeg', '$2y$10$fdDT.L.E5bSN/s.y2uBNOOMeDvrNZARt8KTj6/hufSe83kXmc7ZC2', '2025-05-16 16:02:55'),
(2, 'Hadi', 'MD HADI', 'hadi15432@gmail.com', '0154656564', 'PSTU', 'static/uploads/profile_photos/user_2_1748020762.png', '$2y$10$aCpCFbYjv5qNeC7x5fhF2.cunfbua0G4aJt8Q02CKtMCMKPPvsxhC', '2025-05-17 16:21:55'),
(5, 'Anjan', 'Anjan Das', 'anjandas45@gmail.com', '01838150186', 'Sadar,kalaskati', 'static/uploads/profile_photos/user_5_1748092587.jpeg', '$2y$10$rC8fr2fehu64BaONkQftiuxqowKBZyf9lgPCrbkEzmz/tZ0GXwXKq', '2025-05-24 13:14:05');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_reviews`
-- (See below for the actual view)
--
CREATE TABLE `user_reviews` (
`id` int(11)
,`user_name` varchar(100)
,`restaurant_name` varchar(255)
,`rating` int(11)
,`review_text` text
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `order_details`
--
DROP TABLE IF EXISTS `order_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_details`  AS SELECT `o`.`id` AS `order_id`, `o`.`user_id` AS `user_id`, `u`.`username` AS `user_name`, `o`.`restaurant_id` AS `restaurant_id`, `r`.`name` AS `name`, `o`.`created_at` AS `order_date`, `o`.`delivery_address` AS `delivery_address`, `o`.`special_instructions` AS `special_instructions`, `o`.`status` AS `status`, `o`.`total_amount` AS `total_amount`, `o`.`payment_method` AS `payment_method`, `o`.`payment_number` AS `payment_number`, `o`.`transaction_id` AS `transaction_id`, group_concat(concat(`mi`.`name`,' (',`oi`.`quantity`,' × $',`oi`.`price`,')') separator ', ') AS `items`, count(`oi`.`id`) AS `item_count` FROM ((((`orders` `o` join `restaurants` `r` on(`r`.`id` = `o`.`restaurant_id`)) join `users` `u` on(`o`.`user_id` = `u`.`id`)) join `order_items` `oi` on(`o`.`id` = `oi`.`order_id`)) join `menu_items` `mi` on(`oi`.`menu_item_id` = `mi`.`id`)) GROUP BY `o`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `restaurant_ratings`
--
DROP TABLE IF EXISTS `restaurant_ratings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `restaurant_ratings`  AS SELECT `r`.`id` AS `id`, `r`.`name` AS `name`, `r`.`address` AS `address`, `r`.`phone` AS `phone`, avg(`rv`.`rating`) AS `average_rating`, count(`rv`.`id`) AS `review_count` FROM (`restaurants` `r` left join `restaurant_reviews` `rv` on(`r`.`id` = `rv`.`restaurant_id`)) GROUP BY `r`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `user_reviews`
--
DROP TABLE IF EXISTS `user_reviews`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_reviews`  AS SELECT `rv`.`id` AS `id`, `u`.`name` AS `user_name`, `r`.`name` AS `restaurant_name`, `rv`.`rating` AS `rating`, `rv`.`review_text` AS `review_text`, `rv`.`created_at` AS `created_at` FROM ((`restaurant_reviews` `rv` join `users` `u` on(`rv`.`user_id` = `u`.`id`)) join `restaurants` `r` on(`rv`.`restaurant_id` = `r`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_categories_ibfk_1` (`restaurant_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_ibfk_1` (`category_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offers_ibfk_1` (`restaurant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `orders_ibfk_2` (`restaurant_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `owner_email` (`owner_email`);

--
-- Indexes for table `restaurant_reviews`
--
ALTER TABLE `restaurant_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_restaurant_reviews_restaurant` (`restaurant_id`),
  ADD KEY `idx_restaurant_reviews_user` (`user_id`),
  ADD KEY `idx_restaurant_reviews_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `restaurant_reviews`
--
ALTER TABLE `restaurant_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD CONSTRAINT `menu_categories_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_reviews`
--
ALTER TABLE `restaurant_reviews`
  ADD CONSTRAINT `restaurant_reviews_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `restaurant_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

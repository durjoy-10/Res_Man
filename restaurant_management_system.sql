-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 25, 2025 at 07:26 PM
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
(30, 24, 'Appetizer', ''),
(31, 24, 'Tacos', ''),
(32, 24, 'Wings & Lolopop\'s', ''),
(35, 27, 'Bangla Cuisine Add On', ''),
(36, 27, 'Thai Chinese Cuisine Rice', ''),
(37, 27, 'Mutton', '');

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
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image_path`) VALUES
(107, 30, 'Fried Wonton', '1 pcs', 30.00, 'uploads/menu_items/item_682c05ea95062.jpeg'),
(108, 30, 'Thai Fried Chiken ', '1 pcs', 90.00, 'uploads/menu_items/item_682c05ea95290.jpeg'),
(109, 30, 'Crispy Chiken', '1 pcs', 100.00, 'uploads/menu_items/item_682c05ea9546e.jpeg'),
(110, 30, 'French Fries', '', 130.00, 'uploads/menu_items/item_682c05ea95f23.jpeg'),
(111, 30, 'Wedges', '', 130.00, 'uploads/menu_items/item_682c05ea9612f.jpeg'),
(112, 30, 'Spicy Chiken', '10 pcs', 170.00, 'uploads/menu_items/item_682c05ea96318.jpeg'),
(113, 30, 'Chiken Strips', '6 pcs', 180.00, 'uploads/menu_items/item_682c05ea964ec.jpeg'),
(114, 30, 'Chiken Meatball', '6 pcs', 200.00, 'uploads/menu_items/item_682c05ea966ef.jpeg'),
(115, 30, 'Chiken Popcorn', '', 200.00, 'uploads/menu_items/item_682c05ea968d1.jpeg'),
(116, 30, 'Cheesy Chiken ', '4 pcs', 200.00, 'uploads/menu_items/item_682c05ea96aa4.jpeg'),
(117, 30, 'Chiken Basket', '6 pcs', 250.00, 'uploads/menu_items/item_682c05ea96c7a.jpeg'),
(118, 30, 'Chiken Mushroom Overload', '', 240.00, 'uploads/menu_items/item_682c05ea96e4a.jpeg'),
(119, 30, 'Chiken Cheese Mushroom overload', '', 280.00, 'uploads/menu_items/item_682c05ea97021.jpeg'),
(120, 30, 'Octopus', '', 350.00, 'uploads/menu_items/item_682c05ea971ee.jpeg'),
(121, 31, 'Normal Tacos', '', 150.00, 'uploads/menu_items/item_682c05ea9751d.jpeg'),
(122, 31, 'BBQ Tacos', '', 160.00, 'uploads/menu_items/item_682c05ea97735.jpeg'),
(123, 31, 'Naga Tacos', '', 160.00, 'uploads/menu_items/item_682c05ea9790c.jpeg'),
(124, 32, 'Naga Wings', '', 200.00, 'uploads/menu_items/item_682c05ea97bef.jpeg'),
(125, 32, 'Honey Wings', '', 200.00, 'uploads/menu_items/item_682c05ea97dc3.jpeg'),
(126, 32, 'Buffalo Wings', '', 200.00, NULL),
(127, 32, 'Crispy Wings', '', 200.00, NULL),
(128, 32, 'BBQ Wings', '', 200.00, NULL),
(129, 32, 'Fried Chiken Lolipop', '', 200.00, NULL),
(130, 32, 'BBQ Chiken Lolipop', '', 200.00, NULL),
(131, 32, 'Naga Chiken Lolipop', '', 200.00, NULL),
(134, 35, 'Plain Rice', '', 30.00, 'uploads/menu_items/item_683144c2e01c7.jpeg'),
(135, 35, 'Plain Polao', '', 60.00, 'uploads/menu_items/item_683144c2e0514.jpeg'),
(136, 35, 'Fish Vorta', '', 70.00, 'uploads/menu_items/item_683144c2e06e1.jpeg'),
(137, 35, 'Chiken Achari', '', 150.00, 'uploads/menu_items/item_683144c2e08a7.jpeg'),
(138, 35, 'Mutton Razala', '2 pcs', 250.00, 'uploads/menu_items/item_683144c2e09f3.jpeg'),
(139, 36, 'Chiken Fried Rice', '', 160.00, 'uploads/menu_items/item_683144c2e2537.jpeg'),
(140, 36, 'Egg Fried Rice', '1:1', 120.00, 'uploads/menu_items/item_683144c2e2c30.jpeg'),
(141, 36, 'Hot Plate Special', '', 250.00, 'uploads/menu_items/item_683144c2e2e27.jpeg'),
(142, 36, 'Indonesian Fried Rice', '', 400.00, 'uploads/menu_items/item_683144c2e2fd3.jpeg'),
(143, 36, 'Mixed Fried Rice ', '1:3', 370.00, 'uploads/menu_items/item_683144c2e317f.jpeg'),
(144, 36, 'Prawn Fried Rice', '1:3', 300.00, 'uploads/menu_items/item_683144c2e332d.jpeg'),
(145, 36, 'Vegetable Fried Rice ', '1:1', 120.00, 'uploads/menu_items/item_683144c2e34bc.jpeg'),
(146, 37, 'Mutton Boti Kabab', '', 250.00, 'uploads/menu_items/item_683144c2e36bf.jpeg'),
(147, 37, 'Mutton Seekh Kabab', '', 275.00, 'uploads/menu_items/item_683144c2e3801.jpeg'),
(148, 37, 'Tawa Mutton Jhal Kabab', '', 250.00, 'uploads/menu_items/item_683144c2e395a.jpeg');

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
(18, 27, '10% off on all the food items', '2025-05-30');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_address` text NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','processing','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `restaurant_id`, `restaurant_name`, `order_date`, `delivery_address`, `special_instructions`, `status`, `total_amount`) VALUES
(1, 3, 24, '', '2025-05-23 12:48:48', 'Kalaskati,Barishal', 'Nothing', 'processing', 850.00),
(2, 1, 24, 'Food King Restaurant', '2025-05-23 14:36:31', 'PSTU', 'Focus on Quality not Quantity', 'delivered', 1810.00),
(3, 3, 24, 'Food King Restaurant', '2025-05-23 20:03:10', 'Shitlakhola,Barishal', '', 'pending', 360.00),
(4, 3, 24, 'Food King Restaurant', '2025-05-23 20:06:34', 'Shitlakhola,Barishal', '', 'processing', 1950.00),
(5, 2, 24, 'Food King Restaurant', '2025-05-24 03:25:49', 'Kalaskati', '', 'delivered', 260.00),
(6, 2, 27, 'Hot Plate Restaurant', '2025-05-24 04:10:36', 'Pstu', '', 'processing', 1240.00),
(8, 2, 27, 'Hot Plate Restaurant', '2025-05-24 11:08:29', 'Kalaskati', '', 'pending', 210.00),
(9, 5, 27, 'Hot Plate Restaurant', '2025-05-24 13:17:13', 'PSTU', '', 'delivered', 860.00),
(10, 5, 24, 'Food King Restaurant', '2025-05-24 13:19:23', 'PSTU', '', 'pending', 840.00),
(11, 5, 27, 'Hot Plate Restaurant', '2025-05-24 13:50:57', 'Barishal', '', 'pending', 650.00);

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
,`status` enum('pending','processing','delivered','cancelled')
,`total_amount` decimal(10,2)
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
(1, 1, 107, 1, 30.00),
(2, 1, 108, 1, 90.00),
(3, 1, 109, 1, 100.00),
(4, 1, 110, 1, 130.00),
(5, 1, 111, 1, 130.00),
(6, 1, 112, 1, 170.00),
(7, 1, 115, 1, 200.00),
(8, 2, 111, 1, 130.00),
(9, 2, 113, 1, 180.00),
(10, 2, 114, 1, 200.00),
(11, 2, 115, 1, 200.00),
(12, 2, 116, 1, 200.00),
(13, 2, 117, 1, 250.00),
(14, 2, 120, 1, 350.00),
(15, 2, 121, 2, 150.00),
(16, 3, 108, 1, 90.00),
(17, 3, 109, 1, 100.00),
(18, 3, 112, 1, 170.00),
(19, 4, 108, 2, 90.00),
(20, 4, 109, 1, 100.00),
(21, 4, 112, 2, 170.00),
(22, 4, 120, 3, 350.00),
(23, 4, 119, 1, 280.00),
(24, 5, 107, 1, 30.00),
(25, 5, 109, 1, 100.00),
(26, 5, 110, 1, 130.00),
(27, 6, 135, 1, 60.00),
(28, 6, 137, 1, 150.00),
(29, 6, 143, 1, 370.00),
(30, 6, 139, 1, 160.00),
(31, 6, 148, 1, 250.00),
(32, 6, 146, 1, 250.00),
(33, 8, 134, 1, 30.00),
(34, 8, 135, 1, 60.00),
(35, 8, 145, 1, 120.00),
(36, 9, 134, 1, 30.00),
(37, 9, 135, 1, 60.00),
(38, 9, 136, 1, 70.00),
(39, 9, 142, 1, 400.00),
(40, 9, 144, 1, 300.00),
(41, 10, 113, 1, 180.00),
(42, 10, 111, 1, 130.00),
(43, 10, 110, 1, 130.00),
(44, 10, 125, 1, 200.00),
(45, 10, 126, 1, 200.00),
(46, 11, 137, 1, 150.00),
(47, 11, 146, 1, 250.00),
(48, 11, 148, 1, 250.00);

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
(24, 'Food King Restaurant', 'Hello food lovers,\r\nWelcome to our food palace\r\nEnjoy our food in cheapest rate\r\nThank you.', 'Durjoy Das', 'durjoy15432@gmail.com', '$2y$10$Zo82UdyKt111FzGotLpJpepthtgyhJnvuHD2D/bgcH3inIQ6JiH.u', '01797373835', 'Bottola, Barishal', 'uploads/restaurants/restaurant_682c05ea71f39.jpg', '2025-05-20 04:32:42', '2025-05-20 04:32:42'),
(27, 'Hot Plate Restaurant', 'HOT PLATE RESTAURANT is a specialized CHINESE & KABAB restaurant in the heart', 'Benoy', 'benoy15432@gmail.com', '$2y$10$LrQ3cf0Nd1vYdsKA2MaaQePN3UIu4qdYmIoMzDfqvlXIOXKxcJxsm', '01934014025', 'Jel Gate, Barishal', 'uploads/restaurants/restaurant_683144c2c93b1.jpg', '2025-05-24 04:02:10', '2025-05-24 04:02:10');

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
(1, 24, 2, 5, 'Best foods in Barishal', '2025-05-24 02:49:36', '2025-05-24 02:49:36'),
(2, 27, 2, 4, 'Better but can be best ', '2025-05-24 04:38:31', '2025-05-24 04:38:31'),
(3, 24, 1, 5, 'Best foods in town', '2025-05-24 05:25:55', '2025-05-24 05:25:55'),
(4, 27, 1, 4, 'Overall better.', '2025-05-24 05:26:27', '2025-05-24 05:26:27'),
(5, 24, 5, 5, 'Best ', '2025-05-24 13:28:47', '2025-05-24 13:28:47');

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
(3, 'Hridita', 'Hridita Mitra', 'hrediramitra003@gmail.com', '01546565642', 'Barishal', 'static/uploads/profile_photos/user_3_1748020942.jpg', '$2y$10$XQmxe4Qe62.dwa.9/wfuKuiPCeLJi3aX2Fb7XVzV6DRohmqxQZioK', '2025-05-23 09:23:54'),
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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_details`  AS SELECT `o`.`id` AS `order_id`, `o`.`user_id` AS `user_id`, `u`.`username` AS `user_name`, `o`.`restaurant_id` AS `restaurant_id`, `r`.`name` AS `name`, `o`.`order_date` AS `order_date`, `o`.`delivery_address` AS `delivery_address`, `o`.`special_instructions` AS `special_instructions`, `o`.`status` AS `status`, `o`.`total_amount` AS `total_amount`, group_concat(concat(`mi`.`name`,' (',`oi`.`quantity`,' × $',`oi`.`price`,')') separator ', ') AS `items`, count(`oi`.`id`) AS `item_count` FROM ((((`orders` `o` join `restaurants` `r` on(`r`.`id` = `o`.`restaurant_id`)) join `users` `u` on(`o`.`user_id` = `u`.`id`)) join `order_items` `oi` on(`o`.`id` = `oi`.`order_id`)) join `menu_items` `mi` on(`oi`.`menu_item_id` = `mi`.`id`)) GROUP BY `o`.`id` ;

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
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `restaurant_reviews`
--
ALTER TABLE `restaurant_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);

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

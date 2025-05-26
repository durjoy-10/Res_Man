-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 26, 2025 at 07:11 PM
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
(44, 28, 'Appetizer', ''),
(45, 28, 'Tacos', ''),
(46, 28, 'Wings & Lolipops', '');

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
(166, 44, 'cheesy chiken', '', 220.00, 'uploads/menu_items/item_68349eb5b5b9a.jpeg', 30),
(167, 44, 'Chiken Basket', '', 250.00, 'uploads/menu_items/item_68349eb5b5df2.jpeg', 35),
(168, 44, 'Chiken Cheese Mushroom Overload', '', 270.00, 'uploads/menu_items/item_68349eb5b60d2.jpeg', 25),
(169, 44, 'Chiken Meatball', '', 220.00, 'uploads/menu_items/item_68349eb5b61ca.jpeg', 30),
(170, 44, 'Chiken Mushroom Overload', '', 200.00, 'uploads/menu_items/item_68349eb5b62bd.jpeg', 20),
(171, 44, 'Chiken Popcorn', '', 180.00, 'uploads/menu_items/item_68349eb5b63a9.jpeg', 30),
(172, 44, 'Chiken Strips', '', 230.00, 'uploads/menu_items/item_68349eb5b64a4.jpeg', 30),
(173, 44, 'Crispy Chiken', '', 180.00, 'uploads/menu_items/item_68349eb5b6596.jpeg', 26),
(174, 44, 'French Fries', '', 250.00, 'uploads/menu_items/item_68349eb5b662f.jpeg', 28),
(175, 44, 'Octopus', '', 330.00, 'uploads/menu_items/item_68349eb5b66c6.jpeg', 32),
(176, 44, 'Spicy Chiken', '', 190.00, 'uploads/menu_items/item_68349eb5b675e.jpeg', 29),
(177, 44, 'Thai Fried Chiken', '', 250.00, 'uploads/menu_items/item_68349eb5b67f5.jpeg', 24),
(178, 44, 'Wedges', '', 160.00, 'uploads/menu_items/item_68349eb5b688d.jpeg', 15),
(179, 45, 'BBQ Tacos', '', 190.00, 'uploads/menu_items/item_68349eb5b6edb.jpeg', 32),
(180, 45, 'Naga Tacos', '', 220.00, 'uploads/menu_items/item_68349eb5b7981.jpeg', 33),
(181, 45, 'Tacos', '', 250.00, 'uploads/menu_items/item_68349eb5b7b11.jpeg', 22),
(182, 46, 'BBQ Chiken Lolipop', '', 250.00, 'uploads/menu_items/item_68349eb5b7d49.jpeg', 22),
(183, 46, 'BBQ Wings ', '', 220.00, 'uploads/menu_items/item_68349eb5b7e74.jpeg', 20),
(184, 46, 'Buffalo Wings', '', 220.00, 'uploads/menu_items/item_68349eb5b7f7d.jpeg', 21),
(185, 46, 'Crispy Wings', '', 220.00, NULL, 14),
(186, 46, 'Fried Chiken Lolipop', '', 210.00, NULL, 33),
(187, 46, 'Honey Wings', '', 250.00, NULL, 40),
(188, 46, 'Naga Chiken Lolipop', '', 350.00, NULL, 7),
(189, 46, 'Naga Wings', '', 300.00, NULL, 5);

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
(20, 28, '10% discount on Tacos', '2025-05-30');

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
(28, 'Food King', 'Hello food lovers, Welcome to our food palace Enjoy our food in cheapest rate Thank you.', 'MD Shuvo', 'shuvo009@gmail.com', '$2y$10$d5JWbHIz3cwi.Fmixp8Gu.na4rdl9lSTLAm6A.uRxF/MYMKudHj/6', '0154656564', 'Bottola, Barishal', 'uploads/restaurants/restaurant_68349eb5a844b.jpg', '2025-05-26 17:02:45', '2025-05-26 17:02:45');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `restaurant_reviews`
--
ALTER TABLE `restaurant_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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

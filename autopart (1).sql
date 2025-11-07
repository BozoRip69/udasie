-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Lis 06, 2025 at 01:53 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autopart`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `batteries`
--

CREATE TABLE `batteries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) DEFAULT NULL,
  `brand` varchar(100) NOT NULL,
  `capacity` varchar(20) DEFAULT NULL,
  `voltage` varchar(20) DEFAULT NULL,
  `status` enum('active','replaced','disposed') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batteries`
--

INSERT INTO `batteries` (`id`, `user_id`, `car_id`, `brand`, `capacity`, `voltage`, `status`, `created_at`) VALUES
(2, 4, 2, 'Autopart', '30', '12', 'active', '2025-11-06 08:23:41');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vin` varchar(50) DEFAULT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `mileage` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `user_id`, `vin`, `brand`, `model`, `year`, `mileage`, `image`, `created_at`) VALUES
(2, 4, NULL, 'Mercedes', 'C klasa', 2017, 50000, 'uploads/cars/1762413713_gf-8dd5-HFX2-CCoH_young-multi-zdjecia-z-wypadku-320x213.jpg', '2025-11-06 08:21:53'),
(3, 6, NULL, 'Lamborghini', 'Ronaldo', 2015, 0, 'uploads/cars/1762414438_images (3).jpg', '2025-11-06 08:33:58');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `login_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `content`, `image_path`, `created_at`, `is_read`) VALUES
(220, 15, 14, 'elo', NULL, '2025-11-06 13:29:54', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `message_reactions`
--

CREATE TABLE `message_reactions` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('like','love','laugh','wow','sad') DEFAULT 'like',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('like','comment','follow','message','system') DEFAULT 'system',
  `content` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `content`, `link`, `is_read`, `created_at`) VALUES
(10, 3, 'message', 'Michał \"MultiGameplayGuy\" wysłał Ci wiadomość.', 'messages.php?user_id=4', 1, '2025-11-06 08:17:52'),
(11, 4, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:17:58'),
(12, 3, 'message', 'Michał \"MultiGameplayGuy\" wysłał Ci wiadomość.', 'messages.php?user_id=4', 1, '2025-11-06 08:18:10'),
(13, 4, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:18:27'),
(14, 3, 'message', 'Michał \"MultiGameplayGuy\" wysłał Ci wiadomość.', 'messages.php?user_id=4', 1, '2025-11-06 08:18:41'),
(15, 4, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:22:09'),
(16, 3, 'message', 'Michał \"MultiGameplayGuy\" wysłał Ci wiadomość.', 'messages.php?user_id=4', 1, '2025-11-06 08:22:18'),
(17, 4, 'like', '', 'browse.php#post-2', 1, '2025-11-06 08:24:12'),
(18, 3, 'message', 'Trevor wysłał Ci wiadomość.', 'messages.php?user_id=5', 1, '2025-11-06 08:24:14'),
(19, 3, 'message', 'Trevor wysłał Ci wiadomość.', 'messages.php?user_id=5', 1, '2025-11-06 08:24:39'),
(20, 5, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:24:53'),
(21, 3, 'message', 'Darren \"IShowSpeed\" wysłał Ci wiadomość.', 'messages.php?user_id=6', 1, '2025-11-06 08:30:04'),
(22, 3, 'message', 'Darren \"IShowSpeed\" wysłał Ci wiadomość.', 'messages.php?user_id=6', 1, '2025-11-06 08:30:08'),
(23, 3, 'message', 'Darren \"IShowSpeed\" wysłał Ci wiadomość.', 'messages.php?user_id=6', 1, '2025-11-06 08:30:10'),
(24, 3, 'message', 'Darren \"IShowSpeed\" wysłał Ci wiadomość.', 'messages.php?user_id=6', 1, '2025-11-06 08:30:15'),
(25, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:31:41'),
(26, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:32:40'),
(27, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:32:42'),
(28, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:44:35'),
(29, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:44:45'),
(30, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:44:47'),
(31, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:46:53'),
(32, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:46:53'),
(33, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:46:55'),
(34, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:46:55'),
(35, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:46:56'),
(36, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:46:58'),
(37, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:09'),
(38, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:32'),
(39, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:33'),
(40, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:33'),
(41, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:34'),
(42, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:34'),
(43, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:35'),
(44, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:35'),
(45, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:36'),
(46, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:36'),
(47, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:50'),
(48, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:51'),
(49, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:52'),
(50, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:54'),
(51, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:47:57'),
(52, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:47:57'),
(53, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:48:16'),
(54, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:48:16'),
(55, 6, 'like', '', 'browse.php#post-3', 1, '2025-11-06 08:48:17'),
(56, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:11'),
(57, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:12'),
(58, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:12'),
(59, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:13'),
(60, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:13'),
(61, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:14'),
(62, 6, 'like', '', 'browse.php#post-4', 1, '2025-11-06 08:51:15'),
(63, 6, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:54:58'),
(64, 3, 'message', 'Darren \"IShowSpeed\" wysłał Ci wiadomość.', 'messages.php?user_id=6', 1, '2025-11-06 08:57:19'),
(65, 6, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:57:57'),
(66, 6, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 08:58:03'),
(67, 6, 'message', 'Remigiusz \"reZi\" wysłał Ci wiadomość.', 'messages.php?user_id=3', 1, '2025-11-06 09:00:46'),
(68, 6, 'like', '', 'browse.php#post-3', 0, '2025-11-06 09:54:49'),
(69, 6, 'like', '', 'browse.php#post-3', 0, '2025-11-06 09:54:50'),
(70, 6, 'like', '', 'browse.php#post-3', 0, '2025-11-06 09:54:50'),
(71, 6, 'like', '', 'browse.php#post-3', 0, '2025-11-06 09:54:51'),
(72, 6, 'like', '', 'browse.php#post-3', 0, '2025-11-06 09:54:51'),
(73, 4, 'like', '', 'browse.php#post-2', 0, '2025-11-06 09:54:54'),
(74, 4, 'like', '', 'browse.php#post-2', 0, '2025-11-06 09:54:55'),
(75, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-06 10:35:17'),
(76, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-06 10:35:18');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `media`, `created_at`) VALUES
(4, 6, 'siema kupilem nowe auto', 'uploads/posts/1762414531_images (4).jpg', '2025-11-06 08:35:31');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reactions`
--

CREATE TABLE `reactions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('like','love','haha','wow','sad','angry') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reactions`
--

INSERT INTO `reactions` (`id`, `post_id`, `user_id`, `type`, `created_at`) VALUES
(38, 4, 3, 'sad', '2025-11-06 10:35:17');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `country_code` varchar(10) DEFAULT '+48',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default-avatar.png',
  `bio` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `country_code`, `phone`, `address`, `password`, `reset_token`, `reset_expires`, `last_password_change`, `avatar`, `bio`, `created_at`, `last_login`, `session_token`, `role`) VALUES
(3, 'Remigiusz \"reZi\"', 'Wierzgoń', 'rezistyle@gmail.com', '+48', '323434123', 'ul. Lipowa 2 Warszawa', '$2y$10$SSHltZieE3oMZJgIjoVKkuFt/toqyURkcYmFjxpbUwWn3LMwaLQPW', NULL, NULL, '2025-11-06 10:14:41', 'uploads/avatars/1762413270_7cfa2b5057e0c2f89603ce98eb8a2939.jpg', NULL, '2025-11-06 08:14:30', '2025-11-06 11:47:32', NULL, 'admin'),
(4, 'Michał \"MultiGameplayGuy\"', 'Rychlik', 'mlodyraper@gmail.com', '+48', '123456420', 'ul. białasów 2', '$2y$10$HnptQicTk76mZB2KrFpNY.ya2p3wRgsBWcD19e8QlvAN15KuxeAx2', NULL, NULL, NULL, 'uploads/avatars/1762413463_mulcias.jpg', NULL, '2025-11-06 08:17:43', '2025-11-06 13:32:21', '76d1fdc59011a7f5c536d50bfe34fba187d324e19c066dc555794a19335bd8e1', 'admin'),
(5, 'Trevor', 'Philips', 'xscerus@gmail.com', '+48', '620620620', 'ul. Tulipanowa Gorzyce', '$2y$10$stYucQasIYDlxBeApg5k1O9Nt7z4GFdq9v.NNmbyZTa/BUD.czeiq', 'b625af44378d29e3a8ecf984c9f14380', '2025-11-06 08:54:38', NULL, 'uploads/avatars/1762413743_trever.webp', 'GO F🏌', '2025-11-06 08:22:23', '2025-11-06 13:28:28', '35bdbaabbac8fe690599809bef68b4676b2a9685c0b8323a270fe161e2213d9e', 'admin'),
(6, 'Darren \"IShowSpeed\"', 'Watkins Jr.', 'speed@gmail.com', '+48', '420420420', 'Cincinnati', '$2y$10$KH/Y/tqBxcb0WOb9wnEyxOL4EA7av3NXpnpbEay8JU0gR/sYQeH4i', NULL, NULL, NULL, 'uploads/avatars/1762414099_images (1).jpg', 'please speed i need this, my mom is kinda homeless, i live with my dad i wanna help her out', '2025-11-06 08:28:19', NULL, '5380e8d46b1bff26b729cb9d2f192b0cc3a98dbc1ce85b8933f91c433755fb4d', 'user'),
(7, 'Michał \"Masny Ben\"', 'Andrzej Baron', 'masnygang@gmail.com', '+48', '420696969', 'ul. masna 2115', '$2y$10$l.MkS2hLz/WeAsM4wuJ9veCnMHca71Z9f7.UVmYlK09y5JNMakdE6', NULL, NULL, NULL, 'uploads/avatars/1762420687_kim-jest-masny-ben-35614ff.jpeg', 'zrob mi louda', '2025-11-06 10:18:07', '2025-11-06 10:26:07', NULL, 'user'),
(14, 'Jakub', 'Landa', 'jcob.landa@gmail.com', '+48', '321312312', 'ul. Lipowa 2 Warszawa', '$2y$10$ATN.A9xLv6K9w6LYk7mpRefIDfTyefsaNvDnGN8eIeHqbH0GEX4JK', '189893', '2025-11-06 12:21:09', NULL, 'default-avatar.png', NULL, '2025-11-06 12:01:13', '2025-11-06 13:34:15', '1dd9f0ac6eed8fdacb207b2a25ebc5644c8ea29138df9a5f77b2748d23a4e061', 'admin'),
(15, 'koniu', 'og', 'tomekci34a@gmail.com', '+48', '121221212', 'ul. masna 2115', '$2y$10$J7C.msQNv/gYmRhWoqwQze7cyjrzknE4nx3.OUyALAAdgg4JdUrFC', NULL, NULL, NULL, 'default-avatar.png', NULL, '2025-11-06 13:29:12', '2025-11-06 13:31:32', 'dd265310735371799891d963c076f1c5e5bacf4e95bb90cff8e25010b72dcc40', 'user');

-- --------------------------------------------------------

--
-- Zastąpiona struktura widoku `user_stats`
-- (See below for the actual view)
--
CREATE TABLE `user_stats` (
`user_id` int(11)
,`first_name` varchar(100)
,`last_name` varchar(100)
,`cars_count` bigint(21)
,`batteries_count` bigint(21)
,`total_kilometers` decimal(32,0)
,`posts_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vin` varchar(20) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `engine_type` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `fuel_type` varchar(50) DEFAULT NULL,
  `engine_capacity` varchar(50) DEFAULT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `power` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura widoku `user_stats`
--
DROP TABLE IF EXISTS `user_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_stats`  AS SELECT `u`.`id` AS `user_id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, count(distinct `c`.`id`) AS `cars_count`, count(distinct `b`.`id`) AS `batteries_count`, coalesce(sum(`c`.`mileage`),0) AS `total_kilometers`, count(distinct `p`.`id`) AS `posts_count` FROM (((`users` `u` left join `cars` `c` on(`c`.`user_id` = `u`.`id`)) left join `batteries` `b` on(`b`.`user_id` = `u`.`id`)) left join `posts` `p` on(`p`.`user_id` = `u`.`id`)) GROUP BY `u`.`id` ;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `batteries`
--
ALTER TABLE `batteries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indeksy dla tabeli `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indeksy dla tabeli `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reaction` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batteries`
--
ALTER TABLE `batteries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `message_reactions`
--
ALTER TABLE `message_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batteries`
--
ALTER TABLE `batteries`
  ADD CONSTRAINT `batteries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batteries_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD CONSTRAINT `message_reactions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reactions`
--
ALTER TABLE `reactions`
  ADD CONSTRAINT `reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

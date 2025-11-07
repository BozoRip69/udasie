-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Lis 07, 2025 at 01:42 PM
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
  `serial_number` varchar(50) NOT NULL,
  `conductor_number` varchar(50) NOT NULL,
  `battery_model` varchar(100) NOT NULL,
  `installation_date` date NOT NULL,
  `catalog_current` int(11) NOT NULL,
  `cca_sn` int(11) NOT NULL,
  `cca_from` int(11) NOT NULL,
  `cca_to` int(11) NOT NULL,
  `cca_avg` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batteries`
--

INSERT INTO `batteries` (`id`, `user_id`, `serial_number`, `conductor_number`, `battery_model`, `installation_date`, `catalog_current`, `cca_sn`, `cca_from`, `cca_to`, `cca_avg`) VALUES
(1, 0, '82934751', '41235', 'ARL110R-60', '2025-01-01', 800, 800, 775, 831, 803),
(2, 0, '19284756', '61234', 'VARTA-E44', '2025-01-02', 780, 775, 760, 810, 785),
(3, 0, '37592618', '51247', 'BOSCH-S5-001', '2025-01-03', 820, 810, 790, 850, 820),
(4, 0, '49275681', '73218', 'CENTRA-Futura', '2025-01-04', 760, 755, 740, 780, 760),
(5, 0, '58234791', '84561', 'EXIDE-Premium', '2025-01-05', 810, 805, 780, 830, 805),
(6, 0, '69384712', '24579', 'YUASA-Silver', '2025-01-06', 770, 765, 740, 790, 765),
(7, 0, '71293485', '12456', 'TAB-Polar', '2025-01-07', 800, 798, 770, 825, 797),
(8, 0, '81293745', '36781', 'ACDelco-60Ah', '2025-01-08', 790, 785, 760, 810, 785),
(9, 0, '92384756', '47891', 'FIAMM-Titanium', '2025-01-09', 810, 808, 785, 830, 807),
(10, 0, '10394827', '58912', 'ENERGIZER-Plus', '2025-01-10', 780, 775, 750, 805, 777),
(11, 0, '11384726', '69813', 'DURACELL-Advanced', '2025-01-11', 820, 815, 790, 840, 815),
(12, 0, '12483756', '78914', 'OPTIMA-RedTop', '2025-01-12', 850, 845, 820, 870, 845),
(13, 0, '13594827', '89145', 'BOSCH-S4-002', '2025-01-13', 770, 768, 750, 790, 770),
(14, 0, '14693857', '91246', 'CENTRA-Plus', '2025-01-14', 760, 755, 730, 780, 755),
(15, 0, '15784928', '13467', 'VARTA-Silver', '2025-01-15', 830, 825, 800, 850, 825),
(16, 0, '16873945', '24568', 'EXIDE-EK700', '2025-01-16', 790, 788, 760, 815, 787),
(17, 0, '17928465', '35679', 'YUASA-YBX', '2025-01-17', 780, 778, 755, 800, 778),
(18, 0, '18937456', '46780', 'BOSCH-S6', '2025-01-18', 850, 848, 820, 870, 846),
(19, 0, '19284736', '57891', 'FIAMM-Neptune', '2025-01-19', 770, 768, 740, 790, 766),
(20, 0, '20394856', '68912', 'TAB-Magic', '2025-01-20', 800, 798, 770, 825, 797),
(21, 0, '21493857', '79123', 'ACDelco-65Ah', '2025-01-21', 810, 808, 780, 835, 806),
(22, 0, '22584736', '81234', 'VARTA-BlueDynamic', '2025-01-22', 780, 777, 750, 800, 776),
(23, 0, '23695847', '92345', 'BOSCH-S5-002', '2025-01-23', 820, 818, 790, 840, 816),
(24, 0, '24793856', '13456', 'CENTRA-Futura', '2025-01-24', 790, 785, 760, 810, 785),
(25, 0, '25894736', '24567', 'EXIDE-Premium', '2025-01-25', 810, 808, 780, 830, 806),
(26, 0, '26938475', '35678', 'YUASA-Silver', '2025-01-26', 770, 768, 740, 790, 766),
(27, 0, '27394856', '46789', 'FIAMM-Titanium', '2025-01-27', 810, 805, 785, 825, 805),
(28, 0, '28493756', '57890', 'ENERGIZER-Max', '2025-01-28', 780, 778, 755, 800, 778),
(29, 0, '29584637', '68901', 'DURACELL-Extreme', '2025-01-29', 820, 818, 790, 840, 816),
(30, 0, '30693745', '79012', 'OPTIMA-YellowTop', '2025-01-30', 850, 848, 820, 870, 846),
(31, 0, '31794826', '80123', 'BOSCH-S4-001', '2025-01-31', 770, 768, 750, 790, 769),
(32, 0, '32893756', '91234', 'CENTRA-Plus', '2025-02-01', 760, 758, 730, 780, 756),
(33, 0, '33984725', '12345', 'VARTA-Silver', '2025-02-02', 830, 828, 800, 850, 826),
(34, 0, '34928475', '23456', 'EXIDE-EK600', '2025-02-03', 790, 788, 760, 815, 787),
(35, 0, '35938475', '34567', 'YUASA-YBX', '2025-02-04', 780, 778, 755, 800, 778),
(36, 0, '36927458', '45678', 'BOSCH-S6', '2025-02-05', 850, 848, 820, 870, 846),
(37, 0, '37948562', '56789', 'FIAMM-Neptune', '2025-02-06', 770, 768, 740, 790, 766),
(38, 0, '38947526', '67890', 'TAB-Magic', '2025-02-07', 800, 798, 770, 825, 797),
(39, 0, '39485716', '78901', 'ACDelco-70Ah', '2025-02-08', 810, 808, 780, 835, 806),
(40, 0, '40593827', '89012', 'VARTA-BlueDynamic', '2025-02-09', 780, 778, 750, 800, 777),
(41, 0, '41694827', '90123', 'BOSCH-S5-004', '2025-02-10', 820, 818, 790, 840, 816),
(42, 0, '42793847', '01234', 'CENTRA-Futura', '2025-02-11', 790, 788, 760, 810, 786),
(43, 0, '43894736', '12346', 'EXIDE-Premium', '2025-02-12', 810, 808, 780, 830, 806),
(44, 0, '44938475', '23457', 'YUASA-Silver', '2025-02-13', 770, 768, 740, 790, 766),
(45, 0, '45928475', '34568', 'FIAMM-Titanium', '2025-02-14', 810, 808, 785, 830, 807),
(46, 0, '46938475', '45679', 'ENERGIZER-Max', '2025-02-15', 780, 778, 755, 800, 778),
(47, 0, '47938475', '56780', 'DURACELL-Extreme', '2025-02-16', 820, 818, 790, 840, 816),
(48, 0, '48938475', '67891', 'OPTIMA-RedTop', '2025-02-17', 850, 848, 820, 870, 846),
(49, 0, '49938475', '78902', 'BOSCH-S4-003', '2025-02-18', 770, 768, 750, 790, 769),
(50, 0, '50938475', '89013', 'CENTRA-Plus', '2025-02-19', 760, 758, 730, 780, 756);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `battery_installations`
--

CREATE TABLE `battery_installations` (
  `id` int(11) NOT NULL,
  `battery_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_place` varchar(100) NOT NULL,
  `installation_date` date NOT NULL,
  `vehicle_mileage` int(11) NOT NULL,
  `battery_image` varchar(255) DEFAULT NULL,
  `receipt_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(227, 14, 16, 'siema jaką masz furke', NULL, '2025-11-07 11:38:43', 1),
(228, 14, 16, 'ja taką', 'uploads/messages/1762511929_IMG_20250906_144619.jpg', '2025-11-07 11:38:49', 1),
(229, 16, 14, 'you wpierdalasz kotlety of grandmother', NULL, '2025-11-07 11:39:30', 1),
(230, 14, 16, 'ty kurwa przyczlapie jebany ty rozumiesz co sie do ciebie pisze?', NULL, '2025-11-07 11:39:52', 1),
(231, 14, 16, 'kurwo', NULL, '2025-11-07 11:39:53', 1),
(232, 16, 14, 'you in your house beating your horse and im in hiszpania kurwo', NULL, '2025-11-07 11:40:16', 1),
(233, 14, 16, 'aha', NULL, '2025-11-07 11:40:27', 1),
(234, 14, 16, 'spoko', NULL, '2025-11-07 11:40:28', 1),
(235, 16, 14, 'dzieki', NULL, '2025-11-07 11:40:33', 1),
(236, 14, 16, 'smacznego', NULL, '2025-11-07 11:40:37', 1),
(237, 16, 14, 'okej', NULL, '2025-11-07 11:41:17', 1),
(238, 14, 16, 'wypierdalaj kurwa niggerze', NULL, '2025-11-07 11:59:11', 1),
(239, 14, 16, 'nie prawda', NULL, '2025-11-07 11:59:12', 1),
(240, 14, 16, 'ukradli mi konto', NULL, '2025-11-07 11:59:18', 1),
(241, 16, 14, 'co ty pizsesz do krola sentino cwelu pierdolony???/', NULL, '2025-11-07 11:59:26', 1),
(242, 16, 14, 'pecie', NULL, '2025-11-07 11:59:33', 1),
(243, 16, 14, 'bym cie kurwa zgasil jak peta', NULL, '2025-11-07 11:59:42', 1),
(244, 14, 16, 'ty kurwo', NULL, '2025-11-07 12:20:24', 1),
(245, 14, 16, 'szmato', NULL, '2025-11-07 12:20:25', 1),
(246, 14, 16, 'dziwko', NULL, '2025-11-07 12:20:28', 0),
(247, 14, 16, 'prostytutko', NULL, '2025-11-07 12:20:31', 0),
(248, 16, 14, 'kurwo', NULL, '2025-11-07 12:20:31', 1),
(249, 16, 14, 'zajebac cie', NULL, '2025-11-07 12:20:33', 1),
(250, 14, 16, 'jebana', NULL, '2025-11-07 12:20:35', 0),
(251, 14, 16, 'jebac cie', NULL, '2025-11-07 12:20:38', 0),
(252, 16, 14, 'wiesz kim jestem?', NULL, '2025-11-07 12:20:38', 1),
(253, 14, 16, 'kurwo', NULL, '2025-11-07 12:20:39', 0),
(254, 14, 16, 'cwelem jestes', NULL, '2025-11-07 12:20:43', 0),
(255, 14, 16, 'szmato', NULL, '2025-11-07 12:20:44', 0),
(256, 16, 14, 'jestem krolem sentino', 'uploads/messages/1762514465_sentino_midas.jpg', '2025-11-07 12:21:05', 1),
(257, 16, 14, 'palo jebana', NULL, '2025-11-07 12:21:08', 1);

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
(73, 4, 'like', '', 'browse.php#post-2', 1, '2025-11-06 09:54:54'),
(74, 4, 'like', '', 'browse.php#post-2', 1, '2025-11-06 09:54:55'),
(75, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-06 10:35:17'),
(76, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-06 10:35:18'),
(77, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:11'),
(78, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:12'),
(79, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:12'),
(80, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:13'),
(81, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:14'),
(82, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:15'),
(83, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:15'),
(84, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:16'),
(85, 6, 'like', '', 'browse.php#post-4', 0, '2025-11-07 09:29:17');

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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('open','answered','closed') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `subject`, `message`, `admin_reply`, `status`, `created_at`, `updated_at`) VALUES
(4, 14, 'sd', 'sdsd', 'dsadas', 'answered', '2025-11-07 13:32:43', '2025-11-07 13:32:57');

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
(4, 'Michał \"MultiGameplayGuy\"', 'Rychlik', 'mlodyraper@gmail.com', '+48', '123456420', 'ul. białasów 2', '$2y$10$HnptQicTk76mZB2KrFpNY.ya2p3wRgsBWcD19e8QlvAN15KuxeAx2', NULL, NULL, NULL, 'uploads/avatars/1762413463_mulcias.jpg', NULL, '2025-11-06 08:17:43', '2025-11-07 12:41:53', '76d1fdc59011a7f5c536d50bfe34fba187d324e19c066dc555794a19335bd8e1', 'admin'),
(5, 'Trevor', 'Philips', 'xscerus@gmail.com', '+48', '620620620', 'ul. Tulipanowa Gorzyce', '$2y$10$stYucQasIYDlxBeApg5k1O9Nt7z4GFdq9v.NNmbyZTa/BUD.czeiq', 'b625af44378d29e3a8ecf984c9f14380', '2025-11-06 08:54:38', NULL, 'uploads/avatars/1762413743_trever.webp', 'GO F🏌', '2025-11-06 08:22:23', '2025-11-07 11:11:11', '35bdbaabbac8fe690599809bef68b4676b2a9685c0b8323a270fe161e2213d9e', 'admin'),
(6, 'Darren \"IShowSpeed\"', 'Watkins Jr.', 'speed@gmail.com', '+48', '420420420', 'Cincinnati', '$2y$10$KH/Y/tqBxcb0WOb9wnEyxOL4EA7av3NXpnpbEay8JU0gR/sYQeH4i', NULL, NULL, NULL, 'uploads/avatars/1762414099_images (1).jpg', 'please speed i need this, my mom is kinda homeless, i live with my dad i wanna help her out', '2025-11-06 08:28:19', NULL, '5380e8d46b1bff26b729cb9d2f192b0cc3a98dbc1ce85b8933f91c433755fb4d', 'user'),
(7, 'Michał \"Masny Ben\"', 'Andrzej Baron', 'masnygang@gmail.com', '+48', '420696969', 'ul. masna 2115', '$2y$10$l.MkS2hLz/WeAsM4wuJ9veCnMHca71Z9f7.UVmYlK09y5JNMakdE6', NULL, NULL, NULL, 'uploads/avatars/1762420687_kim-jest-masny-ben-35614ff.jpeg', 'zrob mi louda', '2025-11-06 10:18:07', '2025-11-07 08:22:22', NULL, 'user'),
(14, 'Jakub', 'Landa', 'jcob.landa@gmail.com', '+48', '123123123', 'ul. Lipowa 2 Warszawa', '$2y$10$zRrQBwngXdTAkPIednZ2p.y11DVf3aCuI4gkPsA/N2QJtbgl3wepG', NULL, NULL, NULL, 'uploads/avatars/1762505628_hc5x589wxzda1.jpg', '', '2025-11-06 12:01:13', '2025-11-07 13:25:13', '1dd9f0ac6eed8fdacb207b2a25ebc5644c8ea29138df9a5f77b2748d23a4e061', 'admin'),
(16, 'Sebastian \"Sentino\"', 'Pałucki', 'bezodklejki@gmail.com', '+48', '420420420', 'ul. odjebana 13', '$2y$10$8Z7ZUgQkl39J2DVEsKLAfuV6/Ve.TVvlEc38Clyy7cZFJBE17Q3TG', NULL, NULL, NULL, 'uploads/avatars/1762511896_sentino_midas.jpg', NULL, '2025-11-07 11:38:16', NULL, '33c5830de203ce1afdf07ba7c836ce8747cb2b20716d50ac5ee35bc2eef30a48', 'user');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_batteries`
--

CREATE TABLE `user_batteries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `battery_id` int(11) NOT NULL,
  `purchase_place` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `battery_image` varchar(255) DEFAULT NULL,
  `receipt_image` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` datetime DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `registration_number` varchar(20) DEFAULT NULL,
  `mileage` int(11) DEFAULT 0,
  `purchase_date` date DEFAULT NULL,
  `inspection_date` date DEFAULT NULL,
  `needs_update` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `user_id`, `vin`, `brand`, `model`, `engine_type`, `year`, `fuel_type`, `engine_capacity`, `transmission`, `power`, `country`, `created_at`, `image`, `registration_number`, `mileage`, `purchase_date`, `inspection_date`, `needs_update`) VALUES
(3, 14, 'WDDGF8AB5DR295972', 'Mercedes-Benz', 'C 300', '6', 2013, 'Gasoline', '3500', 'All wheel drive', '243', 'Germany', '2025-11-07 08:45:17', 'uploads/vehicles/1762507069_1kpxxnlkvxy-NddxKgnqh3bG31dLVezHE__2_.jpeg', '', 130000, '2025-09-18', '2025-11-07', 0),
(5, 4, 'W0L0XCF6856106063', 'Opel', 'Corsa', '', 2005, 'opcjonalne', '', '', '175', 'Germany', '2025-11-07 09:05:24', 'uploads/vehicles/1762502836_image.jpg', 'chujwie', 1300000, '1993-04-12', '0001-01-01', 0),
(7, 14, 'WAUFFCFL0FN039272', 'Audi', 'A4', NULL, 2015, 'Gasoline', '1984', 'All wheel drive', '209', 'Germany', '2025-11-07 11:15:28', 'uploads/vehicles/1762510528_IMG_20250906_144619.jpg', 'RT44799', 156450, '2022-01-06', '2025-10-07', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `vehicle_mileage`
--

CREATE TABLE `vehicle_mileage` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mileage` int(11) NOT NULL,
  `recorded_at` date NOT NULL DEFAULT curdate(),
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_mileage`
--

INSERT INTO `vehicle_mileage` (`id`, `vehicle_id`, `user_id`, `mileage`, `recorded_at`, `note`) VALUES
(1, 3, 14, 120000, '2025-11-07', NULL),
(2, 5, 4, 1200000, '2025-11-07', NULL),
(4, 7, 14, 125000, '2025-11-07', NULL),
(5, 7, 14, 140000, '2025-11-07', NULL),
(6, 3, 14, 120000, '2025-11-07', NULL),
(8, 7, 14, 140000, '2025-11-07', NULL),
(9, 3, 14, 130000, '2025-11-07', NULL),
(10, 5, 4, 1300000, '2025-11-07', NULL),
(11, 7, 14, 156450, '2025-11-07', NULL);

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
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `battery_installations`
--
ALTER TABLE `battery_installations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `battery_id` (`battery_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

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
-- Indeksy dla tabeli `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `user_batteries`
--
ALTER TABLE `user_batteries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `battery_id` (`battery_id`);

--
-- Indeksy dla tabeli `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `vehicle_mileage`
--
ALTER TABLE `vehicle_mileage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batteries`
--
ALTER TABLE `batteries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `battery_installations`
--
ALTER TABLE `battery_installations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- AUTO_INCREMENT for table `message_reactions`
--
ALTER TABLE `message_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_batteries`
--
ALTER TABLE `user_batteries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicle_mileage`
--
ALTER TABLE `vehicle_mileage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `battery_installations`
--
ALTER TABLE `battery_installations`
  ADD CONSTRAINT `battery_installations_ibfk_1` FOREIGN KEY (`battery_id`) REFERENCES `batteries` (`id`),
  ADD CONSTRAINT `battery_installations_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

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
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_batteries`
--
ALTER TABLE `user_batteries`
  ADD CONSTRAINT `user_batteries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_batteries_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_batteries_ibfk_3` FOREIGN KEY (`battery_id`) REFERENCES `batteries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vehicle_mileage`
--
ALTER TABLE `vehicle_mileage`
  ADD CONSTRAINT `vehicle_mileage_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_mileage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

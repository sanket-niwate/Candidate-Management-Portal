-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 03:05 PM
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
-- Database: `candidate_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(8, 'Admin User', 'admin@example.com', '$2y$10$MlFlN9TpFraw56DVOZ0uE.qqc8Oj2c6.ieC0RdmQam1aso.EUSRvC'),
(10, 'Admin User', 'admin@admin.com', '$2y$10$iUdAixsWXUMrPOhxJTTS6uBGpdjiqNz3Og0eZ1unyAbl1WKr8Gg1m');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_entries`
--

CREATE TABLE `candidate_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `skills` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_entries`
--

INSERT INTO `candidate_entries` (`id`, `user_id`, `name`, `email`, `phone`, `skills`, `profile_image`, `document`, `created_at`) VALUES
(10, 3, 'neha', 'neha@gmail.com', '1234567890', 'ss', '1764172223_692721bfb29a4.jpg', '1764165170_692706324191d.pdf', '2025-11-26 13:52:50'),
(11, 4, 'sid', 'sid@gmail.com', '1234567890', 's', '1764181743_692746efc14c4.jpg', '1764181743_692746efc1ce7.pdf', '2025-11-26 18:29:03'),
(12, 6, 'tans', 'tan@gmail.com', '1234567890', 'dn', '1764188089_69275fb9c89c7.jpg', '1764188089_69275fb9c8d46.pdf', '2025-11-26 20:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `phone` varchar(15) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `phone`, `profile_image`, `created_at`) VALUES
(2, 'sanket', 'sanket@gmail.com', '$2y$10$g7.bVz5twA1ewLTd.hG5CekKxvq6SF/y/Km.S7e821P8Who/72kHu', 'user', NULL, '1764164897_69270521b359c.jpg', '2025-11-26 13:47:34'),
(3, 'neha', 'neha@gmail.com', '$2y$10$yq.GvYzNDz6NHLy4uU9AFeiaEujErKgJDgb2FAbMUMLzQ9KhUwAye', 'user', NULL, NULL, '2025-11-26 13:50:58'),
(4, 'sid', 'sid@gmail.com', '$2y$10$vp5zOoSED2OUKjUMGJCSBuzOTE4R1W5B0fb0yrziyXSOCBGnxusRe', 'user', NULL, NULL, '2025-11-26 18:27:31'),
(6, 'tan', 'tan@gmail.com', '$2y$10$gGfR9ayJfg3AaXSULstohe8uyolRTySLT.feEZfUaahnePztXB1Xe', 'user', NULL, NULL, '2025-11-26 20:11:53'),
(11, 'admin', 'admin@gmail.com', '$2y$10$yq.GvYzNDz6NHLy4uU9AFeiaEujErKgJDgb2FAbMUMLzQ9KhUwAye', 'admin', NULL, NULL, '2025-11-27 13:02:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `candidate_entries`
--
ALTER TABLE `candidate_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `candidate_entries`
--
ALTER TABLE `candidate_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidate_entries`
--
ALTER TABLE `candidate_entries`
  ADD CONSTRAINT `candidate_entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

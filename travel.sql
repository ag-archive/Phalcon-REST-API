-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 13, 2016 at 01:12 PM
-- Server version: 5.5.50
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(11) NOT NULL,
  `user_is` int(11) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `attachment_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attachments_types`
--

CREATE TABLE IF NOT EXISTS `attachments_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `timelines`
--

CREATE TABLE IF NOT EXISTS `timelines` (
  `id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `timelines_types`
--

CREATE TABLE IF NOT EXISTS `timelines_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oauth_client` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oauth_client_user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `logged_at` datetime DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wrong_password_count` tinyint(1) DEFAULT NULL,
  `wrong_password_date` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `password_hash`, `password_reset_token`, `oauth_client`, `oauth_client_user_id`, `email`, `status`, `created_at`, `updated_at`, `logged_at`, `avatar_path`, `wrong_password_count`, `wrong_password_date`) VALUES
(15, 'test_first', 'test_last', '$2y$08$R3VvNGFIZUtSQis0VERjR.Sl3ITg8maxm0JJc.LFL2gOTXUm3RlkS', NULL, NULL, NULL, 'john_doe@gmail.com', 1, '2016-12-13 12:48:14', NULL, '2016-12-13 01:11:29', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_tokens`
--

CREATE TABLE IF NOT EXISTS `users_tokens` (
  `id` int(11) NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_tokens`
--

INSERT INTO `users_tokens` (`id`, `access_token`, `user_id`, `user_agent`) VALUES
(74, '$2y$08$MWpTSWhzZlozaHl5aFc0UOHhb0pxV5qTvcsFSBTZ2z/EGqBTw8u8K', 15, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attachments_types`
--
ALTER TABLE `attachments_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timelines_types`
--
ALTER TABLE `timelines_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users_tokens`
--
ALTER TABLE `users_tokens`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `users_tokens`
--
ALTER TABLE `users_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=75;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

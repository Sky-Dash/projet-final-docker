-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 24, 2026 at 09:20 AM
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
-- Database: `php_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `created_at`, `image_path`, `description`) VALUES
(14, 'Apex Legends', '2026-03-17 14:49:43', 'uploads/apex.jpeg', 'Apex Legends is great!!!'),
(20, 'Minecraft', '2026-03-24 08:05:07', 'uploads/minecraft.jpeg', 'Minecraft is a 3D sandbox game developed by Mojang Studios where players interact with a fully modifiable three-dimensional environment made of blocks and entities. Its diverse gameplay lets players choose the way they play, allowing for countless possibilities.'),
(21, 'Terraria', '2026-03-24 08:07:04', 'uploads/terraria.jpeg', 'Terraria is a sandbox, action-adventure, role-playing, and platformer video game. It offers gameplay that revolves around exploration, building, crafting, combat, survival, and mining, without having any set goals. It is playable in both single-player and multiplayer modes.'),
(22, 'League of Legends', '2026-03-24 08:12:20', 'uploads/league.jpeg', 'League of Legends is a team-based strategy game where two teams of five powerful champions face off to destroy the other\'s base. Choose from over 140 champions to make epic plays, secure kills, and take down towers as you battle your way to victory.'),
(23, 'Ytrack', '2026-03-24 08:15:59', 'uploads/oh-snap.png', 'Ytrack is a great platform for learning how to code, we all love so much here at ynov.');

-- --------------------------------------------------------

--
-- Table structure for table `success`
--

CREATE TABLE `success` (
  `id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `success`
--

INSERT INTO `success` (`id`, `game_id`, `name`, `description`) VALUES
(1, 14, 'All Round Winner', 'Win a game with every Legend.'),
(2, 14, 'Found my Main', 'Win 100 games with a single Legend.'),
(5, 20, 'King of the End', 'Defeatl the Ender Dragon'),
(6, 20, 'Ready for a Beacon', 'Defeat the wither and collect a Nether Star.'),
(7, 21, 'Saved The World', 'Defeat the Moon Lord.'),
(8, 21, 'True Swordsman', 'Obtain the Zenith, the sword of all swords.'),
(9, 22, 'First game', 'GG you sold your soul.'),
(10, 23, 'Oh Snap!', 'Have ytrack tell you your code is shit.'),
(11, 23, 'Finish a whole Piscine', 'Wow Great job, (we know you didn\'t do it all by yourself)');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `role`, `password_hash`) VALUES
(1, 'SkyDash', 'louis.shore@ynov.com', 'admin', '$2y$10$eGqzDwPhLJ4znihE6YVOcumHX4cF5zT3ZZqKexsBqT3mmgC9LVDuS'),
(5, 'Asd', 'asdasd@asd.a', 'user', '$2y$10$iY4ysSKqJl6g45.Q8IhHAe6j3k42l4mC9QpVaUNjk.iRL9dl5XPky'),
(6, 'asdasd', 'asdasd@asdsad.asd', 'user', '$2y$10$Lbx.ToDPobZEe.RKB2C3BO3j4XfmYLWDq78maxrpgktlAhC1CsONK'),
(7, 'asd', 'asdasd@asdasd', 'user', '$2y$10$dFhe28zq3QQiJFKg4AN9pO7OKZNK/mno8B1jrbaxH06fSXiH/8hv6'),
(8, 'asd', 'adasd@asdsad.ASD', 'user', '$2y$10$5B1KH7GBdDEXexfUwGeq5e2xDobzN4mZMPVSgVR.y2ZgW2BmnEXG.'),
(9, 'asda', 'sddsf@asd.asd', 'user', '$2y$10$OfpgklWb8WZsU5XWCZZtteAxrSYT0QkTjK7YyibTR.38gMt8GzU/S'),
(10, 'asd', 'wasdsad@asdad.awe', 'user', '$2y$10$SXcGK9O0OIs.UyBjz6YU2OQo0Az/eb0DeB8s/iaMn4SH93EqwtF4O'),
(11, 'asdasd', 'asdasdasdaa@Adasd.asda', 'user', '$2y$10$0CpBgK8p3Dm7xvt12Mhn6eF4tyof.I/iXCVKl7vAs.lSgTcElAPPO'),
(12, 'asdasd', 'asdasd@asdasd.asdasd', 'user', '$2y$10$o2uMENQjk4dYb8hjrzsXN.11o917dENCDnH30Vsqkns1IQSHACyoq'),
(13, 'asdasd', 'asdads@asdasd.asdaasdad', 'user', '$2y$10$3xoLhInl6gAqacWcZxXRvOo8mX3VHrsIS1touEYUOrtnJaGC1pJVK'),
(14, 'asda', 'asdasdasd@asdasd.asdasd', 'user', '$2y$10$rDx/Zi76FdI5/2oqaLglZuOpnbesYG41Y8I99XuddcZUdC3.TJq0K'),
(16, 'asd', 'adasdasda@asdasd.asdas', 'user', '$2y$10$I96eP9UHZEprFv8E4u6nv.dDF0vSv9gxVXE7zUeBBVuOm2Susa61.'),
(17, 'asd', 'asd@asd.asd', 'user', '$2y$10$d7AP8yuaCdJGT1DxRYJaBe0IguLNo46yDKVz5/zLuzhRrNPMuEld.'),
(18, 'asd', 'ddfgdf@asdasd.asdasd', 'user', '$2y$10$fmv9z8EYq1GqTcz/K.EVvO8bFxvF48R4hq50D6gEXTImQ/mfHQJRi'),
(19, 'asd', 'sdfssdsdfsd@adasd.asda', 'user', '$2y$10$fRPeTV6ZVJ5DB0Lw4JtOxekdMyILGp9NbAC2M41vXrCVDR.wcuo3a'),
(20, 'test', 'test@test.com', 'user', '$2y$10$oRoiBpSl6elk/8zFXWdz3eAFO2gkYM5tPvGTGpUq7u244.mbk4vaS');

-- --------------------------------------------------------

--
-- Table structure for table `user_games`
--

CREATE TABLE `user_games` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `obtained_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_games`
--

INSERT INTO `user_games` (`id`, `user_id`, `game_id`, `obtained_at`) VALUES
(11, 1, 23, '2026-03-24 08:18:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_success`
--

CREATE TABLE `user_success` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `success_id` int(11) DEFAULT NULL,
  `obtained_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_success`
--

INSERT INTO `user_success` (`id`, `user_id`, `success_id`, `obtained_at`) VALUES
(10, 1, 1, '2026-03-18 08:37:06'),
(11, 1, 10, '2026-03-24 08:18:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `success`
--
ALTER TABLE `success`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_games`
--
ALTER TABLE `user_games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `user_success`
--
ALTER TABLE `user_success`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `success_id` (`success_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `success`
--
ALTER TABLE `success`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_games`
--
ALTER TABLE `user_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_success`
--
ALTER TABLE `user_success`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `success`
--
ALTER TABLE `success`
  ADD CONSTRAINT `success_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

--
-- Constraints for table `user_games`
--
ALTER TABLE `user_games`
  ADD CONSTRAINT `user_games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

--
-- Constraints for table `user_success`
--
ALTER TABLE `user_success`
  ADD CONSTRAINT `user_success_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_success_ibfk_2` FOREIGN KEY (`success_id`) REFERENCES `success` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

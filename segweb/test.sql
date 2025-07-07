-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 07, 2025 at 01:26 PM
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
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `1111`
--

CREATE TABLE `1111` (
  `idx` int(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `score` int(100) NOT NULL,
  `id` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `1111`
--

INSERT INTO `1111` (`idx`, `pass`, `score`, `id`, `date_time`, `location`) VALUES
(1, '4015e9ce43edfb0668ddaa973ebc7e87', 100, 'test', '2025-07-07 17:03:50', 'rrr'),
(2, '4015e9ce43edfb0668ddaa973ebc7e87', 44, 'are', '2025-07-07 17:52:57', '2'),
(3, 'b2ca678b4c936f905fb82f2733f5297f', 1, 'qqq', '2025-07-07 19:11:21', '23231'),
(4, '02c425157ecd32f259548b33402ff6d3', 100, 'last', '2025-07-07 20:09:45', '1');

-- --------------------------------------------------------

--
-- Table structure for table `board`
--

CREATE TABLE `board` (
  `id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `writer` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `board`
--

INSERT INTO `board` (`id`, `title`, `content`, `writer`, `filename`, `filepath`, `created_at`) VALUES
(1, 'test1234', 'sakdsaddsa', 'test', '화면 캡처 2025-06-14 203159.png', 'uploads/1751882113_화면 캡처 2025-06-14 203159.png', '2025-07-07 17:50:32'),
(2, 'aret', 'dd', 'are', '화면 캡처 2025-06-14 203517.png', 'uploads/1751878459_화면 캡처 2025-06-14 203517.png', '2025-07-07 17:54:19'),
(4, 'hihi', 'hihi', 'last', 'SearchPaths.txt', 'filedir/1751886802_SearchPaths.txt', '2025-07-07 20:10:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `1111`
--
ALTER TABLE `1111`
  ADD PRIMARY KEY (`idx`);

--
-- Indexes for table `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `1111`
--
ALTER TABLE `1111`
  MODIFY `idx` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `board`
--
ALTER TABLE `board`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

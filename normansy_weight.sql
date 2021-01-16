-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2021 at 01:17 AM
-- Server version: 5.7.32
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `normansy_weight`
--

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `initial_target` float NOT NULL,
  `real_target` float DEFAULT NULL,
  `weight` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`id`, `date`, `initial_target`, `real_target`, `weight`) VALUES
(1, '2021-01-16', 90.2, 90.2, 90.2),
(2, '2021-01-17', 90.1193, 90.1193, 90),
(3, '2021-01-18', 90.0387, 89.9195, NULL),
(4, '2021-01-19', 89.9581, NULL, NULL),
(5, '2021-01-20', 89.8776, NULL, NULL),
(6, '2021-01-21', 89.7972, NULL, NULL),
(7, '2021-01-22', 89.7169, NULL, NULL),
(8, '2021-01-23', 89.6366, NULL, NULL),
(9, '2021-01-24', 89.5564, NULL, NULL),
(10, '2021-01-25', 89.4763, NULL, NULL),
(11, '2021-01-26', 89.3963, NULL, NULL),
(12, '2021-01-27', 89.3163, NULL, NULL),
(13, '2021-01-28', 89.2364, NULL, NULL),
(14, '2021-01-29', 89.1565, NULL, NULL),
(15, '2021-01-30', 89.0768, NULL, NULL),
(16, '2021-01-31', 88.9971, NULL, NULL),
(17, '2021-02-01', 88.9175, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

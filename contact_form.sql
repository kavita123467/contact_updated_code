-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 14, 2024 at 09:53 AM
-- Server version: 8.0.35
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `contact_form_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_form`
--

CREATE TABLE `contact_form` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `time` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `city` varchar(255) NOT NULL,
  `business` varchar(255) NOT NULL,
  `booked` tinyint(1) NOT NULL DEFAULT '0',
  `is_booked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_form`
--

INSERT INTO `contact_form` (`id`, `date`, `time`, `name`, `email`, `phone`, `city`, `business`, `booked`, `is_booked`) VALUES
(1, '2024-10-10', '12:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(2, '2024-10-09', '11:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(3, '2024-10-09', '11:30', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(4, '2024-10-09', '12:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(5, '2024-10-09', '13:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(6, '2024-10-10', '14:30', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(7, '2024-10-10', '15:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(8, '2024-10-09', '14:30', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(9, '2024-10-10', '15:30', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(10, '2024-10-10', '17:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(11, '2024-10-15', '11:00', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0),
(12, '2024-10-23', '11:30', 'Perfectiongeeks', 'kavitaperfectiongeeks@gmail.com', '9176282062', 'Laguna Beach', 'Perfectiongeeks', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_form`
--
ALTER TABLE `contact_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_form`
--
ALTER TABLE `contact_form`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

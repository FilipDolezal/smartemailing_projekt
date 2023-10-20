-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 20, 2023 at 01:24 PM
-- Server version: 5.7.36
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartemailing_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `points_of_sale`
--

DROP TABLE IF EXISTS `points_of_sale`;
CREATE TABLE IF NOT EXISTS `points_of_sale` (
  `id` varchar(10) CHARACTER SET ascii NOT NULL,
  `type` enum('ticketMachine','ticketOfficeMetro','informationCenter','trainStation','carrierOffice','chipCardDispense') CHARACTER SET ascii NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `lat` decimal(8,6) NOT NULL,
  `lon` decimal(9,6) NOT NULL,
  `services` int(11) NOT NULL,
  `pay_methods` int(11) NOT NULL,
  `link` varchar(2083) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Table structure for table `points_of_sale_hours`
--

DROP TABLE IF EXISTS `points_of_sale_hours`;
CREATE TABLE IF NOT EXISTS `points_of_sale_hours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos_id` varchar(10) NOT NULL,
  `day_from` smallint(6) NOT NULL,
  `day_to` smallint(6) NOT NULL,
  `hours` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_id` (`pos_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2946 DEFAULT CHARSET=ascii;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `points_of_sale_hours`
--
ALTER TABLE `points_of_sale_hours`
  ADD CONSTRAINT `points_of_sale_hours_ibfk_1` FOREIGN KEY (`pos_id`) REFERENCES `points_of_sale` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

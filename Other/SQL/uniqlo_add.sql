-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2014 at 06:19 AM
-- Server version: 5.5.33
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `uniqlo`
--

-- --------------------------------------------------------

--
-- Table structure for table `u_app_key`
--

CREATE TABLE `u_app_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app` varchar(100) NOT NULL,
  `appkey` varchar(100) NOT NULL,
  `invoketime` datetime NOT NULL,
  `counts` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `u_app_key`
--

INSERT INTO `u_app_key` (`id`, `app`, `appkey`, `invoketime`, `counts`) VALUES
(1, 'baiyi', '123456789', '2014-01-29 22:00:34', 156);

-- --------------------------------------------------------

--
-- Table structure for table `u_products_beubeu`
--

CREATE TABLE `u_products_beubeu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uq` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='u_products_beubeu' AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `u_products_beubeu_history`
--

CREATE TABLE `u_products_beubeu_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uq` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `batchid` varchar(255) NOT NULL,
  `createtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Table structure for table `u_settings`
--

CREATE TABLE `u_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `u_settings`
--

INSERT INTO `u_settings` (`id`, `key`, `value`) VALUES
(1, 'APIInvokeCounts', '400'),
(2, 'male', '15478'),
(3, 'female', '15474'),
(4, 'boy', '15581'),
(5, 'girl', '15583'),
(6, 'baiyi_touchid', '854'),
(7, 'baiyi_touchid_key', '8f1a6e3f182904ad22170f56c890e533'),
(8, 'TableRowCounts', '10');

-- --------------------------------------------------------

--
-- Table structure for table `u_settings_color`
--

CREATE TABLE `u_settings_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uq_color_id` varchar(250) NOT NULL,
  `uq_color_name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

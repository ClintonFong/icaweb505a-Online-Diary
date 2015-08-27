-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2015 at 11:26 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `icaweb505a`
--

-- --------------------------------------------------------

--
-- Table structure for table `icaweb505a_attachments`
--

CREATE TABLE IF NOT EXISTS `icaweb505a_attachments` (
  `idAttachment` int(11) NOT NULL AUTO_INCREMENT,
  `diaryID` int(11) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `filetype` varchar(10) NOT NULL,
  PRIMARY KEY (`idAttachment`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `icaweb505a_attachments`
--

-- --------------------------------------------------------

--
-- Table structure for table `icaweb505a_contactus`
--

CREATE TABLE IF NOT EXISTS `icaweb505a_contactus` (
  `idContactUs` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `message` varchar(1024) NOT NULL,
  PRIMARY KEY (`idContactUs`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `icaweb505a_diary`
--

CREATE TABLE IF NOT EXISTS `icaweb505a_diary` (
  `idDiary` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `date` varchar(11) NOT NULL,
  `text` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`idDiary`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `icaweb505a_diary`
--

-- --------------------------------------------------------

--
-- Table structure for table `icaweb505a_users`
--

CREATE TABLE IF NOT EXISTS `icaweb505a_users` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(64) NOT NULL,
  `isLoggedIn` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `icaweb505a_users`
--

INSERT INTO `icaweb505a_users` (`idUser`, `firstname`, `lastname`, `email`, `password`, `isLoggedIn`) VALUES
(1, 'Clinton', 'Fong', 'info@clintonfong.com', '7c5c97d7f5975f0619edacb9a115c809bd25a245c91738012e14e6c0f5856914', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

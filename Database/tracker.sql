-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2016 at 01:28 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `DataPoint`
--

CREATE TABLE IF NOT EXISTS `DataPoint` (
  `pointID` int(11) NOT NULL AUTO_INCREMENT,
  `dataID` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `acceleration` double NOT NULL,
  `velocity` double NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `altitude` double NOT NULL,
  PRIMARY KEY (`pointID`),
  KEY `dataID` (`dataID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `DataPoint`
--

INSERT INTO `DataPoint` (`pointID`, `dataID`, `time`, `acceleration`, `velocity`, `latitude`, `longitude`, `altitude`) VALUES
(1, 1, '0000-00-00 00:00:00', 1.5, 3, 51.08029670228, -114.13417674622, 1106.553),
(2, 1, '0000-00-00 00:00:00', 1.6, 3.1, 51.0802818949, -114.1341732242, 1103.5718),
(3, 1, '0000-00-00 00:00:00', 1.5, 3, 51.08026743408, -114.13417031667, 1100.9688),
(4, 1, '0000-00-00 00:00:00', 1.6, 3.1, 51.08031344056, -114.13417394747, 1107.3743);

-- --------------------------------------------------------

--
-- Table structure for table `DataSet`
--

CREATE TABLE IF NOT EXISTS `DataSet` (
  `dataID` int(11) NOT NULL AUTO_INCREMENT,
  `addedBy` int(11) NOT NULL,
  `raceID` int(11) NOT NULL,
  `datasetname` varchar(255) NOT NULL,
  PRIMARY KEY (`dataID`),
  KEY `addedBy` (`addedBy`),
  KEY `raceID` (`raceID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `DataSet`
--

INSERT INTO `DataSet` (`dataID`, `addedBy`, `raceID`, `datasetname`) VALUES
(1, 1, 3, 'ENGG Parking lot');

-- --------------------------------------------------------

--
-- Table structure for table `Member`
--

CREATE TABLE IF NOT EXISTS `Member` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `Member`
--

INSERT INTO `Member` (`userID`, `name`, `title`, `email`, `admin`, `username`, `password`) VALUES
(1, 'indra', 'team lead', 'indrap1@hotmail.com', 1, 'indra', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `Race`
--

CREATE TABLE IF NOT EXISTS `Race` (
  `raceID` int(11) NOT NULL AUTO_INCREMENT,
  `createdby` int(11) NOT NULL,
  `racename` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`raceID`),
  KEY `createdBy` (`createdby`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Race`
--

INSERT INTO `Race` (`raceID`, `createdby`, `racename`, `location`, `description`) VALUES
(1, 1, 'test', 'testloc', 'this is a test yo'),
(2, 1, 'another test', 'Moon', 'And back'),
(3, 1, 'General', 'N/A', 'Default race all unset data sets go to.');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `DataPoint`
--
ALTER TABLE `DataPoint`
  ADD CONSTRAINT `DataPoint_ibfk_1` FOREIGN KEY (`dataID`) REFERENCES `DataSet` (`dataID`);

--
-- Constraints for table `DataSet`
--
ALTER TABLE `DataSet`
  ADD CONSTRAINT `DataSet_ibfk_2` FOREIGN KEY (`addedBy`) REFERENCES `Member` (`userID`),
  ADD CONSTRAINT `DataSet_ibfk_3` FOREIGN KEY (`raceID`) REFERENCES `Race` (`raceID`);

--
-- Constraints for table `Race`
--
ALTER TABLE `Race`
  ADD CONSTRAINT `Race_ibfk_1` FOREIGN KEY (`createdBy`) REFERENCES `Member` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Race_ibfk_2` FOREIGN KEY (`createdby`) REFERENCES `Member` (`userID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

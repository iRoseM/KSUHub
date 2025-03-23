-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 23, 2025 at 12:33 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ksuhubdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminuser`
--

CREATE TABLE `adminuser` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `clubID` int(11) NOT NULL,
  `clubName` varchar(255) NOT NULL,
  `clubDescription` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventID` int(11) NOT NULL,
  `clubID` int(11) DEFAULT NULL,
  `eventName` varchar(255) NOT NULL,
  `eventDescription` varchar(225) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membershipID` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `clubID` int(11) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `committee` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

CREATE TABLE `record` (
  `membershipID` int(11) NOT NULL,
  `voulnteeringID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `studentuser`
--

CREATE TABLE `studentuser` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `phoneNo` varchar(15) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `studyingLevel` varchar(50) DEFAULT NULL,
  `bio` varchar(225) NOT NULL,
  `volunteeringHours` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `volunteeringhours`
--

CREATE TABLE `volunteeringhours` (
  `membershipID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `volunteeringID` int(11) NOT NULL,
  `totalHours` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `workDescription` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminuser`
--
ALTER TABLE `adminuser`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `clubID` (`clubID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `clubID` (`clubID`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`membershipID`),
  ADD UNIQUE KEY `membershipID` (`membershipID`,`email`),
  ADD KEY `email` (`email`),
  ADD KEY `clubID` (`clubID`);

--
-- Indexes for table `record`
--
ALTER TABLE `record`
  ADD PRIMARY KEY (`membershipID`,`voulnteeringID`,`email`),
  ADD KEY `email` (`email`,`membershipID`,`voulnteeringID`);

--
-- Indexes for table `studentuser`
--
ALTER TABLE `studentuser`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `volunteeringhours`
--
ALTER TABLE `volunteeringhours`
  ADD PRIMARY KEY (`membershipID`,`email`,`volunteeringID`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminuser`
--
ALTER TABLE `adminuser`
  MODIFY `clubID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership`
--
ALTER TABLE `membership`
  MODIFY `membershipID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`clubID`) REFERENCES `adminuser` (`clubID`) ON DELETE CASCADE;

--
-- Constraints for table `membership`
--
ALTER TABLE `membership`
  ADD CONSTRAINT `membership_ibfk_1` FOREIGN KEY (`email`) REFERENCES `studentuser` (`email`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_ibfk_2` FOREIGN KEY (`clubID`) REFERENCES `adminuser` (`clubID`) ON DELETE CASCADE;

--
-- Constraints for table `record`
--
ALTER TABLE `record`
  ADD CONSTRAINT `record_ibfk_1` FOREIGN KEY (`email`,`membershipID`,`voulnteeringID`) REFERENCES `volunteeringhours` (`email`, `membershipID`, `volunteeringID`);

--
-- Constraints for table `volunteeringhours`
--
ALTER TABLE `volunteeringhours`
  ADD CONSTRAINT `volunteeringhours_ibfk_1` FOREIGN KEY (`membershipID`) REFERENCES `membership` (`membershipID`) ON DELETE CASCADE,
  ADD CONSTRAINT `volunteeringhours_ibfk_2` FOREIGN KEY (`email`) REFERENCES `studentuser` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

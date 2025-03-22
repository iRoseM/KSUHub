-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 20, 2025 at 11:42 PM
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
  `clubID` int(11) NOT NULL,
  `email` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `clubName` varchar(225) DEFAULT NULL,
  `clubDescription` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `eventID` int(11) NOT NULL,
  `clubID` int(11) NOT NULL,
  `eventName` varchar(225) DEFAULT NULL,
  `eventDescription` varchar(225) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `eventparticipation`
--

CREATE TABLE `eventparticipation` (
  `email` varchar(255) NOT NULL,
  `eventID` int(11) NOT NULL,
  `participationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `membershipID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT NULL,
  `committee` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `studentuser`
--

CREATE TABLE `studentuser` (
  `email` varchar(225) NOT NULL,
  `clubID` int(11) NOT NULL,
  `fullName` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `phoneNo` varchar(15) NOT NULL,
  `college` varchar(225) NOT NULL,
  `bio` varchar(225) DEFAULT NULL,
  `studyingLevel` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `volunteerhours`
--

CREATE TABLE `volunteerhours` (
  `volunteeringID` int(11) NOT NULL,
  `membershipID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `totalHours` int(11) DEFAULT '0',
  `date` date DEFAULT NULL,
  `workDescription` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminuser`
--
ALTER TABLE `adminuser`
  ADD PRIMARY KEY (`clubID`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `clubID` (`clubID`);

--
-- Indexes for table `eventparticipation`
--
ALTER TABLE `eventparticipation`
  ADD PRIMARY KEY (`email`,`eventID`),
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`membershipID`,`email`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `studentuser`
--
ALTER TABLE `studentuser`
  ADD PRIMARY KEY (`email`),
  ADD KEY `clubID` (`clubID`);

--
-- Indexes for table `volunteerhours`
--
ALTER TABLE `volunteerhours`
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
-- Constraints for dumped tables
--

--
-- Constraints for table `eventparticipation`
--
ALTER TABLE `eventparticipation`
  ADD CONSTRAINT `eventparticipation_ibfk_1` FOREIGN KEY (`email`) REFERENCES `studentuser` (`email`),
  ADD CONSTRAINT `eventparticipation_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`);

--
-- Constraints for table `membership`
--
ALTER TABLE `membership`
  ADD CONSTRAINT `membership_ibfk_1` FOREIGN KEY (`email`) REFERENCES `studentuser` (`email`);

--
-- Constraints for table `volunteerhours`
--
ALTER TABLE `volunteerhours`
  ADD CONSTRAINT `volunteerhours_ibfk_1` FOREIGN KEY (`membershipID`,`email`) REFERENCES `membership` (`membershipID`, `email`),
  ADD CONSTRAINT `volunteerhours_ibfk_2` FOREIGN KEY (`email`) REFERENCES `studentuser` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

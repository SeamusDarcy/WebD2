-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 29, 2025 at 02:46 AM
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
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `Books`
--

CREATE TABLE `Books` (
  `isbn` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `Edition` int(11) NOT NULL,
  `Year` int(5) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `Reserved` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Books`
--

INSERT INTO `Books` (`isbn`, `title`, `author`, `Edition`, `Year`, `categoryId`, `Reserved`) VALUES
('093-403992', 'Computers in Business', 'Alicia Oneill', 3, 1997, 2, 0),
('23472-8729', 'Exploring Peru', 'Stephanie Birchi', 4, 2005, 5, 0),
('237-34823', 'Business Strategy', 'Joe Peppard', 2, 2002, 2, 0),
('23U8-923849', 'A guide to nutrition', 'John Thorpe', 2, 1997, 1, 0),
('2983-3494', 'Cooking for children', 'Anabelle Sharpe', 1, 2003, 7, 0),
('82n8-308', 'computers for idiots', 'Susan O\'Neill', 5, 1998, 4, 0),
('9823-23984', 'My life in picture', 'Kevin Graham', 8, 2004, 3, 0),
('9823-2403-0', 'DaVinci Code', 'Dan Brown', 1, 2003, 8, 0),
('9823-98345', 'How to cook Italian food', 'Jamie Oliver', 2, 2005, 7, 0),
('9823-98487', 'Optimising your business', 'Cleo Blair', 1, 2001, 2, 0),
('98234-029384', 'My ranch in Texas', 'George Bush', 1, 2005, 3, 1),
('988745-234', 'Tara Road', 'Maeve Binchy', 2, 2002, 8, 0),
('993-004-00', 'My life in bits', 'John Smith', 1, 2001, 3, 0),
('9987-0039882', 'Shooting History', 'Jon Snow', 1, 2003, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE `Category` (
  `categoryId` int(11) NOT NULL,
  `categoryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`categoryId`, `categoryName`) VALUES
(1, 'Health'),
(2, 'Business'),
(3, 'Biography'),
(4, 'Technology'),
(5, 'Travel'),
(6, 'Self-Help'),
(7, 'Cooking'),
(8, 'Fiction');

-- --------------------------------------------------------

--
-- Table structure for table `reservedBooks`
--

CREATE TABLE `reservedBooks` (
  `username` varchar(50) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `reservedDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `Surname` varchar(100) NOT NULL,
  `AddressLine` varchar(50) NOT NULL,
  `AddressLine2` varchar(50) NOT NULL,
  `City` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`username`, `password`, `FirstName`, `Surname`, `AddressLine`, `AddressLine2`, `City`, `email`, `mobile`) VALUES
('admin', 'password', 'admin', '', '', '', '', 'admin@gmail.com', '0832087874'),
('alanjmckenna', 't12345', 'Alan ', 'McKenna', '38 Cranley Road', 'Fairview', 'Dublin', 'alanmckenna@gmail.com', '856625567'),
('joecrotty', 'k7b899', 'Joseph ', 'Crotty', 'Apt 5 Clyde Rd', 'Donnybrook', 'Dublin', 'joecrotty@gmail.com', '876554456'),
('tommy100', '123456', 'Tom ', 'Behan', '14 hyde Rd', 'Dalkey', 'Dublin', 'tombehan@gmail.com', '876738782');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Books`
--
ALTER TABLE `Books`
  ADD PRIMARY KEY (`isbn`),
  ADD KEY `books_category_fk` (`categoryId`);

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`categoryId`);

--
-- Indexes for table `reservedBooks`
--
ALTER TABLE `reservedBooks`
  ADD PRIMARY KEY (`isbn`),
  ADD KEY `reserved_user_fk` (`username`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Category`
--
ALTER TABLE `Category`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Books`
--
ALTER TABLE `Books`
  ADD CONSTRAINT `books_category_fk` FOREIGN KEY (`categoryId`) REFERENCES `Category` (`categoryId`);

--
-- Constraints for table `reservedBooks`
--
ALTER TABLE `reservedBooks`
  ADD CONSTRAINT `reserved_book_fk` FOREIGN KEY (`isbn`) REFERENCES `Books` (`isbn`),
  ADD CONSTRAINT `reserved_user_fk` FOREIGN KEY (`username`) REFERENCES `User` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

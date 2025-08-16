-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2025 at 06:28 AM
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
-- Database: `student_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_classes` int(11) NOT NULL,
  `present` int(11) NOT NULL,
  `absent` int(11) NOT NULL,
  `percentage` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `total_classes`, `present`, `absent`, `percentage`) VALUES
(1, 5000, 0, 0, 0, 0),
(2, 7777, 0, 0, 0, 0),
(3, 8888, 0, 0, 0, 0),
(4, 9999, 0, 0, 0, 0),
(5, 1111, 0, 0, 0, 0),
(6, 666, 0, 0, 0, 0),
(7, 789, 0, 0, 0, 0),
(8, 88, 0, 0, 0, 0),
(9, 123, 0, 0, 0, 0),
(17, 78787, 0, 0, 0, 0),
(18, 9898, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_record`
--

CREATE TABLE `attendance_record` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_date` date NOT NULL,
  `status` enum('Present','Absent','Excused') NOT NULL,
  `subject` varchar(100) NOT NULL,
  `total_classes` int(11) NOT NULL DEFAULT 0,
  `present` int(11) NOT NULL DEFAULT 0,
  `absent` int(11) NOT NULL DEFAULT 0,
  `percentage` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_record`
--

INSERT INTO `attendance_record` (`id`, `student_id`, `class_date`, `status`, `subject`, `total_classes`, `present`, `absent`, `percentage`) VALUES
(1, 2002, '2025-02-19', 'Present', 'Math', 1, 1, 0, 100),
(2, 2002, '2025-02-20', 'Absent', 'Math', 2, 1, 1, 50),
(3, 2003, '2025-02-21', 'Present', 'Math', 1, 1, 0, 100),
(4, 2001, '2025-02-20', 'Present', 'Math', 1, 1, 0, 100),
(5, 2001, '2025-02-21', 'Present', 'Math', 2, 2, 0, 100),
(6, 2002, '2025-02-22', 'Present', 'Math', 2, 2, 0, 100),
(7, 2003, '2025-02-22', 'Present', 'Math', 2, 2, 0, 100),
(8, 2004, '2025-02-22', 'Present', 'Math', 3, 3, 0, 100),
(9, 2005, '2025-02-22', 'Present', 'Math', 4, 4, 0, 100),
(10, 2011, '2025-02-22', 'Present', 'Math', 5, 5, 0, 100),
(11, 2013, '2025-02-22', 'Present', 'Math', 6, 6, 0, 100),
(12, 2015, '2025-02-22', 'Present', 'Math', 7, 7, 0, 100),
(13, 2007, '2025-02-23', 'Present', 'Math', 1, 1, 0, 100),
(14, 2009, '2025-02-23', 'Present', 'Math', 2, 2, 0, 100),
(15, 2011, '2025-02-23', 'Present', 'Math', 6, 6, 0, 100),
(16, 2012, '2025-02-23', 'Present', 'Math', 7, 7, 0, 100),
(17, 2015, '2025-02-23', 'Present', 'Math', 8, 8, 0, 100),
(18, 2018, '2025-02-23', 'Present', 'Math', 9, 9, 0, 100),
(19, 2019, '2025-02-23', 'Present', 'Math', 10, 10, 0, 100),
(20, 2021, '2025-02-23', 'Present', 'Math', 11, 11, 0, 100),
(21, 2023, '2025-02-23', 'Present', 'Math', 12, 12, 0, 100),
(22, 2001, '2025-02-28', 'Present', 'Math', 2, 2, 0, 100),
(23, 2002, '2025-02-28', 'Present', 'Math', 2, 2, 0, 100),
(24, 2003, '2025-02-28', 'Present', 'Math', 2, 2, 0, 100),
(25, 2004, '2025-02-28', 'Present', 'Math', 4, 4, 0, 100),
(26, 2005, '2025-02-28', 'Present', 'Math', 5, 5, 0, 100),
(27, 2008, '2025-02-28', 'Present', 'Math', 6, 6, 0, 100),
(28, 2009, '2025-02-28', 'Present', 'Math', 3, 3, 0, 100),
(29, 2011, '2025-02-28', 'Present', 'Math', 6, 6, 0, 100),
(30, 2012, '2025-02-28', 'Present', 'Math', 8, 8, 0, 100),
(31, 2013, '2025-02-28', 'Present', 'Math', 7, 7, 0, 100),
(32, 2014, '2025-02-28', 'Present', 'Math', 8, 8, 0, 100),
(33, 2015, '2025-02-28', 'Present', 'Math', 8, 8, 0, 100),
(34, 2016, '2025-02-28', 'Present', 'Math', 9, 9, 0, 100),
(35, 2017, '2025-02-28', 'Present', 'Math', 10, 10, 0, 100),
(36, 2018, '2025-02-28', 'Present', 'Math', 10, 10, 0, 100),
(37, 2019, '2025-02-28', 'Present', 'Math', 11, 11, 0, 100),
(38, 2020, '2025-02-28', 'Present', 'Math', 12, 12, 0, 100),
(39, 2021, '2025-02-28', 'Present', 'Math', 12, 12, 0, 100),
(40, 2022, '2025-02-28', 'Present', 'Math', 13, 13, 0, 100),
(41, 2023, '2025-02-28', 'Present', 'Math', 13, 13, 0, 100),
(42, 2024, '2025-02-28', 'Present', 'Math', 14, 14, 0, 100),
(43, 2004, '2025-02-27', 'Present', 'Math', 4, 4, 0, 100),
(44, 2007, '2025-02-27', 'Present', 'Math', 2, 2, 0, 100),
(45, 2008, '2025-02-27', 'Present', 'Math', 7, 7, 0, 100),
(46, 2012, '2025-02-27', 'Present', 'Math', 8, 8, 0, 100),
(47, 2014, '2025-02-27', 'Present', 'Math', 9, 9, 0, 100),
(48, 2006, '2025-02-24', 'Present', 'Math', 1, 1, 0, 100),
(49, 2007, '2025-02-24', 'Present', 'Math', 2, 2, 0, 100),
(50, 2008, '2025-02-24', 'Present', 'Math', 7, 7, 0, 100),
(51, 2011, '2025-02-24', 'Present', 'Math', 6, 6, 0, 100),
(52, 2012, '2025-02-24', 'Present', 'Math', 8, 8, 0, 100),
(53, 2003, '2025-02-26', 'Present', 'Math', 2, 2, 0, 100),
(54, 2002, '2025-02-10', 'Present', 'Math', 2, 2, 0, 100),
(55, 2004, '2025-02-06', 'Present', 'Math', 4, 4, 0, 100),
(56, 2014, '2025-02-14', 'Present', 'Math', 9, 9, 0, 100),
(57, 2002, '2025-03-12', 'Present', 'Math', 2, 2, 0, 100),
(58, 2003, '2025-03-12', 'Present', 'Math', 2, 2, 0, 100),
(59, 2006, '2025-03-12', 'Present', 'Math', 2, 2, 0, 100),
(60, 2007, '2025-03-12', 'Present', 'Math', 2, 2, 0, 100),
(61, 2008, '2025-03-12', 'Present', 'Math', 7, 7, 0, 100),
(62, 88, '2025-02-18', '', '', 0, 0, 0, 0),
(63, 123, '2025-02-18', '', '', 0, 0, 0, 0),
(64, 666, '2025-02-18', '', '', 0, 0, 0, 0),
(65, 789, '2025-02-18', '', '', 0, 0, 0, 0),
(66, 1111, '2025-02-18', '', '', 0, 0, 0, 0),
(67, 5000, '2025-02-18', '', '', 0, 0, 0, 0),
(68, 7777, '2025-02-18', '', '', 0, 0, 0, 0),
(69, 8888, '2025-02-18', '', '', 0, 0, 0, 0),
(70, 9999, '2025-02-18', '', '', 0, 0, 0, 0),
(71, 88, '2025-02-14', 'Present', 'Math', 1, 1, 0, 100),
(72, 123, '2025-02-14', 'Present', 'Math', 1, 1, 0, 100),
(73, 468, '2025-02-14', 'Present', 'Math', 2, 2, 0, 100),
(74, 789, '2025-02-20', 'Present', 'Math', 1, 1, 0, 100),
(75, 898, '2025-02-20', 'Present', 'Math', 2, 2, 0, 100),
(76, 1111, '2025-02-20', 'Present', 'Math', 1, 1, 0, 100),
(77, 78787, '2025-02-19', '', '', 0, 0, 0, 0),
(78, 9898, '2025-02-19', '', '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `department`) VALUES
(88, 'mdmahamudulislamriad', 'daa.attrack01@gmail.com', 'Computer Science'),
(123, 'vjhkjh', 'daa.attrack01@gmail.com', 'Computer Science'),
(468, 'fdfdsf', 'asd@GMAIL.COM', 'Electrical Engineering'),
(666, 'gggg', 'ghf@gmail.com', 'Computer Science'),
(789, 'gggg', 'ghf@gmail.com', 'Computer Science'),
(898, 'RIAD', 'mrx@gmail.com', 'Mechanical Engineering'),
(1000, 'xdd', 'mrx@gmail.com', 'Computer Science'),
(1111, 'fgfdg', 'fdgfdg@gmail.com', 'Computer Science'),
(2001, 'Kazi Nahin Mahmud', 'kazi@gmail.com', 'Computer Science'),
(2002, 'Mazia Sultana Mim', 'mazia.mim@gmail.com', 'Electrical Engineering'),
(2003, 'Jahana Sultana Nipa', 'nipajahana@gmail.com', 'Computer Science'),
(2004, 'Md. Muzahirur Rahman', 'muzahir@rahman.com', 'Computer Science'),
(2005, 'Md. Abdur Rahman', 'abdur.rahman@gmail.com', 'Mechanical Engineering'),
(2006, 'Md Mahamudul Islam Riad', 'mahmud@university.com', 'Computer Science'),
(2007, 'Emon Hawlader', 'emon.hawlader@gmail.com', 'Mechanical Engineering'),
(2008, 'Subarna Roy', 'subarna.roy@domain.com', 'Computer Science'),
(2009, 'Anika Tahmin', 'anika.tahmin@gmail.com', 'Electrical Engineering'),
(2010, 'Md. Mahatab Hossain', 'mahatab.hossain@gmail.com', 'Civil Engineering'),
(2011, 'Jasia Hasan Munum', 'jasia.munum@gmail.com', 'Computer Science'),
(2012, 'Aiman Al Mahmud', 'aiman.almhmud@gmail.com', 'Mechanical Engineering'),
(2013, 'Md. Najmul Sakib', 'najmul.sakib@gmail.com', 'Electrical Engineering'),
(2014, 'Mst. Suraya Khatun Hasiba', 'suraya.khatun@gmail.com', 'Computer Science'),
(2015, 'Tahmid Rahman Siam', 'tahmid.siam@gmail.com', 'Mechanical Engineering'),
(2016, 'Gazi Enamul Haque Ratu', 'gazi.ratu@domain.com', 'Civil Engineering'),
(2017, 'Rajonul Islam', 'rajonul.islam@gmail.com', 'Computer Science'),
(2018, 'Al Mamun Shaikh', 'mamun.shaikh@gmail.com', 'Mechanical Engineering'),
(2019, 'Laboni Sarkar', 'laboni.sarkar@gmail.com', 'Computer Science'),
(2020, 'Kazi Ismat Zerin', 'kazi.zerin@gmail.com', 'Electrical Engineering'),
(2021, 'Motaleb Hossain Shimu', 'motaleb.shimu@gmail.com', 'Mechanical Engineering'),
(2022, 'Ratul Hasan Alif', 'ratul.alif@gmail.com', 'Civil Engineering'),
(2023, 'Mehnaz Ahmed', 'mehnaz.ahmed@gmail.com', 'Computer Science'),
(2024, 'Md.Rohit Hasan', 'rohit.hasan@gmail.com', 'Mechanical Engineering'),
(5000, 'testyz', 'asd@GMAIL.COM', 'Computer Science'),
(7777, 'captainxxxxxxx', 'captainxxxxxx@gmail.com', 'Electrical Engineering'),
(8888, 'ttttt', 'ttt@gmail.com', 'Computer Science'),
(9898, 'hhhhhhhhhhhhhhh', 'daa.attrack01@gmail.com', 'Computer Science'),
(9999, 'dsfsdf', 'sef@gmail.com', 'Computer Science'),
(78787, 'mdmahamudulislamriad', 'daa.attrack01@gmail.com', 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `private_code` varchar(100) NOT NULL,
  `role` enum('professor','CR') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `private_code`, `role`) VALUES
(1, 'asifsir', '009', 'professor'),
(2, 'jasia', 'j009', 'CR');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `attendance_record`
--
ALTER TABLE `attendance_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `attendance_record`
--
ALTER TABLE `attendance_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220321029;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `attendance_record`
--
ALTER TABLE `attendance_record`
  ADD CONSTRAINT `attendance_record_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

ALTER TABLE `attendance_record`
ADD COLUMN `subject` VARCHAR(100) NOT NULL AFTER `status`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 04, 2024 at 09:03 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `classes`
--

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `login` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `firstname` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `lastname` varchar(300) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `password`, `email`, `firstname`, `lastname`) VALUES
(12, 'Ryan', '$2y$10$smpcHcnprmOyFA0S87G0.uv1c30i1IB84ym1k4IQDltsEa2no2bO.', 'ryan@laplateforme.io', 'Ryan', 'Wayne'),
(13, 'Tarok', '$2y$10$vEnnt5ZXiqELpOmoTpM.SeNnq9oXxgasOKxMiaS1/Ceu8OpjiCWi2', 'tarok@web.com', 'Tarok', 'To'),
(24, 'END', '$2y$10$m7wKbjoWAEOHpVIDINetVeA8hZnPJQ75ymHwaFQ1/9RhMzMCXLqUe', 'natsu@gmail.com', 'Natsu', 'Dragnir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

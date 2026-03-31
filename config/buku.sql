-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 31, 2026 at 06:47 AM
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
-- Database: `projek_ukk`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(150) NOT NULL,
  `kelas_buku` varchar(50) DEFAULT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `foto` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `judul`, `penulis`, `kelas_buku`, `stok`, `foto`, `created_at`) VALUES
(1, 'PABP', 'Eni Nuraeni', 'Kelas 10', 27, '699838e67eb9e.jpg', '2026-02-20 10:35:18'),
(2, 'PABP', 'Nurdini', 'Kelas 11', 29, '69983db2103e1.jpg', '2026-02-20 10:55:46'),
(3, 'PABP', 'Wandi Herpiandi', 'Kelas 12', 30, '699840e252d48.jpg', '2026-02-20 11:09:22'),
(4, 'Sejarah', 'Sudarmi', 'Kelas 11', 27, '699840feef9c1.jpg', '2026-02-20 11:09:50'),
(5, 'Sejarah', 'Laila F. Umami', 'Kelas 12', 25, '699841573c9c3.jpg', '2026-02-20 11:11:19'),
(6, 'Sejarah', 'Karyadi Nugroho', 'Kelas 10', 28, '699841790bcfb.png', '2026-02-20 11:11:53'),
(7, 'PPKN', 'Zubedi', 'Kelas 10', 29, '69984237d7632.png', '2026-02-20 11:15:03'),
(8, 'PPKN', 'Berti Sagendra', 'Kelas 11', 26, '6998427dd766b.png', '2026-02-20 11:16:13'),
(9, 'PPKN', 'Dwi Harti', 'Kelas 12', 20, '6998429b4d402.jpg', '2026-02-20 11:16:43'),
(10, 'Bahasa Inggris', 'indang Retno', 'Kelas 10', 28, '699842c279022.jpg', '2026-02-20 11:17:22'),
(11, 'Bahasa Inggris', 'Hamdan Tri Atmaja', 'Kelas 11', 25, '699842e54e456.jpg', '2026-02-20 11:17:57'),
(12, 'Bahasa Inggris', 'Pristiadi Utomo', 'Kelas 12', 28, '699843142824d.jpg', '2026-02-20 11:18:44'),
(13, 'Demografi Umum', 'Indayatmi Suwati', 'Umum', 5, '699843416f705.jpg', '2026-02-20 11:19:29'),
(14, 'Ilmu Penyakit Umum &amp; Kejiwaan', 'Ahmad Noor Hadi', 'Umum', 10, '699843791385c.png', '2026-02-20 11:20:25'),
(15, 'Berdamai Dengan Emosi', 'Sudarman Kurnawan', 'Umum', 7, '69984cbf3d6b4.jpg', '2026-02-20 11:59:59'),
(16, 'PJOK', 'Rifky Nurul Huda ', 'Kelas 12', 25, '69984d08cd448.jpg', '2026-02-20 12:01:12'),
(17, 'PJOK', 'Nanang Mulyana', 'Kelas 11', 23, '69984d4100574.jpeg', '2026-02-20 12:02:09'),
(18, 'PJOK', 'Bambang Eko Sugihartadi', 'Kelas 10', 30, '69984d73ef6d8.jpg', '2026-02-20 12:02:59'),
(19, 'Biologi Umum', 'Agus Kasuno Himalaya ', 'Umum', 10, '69984da4a93a8.jpeg', '2026-02-20 12:03:48'),
(20, 'Matematika', 'Rina Andriani', 'Kelas 12', 31, '69984dcbd18a0.jpg', '2026-02-20 12:04:27'),
(21, 'Matematika', 'Eko Titis Prasetyo', 'Kelas 11', 32, '69984e785b6b0.jpg', '2026-02-20 12:05:05'),
(22, 'Matematika', 'Siti Munawaroh', 'Kelas 10', 34, '69984e1065b8a.jpg', '2026-02-20 12:05:36'),
(23, '25 Kisah Nabi dan Rasul', 'Hendra Wijaya', 'Cerita', 12, '69984eae609ba.jpg', '2026-02-20 12:08:14'),
(24, 'Muslim Cilik Teladan', 'Ade Suherman', 'Cerita', 16, '69984ed0ea1df.jpg', '2026-02-20 12:08:48'),
(25, 'Bahasa Indonesia', 'Kurniawan Sanjoyo', 'Kelas 12', 20, '69984f0aba380.jpg', '2026-02-20 12:09:46'),
(26, 'Bahasa Indonesia', 'Ratna Sari', 'Kelas 11', 30, '69984f4da6d24.png', '2026-02-20 12:10:53'),
(27, 'Bahasa Indonesia', 'Yono Sanjaya Wirdayanto', 'Kelas 10', 31, '69984f6cb6a93.jpeg', '2026-02-20 12:11:24'),
(28, 'Bahasa Jepang', 'Siti Kusmawati ', 'Kelas 11', 27, '69984f9819607.jpg', '2026-02-20 12:12:08'),
(29, 'Proyek IPAS', 'Sartono Wirodikromo', 'Kelas 10', 25, '6998502275a53.jpg', '2026-02-20 12:14:26'),
(30, 'Cerita Rakyat Nusantra', 'Teo Sukoco', 'Cerita', 7, '699850776958a.jpg', '2026-02-20 12:15:51'),
(31, 'Psikologi Umum', 'Rohana Kusumawati', 'Umum', 12, '699850a97a943.jpg', '2026-02-20 12:16:41'),
(32, 'Si Tudung Merah', 'Ika Setyowati', 'Cerita', 4, '699850f09840a.jpg', '2026-02-20 12:17:52'),
(33, 'Kisah Bumi', 'Riski Dian Permana', 'Cerita', 3, '6998513799c31.jpg', '2026-02-20 12:19:03'),
(34, 'Kisah Tiga Saudara', 'Gtot Harmanto', 'Cerita', 14, '6998518341db3.jpg', '2026-02-20 12:20:19'),
(35, 'Pada Zaman Dahulu ', 'Nunung Nurhayanti', 'Cerita', 7, '699851fc45e73.jpg', '2026-02-20 12:22:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

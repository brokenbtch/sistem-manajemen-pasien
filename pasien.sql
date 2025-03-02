-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2025 at 03:57 PM
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
-- Database: `pasien`
--

-- --------------------------------------------------------

--
-- Table structure for table `antrian_pasien`
--

CREATE TABLE `antrian_pasien` (
  `id_antrian` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `tanggal_antrian` date DEFAULT NULL,
  `status` enum('menunggu','diperiksa','selesai') DEFAULT 'menunggu',
  `waktu_daftar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `antrian_pasien`
--

INSERT INTO `antrian_pasien` (`id_antrian`, `id_pasien`, `tanggal_antrian`, `status`, `waktu_daftar`) VALUES
(1, 123, '2025-02-28', 'selesai', '2025-02-28 13:55:56'),
(2, 123123, '2025-02-28', 'selesai', '2025-02-28 14:01:57'),
(3, 1, '2025-02-28', 'selesai', '2025-02-28 14:16:07'),
(4, 2, '2025-02-28', 'selesai', '2025-02-28 14:18:34'),
(5, 444, '2025-02-28', 'selesai', '2025-02-28 14:25:57'),
(8, 123152124, '2025-02-28', 'selesai', '2025-02-28 14:29:54'),
(9, 123213123, '2025-02-28', 'menunggu', '2025-02-28 14:30:01'),
(10, 12312312, '2025-02-28', 'selesai', '2025-02-28 14:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(5) NOT NULL,
  `Nama` varchar(100) NOT NULL,
  `Jenis_kelamin` char(1) NOT NULL,
  `Tanggal_lahir` date NOT NULL,
  `No_telepon` varchar(13) NOT NULL,
  `Alamat` varchar(150) NOT NULL,
  `Keluhan_pasien` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `Nama`, `Jenis_kelamin`, `Tanggal_lahir`, `No_telepon`, `Alamat`, `Keluhan_pasien`) VALUES
(1, 'a', 'P', '2004-03-28', '2312412312', 'Canggu', 'Sakit Hati'),
(2, 'Grace', 'P', '2025-02-28', '1235124', 'Canggu', 'Sakit hati'),
(123, 'ardi Budi suka macan', 'P', '2332-11-11', '123123', 'pa ya', 'Sakit pantat'),
(444, '123', 'L', '0001-12-31', 's23123', 'xcvdsf', 'aada'),
(123123, 'naren', 'L', '2323-11-11', '1235124', 'sukawati', 'muntah paku'),
(12312312, 'Iki', 'L', '2025-01-21', '1231321231231', 'Banjarmasin', 'Sakit hati 9 tahun gak jadian'),
(123152124, 'sdawdad', 'L', '2014-06-18', '1231244124', 'awdkawd', 'awdadawd'),
(123213123, '123123', 'L', '2013-08-14', '1235124', 'asdawd', 'adadadad');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id_pemeriksaan` int(11) NOT NULL,
  `id_antrian` int(11) DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `resep` text DEFAULT NULL,
  `catatan_dokter` text DEFAULT NULL,
  `waktu_pemeriksaan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id_pemeriksaan`, `id_antrian`, `diagnosa`, `resep`, `catatan_dokter`, `waktu_pemeriksaan`) VALUES
(1, 1, 'suka beganag\r\n\r\ntidak minum air\r\nsuka makan micin', 'syanida 100gram', 'jangan suka begadang ya dik', '2025-02-28 13:59:11'),
(2, 3, 'Sakit mental\r\n', 'Syanida 100Gram', 'Jangan sama yang beda agama, tapi kalo ama gw gpp', '2025-02-28 14:16:46'),
(3, 4, 'SAkit Mental', 'syanida 100 Gram', 'jangan sama yg beda agama, tapi sama gw gpp', '2025-02-28 14:19:17'),
(4, 2, 'awd', 'awdaw', 'dawd', '2025-02-28 14:23:15'),
(5, 5, 'adw', 'awd', 'awd', '2025-02-28 14:26:07'),
(6, 8, 'ad', 'wefdwf', 'wefef', '2025-02-28 14:30:17'),
(7, 10, 'adw', 'Syanida', 'jangan jadi badut', '2025-02-28 14:54:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antrian_pasien`
--
ALTER TABLE `antrian_pasien`
  ADD PRIMARY KEY (`id_antrian`),
  ADD KEY `id_pasien` (`id_pasien`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indexes for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD KEY `id_antrian` (`id_antrian`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antrian_pasien`
--
ALTER TABLE `antrian_pasien`
  MODIFY `id_antrian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123213124;

--
-- AUTO_INCREMENT for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id_pemeriksaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `antrian_pasien`
--
ALTER TABLE `antrian_pasien`
  ADD CONSTRAINT `antrian_pasien_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`);

--
-- Constraints for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_antrian`) REFERENCES `antrian_pasien` (`id_antrian`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

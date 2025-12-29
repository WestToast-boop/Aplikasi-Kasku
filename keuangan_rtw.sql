-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 27, 2025 at 02:03 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `keuangan_rtw`
--

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `bId` int NOT NULL,
  `userId` int NOT NULL,
  `tId` int NOT NULL,
  `bTanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `bStatus` enum('Verifikasi','Disetujui','Ditolak') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bFoto` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`bId`, `userId`, `tId`, `bTanggal`, `bStatus`, `bFoto`) VALUES
(24, 2, 27, '2025-12-27 11:56:05', 'Disetujui', 'bukti_1766836565_113.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `pId` int NOT NULL,
  `pKeterangan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pTanggal` date NOT NULL,
  `pJumlah` decimal(15,2) NOT NULL,
  `pStatus` enum('Disetujui','Ditolak','Diproses') COLLATE utf8mb4_general_ci NOT NULL,
  `pAlasan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pDetail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `digunakan` enum('Ya','Tidak') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan`
--

INSERT INTO `pengajuan` (`pId`, `pKeterangan`, `pTanggal`, `pJumlah`, `pStatus`, `pAlasan`, `pDetail`, `digunakan`) VALUES
(24, 'Uang Keamanan Bulanan Tahun 2025', '2025-12-25', 50000.00, 'Disetujui', '', 'Uang Keamanan', 'Tidak');

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `tId` int NOT NULL,
  `t_tanggal` date NOT NULL,
  `t_jumlah` decimal(15,2) NOT NULL,
  `no_rek` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `photo` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `atas_nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `t_keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`tId`, `t_tanggal`, `t_jumlah`, `no_rek`, `photo`, `atas_nama`, `t_keterangan`, `pId`) VALUES
(27, '2025-12-27', 100000.00, '123123', 'e', 'e', 'e', 24);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userId` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('ketua','bendahara','warga') COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `username`, `role`, `alamat`, `password`, `created_at`) VALUES
(1, 'Kemas', 'ketua', 'Bukit Palem Permai', 'e10adc3949ba59abbe56e057f20f883e', '2025-11-25 02:39:33'),
(2, 'Faiz Annabil', 'bendahara', 'Tanjung Pinang', '81dc9bdb52d04dc20036dbd8313ed055', '2025-11-25 02:39:33'),
(3, 'Bunda Rahma', 'warga', 'dibumi', '202cb962ac59075b964b07152d234b70', '2025-12-02 04:43:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`bId`),
  ADD UNIQUE KEY `uniq_user_tagihan` (`userId`,`tId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `tId` (`tId`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`pId`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`tId`),
  ADD KEY `fk_tagihan_pengajuan` (`pId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `bId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `pId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `tId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`tId`) REFERENCES `tagihan` (`tId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `fk_tagihan_pengajuan` FOREIGN KEY (`pId`) REFERENCES `pengajuan` (`pId`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

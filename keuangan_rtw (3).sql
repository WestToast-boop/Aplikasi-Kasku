-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Des 2025 pada 14.25
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `bId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `tId` int(11) NOT NULL,
  `bTanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `bStatus` enum('Verifikasi','Disetujui','Ditolak') DEFAULT NULL,
  `bFoto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`bId`, `userId`, `tId`, `bTanggal`, `bStatus`, `bFoto`) VALUES
(24, 2, 27, '2025-12-27 11:56:05', 'Disetujui', 'uploads/bukti_1766836565_113.jpg'),
(25, 1, 28, '2025-12-27 15:01:05', 'Ditolak', 'bukti_1766935167_250.png'),
(26, 2, 28, '2025-12-27 15:01:05', 'Disetujui', 'uploads/bukti_1766847714_735.png'),
(27, 3, 28, '2025-12-27 15:01:05', 'Disetujui', 'bukti_1766935199_796.png'),
(28, 3, 27, '2025-12-28 14:27:20', 'Disetujui', 'bukti_1766932040_889.png'),
(29, 1, 29, '2025-12-28 14:30:45', NULL, ''),
(30, 2, 29, '2025-12-28 14:30:46', 'Disetujui', 'bukti_1766932262_287.png'),
(31, 3, 29, '2025-12-28 14:30:46', 'Disetujui', 'bukti_1766935195_817.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan`
--

CREATE TABLE `pengajuan` (
  `pId` int(11) NOT NULL,
  `pKeterangan` varchar(255) NOT NULL,
  `pTanggal` date NOT NULL,
  `pJumlah` decimal(15,2) NOT NULL,
  `jenis_pengajuan` enum('Pemasukan','Pengeluaran') NOT NULL,
  `pStatus` enum('Disetujui','Ditolak','Diproses') NOT NULL,
  `pAlasan` varchar(255) DEFAULT NULL,
  `pDetail` varchar(255) NOT NULL,
  `digunakan` enum('Ya','Tidak') NOT NULL DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan`
--

INSERT INTO `pengajuan` (`pId`, `pKeterangan`, `pTanggal`, `pJumlah`, `jenis_pengajuan`, `pStatus`, `pAlasan`, `pDetail`, `digunakan`) VALUES
(24, 'Uang Keamanan Bulanan Tahun 2025', '2025-12-25', 50000.00, 'Pemasukan', 'Disetujui', '', 'Uang Keamanan', 'Ya'),
(25, 'Iuran lomba 17 an', '2025-12-28', 10.00, 'Pemasukan', 'Disetujui', '', 'Uang 17an', 'Ya'),
(28, '111', '2025-12-29', 1111111.00, 'Pemasukan', 'Diproses', NULL, '9', 'Tidak'),
(29, '1', '2025-12-29', 1.00, 'Pemasukan', 'Diproses', NULL, 'a', 'Tidak'),
(30, 'Ketua Ganteng', '2025-12-29', 100000.00, 'Pemasukan', 'Disetujui', 'Adalah benar', '9', 'Tidak'),
(31, 'kurang', '2025-12-29', 10000.00, 'Pengeluaran', 'Disetujui', '', 'AE', 'Tidak'),
(32, 'test', '2025-12-29', 10000.00, 'Pemasukan', 'Diproses', NULL, 'test', 'Tidak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tagihan`
--

CREATE TABLE `tagihan` (
  `tId` int(11) NOT NULL,
  `t_tanggal` date NOT NULL,
  `t_jumlah` decimal(15,2) NOT NULL,
  `no_rek` char(10) NOT NULL,
  `photo` varchar(150) NOT NULL,
  `atas_nama` varchar(255) NOT NULL,
  `t_keterangan` varchar(255) DEFAULT NULL,
  `pId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tagihan`
--

INSERT INTO `tagihan` (`tId`, `t_tanggal`, `t_jumlah`, `no_rek`, `photo`, `atas_nama`, `t_keterangan`, `pId`) VALUES
(27, '2025-12-27', 100000.00, '123123', 'uploads/e', 'e', 'e', 24),
(28, '2025-12-27', 10000.00, '233232323', 'uploads/qr_1766847665_341.png', 'Faiz', '-', 24),
(29, '2025-12-28', 10000.00, '3213121312', 'qr_1766932245_298.png', 'Faiz Annabil', 'uang iuran untuk 17 an', 25);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `role` enum('ketua','bendahara','warga') NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`userId`, `username`, `role`, `alamat`, `password`, `created_at`) VALUES
(1, 'Kemas', 'ketua', 'Bukit Palem Permai', 'e10adc3949ba59abbe56e057f20f883e', '2025-11-25 02:39:33'),
(2, 'Faiz Annabil', 'bendahara', 'Tanjung Pinang', '81dc9bdb52d04dc20036dbd8313ed055', '2025-11-25 02:39:33'),
(3, 'Bunda Rahma', 'warga', 'dibumi', '202cb962ac59075b964b07152d234b70', '2025-12-02 04:43:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`bId`),
  ADD UNIQUE KEY `uniq_user_tagihan` (`userId`,`tId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `tId` (`tId`);

--
-- Indeks untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`pId`);

--
-- Indeks untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`tId`),
  ADD KEY `fk_tagihan_pengajuan` (`pId`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `bId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `pId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `tId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`tId`) REFERENCES `tagihan` (`tId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `fk_tagihan_pengajuan` FOREIGN KEY (`pId`) REFERENCES `pengajuan` (`pId`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

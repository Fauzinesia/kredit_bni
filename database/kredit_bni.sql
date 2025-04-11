-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 10:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kredit_bni`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_absensi_karyawan`
--

CREATE TABLE `tb_absensi_karyawan` (
  `absensi_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status_absensi` enum('Hadir','Izin','Sakit','Cuti','Alpha') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `lokasi_absensi` varchar(100) DEFAULT NULL,
  `foto_absensi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_absensi_karyawan`
--

INSERT INTO `tb_absensi_karyawan` (`absensi_id`, `user_id`, `tanggal`, `jam_masuk`, `jam_keluar`, `status_absensi`, `keterangan`, `latitude`, `longitude`, `lokasi_absensi`, `foto_absensi`, `created_at`) VALUES
(3, 3, '2025-04-07', '08:17:00', '16:18:00', 'Hadir', 'Hadiroh', -2.98095380, 114.76080960, 'RSUD Abdul Aziz, Jalan Kartini, Marabahan, Barito Kuala, South Kalimantan, Kalimantan, Indonesia', 'uploads/absensi/absensi_20250407091822_67f37c3e04de6.png', '2025-04-07 07:18:22'),
(4, 2, '2025-04-07', '15:38:00', '15:38:00', 'Hadir', 'p', -2.98095310, 114.76079920, 'RSUD Abdul Aziz, Gang Wijaya Kusuma, Marabahan, Barito Kuala, South Kalimantan, Kalimantan, Indonesi', 'uploads/absensi/absensi_20250407093826_67f380f2237e7.png', '2025-04-07 07:38:26');

-- --------------------------------------------------------

--
-- Table structure for table `tb_angsuran_kredit`
--

CREATE TABLE `tb_angsuran_kredit` (
  `angsuran_id` int(11) NOT NULL,
  `no_kredit` varchar(50) NOT NULL,
  `kredit_id` int(11) NOT NULL,
  `tanggal_angsuran` date NOT NULL,
  `jumlah_angsuran` decimal(15,2) NOT NULL,
  `sisa_pokok_kredit` decimal(15,2) NOT NULL,
  `angsuran_pokok` decimal(15,2) NOT NULL,
  `angsuran_bunga` decimal(15,2) NOT NULL,
  `total_angsuran` decimal(15,2) NOT NULL,
  `status_pembayaran` enum('Lunas','Belum Dibayar','Terlambat','Proses') NOT NULL,
  `denda` decimal(15,2) DEFAULT 0.00,
  `tanggal_pembayaran` date DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_angsuran_kredit`
--

INSERT INTO `tb_angsuran_kredit` (`angsuran_id`, `no_kredit`, `kredit_id`, `tanggal_angsuran`, `jumlah_angsuran`, `sisa_pokok_kredit`, `angsuran_pokok`, `angsuran_bunga`, `total_angsuran`, `status_pembayaran`, `denda`, `tanggal_pembayaran`, `metode_pembayaran`, `bukti_pembayaran`) VALUES
(4, 'KRED-2025-001', 1, '2025-04-04', 100000000.00, 80000000.00, 2000000.00, 100000.00, 2100000.00, 'Terlambat', 50000.00, '2025-04-04', 'Transfer Bank', 'bukti_1743752207_7616.jpeg'),
(5, 'KRED-2025-002', 2, '2025-04-01', 1200000.00, 20000.00, 200000.00, 1021010.00, 1001201.00, 'Belum Dibayar', 500000.00, '2025-04-05', 'Transfer Bank', 'bukti_1743819156_3326.png');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kredit`
--

CREATE TABLE `tb_kredit` (
  `kredit_id` int(11) NOT NULL,
  `no_kredit` varchar(20) NOT NULL,
  `nama_nasabah` varchar(100) NOT NULL,
  `nama_kredit` enum('Kredit Tanpa Agunan','Kredit Kendaraan Bermotor','Kredit Pemilikan Rumah','Kredit Pensiun','Kredit Modal Kerja','Kredit Investasi','Kredit Usaha Mikro','Kredit Pemilikan Rumah Subsidi','Kredit Usaha Rakyat','Kredit Korporasi') NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `jumlah_kredit` decimal(15,2) NOT NULL,
  `angsuran_per_bulan` decimal(15,2) NOT NULL,
  `tenor` int(11) NOT NULL,
  `suku_bunga` decimal(5,2) NOT NULL,
  `status_kredit` enum('Diajukan','Dalam Proses','Disetujui','Ditolak') DEFAULT 'Diajukan',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kredit`
--

INSERT INTO `tb_kredit` (`kredit_id`, `no_kredit`, `nama_nasabah`, `nama_kredit`, `tanggal_pengajuan`, `jumlah_kredit`, `angsuran_per_bulan`, `tenor`, `suku_bunga`, `status_kredit`, `created_by`, `created_at`) VALUES
(1, 'KRED-2025-001', 'HAIQAL', 'Kredit Usaha Rakyat', '2025-04-03', 10000000.00, 1000000.00, 12, 5.00, 'Ditolak', NULL, '2025-04-03 04:45:49'),
(2, 'KRED-2025-002', 'RENY RIZKIYA', 'Kredit Tanpa Agunan', '2025-04-01', 50000000.00, 1750000.00, 36, 5.00, 'Disetujui', NULL, '2025-04-03 05:06:57');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kredit_rumah`
--

CREATE TABLE `tb_kredit_rumah` (
  `kredit_id` int(11) NOT NULL,
  `no_kredit` varchar(20) NOT NULL,
  `nama_nasabah` varchar(100) NOT NULL,
  `nama_kredit` enum('BNI Griya','BNI Griya Subsidi','BNI Griya Refinancing','BNI Griya Multiguna') NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `jumlah_kredit` decimal(15,2) NOT NULL,
  `angsuran_per_bulan` decimal(15,2) NOT NULL,
  `tenor` int(11) NOT NULL,
  `suku_bunga` decimal(5,2) NOT NULL,
  `status_kredit` enum('Diajukan','Dalam Proses','Disetujui','Ditolak') DEFAULT 'Diajukan',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kredit_rumah`
--

INSERT INTO `tb_kredit_rumah` (`kredit_id`, `no_kredit`, `nama_nasabah`, `nama_kredit`, `tanggal_pengajuan`, `jumlah_kredit`, `angsuran_per_bulan`, `tenor`, `suku_bunga`, `status_kredit`, `created_by`, `created_at`) VALUES
(1, 'KR-0001', 'RAJIBI', 'BNI Griya Subsidi', '2025-04-03', 200000000.00, 1000000.00, 240, 0.00, 'Disetujui', NULL, '2025-04-03 10:08:36');

-- --------------------------------------------------------

--
-- Table structure for table `tb_looser`
--

CREATE TABLE `tb_looser` (
  `looser_id` int(11) NOT NULL,
  `angsuran_id` int(11) NOT NULL,
  `no_kredit` varchar(50) NOT NULL,
  `kredit_id` int(11) NOT NULL,
  `nama_nasabah` varchar(100) NOT NULL,
  `tanggal_angsuran` date NOT NULL,
  `jumlah_angsuran` decimal(15,2) NOT NULL,
  `status_pembayaran` enum('Terlambat','Belum Dibayar') NOT NULL,
  `kategori_npl` enum('Ringan','Sedang','Berat') NOT NULL,
  `tanggal_masuk_npl` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_merchant`
--

CREATE TABLE `tb_merchant` (
  `merchant_id` int(11) NOT NULL,
  `kode_merchant` varchar(20) NOT NULL,
  `nama_merchant` varchar(100) NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `nik_pemilik` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jenis_usaha` varchar(100) DEFAULT NULL,
  `npwp` varchar(25) DEFAULT NULL,
  `status_verifikasi` enum('Terverifikasi','Belum Diverifikasi','Ditolak') DEFAULT 'Belum Diverifikasi',
  `status_merchant` enum('Aktif','Nonaktif','Blacklist') DEFAULT 'Aktif',
  `tanggal_terdaftar` date DEFAULT curdate(),
  `foto_merchant` varchar(255) DEFAULT NULL,
  `dokumen_pendukung` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_merchant`
--

INSERT INTO `tb_merchant` (`merchant_id`, `kode_merchant`, `nama_merchant`, `nama_pemilik`, `nik_pemilik`, `alamat`, `provinsi`, `kota`, `kode_pos`, `kontak`, `email`, `jenis_usaha`, `npwp`, `status_verifikasi`, `status_merchant`, `tanggal_terdaftar`, `foto_merchant`, `dokumen_pendukung`, `keterangan`) VALUES
(1, 'MRC2025040001', 'UD MAJU JAYA', 'Ahmad Fauzi', '6304041810990002', 'Jalan Trans Kalimantan', 'Kalimantan Selatan', 'Barito Kuala', '70564', '085822524486', 'rsud@gmail.com', 'Kuliner', '0', 'Terverifikasi', 'Aktif', '2025-04-06', 'uploads/merchant/1743943463_ChatGPT Image Apr 4, 2025, 09_23_58 PM.png', 'uploads/dokumen/1743943463_ChatGPT Image Apr 4, 2025, 09_23_58 PM.png', 'e'),
(2, 'MRC2025040002', 'UD MAJU MUNDUR', 'UDIN', '6178183193193199', 'ANJUR', 'KALBAR', '-', '-', '-', 'fauzicliq5@gmail.com', 'Kuliner', '-', 'Terverifikasi', 'Aktif', '2025-04-06', 'uploads/merchant/1743944520_ChatGPT Image Apr 4, 2025, 09_23_58 PM.png', 'uploads/dokumen/1743944520_ChatGPT Image Apr 4, 2025, 09_23_58 PM.png', 'P');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` enum('Admin','Operator') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 'Admin', '2025-04-01 11:08:02'),
(2, 'Operator', 'operator', '2407bd807d6ca01d1bcd766c730cec9a', 'Operator', '2025-04-01 11:08:02'),
(3, 'Zaky Operator', 'zakyop', 'e10adc3949ba59abbe56e057f20f883e', 'Operator', '2025-04-07 07:01:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_absensi_karyawan`
--
ALTER TABLE `tb_absensi_karyawan`
  ADD PRIMARY KEY (`absensi_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tb_angsuran_kredit`
--
ALTER TABLE `tb_angsuran_kredit`
  ADD PRIMARY KEY (`angsuran_id`),
  ADD KEY `kredit_id` (`kredit_id`);

--
-- Indexes for table `tb_kredit`
--
ALTER TABLE `tb_kredit`
  ADD PRIMARY KEY (`kredit_id`);

--
-- Indexes for table `tb_kredit_rumah`
--
ALTER TABLE `tb_kredit_rumah`
  ADD PRIMARY KEY (`kredit_id`);

--
-- Indexes for table `tb_looser`
--
ALTER TABLE `tb_looser`
  ADD PRIMARY KEY (`looser_id`),
  ADD KEY `angsuran_id` (`angsuran_id`);

--
-- Indexes for table `tb_merchant`
--
ALTER TABLE `tb_merchant`
  ADD PRIMARY KEY (`merchant_id`),
  ADD UNIQUE KEY `kode_merchant` (`kode_merchant`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_absensi_karyawan`
--
ALTER TABLE `tb_absensi_karyawan`
  MODIFY `absensi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_angsuran_kredit`
--
ALTER TABLE `tb_angsuran_kredit`
  MODIFY `angsuran_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_kredit`
--
ALTER TABLE `tb_kredit`
  MODIFY `kredit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_kredit_rumah`
--
ALTER TABLE `tb_kredit_rumah`
  MODIFY `kredit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_looser`
--
ALTER TABLE `tb_looser`
  MODIFY `looser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_merchant`
--
ALTER TABLE `tb_merchant`
  MODIFY `merchant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_absensi_karyawan`
--
ALTER TABLE `tb_absensi_karyawan`
  ADD CONSTRAINT `tb_absensi_karyawan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_angsuran_kredit`
--
ALTER TABLE `tb_angsuran_kredit`
  ADD CONSTRAINT `tb_angsuran_kredit_ibfk_1` FOREIGN KEY (`kredit_id`) REFERENCES `tb_kredit` (`kredit_id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_looser`
--
ALTER TABLE `tb_looser`
  ADD CONSTRAINT `tb_looser_ibfk_1` FOREIGN KEY (`angsuran_id`) REFERENCES `tb_angsuran_kredit` (`angsuran_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

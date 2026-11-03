-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Nov 2026 pada 07.03
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
-- Database: `laundry`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun`
--

CREATE TABLE `akun` (
  `id_akun` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `level` enum('admin','petugas') NOT NULL,
  `id_petugas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akun`
--

INSERT INTO `akun` (`id_akun`, `username`, `password`, `level`, `id_petugas`) VALUES
(1, 'admin', 'c4ca4238a0b923820dcc509a6f75849b', 'admin', 1),
(25, 'Budi', 'c20ad4d76fe97759aa27a0c99bff6710', 'petugas', 24);

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer`
--

CREATE TABLE `customer` (
  `id_customer` int(11) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `no_telp` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `customer`
--

INSERT INTO `customer` (`id_customer`, `nama_customer`, `no_telp`) VALUES
(35, 'Asep Kurniawan', '0812-0000-0001'),
(36, 'Siti Rahmawati', '0813-2000-0002'),
(37, 'Andi Pratama', '0812-3000-0003'),
(38, 'Dewi Lestari', '0857-4000-0004'),
(39, 'Rina Wulandari', '0821-5000-0005'),
(40, 'Joko Santoso', '0878-6000-0006');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail_transaksi` int(11) NOT NULL,
  `berat` float NOT NULL,
  `total_harga` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_jenis_cucian` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail_transaksi`, `berat`, `total_harga`, `id_transaksi`, `id_jenis_cucian`) VALUES
(193, 3, 21000, 301, 1),
(194, 2, 10000, 301, 2),
(195, 4, 28000, 302, 1),
(196, 2, 14000, 303, 1),
(197, 1, 15000, 303, 3),
(198, 5, 35000, 304, 1),
(199, 3, 15000, 304, 2),
(200, 6, 42000, 305, 1),
(201, 2, 30000, 305, 3),
(202, 3, 21000, 306, 1),
(203, 1, 5000, 306, 2),
(204, 4, 28000, 307, 1),
(205, 2, 10000, 307, 2),
(206, 1, 15000, 307, 3),
(207, 8, 56000, 308, 1),
(208, 2, 30000, 309, 3),
(209, 3, 15000, 309, 2),
(210, 5, 35000, 310, 1),
(211, 2, 10000, 310, 2),
(212, 4, 28000, 311, 1),
(213, 1, 15000, 311, 3),
(214, 6, 42000, 312, 1),
(215, 4, 20000, 312, 2),
(216, 3, 21000, 313, 1),
(217, 2, 30000, 313, 3),
(218, 5, 35000, 314, 1),
(219, 2, 14000, 315, 1),
(220, 2, 10000, 315, 2),
(221, 7, 49000, 316, 1),
(222, 1, 15000, 316, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_cucian`
--

CREATE TABLE `jenis_cucian` (
  `id_jenis_cucian` int(11) NOT NULL,
  `nama_jenis_cucian` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_cucian`
--

INSERT INTO `jenis_cucian` (`id_jenis_cucian`, `nama_jenis_cucian`, `harga`) VALUES
(1, 'baju', 7000),
(2, 'celana', 5000),
(3, 'jaket', 15000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(11) NOT NULL,
  `nama_petugas` varchar(50) NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama_petugas`, `no_telp`, `alamat`, `email`) VALUES
(1, 'admin', '0812-0000-0001', 'Jawa Barat', 'admin@gmail.com'),
(24, 'Budi Santoso', '0812-0000-0002', 'Jawa Barat', 'budisantoso@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tgl_transaksi` date NOT NULL,
  `waktu_transaksi` time NOT NULL,
  `status` varchar(50) NOT NULL,
  `status_transaksi` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_petugas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tgl_transaksi`, `waktu_transaksi`, `status`, `status_transaksi`, `id_customer`, `id_petugas`) VALUES
(301, '2026-10-28', '09:15:00', 'Selesai', 1, 35, 24),
(302, '2026-10-28', '14:30:00', 'Selesai', 1, 36, 24),
(303, '2026-10-29', '10:05:00', 'Selesai', 1, 37, 24),
(304, '2026-10-29', '16:40:00', 'Selesai', 1, 38, 24),
(305, '2026-10-30', '08:50:00', 'Selesai', 1, 39, 24),
(306, '2026-10-31', '11:20:00', 'Selesai', 1, 40, 24),
(307, '2026-10-31', '15:10:00', 'Selesai', 1, 35, 24),
(308, '2026-11-01', '09:30:00', 'Selesai', 1, 36, 24),
(309, '2026-11-01', '13:45:00', 'Selesai', 1, 37, 24),
(310, '2026-11-02', '10:15:00', 'Selesai', 1, 38, 24),
(311, '2026-11-02', '17:00:00', 'Disetrika', 0, 39, 24),
(312, '2026-11-03', '08:40:00', 'Selesai', 1, 40, 24),
(313, '2026-11-03', '10:25:00', 'Disetrika', 0, 35, 24),
(314, '2026-11-03', '12:10:00', 'Dicuci', 0, 36, 24),
(315, '2026-11-03', '14:05:00', 'Proses', 0, 37, 24),
(316, '2026-11-03', '15:30:00', 'Proses', 0, 38, 24);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id_akun`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indeks untuk tabel `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail_transaksi`),
  ADD KEY `id_jenis_cucian` (`id_jenis_cucian`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `jenis_cucian`
--
ALTER TABLE `jenis_cucian`
  ADD PRIMARY KEY (`id_jenis_cucian`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_petugas` (`id_petugas`),
  ADD KEY `id_customer` (`id_customer`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akun`
--
ALTER TABLE `akun`
  MODIFY `id_akun` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `customer`
--
ALTER TABLE `customer`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT untuk tabel `jenis_cucian`
--
ALTER TABLE `jenis_cucian`
  MODIFY `id_jenis_cucian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `akun`
--
ALTER TABLE `akun`
  ADD CONSTRAINT `akun_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_jenis_cucian`) REFERENCES `jenis_cucian` (`id_jenis_cucian`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

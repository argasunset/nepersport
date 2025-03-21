-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Mar 2025 pada 04.12
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
-- Database: `olahraga`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `history_sewa_lapangan`
--

CREATE TABLE `history_sewa_lapangan` (
  `id_history` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(255) NOT NULL,
  `jadwal_booking` date NOT NULL,
  `jam` varchar(20) NOT NULL,
  `via_pembayaran` varchar(100) NOT NULL,
  `nomor_pembayaran` varchar(50) NOT NULL,
  `bukti_transfer` varchar(255) NOT NULL,
  `status` enum('Selesai','Dibatalkan') NOT NULL DEFAULT 'Selesai',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `history_sewa_lapangan`
--

INSERT INTO `history_sewa_lapangan` (`id_history`, `id_user`, `nama_user`, `jadwal_booking`, `jam`, `via_pembayaran`, `nomor_pembayaran`, `bukti_transfer`, `status`, `created_at`) VALUES
(1, 9, 'arga', '2025-03-02', '16:00-17:00', 'dana', '', '1740895411_Screenshot (55).png', 'Selesai', '2025-03-02 06:03:31'),
(2, 9, 'arga', '2025-03-02', '21:00-22:00', 'dana', '', '1740927003_Screenshot (53).png', 'Selesai', '2025-03-02 14:50:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `informasi_booking`
--

CREATE TABLE `informasi_booking` (
  `id_booking` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `jadwal_booking` date NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Diterima','Ditolak') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `via_pembayaran` varchar(255) DEFAULT NULL,
  `nomor_pembayaran` varchar(255) DEFAULT NULL,
  `jam` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `member`
--

CREATE TABLE `member` (
  `id_member` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(255) DEFAULT NULL,
  `email_user` varchar(100) NOT NULL,
  `no_telepon_user` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `member`
--

INSERT INTO `member` (`id_member`, `id_user`, `nama_user`, `email_user`, `no_telepon_user`) VALUES
(21, 9, 'arga', 'argaram@gmail.com', '0882000769955');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `password_user` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `no_telepon_user` varchar(15) NOT NULL,
  `alamat_user` text NOT NULL,
  `role_user` varchar(15) NOT NULL,
  `status` enum('member','non member') NOT NULL DEFAULT 'non member',
  `foto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `password_user`, `email`, `no_telepon_user`, `alamat_user`, `role_user`, `status`, `foto`) VALUES
(8, 'admin', '$2y$10$IkCVMPnGFg0gN/XSIIVHcOWQWDtUx0lbo3lTJKVnL4Sct.jCnnhhe', 'admin@gmail.com', '087877691446', 'Perjuangan', 'admin', 'member', 0),
(9, 'arga', '$2y$10$w3ayXsrnidrqitid6XKxmuyUAIB4TleK7EExztb5tQuDERlEjzcLy', 'argaram@gmail.com', '', 'Talun', 'user', 'member', 0),
(12, 'ra', '$2y$10$Fq.ZoQbP/Zz1Ki1GDuXvduVPr.u8YlpF72LxvngtS8Xk.GzETzOZu', 'ra@gmail.com', '098712345678', 'sini', 'user', 'non member', 0),
(13, 'wiliam', '$2y$10$sZw/ksuHnL3Te.R7RwkMfeJ.9.u0QdmyWv5swHW4KBK9ic3BsCSfW', 'we@gmail.com', '08987654321', 'Sumursiat', 'user', 'non member', 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `history_sewa_lapangan`
--
ALTER TABLE `history_sewa_lapangan`
  ADD PRIMARY KEY (`id_history`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `informasi_booking`
--
ALTER TABLE `informasi_booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id_member`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `history_sewa_lapangan`
--
ALTER TABLE `history_sewa_lapangan`
  MODIFY `id_history` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `informasi_booking`
--
ALTER TABLE `informasi_booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `member`
--
ALTER TABLE `member`
  MODIFY `id_member` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `history_sewa_lapangan`
--
ALTER TABLE `history_sewa_lapangan`
  ADD CONSTRAINT `history_sewa_lapangan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `informasi_booking`
--
ALTER TABLE `informasi_booking`
  ADD CONSTRAINT `informasi_booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




-- Database: `db_klinik_hewan`

-- Table structure for table `dokter`
CREATE TABLE `dokter` (
  `id_dokter` int NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialisasi` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jadwal_praktik` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama_dokter`, `spesialisasi`, `no_telepon`, `email`, `jadwal_praktik`, `created_at`) VALUES
(1, 'drh. Andi Wijaya', 'ginjal', '081311122233', 'andi@klinik.com', 'Senin - Jumat, 09:00 - 16:00', '2026-06-15 07:46:03'),
(13, 'drh. Fiki', 'roko ', '08234567892', '2406082@itg.ac.id', 'senen ', '2026-06-16 10:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `hewan`
--

CREATE TABLE `hewan` (
  `id_hewan` int NOT NULL,
  `id_pemilik` int NOT NULL,
  `nama_hewan` varchar(100) NOT NULL,
  `jenis_hewan` varchar(50) DEFAULT NULL,
  `ras` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('Jantan','Betina') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `warna` varchar(50) DEFAULT NULL,
  `keluhan` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hewan`
--

INSERT INTO `hewan` (`id_hewan`, `id_pemilik`, `nama_hewan`, `jenis_hewan`, `ras`, `jenis_kelamin`, `tanggal_lahir`, `warna`, `keluhan`, `foto`, `created_at`) VALUES
(1, 1, 'Luna', 'Kucing', 'Persia', 'Betina', '2022-05-10', 'Putih', 'Demam dan tidak nafsu makan', NULL, '2026-06-15 07:46:03'),
(2, 2, 'Rocky', 'Anjing', 'Golden Retriever', 'Jantan', '2021-08-15', 'Coklat', 'Batuk dan pilek', NULL, '2026-06-15 07:46:03');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_pemeriksaan`
--

CREATE TABLE `jadwal_pemeriksaan` (
  `id_jadwal` int NOT NULL,
  `id_hewan` int NOT NULL,
  `id_dokter` int NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `jam_pemeriksaan` time NOT NULL,
  `keluhan` text,
  `status_pemeriksaan` enum('Menunggu','Sedang Diperiksa','Selesai') DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `resep_terbit` enum('Ya','Tidak') DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_pemeriksaan`
--

INSERT INTO `jadwal_pemeriksaan` (`id_jadwal`, `id_hewan`, `id_dokter`, `tanggal_pemeriksaan`, `jam_pemeriksaan`, `keluhan`, `status_pemeriksaan`, `created_at`, `resep_terbit`) VALUES
(1, 1, 1, '2026-06-15', '09:00:00', 'Demam dan tidak nafsu makan', 'Menunggu', '2026-06-15 07:46:03', 'Tidak');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `id_pemilik` int NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`id_pemilik`, `nama_pemilik`, `jenis_kelamin`, `no_telepon`, `alamat`, `created_at`) VALUES
(1, 'Budi Santoso', 'Laki-laki', '081234567890', 'Jl. Merdeka No. 123, Jakarta', '2026-06-15 07:46:03'),
(2, 'Siti Aminah', 'Perempuan', '081298765432', 'Jl. Sudirman No. 45, Bandung', '2026-06-15 07:46:03'),
(4, 'nabil', 'Laki-laki', '085860720974', 'patariman\r\n', '2026-06-15 08:04:15');

-- --------------------------------------------------------

--
-- Table structure for table `perawatan_harian`
--

CREATE TABLE `perawatan_harian` (
  `id_perawatan` int NOT NULL,
  `id_rawat` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `aktivitas` text,
  `obat` varchar(200) DEFAULT NULL,
  `catatan_perawat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rawat_inap`
--

CREATE TABLE `rawat_inap` (
  `id_rawat` int NOT NULL,
  `id_hewan` int NOT NULL,
  `id_dokter` int NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `diagnosa_awal` text,
  `diagnosa_akhir` text,
  `tindakan` text,
  `status_rawat` enum('Menunggu','Dirawat','Selesai','Dirujuk') DEFAULT 'Menunggu',
  `biaya` decimal(12,2) DEFAULT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resep_obat`
--

CREATE TABLE `resep_obat` (
  `id_resep` int NOT NULL,
  `id_jadwal` int NOT NULL,
  `id_hewan` int NOT NULL,
  `obat` varchar(200) NOT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `aturan_pakai` text,
  `durasi` varchar(50) DEFAULT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_resep`
--

CREATE TABLE `riwayat_resep` (
  `id_riwayat_resep` int NOT NULL,
  `id_resep` int DEFAULT NULL,
  `id_jadwal` int DEFAULT NULL,
  `obat` varchar(200) DEFAULT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `aturan_pakai` text,
  `durasi` varchar(50) DEFAULT NULL,
  `tanggal_diberikan` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`);

--
-- Indexes for table `hewan`
--
ALTER TABLE `hewan`
  ADD PRIMARY KEY (`id_hewan`),
  ADD KEY `id_pemilik` (`id_pemilik`);

--
-- Indexes for table `jadwal_pemeriksaan`
--
ALTER TABLE `jadwal_pemeriksaan`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_hewan` (`id_hewan`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`id_pemilik`);

--
-- Indexes for table `perawatan_harian`
--
ALTER TABLE `perawatan_harian`
  ADD PRIMARY KEY (`id_perawatan`),
  ADD KEY `id_rawat` (`id_rawat`);

--
-- Indexes for table `rawat_inap`
--
ALTER TABLE `rawat_inap`
  ADD PRIMARY KEY (`id_rawat`),
  ADD KEY `id_hewan` (`id_hewan`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_jadwal` (`id_jadwal`),
  ADD KEY `id_hewan` (`id_hewan`);

--
-- Indexes for table `riwayat_resep`
--
ALTER TABLE `riwayat_resep`
  ADD PRIMARY KEY (`id_riwayat_resep`),
  ADD KEY `id_resep` (`id_resep`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `hewan`
--
ALTER TABLE `hewan`
  MODIFY `id_hewan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jadwal_pemeriksaan`
--
ALTER TABLE `jadwal_pemeriksaan`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pemilik`
--
ALTER TABLE `pemilik`
  MODIFY `id_pemilik` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `perawatan_harian`
--
ALTER TABLE `perawatan_harian`
  MODIFY `id_perawatan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rawat_inap`
--
ALTER TABLE `rawat_inap`
  MODIFY `id_rawat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `resep_obat`
--
ALTER TABLE `resep_obat`
  MODIFY `id_resep` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `riwayat_resep`
--
ALTER TABLE `riwayat_resep`
  MODIFY `id_riwayat_resep` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hewan`
--
ALTER TABLE `hewan`
  ADD CONSTRAINT `hewan_ibfk_1` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik` (`id_pemilik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jadwal_pemeriksaan`
--
ALTER TABLE `jadwal_pemeriksaan`
  ADD CONSTRAINT `jadwal_pemeriksaan_ibfk_1` FOREIGN KEY (`id_hewan`) REFERENCES `hewan` (`id_hewan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_pemeriksaan_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `perawatan_harian`
--
ALTER TABLE `perawatan_harian`
  ADD CONSTRAINT `perawatan_harian_ibfk_1` FOREIGN KEY (`id_rawat`) REFERENCES `rawat_inap` (`id_rawat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rawat_inap`
--
ALTER TABLE `rawat_inap`
  ADD CONSTRAINT `rawat_inap_ibfk_1` FOREIGN KEY (`id_hewan`) REFERENCES `hewan` (`id_hewan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rawat_inap_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD CONSTRAINT `resep_obat_ibfk_1` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_pemeriksaan` (`id_jadwal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `resep_obat_ibfk_2` FOREIGN KEY (`id_hewan`) REFERENCES `hewan` (`id_hewan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_resep`
--
ALTER TABLE `riwayat_resep`
  ADD CONSTRAINT `riwayat_resep_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep_obat` (`id_resep`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

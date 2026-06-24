-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: db_klinik_hewan
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dokter`
--

DROP TABLE IF EXISTS `dokter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dokter` (
  `id_dokter` int NOT NULL AUTO_INCREMENT,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialisasi` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jadwal_praktik` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dokter`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokter`
--

LOCK TABLES `dokter` WRITE;
/*!40000 ALTER TABLE `dokter` DISABLE KEYS */;
INSERT INTO `dokter` VALUES (13,'drh. Fikio','Penyakit dalam','08234567892','2406082@itg.ac.id','senen ','2026-06-16 10:17:23'),(16,'pikijo','Bedah','085860720974','pikiahoy@gmail.com','sabtu-senin, 09.11','2026-06-16 13:30:09'),(17,'drh. Ficky Malvialudin','Kulit & kelamin','085860721003','','Rabu-Jumat, 09.00-14.00','2026-06-16 23:36:55'),(18,'drh.Nabil alginat Sutejo diningsih al-Kemis ','Tulang & sendi','085860721007','nabilResing@abogadogo','Rabu-Sabtu 09.00-14.00','2026-06-16 23:39:24'),(19,'Aden Mubarok','gigi','08234567891','','Selasa-Minggu, 10.00-18.00','2026-06-16 23:40:13');
/*!40000 ALTER TABLE `dokter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hewan`
--

DROP TABLE IF EXISTS `hewan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hewan` (
  `id_hewan` int NOT NULL AUTO_INCREMENT,
  `id_pemilik` int NOT NULL,
  `nama_hewan` varchar(100) NOT NULL,
  `jenis_hewan` varchar(50) DEFAULT NULL,
  `ras` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('Jantan','Betina') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `warna` varchar(50) DEFAULT NULL,
  `keluhan` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hewan`),
  KEY `id_pemilik` (`id_pemilik`),
  CONSTRAINT `hewan_ibfk_1` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik` (`id_pemilik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hewan`
--

LOCK TABLES `hewan` WRITE;
/*!40000 ALTER TABLE `hewan` DISABLE KEYS */;
INSERT INTO `hewan` VALUES (11,1,'jakino','genderewo','mamalia','Betina','2026-06-16','hitam legam','khitan','1781616691_6a315033b4f73.jpeg','2026-06-16 11:39:23'),(14,2,'kiro','marmut','persia','Betina','2026-06-15','','nyeri beheung','1781616981_6a3151559b745.jpg','2026-06-16 13:36:21'),(15,2,'riji','kerbau','persia','Betina','2026-06-17','hitam','','1781617463_6a3153373ee3d.jpg','2026-06-16 13:44:23'),(16,25,'asa','Anjing','Buldog','Betina','2026-06-16','abu,unggu','tertabrak mobil','1781653285_6a31df250709a.jpg','2026-06-16 14:13:37'),(18,21,'milo','Kucing','persia','Jantan','2023-05-15','putih','','1781653435_6a31dfbbb2c54.jpg','2026-06-16 23:43:55'),(19,19,'luna','Anjing','Golden retriver','Betina','2022-11-20','Coklat Keemasan','','1781653561_6a31e0397cdfc.jpg','2026-06-16 23:46:01'),(20,18,'coki','Kelinci','Holland Lop','Jantan','0024-12-10','Abu-Au','','1781653719_6a31e0d7cc137.jpg','2026-06-16 23:48:39'),(21,22,'Adit ahmad','marmut','Anomali','Jantan','2000-09-22','hitam legam','tabrakan','','2026-06-17 01:34:52'),(22,28,'Caty','Kucing','Persia','Betina','2026-04-02','Oranye','','','2026-06-17 02:26:15');
/*!40000 ALTER TABLE `hewan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_pemeriksaan`
--

DROP TABLE IF EXISTS `jadwal_pemeriksaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_pemeriksaan` (
  `id_jadwal` int NOT NULL AUTO_INCREMENT,
  `id_hewan` int NOT NULL,
  `id_dokter` int NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `jam_pemeriksaan` time NOT NULL,
  `keluhan` text,
  `status_pemeriksaan` enum('Menunggu','Sedang Diperiksa','Selesai') DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `resep_terbit` enum('Ya','Tidak') DEFAULT 'Tidak',
  PRIMARY KEY (`id_jadwal`),
  KEY `id_hewan` (`id_hewan`),
  KEY `id_dokter` (`id_dokter`),
  CONSTRAINT `jadwal_pemeriksaan_ibfk_1` FOREIGN KEY (`id_hewan`) REFERENCES `hewan` (`id_hewan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jadwal_pemeriksaan_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_pemeriksaan`
--

LOCK TABLES `jadwal_pemeriksaan` WRITE;
/*!40000 ALTER TABLE `jadwal_pemeriksaan` DISABLE KEYS */;
INSERT INTO `jadwal_pemeriksaan` VALUES (18,11,13,'2026-08-13','04:09:00','kkk','Selesai','2026-06-16 14:06:44','Tidak'),(19,11,13,'2026-07-18','22:07:00','tpk','Menunggu','2026-06-16 14:07:39','Tidak'),(20,15,16,'2026-06-17','22:08:00','','Sedang Diperiksa','2026-06-16 14:08:52','Ya'),(21,14,13,'2026-06-15','00:11:00','','Selesai','2026-06-16 14:09:52','Tidak'),(22,22,13,'2026-06-12','11:30:00','cakitt','Menunggu','2026-06-17 02:27:18','Tidak'),(23,22,17,'2026-10-14','16:40:00','Gigi dalam patah dan Kehilangan selera makan','Sedang Diperiksa','2026-06-23 08:40:59','Tidak');
/*!40000 ALTER TABLE `jadwal_pemeriksaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemilik`
--

DROP TABLE IF EXISTS `pemilik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemilik` (
  `id_pemilik` int NOT NULL AUTO_INCREMENT,
  `nama_pemilik` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pemilik`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemilik`
--

LOCK TABLES `pemilik` WRITE;
/*!40000 ALTER TABLE `pemilik` DISABLE KEYS */;
INSERT INTO `pemilik` VALUES (1,'Budi Santoso','Laki-laki','081234567890','Jl. Merdeka No. 123, Jakarta','2026-06-15 07:46:03'),(2,'Muhammad Ihsan Ali','Laki-laki','081298765432','Jl. Pahlawan No. 34, Kec. Garut Kota, Garut','2026-06-15 07:46:03'),(18,'Rio Cahya Ramadhan','Perempuan','081311122233','Perumahan Panyawangan Indah Blok A No. 12, Garut','2026-06-16 13:28:23'),(19,'Adinda Rheina Salsabila','Perempuan','082345678912','Jl. Ahmad Yani No. 78, Kec. Tarogong Kidul, Garut','2026-06-16 14:11:41'),(21,'Mochamad Zidane Bahtiar','Laki-laki','085860720001','Jl. Ciledug No. 45, Kec. Ciledug, Garut','2026-06-16 23:26:15'),(22,'sapaat','Laki-laki','085860720006','Jl. Otista No. 23, Kec. Garut Kota, Garut','2026-06-16 23:29:02'),(23,'Rezha Achmad Muharam','Laki-laki','085860720007','Komplek Perumahan Cimanuk Asri Blok B No. 8, Garut','2026-06-16 23:29:44'),(24,'Sani Aulia Nurafifah','Perempuan','085860720009','Jl. Raya Cipanas No. 12, Kec. Cipanas, Garut','2026-06-16 23:30:38'),(25,'Muhammad Dzikri Shiddiq Muttaqin','Laki-laki','085860720010','Jl. Siliwangi No. 89, Kec. Garut Kota, Garut','2026-06-16 23:31:11'),(26,'Agni Kirani','Perempuan','085860720015','Jl. Dr. Setiabudi No. 21, Kec. Tarogong Kaler, Garut','2026-06-16 23:31:54'),(28,'Sani','Laki-laki','0897654321234','Jl. Banyuresmi','2026-06-17 02:25:01');
/*!40000 ALTER TABLE `pemilik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perawatan_harian`
--

DROP TABLE IF EXISTS `perawatan_harian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perawatan_harian` (
  `id_perawatan` int NOT NULL AUTO_INCREMENT,
  `id_rawat` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `aktivitas` text,
  `obat` varchar(200) DEFAULT NULL,
  `catatan_perawat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_perawatan`),
  KEY `id_rawat` (`id_rawat`),
  CONSTRAINT `perawatan_harian_ibfk_1` FOREIGN KEY (`id_rawat`) REFERENCES `rawat_inap` (`id_rawat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perawatan_harian`
--

LOCK TABLES `perawatan_harian` WRITE;
/*!40000 ALTER TABLE `perawatan_harian` DISABLE KEYS */;
/*!40000 ALTER TABLE `perawatan_harian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rawat_inap`
--

DROP TABLE IF EXISTS `rawat_inap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rawat_inap` (
  `id_rawat` int NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rawat`),
  KEY `id_hewan` (`id_hewan`),
  KEY `id_dokter` (`id_dokter`),
  CONSTRAINT `rawat_inap_ibfk_1` FOREIGN KEY (`id_hewan`) REFERENCES `hewan` (`id_hewan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rawat_inap_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rawat_inap`
--

LOCK TABLES `rawat_inap` WRITE;
/*!40000 ALTER TABLE `rawat_inap` DISABLE KEYS */;
INSERT INTO `rawat_inap` VALUES (21,14,13,'2026-06-18',NULL,'nyeri beheung','','','Dirawat',180000.00,'harus di amputansi','2026-06-16 13:37:17'),(22,14,16,'2026-06-19',NULL,'nyeri pinggang mas euy','sakit','','Selesai',200000.00,'harus segera','2026-06-16 13:39:03'),(24,14,19,'2026-06-17',NULL,'nyeuri beheung','','','Dirawat',200000.00,'','2026-06-17 02:21:31'),(25,16,17,'2026-06-23',NULL,'patah kaki, Kegeleng truk',NULL,NULL,'Dirawat',4.00,'harus dirawat karna keritis kehabisan darah','2026-06-23 08:39:31'),(26,20,17,'2026-06-23',NULL,'kecanduan obat kuat ',NULL,NULL,'Dirawat',30000.00,'harus direhab','2026-06-23 08:43:01');
/*!40000 ALTER TABLE `rawat_inap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep_obat`
--

DROP TABLE IF EXISTS `resep_obat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resep_obat` (
  `id_resep` int NOT NULL AUTO_INCREMENT,
  `id_jadwal` int NOT NULL,
  `id_hewan` int NOT NULL,
  `obat` varchar(200) NOT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `aturan_pakai` text,
  `durasi` varchar(50) DEFAULT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_resep`),
  KEY `id_jadwal` (`id_jadwal`),
  KEY `id_hewan` (`id_hewan`),
  CONSTRAINT `resep_obat_ibfk_1` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_pemeriksaan` (`id_jadwal`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `resep_obat_ibfk_2` FOREIGN KEY (`id_hewan`) REFERENCES `hewan` (`id_hewan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep_obat`
--

LOCK TABLES `resep_obat` WRITE;
/*!40000 ALTER TABLE `resep_obat` DISABLE KEYS */;
INSERT INTO `resep_obat` VALUES (9,20,15,'Aokcillie','2x sehari','sebelum makan','3 hari','','2026-06-16 14:10:44'),(10,20,15,'Aokcillie','1x sehari','sebelum makan','1 hari 1 minggu','jangan di pake mandi','2026-06-16 15:23:07');
/*!40000 ALTER TABLE `resep_obat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_resep`
--

DROP TABLE IF EXISTS `riwayat_resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_resep` (
  `id_riwayat_resep` int NOT NULL AUTO_INCREMENT,
  `id_resep` int DEFAULT NULL,
  `id_jadwal` int DEFAULT NULL,
  `obat` varchar(200) DEFAULT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `aturan_pakai` text,
  `durasi` varchar(50) DEFAULT NULL,
  `tanggal_diberikan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_riwayat_resep`),
  KEY `id_resep` (`id_resep`),
  CONSTRAINT `riwayat_resep_ibfk_1` FOREIGN KEY (`id_resep`) REFERENCES `resep_obat` (`id_resep`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_resep`
--

LOCK TABLES `riwayat_resep` WRITE;
/*!40000 ALTER TABLE `riwayat_resep` DISABLE KEYS */;
/*!40000 ALTER TABLE `riwayat_resep` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-24  8:52:19

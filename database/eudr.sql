-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 192.168.11.21    Database: eudr
-- ------------------------------------------------------
-- Server version	5.7.44-log

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
-- Table structure for table `company_documents`
--

DROP TABLE IF EXISTS `company_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `doc_name` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(500) DEFAULT NULL,
  `upload_by` varchar(100) DEFAULT NULL,
  `token` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_documents`
--

LOCK TABLES `company_documents` WRITE;
/*!40000 ALTER TABLE `company_documents` DISABLE KEYS */;
INSERT INTO `company_documents` VALUES (2,'Forest Certificate','Forest Certificate','uploads/company_doc/Forest Certificate_20260220_085858_d6e4b5.pdf','Admin','dad8ea5689','2026-02-20 08:58:58','2026-02-20 08:58:18'),(3,'DDS Summary','DDS Summary','uploads/company_doc/DDS Summary_20260220_085922_65a0f0.pdf','Admin','0c4b5d098a','2026-02-20 08:59:22','2026-02-20 08:58:43'),(4,'Wastewater Treatment Plant','Wastewater Treatment Plant','uploads/company_doc/Wastewater Treatment Plant_20260220_090012_8a1ca9.pdf','Admin','b7e53d08c4','2026-02-20 09:00:12','2026-02-20 08:59:33'),(5,'ISO 9001 Certificate',NULL,'uploads/company_doc/ISO 9001 Certificate_20260220_090104_fb2764.pdf','Admin','7129af1076','2026-02-20 09:01:04','2026-02-20 09:00:25'),(6,'OEKO-Tex Certificate',NULL,'uploads/company_doc/OEKO-Tex Certificate_20260220_090128_5f17f1.pdf','Admin','2877ec728a','2026-02-20 09:01:28','2026-02-20 09:00:49'),(7,'Supply chain mapping',NULL,'uploads/company_doc/Supply chain mapping_20260220_090211_1c0f8c.pdf','Admin','99beb081ee','2026-02-20 09:02:11','2026-02-20 09:01:32'),(8,'Employment and Working Conditions Declaration Form (KR 11)',NULL,'uploads/company_doc/Employment and Working Conditions Declaration Form (KR 11)_20260220_090237_8d5157.pdf','Admin','591ce55991','2026-02-20 09:02:37','2026-02-20 09:01:58'),(9,'Calibration of Weighing and Measuring Instruments',NULL,'uploads/company_doc/Calibration of Weighing and Measuring Instruments_20260220_090358_aeaa41.pdf','Admin','e8a5056e1b','2026-02-20 09:03:58','2026-02-20 09:03:19'),(10,'PDPA',NULL,'uploads/company_doc/PDPA_20260220_090503_742620.pdf','Admin','3965136a86','2026-02-20 09:05:03','2026-02-20 09:04:23'),(11,'Natural Rubber Trading License',NULL,'uploads/company_doc/Natural Rubber Trading License_20260220_090538_03bfaa.pdf','Admin','6d0d3f91b1','2026-02-20 09:05:38','2026-02-20 09:04:59'),(12,'Certificate of Value Added Tax Registration',NULL,'uploads/company_doc/Certificate of Value Added Tax Registration_20260220_090601_ade28e.pdf','Admin','ccd300cfcf','2026-02-20 09:06:01','2026-02-20 09:05:22'),(13,'Factory license',NULL,'uploads/company_doc/Factory license_20260220_090626_912879.pdf','Admin','53cf4e48cf','2026-02-20 09:06:26','2026-02-20 09:05:47'),(14,'Water Pollution',NULL,'uploads/company_doc/Water Pollution_20260220_090655_8f0c5d.pdf','Admin','a9b90478e3','2026-02-20 09:06:55','2026-02-20 09:06:16'),(15,'Air Pollution',NULL,'uploads/company_doc/Air Pollution_20260506_082258_1a0ae2.pdf','Admin','3a356e41a0','2026-02-20 09:07:43','2026-05-06 08:22:02'),(16,'Hazardous Material Possession License',NULL,'uploads/company_doc/Hazardous Material Possession License_20260220_090859_4db222.pdf','Admin','0c66349597','2026-02-20 09:08:59','2026-02-20 09:08:20'),(17,'Appointment of the Occupational Health and Safety Committee',NULL,'uploads/company_doc/Appointment of the Occupational Health and Safety Committee_20260220_090916_9770ca.pdf','Admin','5436ecd643','2026-02-20 09:09:16','2026-02-20 09:08:37'),(18,'Business registration','ส่วน company หนังสือการประกอบธุรกิจ','uploads/company_doc/Business registration_20260220_091043_8450c4.pdf','Admin','ccd7458cb2','2026-02-20 09:10:43','2026-02-20 09:10:03'),(19,'License for Operating Health Hazardous Activities.',NULL,'uploads/company_doc/License for Operating Health Hazardous Activities._20260220_091115_51709f.pdf','Admin','6aa4254141','2026-02-20 09:11:15','2026-02-20 09:10:36'),(20,'Anti-Bribery and Corruption Policy',NULL,'uploads/company_doc/Anti-Bribery and Corruption Policy_20260508_150017_1104c2.pdf','Admin','3e51ecb8be','2026-02-20 09:14:03','2026-05-08 14:59:21'),(21,'Fire Fighting and Evacuation',NULL,'uploads/company_doc/Fire Fighting and Evacuation_20260220_091456_4fdf12.pdf','Admin','198b38e1b7','2026-02-20 09:14:56','2026-02-20 09:14:16'),(22,'Rubber plantations Demonstration',NULL,'uploads/company_doc/Rubber plantations Demonstration_20260505_153955_04633f.pdf','Admin','ea226c80bc','2026-05-05 15:39:55','2026-05-05 15:38:59'),(23,'DDS Summary',NULL,'uploads/company_doc/DDS Summary_20260505_162004_2d2d4f.pdf','Admin','5620561ea7','2026-05-05 16:20:04','2026-05-05 16:19:08'),(24,'Legal compliance verification for each rubber plantation used for production','ไฟล์ ตัวอย่าง (ยังไม่มีไฟล์จริง)','uploads/company_doc/Legal compliance verification for each rubber plantation used for production_20260505_164334_d7afbe.geojson','Admin','0cd09dd8f8','2026-05-05 16:43:34','2026-05-05 16:42:37'),(25,'Foreigner Workers',NULL,'uploads/company_doc/Foreigner Workers_20260508_145638_90b4cd.xlsx','Admin','f3ce181606','2026-05-08 14:56:38','2026-05-08 14:55:42'),(26,'Employer Registration for Social Security',NULL,'uploads/company_doc/Employer Registration for Social Security_20260508_150223_a2ae42.pdf','Admin','07d6d1e923','2026-05-08 15:02:23','2026-05-08 15:01:26'),(27,'EUDR Supplier Audit Report',NULL,'uploads/company_doc/EUDR Supplier Audit Report_20260623_092556_05baed.pdf','Admin','c8796fc093','2026-06-23 09:25:56','2026-06-23 09:24:49');
/*!40000 ALTER TABLE `company_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compound_fg_links`
--

DROP TABLE IF EXISTS `compound_fg_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compound_fg_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpd_id` varchar(100) NOT NULL,
  `fg_lot_no` varchar(100) NOT NULL,
  `remak` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compound_fg_links`
--

LOCK TABLES `compound_fg_links` WRITE;
/*!40000 ALTER TABLE `compound_fg_links` DISABLE KEYS */;
INSERT INTO `compound_fg_links` VALUES (2,'1','FG3C2600100','test','2026-02-09 16:26:17','admin'),(5,'6','5O2600123','eudr','2026-02-10 17:16:13','admin'),(6,'6','5O2600123-2L',NULL,'2026-02-13 17:36:57','admin'),(7,'6','5P2600127-2L',NULL,'2026-02-13 17:37:06','admin'),(8,'6','5P2600127',NULL,'2026-02-13 17:37:14','admin'),(9,'6','5P2600126',NULL,'2026-02-13 17:37:21','admin'),(10,'6','5P2600126-2L',NULL,'2026-02-13 17:37:29','admin'),(11,'7','11111111',NULL,'2026-05-06 14:51:45','admin');
/*!40000 ALTER TABLE `compound_fg_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compound_lots_links`
--

DROP TABLE IF EXISTS `compound_lots_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compound_lots_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lot_id` int(11) NOT NULL,
  `lot_cpd_no` varchar(100) NOT NULL,
  `remak` varchar(500) DEFAULT NULL,
  `used_weight` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compound_lots_links`
--

LOCK TABLES `compound_lots_links` WRITE;
/*!40000 ALTER TABLE `compound_lots_links` DISABLE KEYS */;
INSERT INTO `compound_lots_links` VALUES (1,5,'3C2600100',NULL,NULL,'2026-02-06 16:47:06',NULL),(2,5,'3C2600100-1',NULL,NULL,'2026-02-06 16:48:15',NULL),(3,5,'3C2600100-2',NULL,NULL,'2026-02-06 17:03:09',NULL),(6,6,'3C2600155-1',NULL,NULL,'2026-02-09 17:33:30','admin'),(7,7,'111111',NULL,NULL,'2026-05-06 14:51:26','admin');
/*!40000 ALTER TABLE `compound_lots_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'test','test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-12-31 23:59:59',1,NULL,'2026-05-07 03:56:40','2026-05-07 03:57:02',NULL,NULL);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_downloads`
--

DROP TABLE IF EXISTS `log_downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `topic_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `downloaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_log_customer` (`customer_id`),
  CONSTRAINT `fk_log_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_downloads`
--

LOCK TABLES `log_downloads` WRITE;
/*!40000 ALTER TABLE `log_downloads` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_downloads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_files`
--

DROP TABLE IF EXISTS `lot_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lot_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` int(10) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` decimal(8,2) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_files`
--

LOCK TABLES `lot_files` WRITE;
/*!40000 ALTER TABLE `lot_files` DISABLE KEYS */;
INSERT INTO `lot_files` VALUES (7,5,'T54M30113_20260206_153642_1664de.geojson',0.00,'uploads/lots/T54M30113_20260206_153642_1664de.geojson','2026-02-06 15:36:42'),(8,6,'T58M30107_20260209_162650_1be111.geojson',0.00,'uploads/lots/T58M30107_20260209_162650_1be111.geojson','2026-02-09 16:26:50'),(9,6,'T58M30107_20260213_174103_5dbab7.pdf',0.36,'uploads/lots/T58M30107_20260213_174103_5dbab7.pdf','2026-02-13 17:41:03'),(10,7,'123456_20260506_145109_4e78a2.pdf',0.24,'uploads/lots/123456_20260506_145109_4e78a2.pdf','2026-05-06 14:51:09');
/*!40000 ALTER TABLE `lot_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lots`
--

DROP TABLE IF EXISTS `lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lots` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `lot_number` varchar(100) NOT NULL,
  `lot_date` date NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lot_number` (`lot_number`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lots`
--

LOCK TABLES `lots` WRITE;
/*!40000 ALTER TABLE `lots` DISABLE KEYS */;
INSERT INTO `lots` VALUES (5,8,'T54M30113','2026-02-06','test EUDR','2026-02-06 15:28:21','2026-02-06 15:27:44',NULL),(6,3,'T58M30107','2026-02-09','test','2026-02-09 16:26:41','2026-02-09 16:26:04',NULL),(7,3,'123456','2026-05-06',NULL,'2026-05-06 14:49:32','2026-05-06 14:48:35',NULL);
/*!40000 ALTER TABLE `lots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_documents`
--

DROP TABLE IF EXISTS `supplier_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `doc_name` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(500) DEFAULT NULL,
  `upload_by` varchar(100) DEFAULT NULL,
  `token` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_documents`
--

LOCK TABLES `supplier_documents` WRITE;
/*!40000 ALTER TABLE `supplier_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(100) NOT NULL,
  `supplier_name` varchar(150) NOT NULL,
  `address` text,
  `contact_name` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `remak` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_code` (`supplier_code`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (3,'SP0001','ยูนิแมครับเบอร์ จำกัด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(4,'SP0002','ไทยฮั้วยางพารา (บจก.)มหาชน','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(5,'SP0003','TEGH ไทยอีสเทิร์น รับเบอร์ จำกัด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(6,'SP0004','TCR ท่าฉางรับเบอร์ (บจก.)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(7,'SP0005','ST ศรีตรังแอโกรอินดัสทรี จำกัด (มหาชน)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(8,'SP0006','TRL ไทยรับเบอร์ลาเท็คซ์กรุ๊ป จำกัด (มหาชน)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(9,'SP0007','ดรากอนอินเตอร์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(10,'SP0008','PALM-OLEO','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(11,'SP0009','ดับเบิ้ล เอ พลัส อินเตอร์เทรด','-','-','-','Double A plus','2026-02-06 15:24:28','2026-02-06 15:24:28'),(12,'SP0010','เอเซี่ยนโพลีเทรด','-','-','-','Asian poly trade','2026-02-06 15:24:28','2026-02-06 15:24:28'),(13,'SP0011','ธโนดม เทรดดิ้ง จำกัด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(14,'SP0012','โคลอสซอล','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(15,'SP0013','วิทย์คอร์ป โปรดักส์','-','-','-','Witcorp Products ltd.','2026-02-06 15:24:28','2026-02-06 15:24:28'),(16,'SP0014','V.I.V','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(17,'SP0015','พัฒนาภัณฑ์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(18,'SP0016','บริษัท สิงค์โปร์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(19,'SP0017','Loxley Public Company','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(20,'SP0018','NP Chemical','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(21,'SP0019','ไบรเทน โปลีเทรดดิ้ง','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(22,'SP0020','Cosmo Chemical','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(23,'SP0021','Hearty Chem Corp','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(24,'SP0022','Inner Mongolia','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(25,'SP0023','HEBI UHOO','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(26,'SP0024','อุทิศเอ็นเตอร์ไพรส์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(27,'SP0025','โพลิเมอร์อินโนเวชั่น','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(28,'SP0026','วราพรพลาสติก','-','-','-','สายรัด','2026-02-06 15:24:28','2026-02-06 15:24:28'),(29,'SP0027','LANXESS','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(30,'SP0028','PT.Citra','-','-','-','Zinc','2026-02-06 15:24:28','2026-02-06 15:24:28'),(31,'SP0029','P.T. Indo Lysaght','-','-','-','Zinc','2026-02-06 15:24:28','2026-02-06 15:24:28'),(32,'SP0030','RASCHING GmBH','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(33,'SP0031','Benja silicone','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(34,'SP0032','VS Chemical SDN BHD','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(35,'SP0033','ศรีกิจพานิช','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(36,'SP0034','Brenntag Ingredients (THAI LAND)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(37,'SP0035','ธนโชติ','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(38,'SP0036','บิ๊กโปร','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(39,'SP0037','บียู','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(40,'SP0038','วิชาร์ด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(41,'SP0039','Benh Meyer','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(42,'SP0040','มหาชัยเคมี','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(43,'SP0041','Willing NEW','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(44,'SP0042','GLOBALCHEMICAL (จักรวาลเคมี)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(45,'SP0043','TVR บริษัท ถาวรอุสหกรรม','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(46,'SP0044','อินสไปร์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(47,'SP0045','ซันนี่เวิลด์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(48,'SP0046','UTICA CHEMICAL','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(49,'SP0047','ไทยอาซาฮี','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(50,'SP0048','เอ็นดับเบิ้ลยูเอส','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(51,'SP0049','CHINA JIANGSU','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(52,'SP0050','แอลเคมมิสท์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(53,'SP0051','Bios Silicone','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(54,'SP0052','การยางแห่งประเทศไทย (R.A.T.)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(55,'SP0053','IOI ACIDCHEM','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(56,'SP0054','เพอร์เฟค','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(57,'SP0055','TAIWAN PULP AND PAPER CORPORATION','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(58,'SP0056','OMNAVA SOLUTIONS','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(59,'SP0057','สหไพศาล','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(60,'SP0058','VB VON BUNDIT','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(61,'SP0059','เอ็กซ์ปา ( IOI OLEICHEMICAL )','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(62,'SP0060','YALA อุตสาหกรรมน้ำยางยะลา','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(63,'SP0061','บ. สยามไทโก มาร์เก็ตติ้ง จำกัด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(64,'SP0062','ยูติก้า','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(65,'SP0063','Thaimac STR Company Limited','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(66,'SP0064','TIANJIN','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(67,'SP0065','SRIJAROEN LATEX CO.,LTD','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(68,'SP0066','Guangken RURBER (TRANG)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(69,'SP0067','ไวท์ กรุ๊ป','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(70,'SP0068','NBR นาบอนรับเบอร์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(71,'SP0069','BEST LATEX','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(72,'SP0070','ไทย-ไลซาท','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(73,'SP0071','SK เอส เค ลาเท็คซ์ จำกัด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(74,'SP0072','ดี เคมีคอล','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(75,'SP0073','เวิลด์ฟู้ด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(76,'SP0074','เวลกิ้น','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(77,'SP0075','JIANGSU FURUIDA NEW (ALLCHEMIST)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(78,'SP0076','D.S RUBBER AND LATEX','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(79,'SP0077','Loman','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(80,'SP0078','NUM RUBBER นำรับเบอร์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(81,'SP0079','บ.ศักดิ์ศรี','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(82,'SP0080','Kao Industrial บ.คาโอ','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(83,'SP0081','บ. เอ็นพีเคมิคอล','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(84,'SP0082','บ. เซ้าท์ ซิตี๊ ปิโตรเคม','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(85,'SP0083','นิวเวิลด์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(86,'SP0084','BOOM GLOBAL','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(87,'SP0085','เอจีซี','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(88,'SP0086','E-HUP HUAT อีฮับฮวด','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(89,'SP0087','V.A. Latex','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(90,'SP0088','SOLVAY','-','-','-','33 AYER MERBAU ROAD SINGAPORE 627528','2026-02-06 15:24:28','2026-02-06 15:24:28'),(91,'SP0089','เคมิคอล','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(92,'SP0090','GOLCHA','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(93,'SP0091','Kaolin (Malaysia)','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(94,'SP0092','แฟลคอน ฮีส Falcon East','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(95,'SP0093','JINHUA ZINC TECHNOLOGY','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(96,'SP0094','วินเนอร์กรุ๊ป WINNER GROUP','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(97,'SP0095','Surint Omya','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(98,'SP0096','โคซัน COSAN','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(99,'SP0097','C GROWTH CO.,LTD','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(100,'SP0098','ไทยรีไอแอนซ์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(101,'SP0099','ELIOKEM','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(102,'SP0100','INTER RUBBER LATEX ( IRL )','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(103,'SP0101','นำโชค AGC VINYTHAI','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(104,'SP0102','สยามลักษณ์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(105,'SP0103','BETTA Latex','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(106,'SP0104','CHEMICAIS','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(107,'SP0105','Chemical Connect','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(108,'SP0106','A.L.T. เอ.แอล.ที มิเนอรัลล์','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(109,'SP0107','MC INDUSTRIAL','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(110,'SP0108','นานดี','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(111,'SP0109','Yoo Sung','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(112,'SP0110','Nanjing Bersilion','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(113,'SP0111','คาลดิก','-','-','-','-','2026-02-06 15:24:28','2026-02-06 15:24:28'),(114,'SP0112','Multiplast Chemical SDN.BHD.','-','-','-','Malaysis','2026-02-06 15:24:28','2026-02-06 15:24:28');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `level` tinyint(4) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','hvf-123','admin','-',1,'2026-06-22 16:23:39','2026-06-22 16:22:31','2025-11-19 17:31:27'),(4,'Wichawut.k','Compound-24','วิชวุฒิ กรดเต็ม',NULL,1,'2026-06-18 17:37:36','2026-06-18 17:36:28','2026-04-06 15:03:11'),(5,'teerapon.s','hvf-123','Teerapon Neng','-',1,NULL,'2026-04-06 15:02:41','2026-04-06 15:02:41');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'eudr'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-23 15:29:03

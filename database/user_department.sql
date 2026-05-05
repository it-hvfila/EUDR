-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for user_department
CREATE DATABASE IF NOT EXISTS `user_department` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `user_department`;

-- Dumping structure for table user_department.department
CREATE TABLE IF NOT EXISTS `department` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `did` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table user_department.department: ~19 rows (approximately)
DELETE FROM `department`;
INSERT INTO `department` (`id`, `did`, `department_code`, `department_name`, `color`, `created_at`, `updated_at`) VALUES
	(2, '0', 'QM', 'QMR', '#d40234', NULL, NULL),
	(3, 'D002', 'HR', 'ทรัพยากรบุคคล', '#00a655', NULL, NULL),
	(4, 'D003', 'PR', 'จัดซื้อ', '#ffb000', NULL, NULL),
	(5, 'D004', 'AC', 'บัญชี', '#fbc88a', NULL, NULL),
	(6, 'D005', 'IT', 'เทคโนโลยีสารสนเทศ', '#2b9ddb', NULL, NULL),
	(7, 'D006', 'MK', 'Marketing', '#ecd300', NULL, NULL),
	(8, 'D007', 'WH', 'คลังสินค้า', '#fda889', NULL, NULL),
	(9, 'D008', 'CL', 'เทคนิค', '#f6d4cf', NULL, NULL),
	(10, 'D009', 'CP', 'เตรียมเคมี', '#aa5ee6', NULL, NULL),
	(11, 'D010', 'EX', 'ผลิต', '#ffd7d7', NULL, NULL),
	(12, 'D011', 'EN', 'วิศวกรรม', '#be90ae', NULL, NULL),
	(13, 'D012', 'PL', 'PLAB', '#e5dece', NULL, NULL),
	(14, 'D013', 'PN', 'Planning', '#7cfac3', NULL, NULL),
	(15, 'D014', 'WW', 'บำบัดน้ำเสีย', '#5b75f9', NULL, NULL),
	(16, 'D015', 'BI', 'Boiler', '#bee687', NULL, NULL),
	(17, 'D017', 'PK', 'บรรจุ', '#ff9486', NULL, NULL),
	(18, 'D019', 'CB', 'สอบเทียบ', '#e06161', NULL, NULL),
	(19, 'D021', 'ST', 'SAFETY', '#61c0bf', NULL, NULL),
	(20, 'D022', 'DO', 'เอกสารส่งออก', '#fff2e5', NULL, NULL);

-- Dumping structure for table user_department.level
CREATE TABLE IF NOT EXISTS `level` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table user_department.level: ~5 rows (approximately)
DELETE FROM `level`;
INSERT INTO `level` (`id`, `level`, `created_at`, `updated_at`) VALUES
	(1, 'Director', NULL, NULL),
	(2, 'Manager', NULL, NULL),
	(3, 'Admin', NULL, NULL),
	(4, 'User', NULL, NULL),
	(5, 'Preuser', NULL, NULL);

-- Dumping structure for table user_department.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empno` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_it` int DEFAULT NULL,
  `approver` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_logout_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`empno`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table user_department.users: ~11 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `empno`, `username`, `password`, `name`, `surname`, `department`, `description`, `level`, `status_it`, `approver`, `last_login_at`, `last_logout_at`, `created_at`, `updated_at`) VALUES
	(2, '3091', 'watcharaphong', 'watcharaphong', 'watcharaphong', 'phimphatham', 'D005', 'Devaloper | test', 'Admin', 1, NULL, '2024-09-05 07:57:35', '2024-09-05 07:11:01', NULL, '2023-07-18 08:14:23'),
	(8, '2662', 'RANGSIMAN', 'HVF-1234', 'RANGSIMAN', 'JANKRA', 'D004', NULL, 'Director', 0, 'watcharaphong', '2024-07-22 02:21:00', '2023-09-25 03:09:18', '2023-07-21 09:02:36', '2023-07-21 09:03:05'),
	(10, '9999', 'SASINUNT', 'hvf-1234', 'SASINUNT', 'SASINUNT', 'D004', NULL, 'Admin', 0, 'watcharaphong', '2023-08-14 07:55:25', NULL, '2023-08-02 06:01:13', '2023-08-02 06:01:47'),
	(23, '3148', 'rattiporn', '29102526', 'รัตติพร', 'อินปา', 'D004', NULL, 'Manager', 0, 'admin', '2024-09-05 07:11:05', '2024-09-05 07:57:31', '2023-09-24 19:53:40', '2023-09-24 19:54:21'),
	(24, '3181', 'natthasit', '31813181', 'Natthasit', 'Sitchanun', 'D005', 'ผมรัก HV ครับ', 'Admin', 1, 'Watcharaphong', '2024-08-14 10:37:04', '2024-08-14 10:42:24', '2024-08-04 20:12:51', '2024-08-14 10:43:10'),
	(26, '4444', 'test', 'testtest2', 'test', 'test', 'D005', NULL, 'Preuser', 0, 'Watcharaphong', NULL, NULL, '2024-08-08 23:07:47', '2024-08-13 08:12:45'),
	(27, '4445', 'test1', 'testtest', 'test1', 'test', 'D003', NULL, 'Preuser', 0, NULL, NULL, NULL, '2024-08-13 08:14:05', '2024-08-13 08:14:05'),
	(28, '4446', 'test3', 'test3test3', 'test3', 'test3', 'D019', NULL, 'User', 0, 'Watcharaphong', NULL, NULL, '2024-08-13 08:16:18', '2024-09-05 06:48:51'),
	(31, '3213', 'sutthiphong.k', '24051615', 'sutthiphong', 'Kawichai', 'D005', 'Manager', 'Manager', 1, 'Watcharaphong', NULL, NULL, '2024-08-22 07:49:13', '2024-08-26 02:03:45'),
	(32, '1967', 'thanapat', 'ithvf-15', 'thanapat', 'duangjamsai', 'D005', 'kj', 'User', 1, 'Watcharaphong', NULL, NULL, '2024-08-26 10:30:42', '2024-08-27 06:31:30'),
	(33, '3174', 'noppadol', 'GGhvf-67', 'นพดล', 'พุ่มพวง', 'D005', 'จะหนีไหม', 'User', 0, 'Watcharaphong', '2024-08-26 10:36:21', NULL, '2024-08-26 10:31:48', '2024-08-26 10:34:21');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pms
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `system_admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authorities`
--

DROP TABLE IF EXISTS `authorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `organization` varchar(150) DEFAULT NULL,
  `nid` varchar(50) DEFAULT NULL,
  `role` enum('Director','Deputy Director') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('approved','suspended') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `authorities_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `system_admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authorities`
--

LOCK TABLES `authorities` WRITE;
/*!40000 ALTER TABLE `authorities` DISABLE KEYS */;
INSERT INTO `authorities` VALUES (1,'aman','aman123@gmail.com','01646549849','Chittagong','53655250956','Director','$2y$10$X4qVycO3U4DHM18b0y1sce66s6lgEkPrtto1A482EwxGrsonfQI96','approved','2025-10-05 15:38:57',1),(2,'mular','mular@gmail.com','01546469494','chittagong','635687654567','Director','$2y$10$KgNmjUEZxna2S.8il3EhK.jB/sofe9/EF0Io2WBVCvBP2uUHsgYXu','approved','2025-10-17 15:45:22',1),(3,'y','y@gmail.com','0178786678','chittagong','6356876543780','','$2y$10$yzqzUH0WBX16tKgFpmksF.ZjUXMP28cwEW79YaOrc1b8Mqbu9kxXS','approved','2025-10-19 18:16:02',1);
/*!40000 ALTER TABLE `authorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authority_requests`
--

DROP TABLE IF EXISTS `authority_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authority_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `organization` varchar(150) DEFAULT NULL,
  `nid` varchar(50) DEFAULT NULL,
  `role` enum('Director','Deputy Director') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authority_requests`
--

LOCK TABLES `authority_requests` WRITE;
/*!40000 ALTER TABLE `authority_requests` DISABLE KEYS */;
INSERT INTO `authority_requests` VALUES (1,'aman','aman123@gmail.com','01646549849','Chittagong','53655250956','Director','$2y$10$X4qVycO3U4DHM18b0y1sce66s6lgEkPrtto1A482EwxGrsonfQI96','approved','2025-10-05 15:35:07'),(2,'mular','mular@gmail.com','01546469494','chittagong','635687654567','Director','$2y$10$KgNmjUEZxna2S.8il3EhK.jB/sofe9/EF0Io2WBVCvBP2uUHsgYXu','approved','2025-10-17 15:32:52'),(4,'y','y@gmail.com','0178786678','chittagong','6356876543780','','$2y$10$yzqzUH0WBX16tKgFpmksF.ZjUXMP28cwEW79YaOrc1b8Mqbu9kxXS','approved','2025-10-19 18:15:50');
/*!40000 ALTER TABLE `authority_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berth_allocations`
--

DROP TABLE IF EXISTS `berth_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `berth_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipping_request_id` int(11) NOT NULL,
  `berth_id` int(11) NOT NULL,
  `docking_time` datetime NOT NULL,
  `status` enum('docked','departed') NOT NULL DEFAULT 'docked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shipping_request_id` (`shipping_request_id`),
  KEY `berth_id` (`berth_id`),
  CONSTRAINT `berth_allocations_ibfk_1` FOREIGN KEY (`shipping_request_id`) REFERENCES `shipping_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `berth_allocations_ibfk_2` FOREIGN KEY (`berth_id`) REFERENCES `berths` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berth_allocations`
--

LOCK TABLES `berth_allocations` WRITE;
/*!40000 ALTER TABLE `berth_allocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `berth_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berths`
--

DROP TABLE IF EXISTS `berths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `berths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `berth_name` varchar(50) NOT NULL,
  `status` enum('available','occupied','maintenance') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `berth_name` (`berth_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berths`
--

LOCK TABLES `berths` WRITE;
/*!40000 ALTER TABLE `berths` DISABLE KEYS */;
INSERT INTO `berths` VALUES (1,'Berth-1','available','2025-10-06 05:51:41','2025-10-06 05:51:41'),(2,'Berth-2','available','2025-10-06 05:51:41','2025-10-06 05:51:41'),(3,'Berth-3','available','2025-10-06 05:51:41','2025-10-06 05:51:41'),(4,'Berth-4','available','2025-10-06 05:51:41','2025-10-06 05:51:41'),(5,'Berth-5','available','2025-10-06 05:51:41','2025-10-06 05:51:41');
/*!40000 ALTER TABLE `berths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo_assignments`
--

DROP TABLE IF EXISTS `cargo_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargo_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipping_request_id` int(11) NOT NULL,
  `storage_slot_id` int(11) NOT NULL,
  `status` enum('Pending','Assigned','Completed') NOT NULL DEFAULT 'Pending',
  `container_name` varchar(255) NOT NULL,
  `storage_slot_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_request_id` (`shipping_request_id`),
  CONSTRAINT `cargo_assignments_ibfk_1` FOREIGN KEY (`shipping_request_id`) REFERENCES `shipping_requests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo_assignments`
--

LOCK TABLES `cargo_assignments` WRITE;
/*!40000 ALTER TABLE `cargo_assignments` DISABLE KEYS */;
INSERT INTO `cargo_assignments` VALUES (1,12,0,'Assigned','abc','slot a1'),(2,11,0,'Assigned','abc2','slot a12'),(3,34,0,'Assigned','abc45464','slot a124564'),(4,35,0,'Assigned','sfsdf','slot x1364563876');
/*!40000 ALTER TABLE `cargo_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customs_officers`
--

DROP TABLE IF EXISTS `customs_officers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customs_officers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `customs_badge_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `customs_officers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customs_officers`
--

LOCK TABLES `customs_officers` WRITE;
/*!40000 ALTER TABLE `customs_officers` DISABLE KEYS */;
INSERT INTO `customs_officers` VALUES (1,2,''),(2,5,''),(3,8,'');
/*!40000 ALTER TABLE `customs_officers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exporter_documents`
--

DROP TABLE IF EXISTS `exporter_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exporter_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exporter_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `tax_id` varchar(255) NOT NULL,
  `trade_license` varchar(255) NOT NULL,
  `lc_number` varchar(255) NOT NULL,
  `container_name` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `ship_id` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `exporter_id` (`exporter_id`),
  CONSTRAINT `exporter_documents_ibfk_1` FOREIGN KEY (`exporter_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exporter_documents`
--

LOCK TABLES `exporter_documents` WRITE;
/*!40000 ALTER TABLE `exporter_documents` DISABLE KEYS */;
INSERT INTO `exporter_documents` VALUES (1,18,'cde','0149849747','4568566','454535437','1345378634','Abccc-14','2545752','14-Sjj','G-789','100 fir road ,natunbazar','uploads/Port Management System.pdf','approved','2025-10-15 16:20:18');
/*!40000 ALTER TABLE `exporter_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exporter_requests`
--

DROP TABLE IF EXISTS `exporter_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exporter_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `erc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  CONSTRAINT `exporter_requests_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `partner_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exporter_requests`
--

LOCK TABLES `exporter_requests` WRITE;
/*!40000 ALTER TABLE `exporter_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `exporter_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exporters`
--

DROP TABLE IF EXISTS `exporters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exporters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `erc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `exporters_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exporters`
--

LOCK TABLES `exporters` WRITE;
/*!40000 ALTER TABLE `exporters` DISABLE KEYS */;
INSERT INTO `exporters` VALUES (1,18,'5634545');
/*!40000 ALTER TABLE `exporters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_billing_officers`
--

DROP TABLE IF EXISTS `finance_billing_officers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `finance_billing_officers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `billing_access_level` int(11) DEFAULT 1,
  `tin` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `finance_billing_officers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_billing_officers`
--

LOCK TABLES `finance_billing_officers` WRITE;
/*!40000 ALTER TABLE `finance_billing_officers` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_billing_officers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `harbor_masters`
--

DROP TABLE IF EXISTS `harbor_masters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harbor_masters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `harbor_license` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `harbor_masters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harbor_masters`
--

LOCK TABLES `harbor_masters` WRITE;
/*!40000 ALTER TABLE `harbor_masters` DISABLE KEYS */;
INSERT INTO `harbor_masters` VALUES (1,1,''),(2,3,''),(3,9,''),(4,10,'');
/*!40000 ALTER TABLE `harbor_masters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `importer_documents`
--

DROP TABLE IF EXISTS `importer_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `importer_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `importer_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `tax_id` varchar(255) NOT NULL,
  `trade_license` varchar(255) NOT NULL,
  `lc_number` varchar(255) NOT NULL,
  `container_name` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `importer_id` (`importer_id`),
  CONSTRAINT `importer_documents_ibfk_1` FOREIGN KEY (`importer_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `importer_documents`
--

LOCK TABLES `importer_documents` WRITE;
/*!40000 ALTER TABLE `importer_documents` DISABLE KEYS */;
INSERT INTO `importer_documents` VALUES (1,17,'ABC','01646469479','6454378','5665466546460','984654','A-14','54','14-S','22/1 the apankor road,dhaka','uploads/','approved','2025-10-13 15:48:28'),(2,9,'jaman\'s association','0149849748','6454780','5665466546465','9846054','b-05','0222','17-A','100 fir road ,natunbazar','uploads/feature.zip','approved','2025-10-13 15:53:46'),(6,17,'cde','01646469479','640000','222222222','222222222','b-05','0222444','14-S','22/1 the apankor road,dhaka','uploads/ssc-physics-note-chapter-5c2a0pressure-and-states-of-matter.pdf','pending','2025-10-13 17:23:11'),(7,17,'ABCd','01646464747','111111111111','11111111111','1111111111','5-I111','111111111','1111111111','100 fir road ,dHAKA','uploads/Assignment 1 (1).pdf','pending','2025-10-13 18:26:17'),(8,19,'Weee','0149849748','6400000','566444444','00000000000','5-I111cc','0278800','0111111147','100 fir road ,dHAKA','uploads/Assignment 1 (1).pdf','pending','2025-10-13 18:40:59');
/*!40000 ALTER TABLE `importer_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `importer_requests`
--

DROP TABLE IF EXISTS `importer_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `importer_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `irc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  CONSTRAINT `importer_requests_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `partner_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `importer_requests`
--

LOCK TABLES `importer_requests` WRITE;
/*!40000 ALTER TABLE `importer_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `importer_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `importers`
--

DROP TABLE IF EXISTS `importers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `importers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `irc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `importers_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `importers`
--

LOCK TABLES `importers` WRITE;
/*!40000 ALTER TABLE `importers` DISABLE KEYS */;
INSERT INTO `importers` VALUES (1,9,'116666666'),(2,10,'6242454'),(3,16,'65468978'),(4,17,'65468978GV'),(5,19,'011111111111');
/*!40000 ALTER TABLE `importers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','overdue') NOT NULL DEFAULT 'pending',
  `due_date` date NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `request_id` (`request_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `shipping_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logistics_coordinators`
--

DROP TABLE IF EXISTS `logistics_coordinators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logistics_coordinators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `transport_mode_specialization` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `logistics_coordinators_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logistics_coordinators`
--

LOCK TABLES `logistics_coordinators` WRITE;
/*!40000 ALTER TABLE `logistics_coordinators` DISABLE KEYS */;
INSERT INTO `logistics_coordinators` VALUES (1,7,NULL);
/*!40000 ALTER TABLE `logistics_coordinators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logistics_tasks`
--

DROP TABLE IF EXISTS `logistics_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logistics_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `container_name` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ship_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `logistics_tasks_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `importer_documents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logistics_tasks`
--

LOCK TABLES `logistics_tasks` WRITE;
/*!40000 ALTER TABLE `logistics_tasks` DISABLE KEYS */;
INSERT INTO `logistics_tasks` VALUES (1,1,'ABC','01646469479','A-14','54','22/1 the apankor road,dhaka','pending','2025-10-13 15:52:00',NULL),(2,2,'jaman\'s association','0149849748','b-05','0222','100 fir road ,natunbazar','pending','2025-10-13 15:57:57',NULL),(3,2,'jaman\'s association','0149849748','b-05','0222','100 fir road ,natunbazar','pending','2025-10-13 16:54:26',NULL),(4,1,'cde','0149849747','Abccc-14','2545752','100 fir road ,natunbazar','pending','2025-10-15 16:21:45','G-789'),(5,2,'jaman\'s association','0149849748','b-05','0222','100 fir road ,natunbazar','pending','2025-10-19 14:55:59',NULL),(6,1,'cde','0149849747','Abccc-14','2545752','100 fir road ,natunbazar','pending','2025-10-19 17:27:39','G-789');
/*!40000 ALTER TABLE `logistics_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
INSERT INTO `notices` VALUES (1,'Port Management','all the ships needs to be alert.','2025-10-19 18:53:42');
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partner_requests`
--

DROP TABLE IF EXISTS `partner_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partner_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `trade_license` varchar(50) NOT NULL,
  `tax_id` varchar(50) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `website` varchar(150) DEFAULT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Bangladesh',
  `notes` text DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_email` (`contact_email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `partner_requests_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `partner_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partner_requests`
--

LOCK TABLES `partner_requests` WRITE;
/*!40000 ALTER TABLE `partner_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partner_roles`
--

DROP TABLE IF EXISTS `partner_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partner_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partner_roles`
--

LOCK TABLES `partner_roles` WRITE;
/*!40000 ALTER TABLE `partner_roles` DISABLE KEYS */;
INSERT INTO `partner_roles` VALUES (3,'Exporter'),(2,'Importer'),(1,'Shipping Company'),(4,'Supplier / Vendor');
/*!40000 ALTER TABLE `partner_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `trade_license` varchar(50) NOT NULL,
  `tax_id` varchar(50) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `website` varchar(150) DEFAULT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Bangladesh',
  `notes` text DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_email` (`contact_email`),
  KEY `role_id` (`role_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `partners_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `partner_roles` (`id`),
  CONSTRAINT `partners_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `system_admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partners`
--

LOCK TABLES `partners` WRITE;
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;
INSERT INTO `partners` VALUES (1,1,'NionBD','5456431694','16512196','nion','nion789@gmail.com','01646549849',NULL,'dhaka','dhka','Bangladesh','fsdfsdfds','$2y$10$6r929lFwsiQAjWw3Q.1wJuMFoKeBn6xOV4QbHuV05HeUMODxHG692','2025-10-05 16:23:18',1),(2,1,'amanBD','5456431690','16512190','aman','aman12@gmail.com','01646549049',NULL,'dhaka','dhka','Bangladesh','fsfdsf','$2y$10$B6E.uWS9OO.QSMvPyd7zHuy9ozGi/psTtzbmYba1WybvvcJIvtgDa','2025-10-05 16:51:05',1),(8,1,'NionBD','5456431694','16512196','nion','nion79@gmail.com','01640549849',NULL,'dhaka','dhka','Bangladesh','dfdsfdsf','$2y$10$hv8diktPw/RUY7T9XYjhY.OTjPzE.2lotVeO85pN4GgV2ppGBd5AC','2025-10-06 10:08:09',NULL),(9,2,'Guidence','5456431694','16502190','jaman','jaman111@gmail.com','01646549849',NULL,'dhaka','dhka','Bangladesh','ddfsdsdfsd','$2y$10$OI2.GrvFssFR/MXJ2foBO.T5h6GfARPAiZ/ExPXWUlMZYlQpOOiAe','2025-10-06 10:41:23',NULL),(10,2,'RomEnterPRice','5456431694','6525445245','joseph','joseph111@gmail.com','01646549849',NULL,'dhaka','dhka','Bangladesh','dddddd','$2y$10$Wk6vJybaGmG0fO17n8Aru.O0gJrAu9b8EEmpM0wkOVxDCtQzh9dNO','2025-10-06 10:45:30',NULL),(11,1,'GuidG40','566546654646','6454378','amirc','amirc1111@gmail.com','21316341161',NULL,'100 fir road ,natunbazar','dhaka','Bangladesh','ddd','$2y$10$YIoAmJqQjQym6HgVUZkaJuMqxQvicE9hvAnp6qSreQ4vZYqgIgg1.','2025-10-07 12:06:56',NULL),(13,1,'GuidG405','566546654646','6454378','manikk','manikk@gmail.com','01546469494',NULL,'22/1 the apankor road,dhaka','dhaka','Bangladesh',NULL,'$2y$10$zZX64BIAHYb8Rks4wAJwCesXwbisEpd6gcbp62JcDnIZMH1MDg65C','2025-10-07 12:19:05',NULL),(14,1,'Jamil Cargo\'s ','5665466546460','6454378','jamil','jamil111@gmail.com','01749496457',NULL,'22/1 the apankor road,dhaka','dhaka','Bangladesh','dddd','$2y$10$7C7AVEGt6GE1Wsy16b7/j.iqjbr31/hl14Jydkx0pnZODgZv/XBzC','2025-10-09 07:36:27',NULL),(15,1,'Robin Cargo\'s ','566546654646','6454378','robin','robin111@gmail.com','01546469494',NULL,'22/1 the apankor road,dhaka','dhaka','Bangladesh','dd','$2y$10$3Ejnv7O6LWuL.uB3Sx2Gvuhjb8t2fZb6cOrIvn5U7oAHZlv3RT0gC','2025-10-09 07:47:35',NULL),(16,2,'aminBD','566546654646','6454378','amin','amin111@gmail.com','01546469494',NULL,'22/1 the apankor road,dhaka','dhaka','Bangladesh','dd','$2y$10$pmRrCrLTD8adB6p13QQ/auYXWmOuLhRFKnp/HpVxYP12AVp.UjNBy','2025-10-12 13:21:35',NULL),(17,2,'Guidance ','566546654646','6454378','jolil','jolil@gmail.com','01444758224',NULL,'22/1 the apankor road,dhaka','dhaka','Bangladesh','ddd','$2y$10$4k9PL5hcnO14gd0biJ5F9.vDTo/LRogsPUQ1SjYyGlqNYPwyrON22','2025-10-12 13:42:54',NULL),(18,3,'Roma\'s ','566546654646','6454378','roma','roma@gmail.com','01546469490',NULL,'100 fir road ,dHAKA','Chittagong ','Bangladesh','ddd','$2y$10$gJ16jvQWNOb6wHMjvJy86OCBAKxuJu5YY1/NL3RM/3AgpCiSLfk0u','2025-10-13 13:21:23',NULL),(19,2,'WEEEE','00000000000','12345678','elif','elif@gmail.com','01749496457',NULL,'100 fir road ,dHAKA','Chittagong ','Bangladesh','ddfdsf','$2y$10$ZerKFhR5cUIWJZbDtSTSpOflqSHlsWP1.L9QgJ6tNn/NO/Epz0b66','2025-10-13 18:39:47',NULL);
/*!40000 ALTER TABLE `partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` enum('Director','Deputy Director') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Director'),(2,'Deputy Director');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ship_locations`
--

DROP TABLE IF EXISTS `ship_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ship_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_id` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `eta` datetime NOT NULL,
  `distance_to_port` decimal(10,2) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ship_id` (`ship_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ship_locations`
--

LOCK TABLES `ship_locations` WRITE;
/*!40000 ALTER TABLE `ship_locations` DISABLE KEYS */;
INSERT INTO `ship_locations` VALUES (2,'gaG-01',22.26630000,91.79240000,'2025-10-17 18:43:00',8.56,'2025-10-16 10:41:24'),(3,'cargo-14',40.26630000,100.79240000,'2025-10-19 16:47:00',2165.16,'2025-10-16 10:44:36');
/*!40000 ALTER TABLE `ship_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(100) NOT NULL,
  `estimated_arrival_time` datetime NOT NULL,
  `importer_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Docked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `importer_id` (`importer_id`),
  CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`importer_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipments`
--

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
INSERT INTO `shipments` VALUES (1,'sndd','2025-10-25 21:56:00',15,'Docked','2025-10-16 12:57:10'),(2,'abc-47','2025-10-17 13:11:00',14,'Docked','2025-10-16 16:12:34'),(3,'abc-05','2025-10-01 12:34:00',15,'Docked','2025-10-17 06:35:10');
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_agents`
--

DROP TABLE IF EXISTS `shipping_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_agents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_number` (`license_number`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `shipping_agents_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_agents`
--

LOCK TABLES `shipping_agents` WRITE;
/*!40000 ALTER TABLE `shipping_agents` DISABLE KEYS */;
INSERT INTO `shipping_agents` VALUES (1,1,'nion',NULL,'nion789@gmail.com','$2y$10$6r929lFwsiQAjWw3Q.1wJuMFoKeBn6xOV4QbHuV05HeUMODxHG692',0,NULL,NULL,'2025-10-07 07:16:47'),(2,2,'aman',NULL,'aman12@gmail.com','$2y$10$B6E.uWS9OO.QSMvPyd7zHuy9ozGi/psTtzbmYba1WybvvcJIvtgDa',0,NULL,NULL,'2025-10-07 07:16:47'),(3,8,'nion',NULL,'nion79@gmail.com','$2y$10$hv8diktPw/RUY7T9XYjhY.OTjPzE.2lotVeO85pN4GgV2ppGBd5AC',0,NULL,NULL,'2025-10-07 07:16:47'),(4,11,'amirc',NULL,'amirc1111@gmail.com','$2y$10$YIoAmJqQjQym6HgVUZkaJuMqxQvicE9hvAnp6qSreQ4vZYqgIgg1.',1,NULL,NULL,'2025-10-09 08:15:01'),(5,13,'manikk',NULL,'manikk@gmail.com','$2y$10$zZX64BIAHYb8Rks4wAJwCesXwbisEpd6gcbp62JcDnIZMH1MDg65C',1,NULL,NULL,'2025-10-09 08:15:01'),(6,14,'jamil',NULL,'jamil111@gmail.com','$2y$10$7C7AVEGt6GE1Wsy16b7/j.iqjbr31/hl14Jydkx0pnZODgZv/XBzC',1,NULL,NULL,'2025-10-09 08:15:01'),(7,15,'robin',NULL,'robin111@gmail.com','$2y$10$3Ejnv7O6LWuL.uB3Sx2Gvuhjb8t2fZb6cOrIvn5U7oAHZlv3RT0gC',1,NULL,NULL,'2025-10-09 08:15:01');
/*!40000 ALTER TABLE `shipping_agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_companies`
--

DROP TABLE IF EXISTS `shipping_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `scac_imo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `shipping_companies_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_companies`
--

LOCK TABLES `shipping_companies` WRITE;
/*!40000 ALTER TABLE `shipping_companies` DISABLE KEYS */;
INSERT INTO `shipping_companies` VALUES (2,8,'6541349847'),(3,11,'6222'),(4,13,'6222'),(5,14,'62220'),(6,15,'6222');
/*!40000 ALTER TABLE `shipping_companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_company_requests`
--

DROP TABLE IF EXISTS `shipping_company_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_company_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `scac_imo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  CONSTRAINT `shipping_company_requests_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `partner_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_company_requests`
--

LOCK TABLES `shipping_company_requests` WRITE;
/*!40000 ALTER TABLE `shipping_company_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_company_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_requests`
--

DROP TABLE IF EXISTS `shipping_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `company_partner_id` int(11) NOT NULL,
  `ship_name` varchar(100) NOT NULL,
  `imo_number` varchar(50) DEFAULT NULL,
  `cargo_type` varchar(100) NOT NULL,
  `requested_berth_id` int(11) DEFAULT NULL,
  `departure_port` varchar(100) DEFAULT NULL,
  `arrival_port` varchar(100) NOT NULL DEFAULT 'Chittagong Port',
  `estimated_departure_time` datetime DEFAULT NULL,
  `estimated_arrival_time` datetime NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `harbor_master_id` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customs_officer_id` int(11) DEFAULT NULL,
  `warehouse_manager_id` int(11) DEFAULT NULL,
  `logistics_coordinator_id` int(11) DEFAULT NULL,
  `finance_officer_id` int(11) DEFAULT NULL,
  `customs_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agent_id` (`agent_id`),
  KEY `company_partner_id` (`company_partner_id`),
  KEY `requested_berth_id` (`requested_berth_id`),
  KEY `harbor_master_id` (`harbor_master_id`),
  KEY `customs_officer_id` (`customs_officer_id`),
  KEY `warehouse_manager_id` (`warehouse_manager_id`),
  KEY `logistics_coordinator_id` (`logistics_coordinator_id`),
  KEY `finance_officer_id` (`finance_officer_id`),
  CONSTRAINT `shipping_requests_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `shipping_agents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shipping_requests_ibfk_2` FOREIGN KEY (`company_partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shipping_requests_ibfk_3` FOREIGN KEY (`requested_berth_id`) REFERENCES `berths` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_requests_ibfk_4` FOREIGN KEY (`harbor_master_id`) REFERENCES `harbor_masters` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_requests_ibfk_5` FOREIGN KEY (`customs_officer_id`) REFERENCES `customs_officers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_requests_ibfk_6` FOREIGN KEY (`warehouse_manager_id`) REFERENCES `warehouse_managers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_requests_ibfk_7` FOREIGN KEY (`logistics_coordinator_id`) REFERENCES `logistics_coordinators` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shipping_requests_ibfk_8` FOREIGN KEY (`finance_officer_id`) REFERENCES `finance_billing_officers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_requests`
--

LOCK TABLES `shipping_requests` WRITE;
/*!40000 ALTER TABLE `shipping_requests` DISABLE KEYS */;
INSERT INTO `shipping_requests` VALUES (8,1,1,'gaG-01',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-08 13:33:00','rejected',1,'busy schedule for now. ','2025-10-07 07:33:35','2025-10-07 07:44:14',NULL,NULL,NULL,NULL,NULL),(9,1,1,'gaG-02',NULL,'matarials',1,NULL,'Chittagong Port',NULL,'2025-10-08 14:57:00','approved',1,'','2025-10-07 07:57:54','2025-10-09 10:48:25',NULL,NULL,NULL,NULL,'approved_by_customs'),(10,1,1,'gaG-20',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-04 16:31:00','rejected',1,'','2025-10-07 10:32:03','2025-10-07 10:32:18',NULL,NULL,NULL,NULL,NULL),(11,1,1,'gaG-05',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-10 16:38:00','approved',1,'','2025-10-07 10:38:20','2025-10-16 11:37:21',NULL,NULL,NULL,NULL,'approved_by_customs'),(12,1,1,'gaG-01',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-16 16:44:00','approved',1,'','2025-10-07 10:44:14','2025-10-09 10:48:27',NULL,NULL,NULL,NULL,'approved_by_customs'),(13,1,1,'gaG-02',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-17 17:36:00','approved',1,NULL,'2025-10-07 11:36:06','2025-10-07 11:36:17',NULL,NULL,NULL,NULL,NULL),(14,1,1,'CargoGO-22',NULL,'matarials',2,NULL,'Chittagong Port',NULL,'2025-10-09 18:26:00','rejected',1,'not available ...','2025-10-07 12:26:28','2025-10-07 13:11:20',NULL,NULL,NULL,NULL,NULL),(24,1,1,'gaG-01',NULL,'matarials',4,NULL,'Chittagong Port',NULL,'2025-10-17 19:54:00','approved',1,NULL,'2025-10-07 13:55:01','2025-10-07 13:55:40',NULL,NULL,NULL,NULL,NULL),(30,1,1,'gaG-09',NULL,'matarials',1,NULL,'Chittagong Port',NULL,'2025-10-03 13:34:00','rejected',1,'no busy schedule for now','2025-10-09 07:34:40','2025-10-09 08:24:22',NULL,NULL,NULL,NULL,NULL),(31,6,14,'cargo-14',NULL,'matarials',4,NULL,'Chittagong Port',NULL,'2025-10-10 14:16:00','approved',1,'','2025-10-09 08:16:05','2025-10-09 10:48:09',NULL,NULL,NULL,NULL,'approved_by_customs'),(32,7,15,'gaG-01122',NULL,'matarials',1,NULL,'Chittagong Port',NULL,'2025-10-17 17:09:00','rejected',1,'dffggd','2025-10-16 11:09:31','2025-10-16 12:43:40',NULL,NULL,NULL,NULL,NULL),(33,7,15,'sndd',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-25 21:56:00','approved',1,NULL,'2025-10-16 12:56:57','2025-10-16 12:57:10',NULL,NULL,NULL,NULL,NULL),(34,6,14,'abc-47',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-17 13:11:00','approved',1,'','2025-10-16 16:11:48','2025-10-16 16:14:33',NULL,NULL,NULL,NULL,'approved_by_customs'),(35,7,15,'abc-05',NULL,'matarials',2,NULL,'Chittagong Port',NULL,'2025-10-01 12:34:00','approved',1,'','2025-10-17 06:34:47','2025-10-17 06:35:43',NULL,NULL,NULL,NULL,'approved_by_customs'),(36,7,15,'Ship-00879',NULL,'matarials',1,NULL,'Chittagong Port',NULL,'2025-10-18 22:49:00','rejected',3,'sdfs','2025-10-17 16:47:37','2025-10-19 17:11:22',NULL,NULL,NULL,NULL,NULL),(37,7,15,'ship -154',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-16 21:24:00','pending',NULL,NULL,'2025-10-19 12:25:20','2025-10-19 12:25:20',NULL,NULL,NULL,NULL,NULL),(38,7,15,'ship-01449',NULL,'matarials',4,NULL,'Chittagong Port',NULL,'2025-10-04 18:33:00','pending',NULL,NULL,'2025-10-19 12:34:04','2025-10-19 12:34:04',NULL,NULL,NULL,NULL,NULL),(39,7,15,'ship-abc',NULL,'matarials',3,NULL,'Chittagong Port',NULL,'2025-10-10 19:34:00','pending',NULL,NULL,'2025-10-19 12:34:22','2025-10-19 12:34:22',NULL,NULL,NULL,NULL,NULL),(40,7,15,'ship-xyz',NULL,'matarials',2,NULL,'Chittagong Port',NULL,'2025-10-16 20:34:00','pending',NULL,NULL,'2025-10-19 12:34:38','2025-10-19 12:34:38',NULL,NULL,NULL,NULL,NULL),(41,7,15,'ship-789',NULL,'matarials',2,NULL,'Chittagong Port',NULL,'2025-10-14 18:35:00','pending',NULL,NULL,'2025-10-19 12:34:53','2025-10-19 12:34:53',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `shipping_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_users`
--

DROP TABLE IF EXISTS `staff_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `role` enum('Harbor Master','Customs & Compliance Officer','Cargo & Warehouse Manager','Logistics & Transport Coordinator','Workforce & Safety Manager','Finance & Billing Officer') NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nid` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','suspended') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `staff_users_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `system_admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_users`
--

LOCK TABLES `staff_users` WRITE;
/*!40000 ALTER TABLE `staff_users` DISABLE KEYS */;
INSERT INTO `staff_users` VALUES (1,'alif','alif7647@gmail.com','01546469494','Harbor Operation','Harbor Master','$2y$10$anacBd/NaiH40w.hae1n..l6dNZ4zjgEzeqKAe3M4vLKEQiTDataG','','dfdsfs','approved','2025-10-06 13:33:11','2025-10-06 13:33:27',1),(2,'abdullah nasir','nasir447@gmail.com','01444758224','customs','Customs & Compliance Officer','$2y$10$wX7eYEBNab/3bM2Fb2gIf.PtDS5bR235rKs3D/TqxaI/W5yGpH0o.','635687654378','dfdsfs','approved','2025-10-06 13:41:24','2025-10-06 13:41:32',1),(3,'abcd','abcd@gmail.com','01546469494','Harbor Operation','Logistics & Transport Coordinator','$2y$10$Da0l38mZevRHl8JvQyTvAuOMDO0/jiwFOR642ZAI4ptXUbwLcu6Iy','635687654378','dddd','approved','2025-10-07 12:27:40','2025-10-19 15:05:58',1),(4,'Tawsif','tawsif@gmail.com','01444758224','CARGO','Cargo & Warehouse Manager','$2y$10$Fmx6ABqcz.4UmXSbZ1AaK.8nvlJheg8QpuAdhp5LSWTQ9IpK0yMMa','','DD','approved','2025-10-09 10:34:08','2025-10-09 10:34:32',1),(5,'richy','richy1@gmail.com','21316341161','customs','Customs & Compliance Officer','$2y$10$zN3nNGRbDcTQCOEZL49kGe9iYQcTwoOdGIkh/SCo2rdXEOuW3QAE2','','dd','suspended','2025-10-09 10:46:44','2025-10-19 14:34:54',1),(6,'alif','alif@gmail.com','01749496457','CARGO','Cargo & Warehouse Manager','$2y$10$rUSy7yOX9OTh1LSETSpWgerg9tVQZesnGqQxiwtMW.w8MoPOXFEm.','','hh','approved','2025-10-09 11:04:41','2025-10-09 11:05:09',1),(7,'kona','kona@gmail.com','01444758224','Logistics','Logistics & Transport Coordinator','$2y$10$ZPQHQLqUc4QMjS3KsndLruINkIhqNKHTqhL0EkCwr6aeX5ROn/SD.','635687654378','kjsdoifdso','approved','2025-10-13 15:56:40','2025-10-13 15:56:47',1),(8,'elif1','elif1@gmail.com','01546469494','customs','Customs & Compliance Officer','$2y$10$zUUM/PL0dc6Rl1EEqd2Du.AoTCOzUxxEjTCxnOl.mUCfmlsc1rWei','6356876543780','sdfdsfsdf','approved','2025-10-13 18:42:10','2025-10-13 18:42:21',1),(9,'x','x@gmail.com','01316341161','Harbor Operation','Harbor Master','$2y$10$V8yRr9YP07TGoEQrEnAHFuybGFqGb9Sn4f3I2ZZMJKnCGUnbxmyuO','','harbors','approved','2025-10-17 16:17:41','2025-10-17 16:17:47',1),(10,'rob','rob@gmail.com','0135484984','Harbor Operation','Harbor Master','$2y$10$BKOOOV4R6ZPS.IZJGNmn1OnsRSiO8qScs1XkVMPVYd5//DlcvVNsS','sdfdsfsdf','sddfsd','pending','2025-10-19 16:49:41','2025-10-19 16:49:41',NULL);
/*!40000 ALTER TABLE `staff_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_requests`
--

DROP TABLE IF EXISTS `supplier_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `service_category` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  CONSTRAINT `supplier_requests_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `partner_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_requests`
--

LOCK TABLES `supplier_requests` WRITE;
/*!40000 ALTER TABLE `supplier_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `service_category` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suspended_users`
--

DROP TABLE IF EXISTS `suspended_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suspended_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('authority','partner','staff_user') NOT NULL,
  `reason` text DEFAULT NULL,
  `suspended_by` int(11) DEFAULT NULL,
  `suspended_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `suspended_by` (`suspended_by`),
  CONSTRAINT `suspended_users_ibfk_1` FOREIGN KEY (`suspended_by`) REFERENCES `system_admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suspended_users`
--

LOCK TABLES `suspended_users` WRITE;
/*!40000 ALTER TABLE `suspended_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `suspended_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_admin`
--

DROP TABLE IF EXISTS `system_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'system_admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_admin`
--

LOCK TABLES `system_admin` WRITE;
/*!40000 ALTER TABLE `system_admin` DISABLE KEYS */;
INSERT INTO `system_admin` VALUES (1,'admin','$2y$10$P5AUXG0EgaPZYmuXCBs7l.XKnWB6tDYeStKd.pdsVpcwyamicXnDu','admin@gmail.com','Super Admin','system_admin','2025-10-05 15:33:41','2025-10-05 15:33:41');
/*!40000 ALTER TABLE `system_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transport_assignments`
--

DROP TABLE IF EXISTS `transport_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transport_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `contact_no` varchar(255) NOT NULL,
  `container_name` varchar(255) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `assigned_transport` varchar(255) NOT NULL,
  `shipment_status` varchar(50) NOT NULL DEFAULT 'Pending Pickup',
  `status_updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport_assignments`
--

LOCK TABLES `transport_assignments` WRITE;
/*!40000 ALTER TABLE `transport_assignments` DISABLE KEYS */;
INSERT INTO `transport_assignments` VALUES (1,'ABC','0164967498','abc-01','46498','banani- 12/4','Transport-4','Dispatched','2025-10-16 23:59:05','2025-10-16 07:29:48');
/*!40000 ALTER TABLE `transport_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse_managers`
--

DROP TABLE IF EXISTS `warehouse_managers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_managers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `warehouse_zone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `warehouse_managers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse_managers`
--

LOCK TABLES `warehouse_managers` WRITE;
/*!40000 ALTER TABLE `warehouse_managers` DISABLE KEYS */;
INSERT INTO `warehouse_managers` VALUES (1,4,NULL),(2,6,NULL);
/*!40000 ALTER TABLE `warehouse_managers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workforce_safety_managers`
--

DROP TABLE IF EXISTS `workforce_safety_managers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workforce_safety_managers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `safety_certification_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `workforce_safety_managers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workforce_safety_managers`
--

LOCK TABLES `workforce_safety_managers` WRITE;
/*!40000 ALTER TABLE `workforce_safety_managers` DISABLE KEYS */;
/*!40000 ALTER TABLE `workforce_safety_managers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-20  1:01:40

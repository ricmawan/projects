-- MySQL dump 10.13  Distrib 5.6.21, for Win32 (x86)
--
-- Host: localhost    Database: tiara_interior
-- ------------------------------------------------------
-- Server version	5.6.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `tiara_interior`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `tiara_interior` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `tiara_interior`;

--
-- Table structure for table `backup_history`
--

DROP TABLE IF EXISTS `backup_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_history` (
  `BackupHistoryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `BackupDate` date DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BackupHistoryID`),
  UNIQUE KEY `BACKUPHISTORY_INDEX` (`BackupHistoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_history`
--

LOCK TABLES `backup_history` WRITE;
/*!40000 ALTER TABLE `backup_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `backup_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_brand`
--

DROP TABLE IF EXISTS `master_brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_brand` (
  `BrandID` bigint(20) NOT NULL AUTO_INCREMENT,
  `BrandName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BrandID`),
  UNIQUE KEY `BRAND_INDEX` (`BrandID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_brand`
--

LOCK TABLES `master_brand` WRITE;
/*!40000 ALTER TABLE `master_brand` DISABLE KEYS */;
INSERT INTO `master_brand` VALUES (1,'Eurowall','2016-03-12 17:02:18','Admin',NULL,NULL),(2,'MAESTRO','2016-03-12 17:02:36','Admin',NULL,NULL),(3,'KING','2016-03-12 17:02:44','Admin',NULL,NULL),(4,'Queen','2016-03-12 17:02:53','Admin',NULL,NULL),(5,'MONCHERI','2016-03-12 17:03:06','Admin',NULL,NULL),(6,'WOW','2016-03-12 17:03:12','Admin',NULL,NULL),(7,'Econia','2016-03-12 17:03:33','Admin',NULL,NULL),(8,'Borneo','2016-03-12 17:03:49','Admin',NULL,NULL),(9,'Bacan','2016-03-12 17:03:56','Admin',NULL,NULL),(13,'Crown','2016-03-12 17:06:03','Admin',NULL,NULL),(15,'Star','2016-03-12 17:09:20','Admin',NULL,NULL),(16,'Bravo','2016-03-12 17:12:21','Admin',NULL,NULL),(17,'Empire','2016-03-12 17:12:30','Admin',NULL,NULL),(18,'Excellent','2016-03-12 17:12:41','Admin',NULL,NULL),(19,'Sky Line','2016-03-12 17:12:53','Admin',NULL,NULL),(20,'Supra','2016-03-12 17:13:04','Admin',NULL,NULL),(21,'Delta','2016-03-12 17:13:15','Admin',NULL,NULL),(22,'Ion','2016-03-12 17:13:19','Admin',NULL,NULL),(23,'Focus','2016-03-12 17:13:26','Admin',NULL,NULL),(24,'Renova','2016-03-12 17:13:35','Admin',NULL,NULL);
/*!40000 ALTER TABLE `master_brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_customer`
--

DROP TABLE IF EXISTS `master_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_customer` (
  `CustomerID` bigint(20) NOT NULL AUTO_INCREMENT,
  `CustomerName` varchar(255) NOT NULL,
  `Telephone` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CustomerID`),
  UNIQUE KEY `CUSTOMER_INDEX` (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_customer`
--

LOCK TABLES `master_customer` WRITE;
/*!40000 ALTER TABLE `master_customer` DISABLE KEYS */;
INSERT INTO `master_customer` VALUES (1,'Bejo','20983091','Pekunden Timur ','Semarang','2016-03-13 01:52:39','Admin',NULL,NULL);
/*!40000 ALTER TABLE `master_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_groupmenu`
--

DROP TABLE IF EXISTS `master_groupmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_groupmenu` (
  `GroupMenuID` int(11) NOT NULL,
  `GroupMenuName` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL,
  PRIMARY KEY (`GroupMenuID`),
  UNIQUE KEY `GROUPMENU_INDEX` (`GroupMenuID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_groupmenu`
--

LOCK TABLES `master_groupmenu` WRITE;
/*!40000 ALTER TABLE `master_groupmenu` DISABLE KEYS */;
INSERT INTO `master_groupmenu` VALUES (1,'Home','fa fa-home fa-3x','./Home.php',1),(2,'Master Data','fa fa-book fa-3x',NULL,2),(3,'Transaksi','fa fa-cart-plus fa-3x',NULL,3),(4,'Laporan','fa fa-line-chart fa-3x',NULL,4),(5,'Tools','fa fa-cogs fa-3x',NULL,5);
/*!40000 ALTER TABLE `master_groupmenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_menu`
--

DROP TABLE IF EXISTS `master_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_menu` (
  `MenuID` bigint(20) NOT NULL AUTO_INCREMENT,
  `GroupMenuID` int(11) DEFAULT NULL,
  `MenuName` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `IsReport` bit(1) DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL,
  PRIMARY KEY (`MenuID`),
  UNIQUE KEY `MENU_INDEX` (`MenuID`),
  KEY `GroupMenuID` (`GroupMenuID`),
  CONSTRAINT `master_menu_ibfk_1` FOREIGN KEY (`GroupMenuID`) REFERENCES `master_groupmenu` (`GroupMenuID`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_menu`
--

LOCK TABLES `master_menu` WRITE;
/*!40000 ALTER TABLE `master_menu` DISABLE KEYS */;
INSERT INTO `master_menu` VALUES (1,2,'User','Master/User/',NULL,'\0',1),(2,2,'Merek','Master/Brand/',NULL,'\0',2),(3,2,'Satuan','Master/Unit/',NULL,'\0',3),(4,2,'Tipe','Master/Type/',NULL,'\0',4),(5,2,'Pelanggan','Master/Customer/',NULL,'\0',6),(6,2,'Supplier','Master/Supplier/',NULL,'\0',7),(7,3,'Stok Awal','Transaction/FirstStock/',NULL,'\0',1),(8,3,'Barang Masuk','Transaction/Incoming/',NULL,'\0',2),(9,3,'Barang Keluar','Transaction/Outgoing/',NULL,'\0',3),(10,3,'Retur Beli','Transaction/BuyReturn/',NULL,'\0',4),(11,3,'Retur Jual','Transaction/SaleReturn/',NULL,'\0',5),(12,4,'Pembelian','Report/Purchase/',NULL,'',1),(13,4,'Penjualan','Report/Selling/',NULL,'',2),(14,4,'Penjualan Per Barang','Report/SaleByItem/',NULL,'',3),(15,2,'Barang','Master/Item/',NULL,'',5),(16,2,'Sales','Master/Sales/',NULL,'\0',8),(17,5,'Bakcup Database','Tools/BackupDB/',NULL,'\0',1),(18,5,'Restore Database','Tools/RestoreDB/',NULL,'\0',2),(19,5,'Reset','Tools/Reset/',NULL,'\0',3);
/*!40000 ALTER TABLE `master_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_parameter`
--

DROP TABLE IF EXISTS `master_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_parameter` (
  `ParameterID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ParameterName` varchar(255) NOT NULL,
  `ParameterValue` varchar(255) NOT NULL,
  `Remarks` text,
  `IsNumber` int(11) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ParameterID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_parameter`
--

LOCK TABLES `master_parameter` WRITE;
/*!40000 ALTER TABLE `master_parameter` DISABLE KEYS */;
INSERT INTO `master_parameter` VALUES (1,'APPLICATION_PATH','/Projects/TiaraInterior/Source/','Location of the application',0,'2016-03-12 15:01:05','System',NULL,NULL),(2,'MYSQL_DUMP_PATH','C:\\xampp\\mysql\\bin\\mysqldump.exe','Path of mysqldump.exe',0,'2016-03-12 00:00:00','Admin',NULL,NULL),(3,'ERROR_LOG_PATH','C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\BackupFiles\\dumperrors.txt','log error when backup failed',0,'2016-03-12 00:00:00','admin',NULL,NULL),(4,'BACKUP_FULLPATH','C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\BackupFiles\\','Directory where backup files located',0,'2016-03-12 00:00:00','admin','2016-03-12 14:25:59',NULL),(5,'BACKUP_FOLDER','BackupFiles\\\\','Backup path',0,'2016-03-12 00:00:00','Admin','2016-03-12 14:43:21',NULL),(6,'MYSQL_PATH','C:\\xampp\\mysql\\bin\\mysql.exe','mysql.exe path',0,'2016-03-12 00:00:00','Admin',NULL,NULL),(8,'UPLOAD_PATH','C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\UploadedFiles\\','Upload Path',0,'2016-03-12 00:00:00','Admin',NULL,NULL);
/*!40000 ALTER TABLE `master_parameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_role`
--

DROP TABLE IF EXISTS `master_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_role` (
  `RoleID` bigint(20) NOT NULL AUTO_INCREMENT,
  `UserID` bigint(20) DEFAULT NULL,
  `MenuID` bigint(20) DEFAULT NULL,
  `EditFlag` tinyint(1) DEFAULT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`RoleID`),
  UNIQUE KEY `ROLE_INDEX` (`RoleID`,`UserID`,`MenuID`),
  KEY `UserID` (`UserID`),
  KEY `MenuID` (`MenuID`),
  CONSTRAINT `master_role_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `master_user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `master_role_ibfk_2` FOREIGN KEY (`MenuID`) REFERENCES `master_menu` (`MenuID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_role`
--

LOCK TABLES `master_role` WRITE;
/*!40000 ALTER TABLE `master_role` DISABLE KEYS */;
INSERT INTO `master_role` VALUES (1,1,1,1,1),(2,1,2,1,1),(3,1,3,1,1),(4,1,4,1,1),(5,1,5,1,1),(6,1,6,1,1),(7,1,7,1,1),(8,1,8,1,1),(9,1,9,1,1),(10,1,10,1,1),(11,1,11,1,1),(12,1,12,1,1),(13,1,13,1,1),(14,1,14,1,1),(15,1,15,1,1),(16,1,16,1,1),(17,1,17,1,1),(18,1,18,1,1),(19,1,19,1,1);
/*!40000 ALTER TABLE `master_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_sales`
--

DROP TABLE IF EXISTS `master_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_sales` (
  `SalesID` bigint(20) NOT NULL AUTO_INCREMENT,
  `SalesName` varchar(255) NOT NULL,
  `Telephone` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SalesID`),
  UNIQUE KEY `SALES_INDEX` (`SalesID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_sales`
--

LOCK TABLES `master_sales` WRITE;
/*!40000 ALTER TABLE `master_sales` DISABLE KEYS */;
INSERT INTO `master_sales` VALUES (1,'Budi','08128716','Pekunden Barat ','2016-03-13 01:51:22','Admin',NULL,NULL);
/*!40000 ALTER TABLE `master_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_supplier`
--

DROP TABLE IF EXISTS `master_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_supplier` (
  `SupplierID` bigint(20) NOT NULL AUTO_INCREMENT,
  `SupplierName` varchar(255) NOT NULL,
  `Telephone` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SupplierID`),
  UNIQUE KEY `SUPPLIER_INDEX` (`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_supplier`
--

LOCK TABLES `master_supplier` WRITE;
/*!40000 ALTER TABLE `master_supplier` DISABLE KEYS */;
INSERT INTO `master_supplier` VALUES (1,'CV. WallPaper','081286098','Pekunden Tengah','Semarang','2016-03-13 01:51:07','Admin',NULL,NULL);
/*!40000 ALTER TABLE `master_supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_type`
--

DROP TABLE IF EXISTS `master_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_type` (
  `TypeID` bigint(20) NOT NULL AUTO_INCREMENT,
  `TypeName` varchar(255) NOT NULL,
  `UnitID` bigint(20) DEFAULT NULL,
  `BrandID` bigint(20) NOT NULL,
  `ReminderCount` int(11) DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`TypeID`),
  UNIQUE KEY `TYPE_INDEX` (`TypeID`,`BrandID`,`UnitID`),
  KEY `BrandID` (`BrandID`),
  KEY `UnitID` (`UnitID`),
  CONSTRAINT `master_type_ibfk_1` FOREIGN KEY (`BrandID`) REFERENCES `master_brand` (`BrandID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `master_type_ibfk_2` FOREIGN KEY (`UnitID`) REFERENCES `master_unit` (`UnitID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_type`
--

LOCK TABLES `master_type` WRITE;
/*!40000 ALTER TABLE `master_type` DISABLE KEYS */;
INSERT INTO `master_type` VALUES (1,'001',1,1,0,17100,27000,NULL,'2016-03-13 01:49:16','Admin','2016-03-12 18:51:54','Admin'),(2,'002',1,1,0,25000,35000,NULL,'2016-03-13 01:49:31','Admin','2016-03-12 18:50:29','Admin');
/*!40000 ALTER TABLE `master_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_unit`
--

DROP TABLE IF EXISTS `master_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_unit` (
  `UnitID` bigint(20) NOT NULL AUTO_INCREMENT,
  `UnitName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UnitID`),
  UNIQUE KEY `UNIT_INDEX` (`UnitID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_unit`
--

LOCK TABLES `master_unit` WRITE;
/*!40000 ALTER TABLE `master_unit` DISABLE KEYS */;
INSERT INTO `master_unit` VALUES (1,'Roll','2016-03-12 17:09:55','Admin',NULL,NULL),(2,'mÂ²','2016-03-12 17:10:54','Admin',NULL,NULL),(4,'m lari','2016-03-12 17:11:14','Admin',NULL,NULL),(5,'Box','2016-03-12 17:11:36','Admin',NULL,NULL);
/*!40000 ALTER TABLE `master_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_user`
--

DROP TABLE IF EXISTS `master_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_user` (
  `UserID` bigint(20) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(255) NOT NULL,
  `UserLogin` varchar(100) NOT NULL,
  `UserPassword` varchar(255) NOT NULL,
  `IsActive` tinyint(1) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `USER_INDEX` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_user`
--

LOCK TABLES `master_user` WRITE;
/*!40000 ALTER TABLE `master_user` DISABLE KEYS */;
INSERT INTO `master_user` VALUES (1,'System Administrator','Admin','e80b5017098950fc58aad83c8c14978e',1,'2016-03-12 15:01:01','System',NULL,NULL);
/*!40000 ALTER TABLE `master_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reset_history`
--

DROP TABLE IF EXISTS `reset_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reset_history` (
  `ResetHistoryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ResetDate` date DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ResetHistoryID`),
  UNIQUE KEY `RESETHISTORY_INDEX` (`ResetHistoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset_history`
--

LOCK TABLES `reset_history` WRITE;
/*!40000 ALTER TABLE `reset_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `reset_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restore_history`
--

DROP TABLE IF EXISTS `restore_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restore_history` (
  `RestoreHistoryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `RestoreDate` date DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`RestoreHistoryID`),
  UNIQUE KEY `RESTOREHISTORY_INDEX` (`RestoreHistoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restore_history`
--

LOCK TABLES `restore_history` WRITE;
/*!40000 ALTER TABLE `restore_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `restore_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_buyreturn`
--

DROP TABLE IF EXISTS `transaction_buyreturn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_buyreturn` (
  `BuyReturnID` bigint(20) NOT NULL AUTO_INCREMENT,
  `BuyReturnNumber` varchar(100) DEFAULT NULL,
  `SupplierID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BuyReturnID`),
  UNIQUE KEY `BUYRETURN_INDEX` (`BuyReturnID`,`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_buyreturn`
--

LOCK TABLES `transaction_buyreturn` WRITE;
/*!40000 ALTER TABLE `transaction_buyreturn` DISABLE KEYS */;
INSERT INTO `transaction_buyreturn` VALUES (1,'RB-130320160001',1,'2016-03-13 00:00:00','','2016-03-13 01:54:34','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_buyreturn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_buyreturndetails`
--

DROP TABLE IF EXISTS `transaction_buyreturndetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_buyreturndetails` (
  `BuyReturnDetailsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `BuyReturnID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BuyReturnDetailsID`),
  UNIQUE KEY `BUYRETURNDETAILS_INDEX` (`BuyReturnDetailsID`,`BuyReturnID`),
  KEY `BuyReturnID` (`BuyReturnID`),
  KEY `TypeID` (`TypeID`),
  CONSTRAINT `transaction_buyreturndetails_ibfk_1` FOREIGN KEY (`BuyReturnID`) REFERENCES `transaction_buyreturn` (`BuyReturnID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaction_buyreturndetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_buyreturndetails`
--

LOCK TABLES `transaction_buyreturndetails` WRITE;
/*!40000 ALTER TABLE `transaction_buyreturndetails` DISABLE KEYS */;
INSERT INTO `transaction_buyreturndetails` VALUES (1,1,1,3,17100,'125','2016-03-13 01:54:34','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_buyreturndetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_firststock`
--

DROP TABLE IF EXISTS `transaction_firststock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_firststock` (
  `FirstStockID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FirstStockNumber` varchar(100) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`FirstStockID`),
  UNIQUE KEY `FIRSTSTOCK_INDEX` (`FirstStockID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_firststock`
--

LOCK TABLES `transaction_firststock` WRITE;
/*!40000 ALTER TABLE `transaction_firststock` DISABLE KEYS */;
INSERT INTO `transaction_firststock` VALUES (1,'SA-130320160002','2016-03-13 00:00:00','','2016-03-13 01:49:55','Admin',NULL,NULL),(2,'SA-130320160003','2016-03-13 00:00:00','','2016-03-13 01:50:29','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_firststock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_firststockdetails`
--

DROP TABLE IF EXISTS `transaction_firststockdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_firststockdetails` (
  `FirstStockDetailsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `FirstStockID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`FirstStockDetailsID`),
  UNIQUE KEY `FIRSTSTOCKDETAILS_INDEX` (`FirstStockDetailsID`,`FirstStockID`,`TypeID`),
  KEY `FirstStockID` (`FirstStockID`),
  KEY `TypeID` (`TypeID`),
  CONSTRAINT `transaction_firststockdetails_ibfk_1` FOREIGN KEY (`FirstStockID`) REFERENCES `transaction_firststock` (`FirstStockID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaction_firststockdetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_firststockdetails`
--

LOCK TABLES `transaction_firststockdetails` WRITE;
/*!40000 ALTER TABLE `transaction_firststockdetails` DISABLE KEYS */;
INSERT INTO `transaction_firststockdetails` VALUES (1,1,1,100,15000,25000,0,'125','2016-03-13 01:49:55','Admin',NULL,NULL),(2,2,2,55,25000,35000,0,'100','2016-03-13 01:50:29','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_firststockdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_incoming`
--

DROP TABLE IF EXISTS `transaction_incoming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_incoming` (
  `IncomingID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IncomingNumber` varchar(100) DEFAULT NULL,
  `SupplierID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IncomingID`),
  UNIQUE KEY `INCOMING_INDEX` (`IncomingID`,`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_incoming`
--

LOCK TABLES `transaction_incoming` WRITE;
/*!40000 ALTER TABLE `transaction_incoming` DISABLE KEYS */;
INSERT INTO `transaction_incoming` VALUES (1,'TB-060320160001',1,'2016-03-06 00:00:00','','2016-03-13 01:51:54','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_incoming` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_incomingdetails`
--

DROP TABLE IF EXISTS `transaction_incomingdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_incomingdetails` (
  `IncomingDetailsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `IncomingID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IncomingDetailsID`),
  UNIQUE KEY `INCOMINGDETAILS_INDEX` (`IncomingDetailsID`,`IncomingID`,`TypeID`),
  KEY `IncomingID` (`IncomingID`),
  KEY `TypeID` (`TypeID`),
  CONSTRAINT `transaction_incomingdetails_ibfk_1` FOREIGN KEY (`IncomingID`) REFERENCES `transaction_incoming` (`IncomingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaction_incomingdetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_incomingdetails`
--

LOCK TABLES `transaction_incomingdetails` WRITE;
/*!40000 ALTER TABLE `transaction_incomingdetails` DISABLE KEYS */;
INSERT INTO `transaction_incomingdetails` VALUES (1,1,1,100,18000,27000,5,'135','2016-03-13 01:51:54','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_incomingdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_invoicenumber`
--

DROP TABLE IF EXISTS `transaction_invoicenumber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_invoicenumber` (
  `InvoiceNumberID` bigint(20) NOT NULL AUTO_INCREMENT,
  `InvoiceNumberType` varchar(2) DEFAULT NULL,
  `InvoiceDate` date DEFAULT NULL,
  `InvoiceNumber` varchar(20) DEFAULT NULL,
  `DeleteFlag` bit(1) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`InvoiceNumberID`),
  UNIQUE KEY `INVOICENUMBER_INDEX` (`InvoiceNumberID`,`InvoiceNumberType`,`InvoiceDate`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_invoicenumber`
--

LOCK TABLES `transaction_invoicenumber` WRITE;
/*!40000 ALTER TABLE `transaction_invoicenumber` DISABLE KEYS */;
INSERT INTO `transaction_invoicenumber` VALUES (1,'SA','2016-03-13','131312','\0','2016-03-13 00:00:00','Admin',NULL,NULL),(2,'SA','2016-03-13','SA-130320160002','','2016-03-13 01:49:55','Admin',NULL,NULL),(3,'SA','2016-03-13','SA-130320160003','','2016-03-13 01:50:29','Admin',NULL,NULL),(4,'TB','2016-03-06','TB-060320160001','','2016-03-13 01:51:54','Admin',NULL,NULL),(5,'TJ','2016-03-08','TJ-080320160001','','2016-03-13 01:53:13','Admin',NULL,NULL),(6,'RB','2016-03-13','RB-130320160001','','2016-03-13 01:54:34','Admin',NULL,NULL),(7,'TB','2016-03-13','','','2016-03-13 01:54:58','Admin',NULL,NULL),(8,'TB','2016-03-13','','','2016-03-13 01:55:08','Admin',NULL,NULL),(9,'TB','2016-03-13','RJ-130320160001','','2016-03-13 01:57:23','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_invoicenumber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_outgoing`
--

DROP TABLE IF EXISTS `transaction_outgoing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_outgoing` (
  `OutgoingID` bigint(20) NOT NULL AUTO_INCREMENT,
  `OutgoingNumber` varchar(100) DEFAULT NULL,
  `CustomerID` bigint(20) DEFAULT NULL,
  `SalesID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `DeliveryCost` double DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`OutgoingID`),
  UNIQUE KEY `OUTGOING_INDEX` (`OutgoingID`,`CustomerID`),
  KEY `CustomerID` (`CustomerID`),
  KEY `SalesID` (`SalesID`),
  CONSTRAINT `transaction_outgoing_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `master_customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaction_outgoing_ibfk_2` FOREIGN KEY (`SalesID`) REFERENCES `master_sales` (`SalesID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_outgoing`
--

LOCK TABLES `transaction_outgoing` WRITE;
/*!40000 ALTER TABLE `transaction_outgoing` DISABLE KEYS */;
INSERT INTO `transaction_outgoing` VALUES (1,'TJ-080320160001',1,1,'2016-03-08 00:00:00',0,'','2016-03-13 01:53:13','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_outgoing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_outgoingdetails`
--

DROP TABLE IF EXISTS `transaction_outgoingdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_outgoingdetails` (
  `OutgoingDetailsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `OutgoingID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`OutgoingDetailsID`),
  UNIQUE KEY `OUTGOINGDETAILS_INDEX` (`OutgoingDetailsID`,`OutgoingID`,`TypeID`),
  KEY `OutgoingID` (`OutgoingID`),
  KEY `TypeID` (`TypeID`),
  CONSTRAINT `transaction_outgoingdetails_ibfk_1` FOREIGN KEY (`OutgoingID`) REFERENCES `transaction_outgoing` (`OutgoingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaction_outgoingdetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_outgoingdetails`
--

LOCK TABLES `transaction_outgoingdetails` WRITE;
/*!40000 ALTER TABLE `transaction_outgoingdetails` DISABLE KEYS */;
INSERT INTO `transaction_outgoingdetails` VALUES (1,1,1,5,27000,27000,0,'125','2016-03-13 01:53:13','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_outgoingdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_salereturn`
--

DROP TABLE IF EXISTS `transaction_salereturn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_salereturn` (
  `SaleReturnID` bigint(20) NOT NULL AUTO_INCREMENT,
  `SaleReturnNumber` varchar(100) DEFAULT NULL,
  `CustomerID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SaleReturnID`),
  UNIQUE KEY `SALERETURN_INDEX` (`SaleReturnID`,`CustomerID`),
  KEY `CustomerID` (`CustomerID`),
  CONSTRAINT `transaction_salereturn_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `master_customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_salereturn`
--

LOCK TABLES `transaction_salereturn` WRITE;
/*!40000 ALTER TABLE `transaction_salereturn` DISABLE KEYS */;
INSERT INTO `transaction_salereturn` VALUES (3,'RJ-130320160001',1,'2016-03-13 00:00:00','','2016-03-13 01:57:23','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_salereturn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_salereturndetails`
--

DROP TABLE IF EXISTS `transaction_salereturndetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_salereturndetails` (
  `SaleReturnDetailsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `SaleReturnID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SaleReturnDetailsID`),
  UNIQUE KEY `SALERETURNDETAILS_INDEX` (`SaleReturnDetailsID`,`SaleReturnID`),
  KEY `SaleReturnID` (`SaleReturnID`),
  KEY `TypeID` (`TypeID`),
  CONSTRAINT `transaction_salereturndetails_ibfk_1` FOREIGN KEY (`SaleReturnID`) REFERENCES `transaction_salereturn` (`SaleReturnID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaction_salereturndetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_salereturndetails`
--

LOCK TABLES `transaction_salereturndetails` WRITE;
/*!40000 ALTER TABLE `transaction_salereturndetails` DISABLE KEYS */;
INSERT INTO `transaction_salereturndetails` VALUES (3,3,1,1,27000,'125','2016-03-13 01:57:23','Admin',NULL,NULL);
/*!40000 ALTER TABLE `transaction_salereturndetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tiara_interior'
--
/*!50003 DROP PROCEDURE IF EXISTS `spInsBackup` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsBackup`(
	pFilePath	 	VARCHAR(255), 
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;
		INSERT INTO backup_history
		(
			BackupDate,
			FilePath,
			CreatedDate,
			CreatedBy
		)
		VALUES (
			NOW(),
			pFilePath,
			NOW(),
			pCurrentUser
		);
			
SET State = 2;			               
		SELECT
			0 AS 'ID',
			'Backup Berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
			
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsBrand` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsBrand`(
	pID				BIGINT, 
	pBrandName 	VARCHAR(255), 
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_brand
		WHERE
			TRIM(BrandName) = TRIM(pBrandName)
			AND BrandID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Merek sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_brand
				(
					BrandName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pBrandName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Merek Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_brand
				SET
					BrandName = pBrandName,
					ModifiedBy = pCurrentUser
				WHERE
					BrandID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Merek Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
				
			END IF;
		END IF;			
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsCustomer` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsCustomer`(
	pID 			BIGINT, 
	pCustomerName 	VARCHAR(255),
	pAddress 		TEXT,
	pCity			VARCHAR(100),
	pTelephone		VARCHAR(255),
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;	

SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_customer
		WHERE
			TRIM(CustomerName) = TRIM(pCustomerName)
			AND TRIM(Address) = TRIM(pAddress)
			AND TRIM(City) = TRIM(pCity)
			AND CustomerID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Customer sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_customer
				(
					CustomerName,
					Address,
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCustomerName,
					pAddress,
					pCity,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Pelanggan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';		
			ELSE
SET State = 5;
				UPDATE
					master_customer
				SET
					CustomerName = pCustomerName,
					Address = pAddress,
					City = pCity,
					Telephone = pTelephone,
					ModifiedBy = pCurrentUser
				WHERE
					CustomerID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Pelanggan Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsFirstStock` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsFirstStock`(
	pFilePath	 	VARCHAR(255), 
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;
	DECLARE CurrentDate  DATETIME;
	DECLARE FirstStockID BIGINT;
	DECLARE PassValidate INT;
	
	SET CurrentDate = NOW();
	/*DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR ", IFNULL(@ErrNo, ''), " (", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		SELECT 
			pId AS 'ID', 
			'Terjadi Kesalahan Sistem' AS 'Message', 
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag', 
			State AS 'State';
	END;*/
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;
		INSERT INTO transaction_firststock
		(
			FirstStockNumber,
			TransactionDate,
			Remarks,
			CreatedDate,
			CreatedBy
		)
		SELECT
			CONCAT('SA', DATE_FORMAT(NOW(), '%Y%m%d'), RIGHT(CONCAT('0000', COUNT(1) + 1), 4)),
			CurrentDate,
			'Reset',
			NOW(),
			'Admin'
		FROM 
			transaction_invoicenumber
		WHERE
			InvoiceDate = DATE_FORMAT(NOW(), '%Y-%m-%d')
			AND InvoiceNumberType = 'SA';
SET @State = 2;
		SET FirstStockID = (SELECT MAX(FirstStockID) FROM transaction_firststock);

SET State = 3;			               
		INSERT INTO transaction_firststockdetails
		(
			FirstStockID,
			TypeID,
			Quantity,
			BuyPrice,
			SalePrice,
			Discount,
			BatchNumber,
			CreatedDate,
			CreatedBy
		)
		SELECT
			FirstStockID,
			MT.TypeID,
			(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0)),
			IFNULL(FS.BuyPrice, MT.BuyPrice) BuyPrice,
			IFNULL(FS.SalePrice, MT.SalePrice) SalePrice,
			FS.Discount,
			FS.BatchNumber,
			NOW(),
			pCurrentUser
		FROM
			master_type MT
			JOIN master_brand MB
				ON MB.BrandID = MT.BrandID
			JOIN master_unit MU
				ON MU.UnitID = MT.UnitID
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(SA.Quantity) Quantity,
					BuyPrice,
					SalePrice,
					Discount
				FROM
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity,
						BuyPrice,
						SalePrice,
						Discount
					FROM
						transaction_firststockdetails
					GROUP BY
						TypeID,
						BatchNumber
					UNION
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity,
						BuyPrice,
						SalePrice,
						Discount
					FROM
						transaction_incomingdetails
					GROUP BY
						TypeID,
						BatchNumber
				)SA
				GROUP BY
					TypeID,
					BatchNumber,
					BuyPrice,
					SalePrice,
					Discount
			)FS
				ON FS.TypeID = MT.TypeID
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(Quantity) Quantity
				FROM
					transaction_outgoingdetails
				GROUP BY
					TypeID,
					BatchNumber
			)TOD
				ON TOD.TypeID = MT.TypeID
				AND TOD.BatchNumber = FS.BatchNumber
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(Quantity) Quantity
				FROM
					transaction_buyreturndetails
				GROUP BY
					TypeID,
					BatchNumber
			)BR
				ON BR.TypeID = MT.TypeID
				AND BR.BatchNumber = FS.BatchNumber
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(Quantity) Quantity
				FROM
					transaction_salereturndetails
				GROUP BY
					TypeID,
					BatchNumber
			)SR
				ON SR.TypeID = MT.TypeID
				AND SR.BatchNumber = FS.BatchNumber
		WHERE
			(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0)) > 0;
			
SET State = 4;
		DELETE FROM transaction_firststock WHERE TransactionDate < CurrentDate;
		
SET State = 5;
		DELETE FROM transaction_incoming WHERE TransactionDate < CurrentDate;

SET State = 6;
		DELETE FROM transaction_buyreturn WHERE TransactionDate < CurrentDate;

SET State = 7;		
		DELETE FROM transaction_salereturn WHERE TransactionDate < CurrentDate;
		
SET State = 8;
		DELETE FROM transaction_outgoing WHERE TransactionDate < CurrentDate;
		
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsItem` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsItem`(
	pID 			BIGINT, 
	pItemName 		VARCHAR(255),
	pBrandID 	BIGINT,
	pUnitID			BIGINT,
	pReminderCount	INT,
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item
		WHERE
			TRIM(ItemName) = TRIM(pItemName)
			AND BrandID = pBrandID
			AND ItemID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Barang sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_item
				(
					ItemName,
					BrandID,
					UnitID,
					ReminderCount,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemName,
					pBrandID,
					pUnitID,
					pReminderCount,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_item
				SET
					ItemName = pItemName,
					ReminderCount = pReminderCount,
					BrandID = pBrandID,
					UnitID = pUnitID,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsRestore` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsRestore`(
	pFilePath	 	VARCHAR(255), 
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	/*DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR ", IFNULL(@ErrNo, ''), " (", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		SELECT 
			pId AS 'ID', 
			'Terjadi Kesalahan Sistem' AS 'Message', 
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag', 
			State AS 'State';
	END;*/
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;
		INSERT INTO restore_history
		(
			RestoreDate,
			FilePath,
			CreatedDate,
			CreatedBy
		)
		VALUES (
			NOW(),
			pFilePath,
			NOW(),
			pCurrentUser
		);
			
SET State = 2;			               
		SELECT
			0 AS 'ID',
			'Restore Berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
			
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsSales` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSales`(
	pID 			BIGINT, 
	pSalesName 		VARCHAR(255),
	pAddress 		TEXT,
	pTelephone		VARCHAR(255),
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;	

SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_sales
		WHERE
			TRIM(SalesName) = TRIM(pSalesName)
			AND SalesID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Sales sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_sales
				(
					SalesName,
					Address,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSalesName,
					pAddress,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Sales Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';		
			ELSE
SET State = 5;
				UPDATE
					master_sales
				SET
					SalesName = pSalesName,
					Address = pAddress,
					Telephone = pTelephone,
					ModifiedBy = pCurrentUser
				WHERE
					SalesID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Sales Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsSupplier` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSupplier`(
	pID 			BIGINT, 
	pSupplierName 	VARCHAR(255),
	pAddress		TEXT,
	pCity			VARCHAR(100),
	pTelephone		VARCHAR(255),
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;	

SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_supplier
		WHERE
			TRIM(SupplierName) = TRIM(pSupplierName)
			AND TRIM(Address) = TRIM(pAddress)
			AND TRIM(City) = TRIM(pCity)
			AND SupplierID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Supplier sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_supplier
				(
					SupplierName,
					Address,
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSupplierName,
					pAddress,
					pCity,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Supplier Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';		
			ELSE
SET State = 5;
				UPDATE
					master_supplier
				SET
					SupplierName = pSupplierName,
					Address = pAddress,
					City = pCity,
					Telephone = pTelephone,
					ModifiedBy = pCurrentUser
				WHERE
					SupplierID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Supplier Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsType` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsType`(
	pID 			BIGINT, 
	pTypeName 		VARCHAR(255),
	pBrandID 		BIGINT,
	pUnitID			BIGINT,
	pReminderCount	INT,
	pBuyPrice		DOUBLE,
	pSalePrice		DOUBLE,
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_type
		WHERE
			TRIM(TypeName) = TRIM(pTypeName)
			AND BrandID = pBrandID
			AND TypeID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Tipe sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_type
				(
					TypeName,
					BrandID,
					UnitID,
					ReminderCount,
					BuyPrice,
					SalePrice,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pTypeName,
					pBrandID,
					pUnitID,
					pReminderCount,
					pBuyPrice,
					pSalePrice,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Tipe Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_type
				SET
					TypeName = pTypeName,
					ReminderCount = pReminderCount,
					BrandID = pBrandID,
					UnitID = pUnitID,
					BuyPrice = pBuyPrice,
					SalePrice = pSalePrice,
					ModifiedBy = pCurrentUser
				WHERE
					TypeID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Tipe Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsUnit` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsUnit`(
	pID				BIGINT, 
	pUnitName 	VARCHAR(255), 
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_unit
		WHERE
			TRIM(UnitName) = TRIM(pUnitName)
			AND UnitID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Satuan sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_unit
				(
					UnitName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUnitName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Satuan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_unit
				SET
					UnitName = pUnitName,
					ModifiedBy = pCurrentUser
				WHERE
					UnitID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Satuan Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
				
			END IF;
		END IF;			
	COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `spInsUser` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsUser`(
	pID 			BIGINT, 
	pUserName 		VARCHAR(255), 
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
	pMenuID 		VARCHAR(255),
	pEditMenuID 	VARCHAR(255),
	pDeleteMenuID	VARCHAR(255),
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_user 
		WHERE
			UserName = pUserName
			AND UserLogin = pUserLogin
			AND UserID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Username sudah dipakai' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
		
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_user
				(
					UserName,
					UserLogin,
					UserPassword,
					IsActive,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
					pPassword,
					pIsActive,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					MAX(UserID)
				INTO 
					pID
				FROM
					master_user;
					
			ELSE
SET State = 5;
				UPDATE
					master_user
				SET
					UserName = pUserName,
					UserLogin = pUserLogin,
					UserPassword = pPassword,
					IsActive = pIsActive,
					ModifiedBy = pCurrentUser
				WHERE
					UserID = pID;
		
			END IF;
	
SET State = 6;

			DELETE 
			FROM 
				master_role
			WHERE
				UserID = pID;
				
SET State = 7;
			loopdata : WHILE pMenuID <> "" DO
				INSERT INTO master_role
				(
					UserID,
					MenuID,
					EditFlag,
					DeleteFlag
				)
				VALUES
				(
					pID,
					SUBSTRING(pMenuID, 1, IF((INSTR(pMenuID, ',') - 1) = -1, LENGTH(pMenuID), (INSTR(pMenuID, ',') - 1))),
					SUBSTRING(pEditMenuID, 1, IF((INSTR(pEditMenuID, ',') - 1) = -1, LENGTH(pEditMenuID), (INSTR(pEditMenuID, ',') - 1))),
					SUBSTRING(pDeleteMenuID, 1, IF((INSTR(pDeleteMenuID, ',') - 1) = -1, LENGTH(pDeleteMenuID), (INSTR(pDeleteMenuID, ',') - 1)))
				);
				
				IF(INSTR(pMenuID, ',') = 0) THEN SET pMenuID = "";
				ELSE
					SET pMenuID = SUBSTRING(pMenuID, INSTR(pMenuID, ',') + 1, LENGTH(pMenuID));
					SET pEditMenuID = SUBSTRING(pEditMenuID, INSTR(pEditMenuID, ',') + 1, LENGTH(pEditMenuID));
					SET pDeleteMenuID = SUBSTRING(pDeleteMenuID, INSTR(pDeleteMenuID, ',') + 1, LENGTH(pDeleteMenuID));
				END IF;
				
			END WHILE loopdata; 
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
		SELECT
			pID AS 'ID',
			'User Berhasil Ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
SET State = 9;
		SELECT
			pID AS 'ID',
			'User Berhasil Diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-13  2:02:41

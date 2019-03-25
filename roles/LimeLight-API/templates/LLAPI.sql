-- MySQL dump 10.14  Distrib 5.5.60-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: LLAPI
-- ------------------------------------------------------
-- Server version	5.5.60-MariaDB

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
-- Table structure for table `all_host_requests`
--

DROP TABLE IF EXISTS `all_host_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `all_host_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_ip` varchar(15) NOT NULL,
  `userid` varchar(5) NOT NULL,
  `mygroup` varchar(32) NOT NULL,
  `myhost` varchar(32) NOT NULL,
  `myvar` varchar(256) NOT NULL,
  `myval` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `all_host_requests`
--

LOCK TABLES `all_host_requests` WRITE;
/*!40000 ALTER TABLE `all_host_requests` DISABLE KEYS */;
INSERT INTO `all_host_requests` VALUES (1,'kev.kev.kev.kev','005','web','web03','web03_ansible_host','1.2.3.4'),(2,'kev.kev.kev.kev','005','web','','web_var_one','this is var one'),(3,'kev.kev.kev.kev','005','all','','all_some_global_var','123'),(4,'kev.kev.kev.kev','005','web','web03','web03_ansible_user','ec2-user'),(5,'kev.kev.kev.kev','005','web','web03','web03_ansible_password','ansibleday'),(6,'kev.kev.kev.kev','005','web','','web_group_var_two','two'),(7,'kev.kev.kev.kev','005','all','','all_another_global_var','this is another global var'),(8,'kev.kev.kev.kev','005','app','app01','app_ansible_host','1.2.3.4'),(9,'kev.kev.kev.kev','005','app','web03','app_ansible_password','ansibleday'),(10,'kev.kev.kev.kev','005','web','web03','',''),(11,'kev.kev.kev.kev','005','app','web03','app_ansible_password','ansibleday'),(12,'kev.kev.kev.kev','005','web','web01','',''),(13,'kev.kev.kev.kev','005','web','web02','web03_ansible_password','ansibleday'),(14,'kev.kev.kev.kev','005','web','web03','web03_ansible_password','ansibleday'),(15,'','003','web','lasjdlkasja','ansible_host','1.2.3.4'),(16,'','003','web','fasdsad','ansible_host','2.3.4.5');
/*!40000 ALTER TABLE `all_host_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `var` int(11) NOT NULL,
  `val` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requestors`
--

DROP TABLE IF EXISTS `requestors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requestors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `userid` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requestors`
--

LOCK TABLES `requestors` WRITE;
/*!40000 ALTER TABLE `requestors` DISABLE KEYS */;
INSERT INTO `requestors` VALUES (1,'kev.kev.kev.kev','000');
/*!40000 ALTER TABLE `requestors` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-25  6:40:06


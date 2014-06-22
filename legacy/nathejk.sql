-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: nathejk
-- ------------------------------------------------------
-- Server version	5.5.37-0ubuntu0.14.04.1

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
-- Table structure for table `enter_user`
--

DROP TABLE IF EXISTS `enter_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enter_user` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `customerId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` blob NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_danish_ci DEFAULT NULL,
  `uniqueEmail` varchar(100) CHARACTER SET latin1 COLLATE latin1_danish_ci DEFAULT NULL,
  `emailIsDisabled` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_danish_ci NOT NULL,
  `foreignId` varchar(50) CHARACTER SET latin1 COLLATE latin1_danish_ci DEFAULT NULL,
  `foreignData` text COLLATE utf8_danish_ci NOT NULL,
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` int(10) unsigned NOT NULL DEFAULT '0',
  `lastLogin` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `foreignId` (`customerId`,`foreignId`),
  UNIQUE KEY `customerId` (`customerId`,`uniqueEmail`),
  UNIQUE KEY `username` (`customerId`,`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enter_user`
--

LOCK TABLES `enter_user` WRITE;
/*!40000 ALTER TABLE `enter_user` DISABLE KEYS */;
INSERT INTO `enter_user` VALUES (1772258,4,'nathejk','���','nathejk@nathejk.dk','nathejk@nathejk.dk',0,'',NULL,'a:29:{s:9:\"firstName\";s:0:\"\";s:8:\"lastName\";s:0:\"\";s:5:\"firma\";s:0:\"\";s:8:\"stilling\";s:0:\"\";s:4:\"isPm\";s:0:\"\";s:18:\"supervisorUsername\";s:0:\"\";s:7:\"mobilnr\";s:0:\"\";s:15:\"epinionPassword\";s:0:\"\";s:24:\"mailreportMailinglistIds\";s:0:\"\";s:18:\"mailreportLanguage\";s:0:\"\";s:9:\"birthDate\";s:0:\"\";s:15:\"departmentTitle\";s:0:\"\";s:10:\"phoneLocal\";s:0:\"\";s:11:\"phonePublic\";s:0:\"\";s:9:\"phoneHome\";s:0:\"\";s:13:\"phoneCellular\";s:0:\"\";s:22:\"subscribePcoNewsletter\";s:0:\"\";s:11:\"senderEmail\";s:0:\"\";s:10:\"senderName\";s:0:\"\";s:13:\"debatIsBanned\";s:0:\"\";s:12:\"debatAboutMe\";s:0:\"\";s:13:\"debatHomepage\";s:0:\"\";s:8:\"debatMsm\";s:0:\"\";s:9:\"debatSort\";s:0:\"\";s:15:\"debatNumPerPage\";s:0:\"\";s:15:\"debatShowOnline\";s:0:\"\";s:14:\"debatShowEmail\";s:0:\"\";s:14:\"debatSignature\";s:0:\"\";s:14:\"unsubscribeurl\";s:0:\"\";}',1304372900,1399661735,1403348609);
/*!40000 ALTER TABLE `enter_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_agenda`
--

DROP TABLE IF EXISTS `nathejk_agenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_agenda` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_danish_ci DEFAULT NULL,
  `signupStartUts` int(10) unsigned NOT NULL,
  `signupSpejderOpen` tinyint(3) unsigned NOT NULL,
  `signupSeniorOpen` tinyint(3) unsigned NOT NULL,
  `spejderIntro` text COLLATE utf8_danish_ci NOT NULL,
  `seniorIntro` text COLLATE utf8_danish_ci NOT NULL,
  `maxSeniorMemberCount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_agenda`
--

LOCK TABLES `nathejk_agenda` WRITE;
/*!40000 ALTER TABLE `nathejk_agenda` DISABLE KEYS */;
INSERT INTO `nathejk_agenda` VALUES (1,'Nathejk 2013',1367359260,1,1,'','',120);
/*!40000 ALTER TABLE `nathejk_agenda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_checkIn`
--

DROP TABLE IF EXISTS `nathejk_checkIn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_checkIn` (
  `id` int(10) unsigned NOT NULL,
  `memberId` int(10) unsigned NOT NULL,
  `teamId` int(10) unsigned NOT NULL,
  `createdUts` int(10) unsigned NOT NULL,
  `outUts` int(10) unsigned NOT NULL,
  `deletedUts` int(10) unsigned NOT NULL,
  `location` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `typeName` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `remark` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `isCaught` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `memberId` (`memberId`),
  KEY `teamId` (`teamId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_checkIn`
--

LOCK TABLES `nathejk_checkIn` WRITE;
/*!40000 ALTER TABLE `nathejk_checkIn` DISABLE KEYS */;
/*!40000 ALTER TABLE `nathejk_checkIn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_httplog`
--

DROP TABLE IF EXISTS `nathejk_httplog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_httplog` (
  `id` int(10) unsigned NOT NULL,
  `uts` int(10) unsigned NOT NULL,
  `request` text COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uts` (`uts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_httplog`
--

LOCK TABLES `nathejk_httplog` WRITE;
/*!40000 ALTER TABLE `nathejk_httplog` DISABLE KEYS */;
/*!40000 ALTER TABLE `nathejk_httplog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_mail`
--

DROP TABLE IF EXISTS `nathejk_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_mail` (
  `id` int(10) unsigned NOT NULL,
  `teamId` int(10) unsigned NOT NULL,
  `sendUts` int(10) unsigned NOT NULL,
  `rcptTo` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `mailFrom` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `body` text COLLATE utf8_danish_ci NOT NULL,
  `smtpErrorMessage` varchar(250) COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_mail`
--

LOCK TABLES `nathejk_mail` WRITE;
/*!40000 ALTER TABLE `nathejk_mail` DISABLE KEYS */;
INSERT INTO `nathejk_mail` VALUES (1,2014000,1399486040,'klaus <test@test.test>','Nathejk <tilmeld@nathejk.dk>','Spejdertilmelding','For at starte tilmeldingen skal du fÃ¸lge nedenstÃ¥ende link\n\nhttp://tilmelding.dev.nathejk.dk/spejder/2014000:c3804\n\nMed venlig hilsen\nNathejk',''),(2,2014001,1399488214,'klaus <test@test.test>','Nathejk <tilmeld@nathejk.dk>','Spejdertilmelding','For at starte tilmeldingen skal du fÃ¸lge nedenstÃ¥ende link\n\nhttp://tilmelding.dev.nathejk.dk/spejder/2014001:4e51f\n\nMed venlig hilsen\nNathejk',''),(3,2014001,1399659039,'klaus <test@test.test>','Nathejk <tilmeld@nathejk.dk>','hej','I har nr 2014001',''),(4,2014001,1399659062,'klaus <test@test.test>','Nathejk <tilmeld@nathejk.dk>','hello','there','');
/*!40000 ALTER TABLE `nathejk_mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_mailTemplate`
--

DROP TABLE IF EXISTS `nathejk_mailTemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_mailTemplate` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `body` text COLLATE utf8_danish_ci NOT NULL,
  `optgroup` varchar(20) COLLATE utf8_danish_ci NOT NULL,
  `sortOrder` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_mailTemplate`
--

LOCK TABLES `nathejk_mailTemplate` WRITE;
/*!40000 ALTER TABLE `nathejk_mailTemplate` DISABLE KEYS */;
/*!40000 ALTER TABLE `nathejk_mailTemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_marker`
--

DROP TABLE IF EXISTS `nathejk_marker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_marker` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(100) COLLATE utf8_danish_ci NOT NULL,
  `typeName` varchar(100) COLLATE utf8_danish_ci NOT NULL,
  `iconName` varchar(100) COLLATE utf8_danish_ci NOT NULL,
  `colorName` varchar(100) COLLATE utf8_danish_ci NOT NULL,
  `value` text COLLATE utf8_danish_ci NOT NULL,
  `description` text COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_marker`
--

LOCK TABLES `nathejk_marker` WRITE;
/*!40000 ALTER TABLE `nathejk_marker` DISABLE KEYS */;
/*!40000 ALTER TABLE `nathejk_marker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_member`
--

DROP TABLE IF EXISTS `nathejk_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_member` (
  `id` int(10) unsigned NOT NULL,
  `teamId` int(10) unsigned NOT NULL,
  `number` int(10) unsigned NOT NULL,
  `createdUts` int(10) unsigned NOT NULL,
  `pausedUts` int(10) unsigned NOT NULL,
  `discontinuedUts` int(10) unsigned NOT NULL,
  `deletedUts` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `postalCode` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactPhone` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactSms` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `spejderTelefon` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `birthDate` date NOT NULL,
  `returning` tinyint(3) unsigned NOT NULL,
  `remark` text COLLATE utf8_danish_ci NOT NULL,
  `photoId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_member`
--

LOCK TABLES `nathejk_member` WRITE;
/*!40000 ALTER TABLE `nathejk_member` DISABLE KEYS */;
INSERT INTO `nathejk_member` VALUES (1,2014001,0,1399495366,0,0,0,'','','','','','','','','0000-00-00',0,'',0),(2,2014001,0,1399495366,0,0,0,'','','','','','','','','0000-00-00',0,'',0),(3,2014001,0,1399495366,0,0,0,'','','','','','','','','0000-00-00',0,'',0);
/*!40000 ALTER TABLE `nathejk_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_payment`
--

DROP TABLE IF EXISTS `nathejk_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_payment` (
  `id` int(10) unsigned NOT NULL,
  `teamId` int(10) unsigned NOT NULL,
  `uts` int(10) unsigned NOT NULL,
  `amount` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teamId` (`teamId`,`uts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_payment`
--

LOCK TABLES `nathejk_payment` WRITE;
/*!40000 ALTER TABLE `nathejk_payment` DISABLE KEYS */;
INSERT INTO `nathejk_payment` VALUES (1,2014001,1400112000,450);
/*!40000 ALTER TABLE `nathejk_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_photo`
--

DROP TABLE IF EXISTS `nathejk_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_photo` (
  `id` int(10) unsigned NOT NULL,
  `teamId` int(10) unsigned NOT NULL,
  `memberId` int(10) unsigned NOT NULL,
  `createUts` int(10) unsigned NOT NULL,
  `deleteUts` int(10) unsigned NOT NULL,
  `source` mediumblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_photo`
--

LOCK TABLES `nathejk_photo` WRITE;
/*!40000 ALTER TABLE `nathejk_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `nathejk_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_sms`
--

DROP TABLE IF EXISTS `nathejk_sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_sms` (
  `id` int(10) unsigned NOT NULL,
  `uts` int(10) unsigned NOT NULL,
  `_post` text COLLATE utf8_danish_ci NOT NULL,
  `_get` text COLLATE utf8_danish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uts` (`uts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_sms`
--

LOCK TABLES `nathejk_sms` WRITE;
/*!40000 ALTER TABLE `nathejk_sms` DISABLE KEYS */;
/*!40000 ALTER TABLE `nathejk_sms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nathejk_team`
--

DROP TABLE IF EXISTS `nathejk_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nathejk_team` (
  `id` int(10) unsigned NOT NULL,
  `createdUts` int(10) unsigned NOT NULL,
  `verifiedUts` int(10) unsigned NOT NULL,
  `openedUts` int(10) unsigned NOT NULL,
  `finishedUts` int(10) unsigned NOT NULL,
  `canceledUts` int(10) unsigned NOT NULL,
  `deletedUts` int(10) unsigned NOT NULL,
  `lastModifyUts` int(10) unsigned NOT NULL,
  `startUts` int(10) unsigned NOT NULL,
  `finishUts` int(10) unsigned NOT NULL,
  `signupStatusTypeName` varchar(20) COLLATE utf8_danish_ci NOT NULL,
  `typeName` varchar(9) COLLATE utf8_danish_ci NOT NULL,
  `parentTeamId` int(10) unsigned NOT NULL,
  `ip` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `gruppe` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `division` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `korps` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactTitle` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactAddress` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactPostalCode` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactMail` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactPhone` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `contactRole` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `teamNumber` tinyint(3) unsigned NOT NULL,
  `ligaNumber` varchar(20) COLLATE utf8_danish_ci NOT NULL,
  `ligaNumberVerified` tinyint(3) unsigned DEFAULT NULL,
  `lokNumber` tinyint(3) unsigned NOT NULL,
  `memberCount` tinyint(3) unsigned NOT NULL,
  `paidMemberCount` tinyint(3) unsigned NOT NULL,
  `paid` tinyint(3) unsigned NOT NULL,
  `paidStatus` varchar(9) COLLATE utf8_danish_ci DEFAULT NULL,
  `remark` text COLLATE utf8_danish_ci NOT NULL,
  `photoId` int(10) unsigned NOT NULL,
  `photoUts` int(10) unsigned NOT NULL,
  `advSpejdNightCount` tinyint(3) unsigned NOT NULL,
  `arrivalName` varchar(20) COLLATE utf8_danish_ci NOT NULL,
  `checkedAtStart` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nathejk_team`
--

LOCK TABLES `nathejk_team` WRITE;
/*!40000 ALTER TABLE `nathejk_team` DISABLE KEYS */;
INSERT INTO `nathejk_team` VALUES (2014000,1399486040,0,0,0,0,0,0,0,0,'NEW','patrulje',0,'192.168.50.1','','','','','klaus','','','test@test.test','40733886','',0,'',NULL,0,0,0,0,NULL,'',0,0,0,'',0),(2014001,1399488214,1399491634,1399490355,1399495366,0,0,1399495366,0,0,'PAID','patrulje',0,'192.168.50.1','test','test','','dds','klaus','test','2720','test@test.test','40733886','leder',1,'',NULL,0,3,3,0,NULL,'',0,0,0,'vÃ¦lg ankomst',0);
/*!40000 ALTER TABLE `nathejk_team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pasta_error`
--

DROP TABLE IF EXISTS `pasta_error`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pasta_error` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `md5Key` binary(32) NOT NULL,
  `fileName` varchar(255) NOT NULL DEFAULT '',
  `lineNumber` smallint(5) unsigned NOT NULL DEFAULT '0',
  `errorNumber` int(10) unsigned NOT NULL DEFAULT '0',
  `message` varchar(512) NOT NULL,
  `productId` int(10) unsigned NOT NULL,
  `firstOccurrence` int(10) unsigned NOT NULL DEFAULT '0',
  `lastOccurrence` int(10) unsigned NOT NULL DEFAULT '0',
  `resetUts` int(10) unsigned NOT NULL,
  `recentOccurrenceCount` int(10) unsigned NOT NULL,
  `totalOccurrenceCount` int(10) unsigned NOT NULL,
  `stackTrace` mediumblob NOT NULL,
  `globals` mediumblob NOT NULL,
  `lastModifiedByUserId` int(10) unsigned NOT NULL DEFAULT '0',
  `statusType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `statusText` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `md5Key` (`md5Key`),
  KEY `lastOccurrence` (`lastOccurrence`),
  KEY `fileName` (`fileName`(100),`lineNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pasta_error`
--

LOCK TABLES `pasta_error` WRITE;
/*!40000 ALTER TABLE `pasta_error` DISABLE KEYS */;
/*!40000 ALTER TABLE `pasta_error` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sequence`
--

DROP TABLE IF EXISTS `sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sequence` (
  `sequence` varchar(255) COLLATE utf8_danish_ci NOT NULL DEFAULT '',
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sequence`
--

LOCK TABLES `sequence` WRITE;
/*!40000 ALTER TABLE `sequence` DISABLE KEYS */;
INSERT INTO `sequence` VALUES ('pasta_error',6),('nathejk_team',2014001),('nathejk_mail',4),('nathejk_member',3),('nathejk_payment',1),('teamNumber2014',1);
/*!40000 ALTER TABLE `sequence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `test` varchar(99) COLLATE utf8_danish_ci DEFAULT NULL,
  `id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vip_action`
--

DROP TABLE IF EXISTS `vip_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vip_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `productId` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `parameterListMethod` varchar(255) NOT NULL DEFAULT '',
  `parameterValueMethod` varchar(255) NOT NULL DEFAULT '',
  `parameterLabelMethod` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vip_action`
--

LOCK TABLES `vip_action` WRITE;
/*!40000 ALTER TABLE `vip_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `vip_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vip_customer`
--

DROP TABLE IF EXISTS `vip_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vip_customer` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 COLLATE latin1_danish_ci NOT NULL,
  `facilityId` tinyint(3) unsigned NOT NULL,
  `isActive` tinyint(3) unsigned NOT NULL,
  `managerVipUserId` int(10) unsigned NOT NULL,
  `consultantVipUserId` int(10) unsigned NOT NULL,
  `technicalAnchorVipUserId` int(10) unsigned NOT NULL,
  `cvrNo` int(10) unsigned NOT NULL,
  `passwordCheckerUris` varchar(100) CHARACTER SET latin1 COLLATE latin1_danish_ci NOT NULL,
  `languageCode` varchar(5) NOT NULL DEFAULT 'da',
  `countryCode` varchar(5) NOT NULL DEFAULT 'dk',
  `dsns` blob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vip_customer`
--

LOCK TABLES `vip_customer` WRITE;
/*!40000 ALTER TABLE `vip_customer` DISABLE KEYS */;
INSERT INTO `vip_customer` VALUES (4,'nathejk','Nathejk',1,1,0,0,0,0,'imaps://imap.gmail.com/?usernameDomain=@nathejk.dk','en','dk',NULL);
/*!40000 ALTER TABLE `vip_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vip_permission`
--

DROP TABLE IF EXISTS `vip_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vip_permission` (
  `actionId` int(10) unsigned NOT NULL DEFAULT '0',
  `doerId` int(10) unsigned NOT NULL DEFAULT '0',
  `parameterValue` int(10) unsigned NOT NULL DEFAULT '0',
  `allowed` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`actionId`,`doerId`,`parameterValue`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vip_permission`
--

LOCK TABLES `vip_permission` WRITE;
/*!40000 ALTER TABLE `vip_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `vip_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vip_product`
--

DROP TABLE IF EXISTS `vip_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vip_product` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `className` varchar(50) DEFAULT NULL,
  `siteClassName` varchar(50) NOT NULL,
  `customerId` int(10) unsigned NOT NULL COMMENT 'if product is made for one specific customer, this is the customerId; otherwise 0',
  `notifyEmails` varchar(255) NOT NULL DEFAULT 'tech@peytz.dk',
  `publishNotifyEmails` varchar(255) NOT NULL DEFAULT 'publish-notification@peytz.dk',
  `lastUnpublishedCommitUts` int(10) unsigned NOT NULL,
  `unpublishedFiles` text NOT NULL,
  `sortOrder` tinyint(4) NOT NULL DEFAULT '100',
  `isHidden` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vip_product`
--

LOCK TABLES `vip_product` WRITE;
/*!40000 ALTER TABLE `vip_product` DISABLE KEYS */;
INSERT INTO `vip_product` VALUES (9,'ing-ticket','Ticket',NULL,'',14,'tech@peytz.dk','publish-notification@peytz.dk,',0,'',1,0),(12,'enter','Brugere','Enter_Product','',0,'tech@peytz.dk','publish-notification@peytz.dk,',1280836667,'backend/user/undelete.php',13,0);
/*!40000 ALTER TABLE `vip_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vip_setting`
--

DROP TABLE IF EXISTS `vip_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vip_setting` (
  `id` smallint(5) unsigned NOT NULL,
  `customerId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `productId` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customerId` (`customerId`,`productId`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vip_setting`
--

LOCK TABLES `vip_setting` WRITE;
/*!40000 ALTER TABLE `vip_setting` DISABLE KEYS */;
INSERT INTO `vip_setting` VALUES (77,0,12,'validators','autologinurl://na?key=je6DVzS8UWqIGUVy&redirect=1&hosts=localhost,form://na,cookie://na?key=tNxfAnGkMdntNxfAnGkMdnXUfmt'),(88,0,12,'passwordCheckers','enter://na'),(9,0,12,'userClass','Enter_DefaultUser'),(84,0,12,'autologinHosts',''),(18,0,12,'fromEmail','blackhole@peytz.dk'),(61,0,12,'sendGoodbyeMailFromBackend','0'),(60,0,12,'sendGoodbyeMailFromFrontend','0'),(58,0,12,'sendWelcomeMailFromBackend','0'),(59,0,12,'sendWelcomeMailFromFrontend','0'),(11,0,12,'templateEdit','edit.tpl'),(10,0,12,'templateIndex','index.tpl'),(6,0,12,'userClassEmailIsUnique','1'),(733,4,12,'validators','autologinurl://na?key=BUrYrOXyBfphx5Pt&redirect=1&hosts=*,form://na,cookie://na?name=snaps&key=foo'),(35,4,12,'passwordKey','peytz');
/*!40000 ALTER TABLE `vip_setting` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-22 17:02:22

/*
SQLyog Community v9.50 
MySQL - 5.1.58-1ubuntu1 : Database - ol_beats
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ol_beats` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `ol_beats`;

/*Table structure for table `config` */

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ConfigKey` varchar(200) DEFAULT NULL,
  `Value` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Table structure for table `fb_friends` */

DROP TABLE IF EXISTS `fb_friends`;

CREATE TABLE `fb_friends` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `FbId` varchar(350) DEFAULT NULL,
  `Name` varchar(400) DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `fb_groups` */

DROP TABLE IF EXISTS `fb_groups`;

CREATE TABLE `fb_groups` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `GroupId` varchar(100) DEFAULT NULL,
  `Email` varchar(350) DEFAULT NULL,
  `Name` varchar(400) DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Table structure for table `fb_posts` */

DROP TABLE IF EXISTS `fb_posts`;

CREATE TABLE `fb_posts` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(300) DEFAULT NULL,
  `Post` text,
  `Image` varchar(400) DEFAULT NULL,
  `Time` datetime DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Table structure for table `post_log` */

DROP TABLE IF EXISTS `post_log`;

CREATE TABLE `post_log` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PostId` int(11) DEFAULT NULL,
  `FbFriendId` varchar(50) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

/*Table structure for table `post_queue` */

DROP TABLE IF EXISTS `post_queue`;

CREATE TABLE `post_queue` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `PostId` int(11) DEFAULT NULL,
  `GroupId` varchar(100) DEFAULT NULL,
  `SentTime` datetime DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `tabs` */

DROP TABLE IF EXISTS `tabs`;

CREATE TABLE `tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabHtml` longtext,
  `pageId` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `FbUserId` varchar(20) NOT NULL,
  `FbEmail` varchar(100) DEFAULT NULL,
  `AccessToken` varchar(500) DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`Id`,`FbUserId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

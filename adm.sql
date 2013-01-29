-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 28, 2013 at 09:03 
-- Server version: 5.1.41
-- PHP Version: 5.3.1

CREATE SCHEMA IF NOT EXISTS adm;

USE adm;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `adm`
--

-- --------------------------------------------------------

--
-- Table structure for table `adm_chat`
--

DROP TABLE IF EXISTS `adm_chat`;
CREATE TABLE IF NOT EXISTS `adm_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `messtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` mediumtext CHARACTER SET cp1251,
  `viewed` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `adm_chat`
--

INSERT INTO `adm_chat` (`id`, `sender_id`, `receiver_id`, `messtime`, `message`, `viewed`) VALUES
(1, 3, NULL, '2013-01-26 23:02:27', 'test', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `adm_chatviewed`
--

DROP TABLE IF EXISTS `adm_chatviewed`;
CREATE TABLE IF NOT EXISTS `adm_chatviewed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `chat_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `adm_chatviewed`
--

INSERT INTO `adm_chatviewed` (`id`, `user_id`, `chat_id`) VALUES
(1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `adm_rights`
--

DROP TABLE IF EXISTS `adm_rights`;
CREATE TABLE IF NOT EXISTS `adm_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(250) CHARACTER SET cp1251 DEFAULT NULL,
  `view` int(11) DEFAULT NULL,
  `edit` int(11) DEFAULT NULL,
  `add` int(11) DEFAULT NULL,
  `delete_` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` varchar(250) CHARACTER SET cp1251 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;

--
-- Dumping data for table `adm_rights`
--

INSERT INTO `adm_rights` (`id`, `table_name`, `view`, `edit`, `add`, `delete_`, `user_id`, `description`) VALUES
(1, 'tema', 1, 1, 1, 0, 3, 'doc_type_1'),
(2, 'excl', 1, 1, 1, 0, 3, 'doc_type_2'),
(3, 'doc', 1, 1, 1, 0, 3, 'doc_type_3'),
(4, 'news', 1, 1, 1, 0, 3, 'doc_type_4'),
(5, 'blog', 1, 1, 0, 0, 3, 'doc_type_5'),
(6, 'rubr', NULL, NULL, NULL, NULL, 3, 'tags'),
(7, 'mtag', NULL, NULL, NULL, NULL, 3, 'general tags'),
(8, 'utag', NULL, NULL, NULL, NULL, 3, 'user tags'),
(9, 'comment', 1, 1, 1, 1, 3, 'comments'),
(10, 'nhead', 1, 1, 1, 1, 3, 'doc headers'),
(11, 'vars', 1, 1, 1, 1, 3, 'settings'),
(12, 'photo', 1, 1, 1, 1, 3, 'Best photo'),
(13, 'mov', 1, 1, 1, 1, 3, 'Best movie'),
(14, 'ukrtwn', NULL, NULL, NULL, NULL, 3, 'Towns'),
(15, 'fotobnk', 1, 1, 1, 1, 3, 'Photo store'),
(16, 'tema', 1, 1, 1, 1, 1, 'doc_type_1'),
(17, 'excl', 1, 1, 1, 1, 1, 'doc_type_2'),
(18, 'doc', 1, 1, 1, 1, 1, 'doc_type_3'),
(19, 'news', 1, 1, 1, 1, 1, 'doc_type_4'),
(20, 'blog', 1, 1, 0, 1, 1, 'doc_type_5'),
(21, 'rubr', 1, 1, 1, 1, 1, 'tags'),
(22, 'mtag', 1, 1, 1, 1, 1, 'general tags'),
(23, 'utag', 1, 1, 1, 1, 1, 'user tags'),
(24, 'comment', 1, 1, 1, 1, 1, 'comments'),
(25, 'nhead', 1, 1, 1, 1, 1, 'doc headers'),
(26, 'vars', 1, 1, 1, 1, 1, 'settings'),
(27, 'photo', 1, 1, 1, 1, 1, 'Best photo'),
(28, 'mov', 1, 1, 1, 1, 1, 'Best movie'),
(29, 'ukrtwn', 1, 1, 1, 1, 1, 'Towns'),
(30, 'fotobnk', 1, 1, 1, 1, 1, 'Photo store'),
(31, 'tema', NULL, NULL, NULL, NULL, 2, 'doc_type_1'),
(32, 'excl', NULL, NULL, NULL, NULL, 2, 'doc_type_2'),
(33, 'doc', NULL, NULL, NULL, NULL, 2, 'doc_type_3'),
(34, 'news', NULL, NULL, NULL, NULL, 2, 'doc_type_4'),
(35, 'blog', NULL, NULL, NULL, NULL, 2, 'doc_type_5'),
(36, 'rubr', NULL, NULL, NULL, NULL, 2, 'tags'),
(37, 'mtag', NULL, NULL, NULL, NULL, 2, 'general tags'),
(38, 'utag', NULL, NULL, NULL, NULL, 2, 'user tags'),
(39, 'comment', NULL, NULL, NULL, NULL, 2, 'comments'),
(40, 'nhead', NULL, NULL, NULL, NULL, 2, 'doc headers'),
(41, 'vars', NULL, NULL, NULL, NULL, 2, 'settings'),
(42, 'photo', NULL, NULL, NULL, NULL, 2, 'Best photo'),
(43, 'mov', NULL, NULL, NULL, NULL, 2, 'Best movie'),
(44, 'ukrtwn', NULL, NULL, NULL, NULL, 2, 'Towns'),
(45, 'fotobnk', NULL, NULL, NULL, NULL, 2, 'Photo store'),
(46, 'tema', 1, 1, 1, NULL, 5, 'doc_type_1'),
(47, 'excl', 1, 1, 1, NULL, 5, 'doc_type_2'),
(48, 'doc', 1, 1, 1, NULL, 5, 'doc_type_3'),
(49, 'news', 1, 1, 1, 0, 5, 'doc_type_4'),
(50, 'blog', 1, NULL, 0, NULL, 5, 'doc_type_5'),
(51, 'rubr', NULL, NULL, NULL, NULL, 5, 'tags'),
(52, 'mtag', NULL, NULL, NULL, NULL, 5, 'general tags'),
(53, 'utag', NULL, NULL, NULL, NULL, 5, 'user tags'),
(54, 'comment', 1, 1, 1, 1, 5, 'comments'),
(55, 'nhead', 1, 1, 1, 1, 5, 'doc headers'),
(56, 'vars', 1, 1, 1, 1, 5, 'settings'),
(57, 'photo', 1, 1, 1, 1, 5, 'Best photo'),
(58, 'mov', 1, 1, 1, 1, 5, 'Best movie'),
(59, 'ukrtwn', NULL, NULL, NULL, NULL, 5, 'Towns'),
(60, 'fotobnk', NULL, NULL, NULL, NULL, 5, 'Photo store'),
(61, 'tema', 1, 1, 1, NULL, 6, 'doc_type_1'),
(62, 'excl', 1, 1, 1, NULL, 6, 'doc_type_2'),
(63, 'doc', 1, 1, 1, NULL, 6, 'doc_type_3'),
(64, 'news', 1, 1, 1, NULL, 6, 'doc_type_4'),
(65, 'blog', 1, 1, 0, 0, 6, 'doc_type_5'),
(66, 'rubr', NULL, NULL, NULL, NULL, 6, 'tags'),
(67, 'mtag', NULL, NULL, NULL, NULL, 6, 'general tags'),
(68, 'utag', NULL, NULL, NULL, NULL, 6, 'user tags'),
(69, 'comment', 1, 1, 1, NULL, 6, 'comments'),
(70, 'nhead', NULL, NULL, NULL, NULL, 6, 'doc headers'),
(71, 'vars', 0, 0, 0, 0, 6, 'settings'),
(72, 'photo', 0, 0, 0, 0, 6, 'Best photo'),
(73, 'mov', 0, 0, 0, 0, 6, 'Best movie'),
(74, 'ukrtwn', NULL, NULL, NULL, NULL, 6, 'Towns'),
(75, 'fotobnk', 1, 0, 1, NULL, 6, 'Photo store'),
(76, 'tema', 1, 1, 1, NULL, 7, 'doc_type_1'),
(77, 'excl', 1, 1, 1, NULL, 7, 'doc_type_2'),
(78, 'doc', 1, 1, 1, NULL, 7, 'doc_type_3'),
(79, 'news', 1, 1, 1, NULL, 7, 'doc_type_4'),
(80, 'blog', 0, 0, 0, NULL, 7, 'doc_type_5'),
(81, 'rubr', NULL, NULL, NULL, NULL, 7, 'tags'),
(82, 'mtag', NULL, NULL, NULL, NULL, 7, 'general tags'),
(83, 'utag', NULL, NULL, NULL, NULL, 7, 'user tags'),
(84, 'comment', NULL, NULL, NULL, NULL, 7, 'comments'),
(85, 'nhead', NULL, NULL, NULL, NULL, 7, 'doc headers'),
(86, 'vars', NULL, NULL, NULL, NULL, 7, 'settings'),
(87, 'photo', 0, NULL, 0, NULL, 7, 'Best photo'),
(88, 'mov', NULL, NULL, NULL, NULL, 7, 'Best movie'),
(89, 'ukrtwn', NULL, NULL, NULL, NULL, 7, 'Towns'),
(90, 'fotobnk', 1, 1, 1, NULL, 7, 'Photo store'),
(91, 'spam', NULL, NULL, NULL, NULL, 3, 'Spam'),
(92, 'spam', 1, 1, 1, 1, 5, 'Spam'),
(93, 'spam', NULL, NULL, NULL, NULL, 6, 'Spam'),
(94, 'spam', NULL, NULL, NULL, NULL, 7, 'Spam'),
(95, 'spam', NULL, NULL, NULL, NULL, 2, 'Spam'),
(96, 'spam', NULL, NULL, NULL, NULL, 1, 'Spam'),
(97, 'photostore', 1, 1, 1, NULL, 3, 'Image day'),
(98, 'imageday', 1, 1, 1, NULL, 3, 'photos'),
(99, 'photostore', 1, 1, 1, NULL, 5, 'Image day'),
(100, 'imageday', 1, 1, 1, 1, 5, 'photos'),
(101, 'photostore', NULL, NULL, NULL, NULL, 2, 'Image day'),
(102, 'imageday', NULL, NULL, NULL, NULL, 2, 'photos'),
(103, 'photostore', 1, 1, 1, NULL, 7, 'Image day'),
(104, 'imageday', NULL, NULL, NULL, NULL, 7, 'photos'),
(105, 'photostore', 1, 1, 1, NULL, 6, 'Image day'),
(106, 'imageday', 1, 1, 1, NULL, 6, 'photos'),
(107, 'photostore', NULL, NULL, NULL, NULL, 1, 'Image day'),
(108, 'imageday', NULL, NULL, NULL, NULL, 1, 'photos');

-- --------------------------------------------------------

--
-- Table structure for table `adm_session`
--

DROP TABLE IF EXISTS `adm_session`;
CREATE TABLE IF NOT EXISTS `adm_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET cp1251 DEFAULT '',
  `time` varchar(14) CHARACTER SET cp1251 DEFAULT '',
  `session_id` varchar(32) CHARACTER SET cp1251 NOT NULL DEFAULT '0',
  `userid` int(11) DEFAULT '0',
  `usertype` varchar(50) CHARACTER SET cp1251 DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `adm_session`
--

INSERT INTO `adm_session` (`id`, `username`, `time`, `session_id`, `userid`, `usertype`) VALUES
(31, 'Sergiy Rasimov', '1359325329', '381b987204891fa8399740e98cce5d2c', 3, '2'),
(32, 'Sergiy Rasimov', '1359325865', '33573d5e2e0a4ee4d4c8252f059be58d', 3, '2');

-- --------------------------------------------------------

--
-- Table structure for table `adm_users`
--

DROP TABLE IF EXISTS `adm_users`;
CREATE TABLE IF NOT EXISTS `adm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET cp1251 NOT NULL,
  `username` varchar(255) CHARACTER SET cp1251 NOT NULL,
  `password` varchar(150) CHARACTER SET cp1251 NOT NULL DEFAULT '',
  `usertype` char(1) CHARACTER SET cp1251 DEFAULT '',
  `lastvisit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `adm_users`
--

INSERT INTO `adm_users` (`id`, `name`, `username`, `password`, `usertype`, `lastvisit`) VALUES
(3, 'zerg', 'Sergiy Rasimov', '8c56d69a8727346ef95f0401f507d1f0', '2', NULL),
(4, 'test', 'test', 'd41d8cd98f00b204e9800998ecf8427e', '4', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `adm_usertypes`
--

DROP TABLE IF EXISTS `adm_usertypes`;
CREATE TABLE IF NOT EXISTS `adm_usertypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usertype` char(1) CHARACTER SET cp1251 DEFAULT '',
  `typename` varchar(50) CHARACTER SET cp1251 DEFAULT '',
  `closed` int(11) DEFAULT NULL,
  `st_tema` int(11) NOT NULL DEFAULT '1',
  `st_news` int(11) NOT NULL DEFAULT '1',
  `st_exc` int(11) NOT NULL DEFAULT '1',
  `st_doc` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `adm_usertypes`
--

INSERT INTO `adm_usertypes` (`id`, `usertype`, `typename`, `closed`, `st_tema`, `st_news`, `st_exc`, `st_doc`) VALUES
(1, '4', 'superadmin', NULL, 4, 4, 4, 4),
(3, '2', 'role 1', NULL, 4, 4, 4, 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

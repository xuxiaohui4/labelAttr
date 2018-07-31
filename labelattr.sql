-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2018-07-06 10:34:56
-- 服务器版本: 5.5.60-0ubuntu0.14.04.1
-- PHP 版本: 5.5.9-1ubuntu4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `labelattr`
--

USE labelattr

-- --------------------------------------------------------

--
-- 表的结构 `img_label`
--

CREATE TABLE IF NOT EXISTS `img_label` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) NOT NULL DEFAULT '0',
  `data_source` varchar(255) DEFAULT NULL,
  `squarePlate` tinyint(1) DEFAULT NULL,
  `identifiable` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

alter table img_label add plateNum varchar(255);
-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT 'unknown',
  `passwd` varchar(255) DEFAULT '000000',
  PRIMARY KEY (`Id`,`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- 表的结构 `user_label`
--

CREATE TABLE IF NOT EXISTS `user_label` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `imgid` int(11) unsigned NOT NULL DEFAULT '0',
  `attr` varchar(255) DEFAULT NULL,
  `label_time` datetime DEFAULT NULL ,
  `label_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

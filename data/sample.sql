-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 22, 2013 at 06:18 PM
-- Server version: 5.5.32-0ubuntu0.13.04.1
-- PHP Version: 5.3.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `oldstuff`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `state` smallint(6) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `cat_name`, `description`, `state`) VALUES
(1, 'Book', '', 1),
(2, 'Clothes', '', 1),
(3, 'Electronics', '', 1),
(5, 'Jewelry', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stuff`
--

DROP TABLE IF EXISTS `stuff`;
CREATE TABLE IF NOT EXISTS `stuff` (
  `stuff_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `price` double NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `state` smallint(6) NOT NULL,
  `stuff_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:simple_array)',
  `purpose` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `desired_stuff` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`stuff_id`),
  KEY `IDX_5941F83EA76ED395` (`user_id`),
  KEY `IDX_5941F83EE6ADA943` (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

--
-- Dumping data for table `stuff`
--

INSERT INTO `stuff` (`stuff_id`, `user_id`, `price`, `description`, `state`, `stuff_name`, `image`, `purpose`, `desired_stuff`, `cat_id`) VALUES
(1, 1, 500000, 'Enim ultricies pellentesque placerat sit pid diam magnis tempor porta! Ut augue, dictumst?', 1, 'Jean Jacket', 'upload/01.jpg', 'sell', '', 2),
(2, 2, 200000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Clock', 'upload/02.jpg', 'sell', '', 5),
(3, 3, 300000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Harry Potter Series', 'upload/03.jpg', 'trade', 'Jeans', 1),
(4, 2, 10000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Acer Laptop', 'upload/04.jpg', 'sell', '', 3),
(5, 2, 4000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Samsung Laptop', 'upload/05.jpg', 'sell', '', 5),
(6, 2, 75000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Computer Fan', 'upload/06.jpg', 'sell', '', 3),
(7, 1, 500000, 'Enim ultricies pellentesque placerat sit pid diam magnis tempor porta! Ut augue, dictumst?', 1, 'Jean Jacket', 'upload/01.jpg', 'sell', '', 2),
(8, 2, 200000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Clock', 'upload/02.jpg', 'sell', '', 5),
(9, 3, 300000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Harry Potter Series', 'upload/03.jpg', 'trade', 'Jeans', 1),
(10, 2, 10000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Acer Laptop', 'upload/04.jpg', 'sell', '', 3),
(11, 4, 4000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Samsung Laptop', 'upload/05.jpg', 'sell', '', 5),
(12, 2, 75000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Computer Fan', 'upload/06.jpg', 'sell', '', 3),
(14, 1, 500000, 'Enim ultricies pellentesque placerat sit pid diam magnis tempor porta! Ut augue, dictumst?', 1, 'Jean Jacket', 'upload/01.jpg', 'sell', '', 2),
(15, 2, 200000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Clock', 'upload/02.jpg', 'sell', '', 5),
(16, 3, 300000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Harry Potter Series', 'upload/03.jpg', 'trade', 'Jeans', 1),
(17, 2, 10000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Acer Laptop', 'upload/04.jpg', 'sell', '', 3),
(18, 4, 4000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Samsung Laptop', 'upload/05.jpg', 'sell', '', 5),
(19, 2, 75000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Computer Fan', 'upload/06.jpg', 'sell', '', 3),
(20, 1, 500000, 'Enim ultricies pellentesque placerat sit pid diam magnis tempor porta! Ut augue, dictumst?', 1, 'Jean Jacket', 'upload/01.jpg', 'sell', '', 2),
(21, 2, 200000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Clock', 'upload/02.jpg', 'sell', '', 5),
(22, 3, 300000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Harry Potter Series', 'upload/03.jpg', 'trade', 'Jeans', 1),
(23, 4, 10000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Acer Laptop', 'upload/04.jpg', 'sell', '', 3),
(24, 2, 4000000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Samsung Laptop', 'upload/05.jpg', 'sell', '', 5),
(25, 4, 75000, 'Nascetur. Sed cum ac magnis! Nascetur ac, montes, ac augue cum, amet, ac rhoncus purus, in nisi aliquet, est', 1, 'Old Computer Fan', 'upload/06.jpg', 'sell', '', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `display_name`, `password`, `state`) VALUES
(1, 'herophuong93@gmail.com', 'Lê Hoàng Phương', '$2y$14$XMud2S3aMQBigrYa0qssE.rv0IHuupz2WWH6/.RFNvtG10MFJzINW', 1),
(2, 'piavghoang@gmail.com', 'Trịnh Huy Hoàng', '$2y$14$8s3MzxtyyLKRQhltKzzQH./8SZo5OwE42lxiIuR6zgBhg42P2P5B.', 1),
(3, 'sonnguyenhoang2309@gmail.com', 'Nguyễn Hoàng Sơn', '$2y$14$4bMY5d2GDKjn5CMOhULUIOQniHhjyvlvndNXDQW3ufUl0kISadiwq', 1),
(4, 'forgottenone3510@gmail.com', 'Tăng Thế Cường', '$2y$14$K2PzZENAlN34g4c6/QT6Temx6WdKapmDpcQs0AT2kf9iQLgVMrJty', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stuff`
--
ALTER TABLE `stuff`
  ADD CONSTRAINT `FK_5941F83EE6ADA943` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`),
  ADD CONSTRAINT `FK_5941F83EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- 主機: 127.0.0.1
-- 產生時間： 2018 年 08 月 04 日 20:57
-- 伺服器版本: 10.1.32-MariaDB
-- PHP 版本： 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `restful3`
--

-- --------------------------------------------------------

--
-- 資料表結構 `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `Token` text COLLATE utf8_unicode_ci NOT NULL,
  `Account` text COLLATE utf8_unicode_ci NOT NULL,
  `Bio` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `accounts`
--

INSERT INTO `accounts` (`id`, `Token`, `Account`, `Bio`, `created_at`, `updated_at`) VALUES
(4, 'Ex9k9e2', 'joshTest', 'Hello world.', '2018-08-04 08:26:44', '2018-08-04 08:26:44');

-- --------------------------------------------------------

--
-- 資料表結構 `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `id2` text COLLATE utf8_unicode_ci NOT NULL,
  `AccountToken` text COLLATE utf8_unicode_ci NOT NULL,
  `Title` text COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `albums`
--

INSERT INTO `albums` (`id`, `id2`, `AccountToken`, `Title`, `Description`, `created_at`, `updated_at`) VALUES
(5, 'BIV8T', 'Ex9k9e2', 'hihi', '我的第二個相簿', '2018-08-04 18:56:35', '2018-08-04 10:56:35');

-- --------------------------------------------------------

--
-- 資料表結構 `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `id2` text COLLATE utf8_unicode_ci NOT NULL,
  `AlbumId` text COLLATE utf8_unicode_ci NOT NULL,
  `Cover` text COLLATE utf8_unicode_ci NOT NULL,
  `Title` text COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  `view` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 資料表的匯出資料 `images`
--

INSERT INTO `images` (`id`, `id2`, `AlbumId`, `Cover`, `Title`, `Description`, `view`, `created_at`, `updated_at`) VALUES
(34, '6fs112o7He', 'BIV8T', '1', 'test', '1324', 0, '2018-08-04 18:54:07', '2018-08-04 10:54:07'),
(35, 'NIl5ap3mV8', 'BIV8T', '1', 'test', '1324', 0, '2018-08-04 18:56:19', '2018-08-04 10:56:19'),
(36, 'zau2734xdY', 'BIV8T', '0', 'test', '1324', 0, '2018-08-04 18:26:05', '2018-08-04 09:57:34');

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表 AUTO_INCREMENT `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表 AUTO_INCREMENT `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

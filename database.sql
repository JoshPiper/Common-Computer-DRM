SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `darknet_articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `image_url` varchar(2048) NOT NULL,
  `server_key` int(10) UNSIGNED NOT NULL,
  `price` float UNSIGNED NOT NULL,
  `description` varchar(512) NOT NULL,
  `category` int(10) UNSIGNED NOT NULL,
  `class` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `darknet_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `image_url` varchar(2048) NOT NULL,
  `server_key` int(10) UNSIGNED NOT NULL,
  `description` varchar(512) NOT NULL,
  `footer` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `player_keys` (
  `id` int(10) UNSIGNED NOT NULL,
  `steamid` bigint(20) UNSIGNED NOT NULL,
  `server` int(10) UNSIGNED NOT NULL,
  `player_key` char(32) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `servers` (
  `id` int(10) UNSIGNED NOT NULL,
  `server_key` int(10) UNSIGNED NOT NULL,
  `ipv4` bigint(20) UNSIGNED NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `server_keys` (
  `id` int(10) UNSIGNED NOT NULL,
  `steamid` bigint(20) UNSIGNED NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `server_key` char(32) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `word_backgrounds` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `server_key` int(10) UNSIGNED NOT NULL,
  `url` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `word_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `server_key` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `word_docs` (
  `id` int(10) UNSIGNED NOT NULL,
  `steamid` bigint(20) UNSIGNED NOT NULL,
  `server_key` int(10) UNSIGNED NOT NULL,
  `category` int(10) UNSIGNED NOT NULL,
  `background` int(10) UNSIGNED NOT NULL,
  `title` varchar(128) NOT NULL,
  `content` varchar(4096) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `darknet_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_key` (`server_key`),
  ADD KEY `category` (`category`);

ALTER TABLE `darknet_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_key` (`server_key`);

ALTER TABLE `player_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `player_key` (`player_key`),
  ADD KEY `server` (`server`);

ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_key` (`server_key`);

ALTER TABLE `server_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `steamid` (`steamid`),
  ADD UNIQUE KEY `server_key` (`server_key`);

ALTER TABLE `word_backgrounds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_key` (`server_key`);

ALTER TABLE `word_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_key` (`server_key`);

ALTER TABLE `word_docs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `server_key` (`server_key`),
  ADD KEY `category` (`category`),
  ADD KEY `background` (`background`);


ALTER TABLE `darknet_articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `darknet_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
ALTER TABLE `player_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;
ALTER TABLE `servers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
ALTER TABLE `server_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `word_backgrounds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `word_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
ALTER TABLE `word_docs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

ALTER TABLE `darknet_articles`
  ADD CONSTRAINT `darknet_articles_ibfk_1` FOREIGN KEY (`server_key`) REFERENCES `server_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `darknet_articles_ibfk_2` FOREIGN KEY (`category`) REFERENCES `darknet_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `darknet_categories`
  ADD CONSTRAINT `darknet_categories_ibfk_1` FOREIGN KEY (`server_key`) REFERENCES `server_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `player_keys`
  ADD CONSTRAINT `player_keys_ibfk_1` FOREIGN KEY (`server`) REFERENCES `servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `servers`
  ADD CONSTRAINT `servers_ibfk_1` FOREIGN KEY (`server_key`) REFERENCES `server_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `word_backgrounds`
  ADD CONSTRAINT `word_backgrounds_ibfk_1` FOREIGN KEY (`server_key`) REFERENCES `server_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `word_categories`
  ADD CONSTRAINT `word_categories_ibfk_1` FOREIGN KEY (`server_key`) REFERENCES `server_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `word_docs`
  ADD CONSTRAINT `word_docs_ibfk_1` FOREIGN KEY (`server_key`) REFERENCES `server_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `word_docs_ibfk_2` FOREIGN KEY (`category`) REFERENCES `word_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `word_docs_ibfk_3` FOREIGN KEY (`background`) REFERENCES `word_backgrounds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

CREATE TABLE `event` (
  `id` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `date` date NOT NULL,
  `location` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `user_id` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `track_id` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `artist_name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `album_name` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `album_image_url` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `item_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `user` (
  `id` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `image` varchar(500) COLLATE utf8_czech_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
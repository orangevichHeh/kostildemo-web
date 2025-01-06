-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.7.34-37 - Percona Server (GPL), Release '37', Revision '7c516e9'
-- Операционная система:         debian-linux-gnu
-- HeidiSQL Версия:              11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Дамп структуры для таблица ash_demo.data_registry
CREATE TABLE IF NOT EXISTS `data_registry` (
  `data_key` varchar(32) NOT NULL,
  `data_value` mediumblob NOT NULL,
  PRIMARY KEY (`data_key`),
  UNIQUE KEY `data_registry_data_key_uindex` (`data_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ash_demo.data_registry: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `data_registry` DISABLE KEYS */;
INSERT INTO `data_registry` (`data_key`, `data_value`) VALUES
	('cleanupRunTime', _binary 0x30);
/*!40000 ALTER TABLE `data_registry` ENABLE KEYS */;

-- Дамп структуры для таблица ash_demo.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `name` varchar(128) NOT NULL,
  `runned_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ash_demo.migration: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` (`name`, `runned_at`) VALUES
	('App\\Migration\\Install', 1625498950);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;

-- Дамп структуры для таблица ash_demo.record
CREATE TABLE IF NOT EXISTS `record` (
  `record_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `demo_id` varchar(36) NOT NULL,
  `server_id` int(11) NOT NULL,
  `map` varchar(128) NOT NULL,
  `uploaded_at` int(11) NOT NULL,
  `started_at` int(11) NOT NULL,
  `finished_at` int(11) NOT NULL,
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ash_demo.record: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `record` DISABLE KEYS */;
/*!40000 ALTER TABLE `record` ENABLE KEYS */;

-- Дамп структуры для таблица ash_demo.record_player
CREATE TABLE IF NOT EXISTS `record_player` (
  `record_id` int(11) unsigned NOT NULL,
  `account_id` int(11) unsigned NOT NULL,
  `username` varchar(128) NOT NULL,
  PRIMARY KEY (`record_id`,`account_id`),
  CONSTRAINT `FK_record_player_record` FOREIGN KEY (`record_id`) REFERENCES `record` (`record_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы ash_demo.record_player: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `record_player` DISABLE KEYS */;
/*!40000 ALTER TABLE `record_player` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

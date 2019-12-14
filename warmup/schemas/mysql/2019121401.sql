
CREATE TABLE IF NOT EXISTS `entities_search` (
  `_id` varchar(32) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `entities_search` SELECT `_id`, `search` FROM `entities`;

ALTER TABLE `entities` DROP COLUMN `search`;
ALTER TABLE `entities` ENGINE=InnoDB;




CREATE TABLE IF NOT EXISTS `reader_search` (
  `_id` varchar(32) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reader_search` SELECT `_id`, `search` FROM `reader`;

ALTER TABLE `reader` DROP COLUMN `search`;
ALTER TABLE `reader` ENGINE=InnoDB;




CREATE TABLE IF NOT EXISTS `config_search` (
  `_id` varchar(32) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `config_search` SELECT `_id`, `search` FROM `config`;

ALTER TABLE `config` DROP COLUMN `search`;
ALTER TABLE `config` ENGINE=InnoDB;




REPLACE INTO `versions` VALUES('schema', '2019121401');
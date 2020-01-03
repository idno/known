
CREATE TABLE IF NOT EXISTS `entities_search` (
  `_id` varchar(32) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `entities_search` SELECT `_id`, `search` FROM `entities`;

ALTER TABLE `entities` DROP COLUMN `search`;
ALTER TABLE `entities` ENGINE=InnoDB;

ALTER TABLE `entities_search` ADD CONSTRAINT `es_id_id` FOREIGN KEY (`_id`) REFERENCES `entities` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE;




CREATE TABLE IF NOT EXISTS `reader_search` (
  `_id` varchar(32) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reader_search` SELECT `_id`, `search` FROM `reader`;

ALTER TABLE `reader` DROP COLUMN `search`;
ALTER TABLE `reader` ENGINE=InnoDB;

ALTER TABLE `reader_search` ADD CONSTRAINT `rs_id_id` FOREIGN KEY (`_id`) REFERENCES `reader` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE;




CREATE TABLE IF NOT EXISTS `config_search` (
  `_id` varchar(32) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `config_search` SELECT `_id`, `search` FROM `config`;

ALTER TABLE `config` DROP COLUMN `search`;
ALTER TABLE `config` ENGINE=InnoDB;

ALTER TABLE `config_search` ADD CONSTRAINT `cs_id_id` FOREIGN KEY (`_id`) REFERENCES `config` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE;



REPLACE INTO `versions` VALUES('schema', '2019121401');

CREATE TABLE IF NOT EXISTS `entities_metadata` (
  `_id` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  KEY `value` (`value`(255)),
  KEY `name` (`name`),
  KEY (`_id`),
  CONSTRAINT `em_id_id` FOREIGN KEY (`_id`) REFERENCES `entities` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `entities_metadata` SELECT `_id`, `name`, `value` FROM metadata WHERE `collection` = 'entities';

CREATE TABLE IF NOT EXISTS `config_metadata` (
  `_id` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  KEY `value` (`value`(255)),
  KEY `name` (`name`),
  KEY (`_id`),
  CONSTRAINT `cm_id_id` FOREIGN KEY (`_id`) REFERENCES `config` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `config_metadata` SELECT `_id`, `name`, `value` FROM metadata WHERE `collection` = 'config';


CREATE TABLE IF NOT EXISTS `reader_metadata` (
  `_id` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  KEY `value` (`value`(255)),
  KEY `name` (`name`),
  KEY (`_id`),
  CONSTRAINT `rm_id_id` FOREIGN KEY (`_id`) REFERENCES `reader` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reader_metadata` SELECT `_id`, `name`, `value` FROM metadata WHERE `collection` = 'reader';



-- Create a backup so we can clear it up later
CREATE TABLE deprecated_metadata LIKE metadata;
DROP TABLE metadata;


REPLACE INTO `versions` VALUES('schema', '2020042101');
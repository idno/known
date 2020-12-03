
CREATE TABLE IF NOT EXISTS `site_search` (
  `_id` varchar(36) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`),
  CONSTRAINT `ss_id_id` FOREIGN KEY (`_id`) REFERENCES `site` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

REPLACE INTO `versions` VALUES('schema', '2020120301');

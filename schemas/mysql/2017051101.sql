
CREATE TABLE IF NOT EXISTS `tombstones` (
  `_id` varchar(32) NOT NULL,
  `id` varchar(32) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `_id` (`_id`),
  KEY `id` (`id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


REPLACE INTO `versions` VALUES('schema', '2017051101');
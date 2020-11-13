--
-- Base Known schema
--

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `site` (
    `_id` varchar(36) NOT NULL,
    `domain` varchar(255) NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `contents` longblob NOT NULL,

    PRIMARY KEY (`_id`),
    KEY `domain` (`domain`),
    KEY `created` (`created`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `site_metadata` (
  `_id` varchar(36) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  KEY `value` (`value`(255)),
  KEY `name` (`name`),
  KEY (`_id`),
  CONSTRAINT `sm_id_id` FOREIGN KEY (`_id`) REFERENCES `site` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



REPLACE INTO `versions` VALUES('schema', '2020111301');

--
-- Base Known schema
--

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `site` (
    `uuid` varchar(255) NOT NULL,
    `_id` varchar(36) NOT NULL,
    `siteid` varchar(36),
    `owner` varchar(255) NOT NULL,
    `entity_subtype` varchar(64) NOT NULL,
    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `contents` longblob NOT NULL,
    `publish_status` varchar(255) NOT NULL DEFAULT 'published',

    PRIMARY KEY (`uuid`),
    KEY `owner` (`owner`,`created`),
    KEY `_id` (`_id`),
    KEY `entity_subtype` (`entity_subtype`),
    KEY `publish_status` (`publish_status`),
    KEY `siteid` (`siteid`)

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


CREATE TABLE IF NOT EXISTS `site_search` (
  `_id` varchar(36) NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`_id`),
  FULLTEXT KEY `search` (`search`),
  CONSTRAINT `ss_id_id` FOREIGN KEY (`_id`) REFERENCES `site` (`_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `config` ADD COLUMN `siteid` varchar(36) AFTER `_id`;
ALTER TABLE `entities` ADD COLUMN `siteid` varchar(36) AFTER `_id`;
ALTER TABLE `reader` ADD COLUMN `siteid` varchar(36) AFTER `_id`;

ALTER TABLE `config` ADD KEY `siteid` (`siteid`);
ALTER TABLE `entities` ADD KEY `siteid` (`siteid`);
ALTER TABLE `reader` ADD KEY `siteid` (`siteid`);



ALTER TABLE  `config` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `config_metadata` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `config_search` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `entities` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `entities_search` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `entities_metadata` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `reader` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `reader_search` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `reader_metadata` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;


REPLACE INTO `versions` VALUES('schema', '2020111301');

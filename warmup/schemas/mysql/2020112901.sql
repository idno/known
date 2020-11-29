
ALTER TABLE  `config` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `config_metadata` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `config_search` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `entities` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `entities_search` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `entities_metadata` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `reader` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `reader_search` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;
ALTER TABLE  `reader_metadata` CHANGE COLUMN `_id` `_id` varchar(36) NOT NULL;


REPLACE INTO `versions` VALUES('schema', '2020112901');

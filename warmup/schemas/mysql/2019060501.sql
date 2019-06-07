ALTER TABLE  `session` ADD COLUMN `session_lifetime` int(11) NOT NULL AFTER `session_value`;
ALTER TABLE  `session` CHANGE COLUMN `session_value` `session_value` mediumblob NOT NULL;
ALTER TABLE  `session` convert to character set utf8 collate utf8_bin;
	
UPDATE `versions` SET `value` = '2019060501' WHERE `label` = 'schema';

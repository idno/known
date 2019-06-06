ALTER TABLE  `session` ADD COLUMN `session_lifetime` int(11) NOT NULL AFTER `session_value`;
ALTER TABLE  `session` CHANGE COLUMN `session_value` `session_value` blob NOT NULL;
	
UPDATE `versions` SET `value` = '2019060501' WHERE `label` = 'schema';

ALTER TABLE  `session` ADD COLUMN `session_lifetime` int(11) NOT NULL AFTER `session_value`;
	
UPDATE `versions` SET `value` = '2019060501' WHERE `label` = 'schema';

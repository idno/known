ALTER TABLE `entities` CHANGE `owner` `owner` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `entities` CHANGE `entity_subtype` `entity_subtype` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
UPDATE `versions` SET `value` = '2015061501' WHERE `label` = 'schema';

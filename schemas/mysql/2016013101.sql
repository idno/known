ALTER TABLE  `entities` CHANGE  `search`  `search` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `reader` CHANGE  `search`  `search` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `reader` CHANGE  `contents`  `contents` LONGBLOB NOT NULL;
UPDATE `versions` SET `value` = '2016013101' WHERE `label` = 'schema';

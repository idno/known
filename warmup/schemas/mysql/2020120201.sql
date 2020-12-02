UPDATE `entities` set `siteid` = ( SELECT `_id` FROM `site` limit 1 );
UPDATE `reader` set `siteid` = ( SELECT `_id` FROM `site` limit 1 );
UPDATE `config` set `siteid` = ( SELECT `_id` FROM `site` limit 1 );

REPLACE INTO `versions` VALUES('schema', '2020120201');

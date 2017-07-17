ALTER TABLE `metadata` MODIFY `value` longtext NOT NULL;
REPLACE INTO `versions` VALUES('schema', '2017032001');
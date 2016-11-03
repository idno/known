CREATE TABLE IF NOT EXISTS `versions` (
  `label` varchar(32) NOT NULL,
  `value` varchar(10) NOT NULL,
  PRIMARY KEY (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

REPLACE INTO `versions` VALUES('schema', '2016110301');
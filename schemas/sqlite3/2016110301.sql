CREATE TABLE IF NOT EXISTS versions (
  label varchar(32) NOT NULL PRIMARY KEY,
  value varchar(10) NOT NULL
);

REPLACE INTO `versions` VALUES('schema', '2016110301');
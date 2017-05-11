
CREATE TABLE IF NOT EXISTS tombstones (
  _id varchar(32) NOT NULL UNIQUE,
  id varchar(32) NOT NULL,
  uuid varchar(255) NOT NULL PRIMARY KEY,
  slug varchar(255) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS id ON tombstones (id);
CREATE INDEX IF NOT EXISTS slug ON tombstones (slug);

REPLACE INTO `versions` VALUES('schema', '2017051101');

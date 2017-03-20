
ALTER TABLE metadata RENAME TO metadata_2017032001;

CREATE TABLE IF NOT EXISTS metadata (
  entity varchar(255) NOT NULL,
  _id varchar(32) NOT NULL,
  collection varchar(64) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL
);

CREATE INDEX IF NOT EXISTS entity ON metadata (entity,name);
CREATE INDEX IF NOT EXISTS value ON metadata (value);
CREATE INDEX IF NOT EXISTS name ON metadata (name);
CREATE INDEX IF NOT EXISTS collection ON metadata (collection);
CREATE INDEX IF NOT EXISTS _id ON metadata (_id);

INSERT INTO metadata entity,_id,collection,name,value FROM metadata_2017032001;

REPLACE INTO `versions` VALUES('schema', '2017032001');
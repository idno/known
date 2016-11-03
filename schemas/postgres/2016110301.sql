CREATE TABLE IF NOT EXISTS versions (
  label varchar(32) NOT NULL,
  value varchar(10) NOT NULL,
  PRIMARY KEY (label)
);

DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2016110301');
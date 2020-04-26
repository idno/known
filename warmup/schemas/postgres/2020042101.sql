
CREATE TABLE IF NOT EXISTS entities_metadata (
  _id varchar(32) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL,
  FOREIGN KEY (_id) REFERENCES entities (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON entities_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON entities_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON entities_metadata (_id);

INSERT INTO entities_metadata SELECT _id, name, value FROM metadata WHERE collection = 'entities';



CREATE TABLE IF NOT EXISTS config_metadata (
  _id varchar(32) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL,
  FOREIGN KEY (_id) REFERENCES config (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON config_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON config_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON config_metadata (_id);

INSERT INTO config_metadata SELECT _id, name, value FROM metadata WHERE collection = 'config';



CREATE TABLE IF NOT EXISTS reader_metadata (
  _id varchar(32) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL,
  FOREIGN KEY (_id) REFERENCES reader (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON reader_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON reader_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON reader_metadata (_id);

INSERT INTO reader_metadata SELECT _id, name, value FROM metadata WHERE collection = 'reader';




CREATE TABLE IF NOT EXISTS deprecated_metadata (
  entity varchar(255) NOT NULL,
  _id varchar(32) NOT NULL,
  collection varchar(64) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL
);

INSERT INTO deprecated_metadata SELECT entity, _id, collection, name, value FROM metadata;

DROP TABLE metadata;



DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2020042101');
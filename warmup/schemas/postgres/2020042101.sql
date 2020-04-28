
CREATE TABLE IF NOT EXISTS entities_metadata (
  _id varchar(32) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL,
  FOREIGN KEY (_id) REFERENCES entities (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON entities_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON entities_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON entities_metadata (_id);

DELETE FROM entities_metadata;
INSERT INTO entities_metadata SELECT M._id, M.name, M.value FROM metadata M LEFT JOIN config C ON C._id = M._id WHERE M.collection = 'entities' AND C._id IS NOT NULL
ON CONFLICT DO NOTHING;

CREATE TABLE IF NOT EXISTS config_metadata (
  _id varchar(32) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL,
  FOREIGN KEY (_id) REFERENCES config (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON config_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON config_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON config_metadata (_id);

DELETE FROM config_metadata;
INSERT INTO config_metadata SELECT M._id, M.name, M.value FROM metadata M LEFT JOIN config C ON C._id = M._id WHERE M.collection = 'config' AND C._id IS NOT NULL
ON CONFLICT DO NOTHING;

CREATE TABLE IF NOT EXISTS reader_metadata (
  _id varchar(32) NOT NULL,
  name varchar(64) NOT NULL,
  value text NOT NULL,
  FOREIGN KEY (_id) REFERENCES reader (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON reader_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON reader_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON reader_metadata (_id);

DELETE FROM reader_metadata;
INSERT INTO reader_metadata SELECT M._id, M.name, M.value FROM metadata M LEFT JOIN config C ON C._id = M._id WHERE M.collection = 'reader' AND C._id IS NOT NULL
ON CONFLICT DO NOTHING;

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

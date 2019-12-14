
CREATE TABLE IF NOT EXISTS entities_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id)
);

INSERT INTO entities_search SELECT _id, search FROM entities;

ALTER TABLE entities DROP COLUMN search;


CREATE TABLE IF NOT EXISTS reader_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id)
);


INSERT INTO reader_search SELECT _id, search FROM reader;

ALTER TABLE reader DROP COLUMN search;


CREATE TABLE IF NOT EXISTS config_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id)
);

INSERT INTO config_search SELECT _id, search FROM config;

ALTER TABLE config DROP COLUMN search;


DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2019121401');

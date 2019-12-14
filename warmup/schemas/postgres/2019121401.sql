
CREATE TABLE IF NOT EXISTS entities_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id)
);

INSERT INTO entities_search SELECT _id, search FROM entities;

ALTER TABLE entities DROP COLUMN search;

ALTER TABLE entities_search ADD CONSTRAINT es_id_id FOREIGN KEY (_id) REFERENCES entities (_id) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE IF NOT EXISTS reader_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id)
);


INSERT INTO reader_search SELECT _id, search FROM reader;

ALTER TABLE reader DROP COLUMN search;

ALTER TABLE reader_search ADD CONSTRAINT es_id_id FOREIGN KEY (_id) REFERENCES reader (_id) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE IF NOT EXISTS config_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id)
);

INSERT INTO config_search SELECT _id, search FROM config;

ALTER TABLE config DROP COLUMN search;

ALTER TABLE config_search ADD CONSTRAINT es_id_id FOREIGN KEY (_id) REFERENCES config (_id) ON DELETE CASCADE ON UPDATE CASCADE;






DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2019121401');

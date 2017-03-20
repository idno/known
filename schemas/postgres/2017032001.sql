
ALTER TABLE metadata ALTER COLUMN name TYPE varchar(64);

DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2017032001');

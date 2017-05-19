
ALTER TABLE metadata MODIFY name varchar(64) NOT NULL;
REPLACE INTO versions VALUES('schema', '2017032001');

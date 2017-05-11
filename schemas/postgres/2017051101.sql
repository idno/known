--
-- Table structure for table reader
--

CREATE TABLE IF NOT EXISTS tombstones (
  _id varchar(32) NOT NULL UNIQUE,
  id varchar(32) NOT NULL,
  uuid varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (uuid)
);

CREATE INDEX t_slug ON tombstones (slug);
CREATE INDEX r_id ON tombstones (id);



-- Update schema
DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2017051101');

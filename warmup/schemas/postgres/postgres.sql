--
-- Base Known schema
--

--
-- Table structure for table config
--

CREATE TABLE IF NOT EXISTS config (
  uuid varchar(255) NOT NULL,
  _id varchar(32) NOT NULL UNIQUE,
  owner varchar(255) NOT NULL,
  entity_subtype varchar(64) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contents text NOT NULL,
  publish_status varchar(255) NOT NULL DEFAULT 'published',
  PRIMARY KEY (uuid)
);
CREATE INDEX CONCURRENTLY IF NOT EXISTS c__id ON config (_id);
CREATE INDEX CONCURRENTLY IF NOT EXISTS c_owner ON config (owner);
CREATE INDEX CONCURRENTLY IF NOT EXISTS c_entity_subtype ON config (entity_subtype);
CREATE INDEX CONCURRENTLY IF NOT EXISTS c_publish_status ON config (publish_status);

CREATE TABLE IF NOT EXISTS config_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id),
  FOREIGN KEY (_id) REFERENCES config (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS config_metadata (
  _id varchar(32) NOT NULL FOREIGN KEY (_id) REFERENCES entities (_id) ON DELETE CASCADE ON UPDATE CASCADE,
  name varchar(64) NOT NULL,
  value text NOT NULL
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON config_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON config_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON config_metadata (_id);

-- --------------------------------------------------------

--
-- Table structure for table entities
--

CREATE TABLE IF NOT EXISTS entities (
  uuid varchar(255) NOT NULL,
  _id varchar(32) NOT NULL UNIQUE,
  owner varchar(255) NOT NULL,
  entity_subtype varchar(64) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contents text NOT NULL,
  publish_status varchar(255) NOT NULL DEFAULT 'published',
  PRIMARY KEY (uuid)
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS e_owner ON entities (owner, created);
CREATE INDEX CONCURRENTLY IF NOT EXISTS e_entity_subtype ON entities (entity_subtype);
CREATE INDEX CONCURRENTLY IF NOT EXISTS e_publish_status ON entities (publish_status);


CREATE TABLE IF NOT EXISTS entities_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id),
  FOREIGN KEY (_id) REFERENCES entities (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS entities_metadata (
  _id varchar(32) NOT NULL FOREIGN KEY (_id) REFERENCES entities (_id) ON DELETE CASCADE ON UPDATE CASCADE,
  name varchar(64) NOT NULL,
  value text NOT NULL
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON entities_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON entities_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON entities_metadata (_id);



-- --------------------------------------------------------

--
-- Table structure for table reader
--

CREATE TABLE IF NOT EXISTS reader (
  uuid varchar(255) NOT NULL,
  _id varchar(32) NOT NULL UNIQUE,
  owner varchar(255) NOT NULL,
  entity_subtype varchar(64) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contents text NOT NULL,
  publish_status varchar(255) NOT NULL DEFAULT 'published',
  PRIMARY KEY (uuid)
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS r_owner ON reader (owner, created);
CREATE INDEX CONCURRENTLY IF NOT EXISTS r_entity_subtype ON reader (entity_subtype);
CREATE INDEX CONCURRENTLY IF NOT EXISTS r_publish_status ON reader (publish_status);


CREATE TABLE IF NOT EXISTS reader_search (
  _id varchar(32) NOT NULL,
  search text NOT NULL,
  PRIMARY KEY (_id),
  FOREIGN KEY (_id) REFERENCES reader (_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS reader_metadata (
  _id varchar(32) NOT NULL FOREIGN KEY (_id) REFERENCES reader (_id) ON DELETE CASCADE ON UPDATE CASCADE,
  name varchar(64) NOT NULL,
  value text NOT NULL
);

CREATE INDEX CONCURRENTLY IF NOT EXISTS m_value ON reader_metadata (value);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m_name ON reader_metadata (name);
CREATE INDEX CONCURRENTLY IF NOT EXISTS m__id ON reader_metadata (_id);

-- --------------------------------------------------------

--
-- Session handling table
--

CREATE TABLE IF NOT EXISTS session (
    session_id varchar(255) NOT NULL,
    session_value bytea NOT NULL,
    session_lifetime integer NOT NULL,
    session_time integer NOT NULL,
    PRIMARY KEY (session_id)
);


--
-- Table structure for table versions
--

CREATE TABLE IF NOT EXISTS versions (
  label varchar(32) NOT NULL,
  value varchar(10) NOT NULL,
  PRIMARY KEY (label)
);

DELETE FROM versions WHERE label = 'schema';
INSERT INTO versions VALUES('schema', '2020042101');

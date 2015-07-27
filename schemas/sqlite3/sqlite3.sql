
--
-- Base Known schema
--

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS config (
  uuid varchar(255) NOT NULL PRIMARY KEY,
  _id varchar(32) NOT NULL,
  owner varchar(255) NOT NULL,
  entity_subtype varchar(64) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contents longblob NOT NULL
);
CREATE INDEX IF NOT EXISTS _id ON config (_id);
CREATE INDEX IF NOT EXISTS owner ON config (owner);
CREATE INDEX IF NOT EXISTS entity_subtype ON config (entity_subtype);

CREATE VIRTUAL TABLE config_search USING fts4 (
  uuid varchar(255) NOT NULL PRIMARY KEY,
  search text NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE IF NOT EXISTS entities (
  uuid varchar(255) NOT NULL PRIMARY KEY,
  _id varchar(32) NOT NULL UNIQUE,
  owner varchar(255) NOT NULL,
  entity_subtype varchar(64) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contents longblob NOT NULL
);

CREATE INDEX IF NOT EXISTS owner ON entities (owner, created);
CREATE INDEX IF NOT EXISTS entity_subtype ON entities (entity_subtype);

CREATE VIRTUAL TABLE entities_search USING fts4 (
  uuid varchar(255) NOT NULL PRIMARY KEY,
  search text NOT NULL
);


-- --------------------------------------------------------

--
-- Table structure for table `reader`
--

CREATE TABLE IF NOT EXISTS reader (
  uuid varchar(255) NOT NULL PRIMARY KEY,
  _id varchar(32) NOT NULL UNIQUE,
  owner varchar(255) NOT NULL,
  entity_subtype varchar(64) NOT NULL,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contents blob NOT NULL
);

CREATE INDEX IF NOT EXISTS owner ON reader (owner, created);
CREATE INDEX IF NOT EXISTS entity_subtype ON reader (entity_subtype);

CREATE VIRTUAL TABLE reader_search USING fts4 (
  uuid varchar(255) NOT NULL PRIMARY KEY,
  search text NOT NULL
);


-- --------------------------------------------------------

--
-- Table structure for table `metadata`
--

CREATE TABLE IF NOT EXISTS metadata (
  entity varchar(255) NOT NULL,
  _id varchar(32) NOT NULL,
  collection varchar(64) NOT NULL,
  name varchar(32) NOT NULL,
  value text NOT NULL
);

CREATE INDEX IF NOT EXISTS entity ON metadata (entity,name);
CREATE INDEX IF NOT EXISTS value ON metadata (value);
CREATE INDEX IF NOT EXISTS name ON metadata (name);
CREATE INDEX IF NOT EXISTS collection ON metadata (collection);
CREATE INDEX IF NOT EXISTS _id ON metadata (_id);

-- --------------------------------------------------------

--
-- Table structure for table `versions`
--

CREATE TABLE IF NOT EXISTS versions (
  label varchar(32) NOT NULL PRIMARY KEY,
  value varchar(10) NOT NULL
);

--
-- Session handling table
--

CREATE TABLE IF NOT EXISTS session (
    session_id varchar(255) NOT NULL PRIMARY KEY,
    session_value text NOT NULL,
    session_time int(11) NOT NULL
);

REPLACE INTO `versions` VALUES('schema', '2015051602');
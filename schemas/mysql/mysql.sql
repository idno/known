--
-- Base Known schema
--

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `uuid` varchar(255) NOT NULL,
  `_id` varchar(32) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `entity_subtype` varchar(64) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contents` longblob NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`uuid`),
  KEY `owner` (`owner`,`created`),
  KEY `_id` (`_id`),
  KEY `entity_subtype` (`entity_subtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE IF NOT EXISTS `entities` (
  `uuid` varchar(255) NOT NULL,
  `_id` varchar(32) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `entity_subtype` varchar(64) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contents` longblob NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `_id` (`_id`),
  KEY `owner` (`owner`,`created`),
  KEY `entity_subtype` (`entity_subtype`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reader`
--

CREATE TABLE IF NOT EXISTS `reader` (
  `uuid` varchar(255) NOT NULL,
  `_id` varchar(32) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `entity_subtype` varchar(64) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contents` longblob NOT NULL,
  `search` longtext NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `_id` (`_id`),
  KEY `owner` (`owner`,`created`),
  KEY `entity_subtype` (`entity_subtype`),
  FULLTEXT KEY `search` (`search`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `metadata`
--

CREATE TABLE IF NOT EXISTS `metadata` (
  `entity` varchar(255) NOT NULL,
  `_id` varchar(32) NOT NULL,
  `collection` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` text NOT NULL,
  KEY `entity` (`entity`,`name`),
  KEY `value` (`value`(255)),
  KEY `name` (`name`),
  KEY `collection` (`collection`),
  KEY `_id` (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `versions`
--

CREATE TABLE IF NOT EXISTS `versions` (
  `label` varchar(32) NOT NULL,
  `value` varchar(10) NOT NULL,
  PRIMARY KEY (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Session handling table
--

CREATE TABLE IF NOT EXISTS `session` (
    `session_id` varchar(255) NOT NULL,
    `session_value` text NOT NULL,
    `session_time` int(11) NOT NULL,
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

REPLACE INTO `versions` VALUES('schema', '2014100801');
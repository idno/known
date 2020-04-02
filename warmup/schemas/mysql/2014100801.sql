--
-- Upgraded Known scheme
--

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
  `contents` blob NOT NULL,
  `search` text NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `_id` (`_id`),
  KEY `owner` (`owner`,`created`),
  KEY `entity_subtype` (`entity_subtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

UPDATE `versions` SET `value` = '2014100801' WHERE `label` = 'schema';

ALTER TABLE config ADD COLUMN publish_status varchar(255) NOT NULL DEFAULT 'published'
ALTER TABLE entities ADD COLUMN publish_status varchar(255) NOT NULL DEFAULT 'published';
ALTER TABLE reader ADD COLUMN publish_status varchar(255) NOT NULL DEFAULT 'published';

CREATE INDEX IF NOT EXISTS publish_status ON config (publish_status);
CREATE INDEX IF NOT EXISTS publish_status ON entities (publish_status);
CREATE INDEX IF NOT EXISTS publish_status ON reader (publish_status);

REPLACE INTO `versions` VALUES('schema', '2016102601');

DROP INDEX m_value;
CREATE INDEX m_value ON metadata (md5(value));

REPLACE INTO versions VALUES('schema', '2017071701');
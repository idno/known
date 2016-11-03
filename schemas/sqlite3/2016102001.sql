CREATE TABLE session_2016102001 AS SELECT * FROM session;
DROP TABLE session;
CREATE TABLE IF NOT EXISTS session (
    session_id varchar(255) NOT NULL PRIMARY KEY,
    session_value text NOT NULL,
    session_lifetime int(11) NOT NULL,
    session_time int(11) NOT NULL
);
INSERT INTO session (session_id, session_value, session_lifetime, session_time) SELECT session_id, session_value, 0, session_time FROM session_2016102001;
DROP TABLE session_2016102001;


REPLACE INTO `versions` VALUES('schema', '2016102001');

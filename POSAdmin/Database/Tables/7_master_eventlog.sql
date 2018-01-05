DROP TABLE IF EXISTS master_eventlog;

CREATE TABLE master_eventlog
(
	EventLogID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	EventLogDate 	DATETIME,
	Description 	TEXT,
	Source			VARCHAR(100),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL
)ENGINE=InnoDB;
DROP TABLE IF EXISTS restore_history;

CREATE TABLE restore_history
(
	RestoreHistoryID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	RestoreDate			DATE,
	FilePath			VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX RESTOREHISTORY_INDEX
ON restore_history (RestoreHistoryID);
DROP TABLE IF EXISTS backup_history;

CREATE TABLE backup_history
(
	BackupHistoryID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	BackupDate			DATE,
	FilePath			VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BACKUPHISTORY_INDEX
ON backup_history (BackupHistoryID);
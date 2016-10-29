DROP TABLE IF EXISTS transaction_checkschedule;

CREATE TABLE transaction_checkschedule
(
	CheckScheduleID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	MedicationID		BIGINT NOT NULL,
	ScheduledDate		DATE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(MedicationID) REFERENCES transaction_medication(MedicationID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CHECKSCHEDULE_INDEX
ON transaction_checkschedule (CheckScheduleID, MedicationID);
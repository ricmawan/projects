DROP TABLE IF EXISTS transaction_checkschedule;

CREATE TABLE transaction_checkschedule
(
	CheckScheduleID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	MedicationID		BIGINT,
	PatientID			BIGINT,
	ScheduledDate		DATETIME,
	EmailStatus			TEXT,
	EmailMessage		TEXT,
	DeliveredDate		DATETIME,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(PatientID) REFERENCES master_patient(PatientID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CHECKSCHEDULE_INDEX
ON transaction_checkschedule (CheckScheduleID, MedicationID, PatientID);
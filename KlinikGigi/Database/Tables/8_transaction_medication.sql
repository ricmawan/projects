DROP TABLE IF EXISTS transaction_medication;

CREATE TABLE transaction_medication
(
	MedicationID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PatientID			BIGINT NOT NULL,
	OrderNumber			VARCHAR(100) NULL,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	IsDone				BIT,
	IsCancelled			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(PatientID) REFERENCES master_patient(PatientID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MEDICATION_INDEX
ON transaction_medication (MedicationID, PatientID);
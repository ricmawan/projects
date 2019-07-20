DROP TABLE IF EXISTS transaction_medicationdetails;

CREATE TABLE transaction_medicationdetails
(
	MedicationDetailsID BIGINT PRIMARY KEY AUTO_INCREMENT,
	DoctorID			BIGINT NOT NULL,
	ExaminationID		BIGINT NOT NULL,
	MedicationID		BIGINT NOT NULL,
	Remarks				TEXT,
	Price				DOUBLE,
	Quantity			DOUBLE,
	Synchronized		BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(DoctorID) REFERENCES master_user(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(ExaminationID) REFERENCES master_examination(ExaminationID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(MedicationID) REFERENCES transaction_medication(MedicationID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MEDICATIONDETAILS_INDEX
ON transaction_medicationdetails (MedicationDetailsID, DoctorID, ExaminationID, MedicationID);
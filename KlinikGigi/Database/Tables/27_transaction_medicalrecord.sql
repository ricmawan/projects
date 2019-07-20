DROP TABLE IF EXISTS transaction_medicalrecord;

CREATE TABLE transaction_medicalrecord
(
	MedicalRecordID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PatientID				BIGINT NOT NULL,
	BranchID				SMALLINT,
	MedicationID			BIGINT,
	MedicationDetailsID		BIGINT,
	ExaminationName			VARCHAR(255),
	TransactionDate			DATETIME,
	Remarks					TEXT,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
)ENGINE=InnoDB;
DROP TABLE IF EXISTS master_patient;

CREATE TABLE master_patient
(
	PatientID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PatientNumber	VARCHAR(100) NOT NULL,
	PatientName		VARCHAR(255) NOT NULL,
	BirthDate		DATE NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address 		TEXT,
	City			VARCHAR(100),
	Allergy			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PATIENT_INDEX
ON master_patient (PatientID);
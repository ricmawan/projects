DROP TABLE IF EXISTS transaction_outgoingdummydetails;

CREATE TABLE transaction_outgoingdummydetails
(
	OutgoingDummyDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	DoctorID				BIGINT,
	PatientID				BIGINT,
	Medication				VARCHAR(255),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL,
	FOREIGN KEY (DoctorID) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (PatientID) REFERENCES master_patient(PatientID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOINGDUMMYDETAILS_INDEX
ON transaction_outgoingdummydetails (OutgoingDummyDetailsID, DoctorID, PatientID);
DROP TABLE IF EXISTS transaction_outgoingmodeldetails;

CREATE TABLE transaction_outgoingmodeldetails
(
	OutgoingModelDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingModelID 		BIGINT,
	DoctorID				BIGINT,
	PatientID				BIGINT,
	ExaminationName			VARCHAR(255),
	Remarks					TEXT,
	IsReceived				BIT,
	ReceivedDate			DATETIME,
	IncomingReceiptNumber	VARCHAR(255),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL,
	FOREIGN KEY (DoctorID) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (PatientID) REFERENCES master_patient(PatientID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (OutgoingModelID) REFERENCES transaction_outgoingmodel(OutgoingModelID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOINGMODELDETAILS_INDEX
ON transaction_outgoingmodeldetails (OutgoingModelDetailsID, OutgoingModelID, DoctorID, PatientID);
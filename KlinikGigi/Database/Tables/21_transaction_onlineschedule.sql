DROP TABLE IF EXISTS transaction_onlineschedule;

CREATE TABLE transaction_onlineschedule
(
	OnlineScheduleID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	BranchID				SMALLINT,
	DoctorID				BIGINT,
	ScheduledDate			DATETIME,
	PatientName				VARCHAR(255),
	PhoneNumber				VARCHAR(20),
	Email					VARCHAR(255),
	EmailStatus				TEXT,
	EmailMessage			TEXT,
	DeliveredDate			DATETIME,
	Medication				VARCHAR(255),
	CustomerSelfRegFlag		BIT,
	CustomerConfirmation	CHAR(1),
	ConfirmedDate			DATETIME,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (DoctorID) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ONLINESCHEDULE_INDEX
ON transaction_onlineschedule (OnlineScheduleID, BranchID, DoctorID);
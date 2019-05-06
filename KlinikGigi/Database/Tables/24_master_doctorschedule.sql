DROP TABLE IF EXISTS master_doctorschedule;

CREATE TABLE master_doctorschedule
(
	DoctorScheduleID	INT PRIMARY KEY AUTO_INCREMENT,
	DoctorID			BIGINT,
	BranchID			SMALLINT,
	DayOfWeek			SMALLINT,
	BusinessHour		VARCHAR(10),
	EndHour				SMALLINT,
	IsAdmin				SMALLINT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY (DoctorID) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX DOCTORSCHEDULE_INDEX
ON master_doctorschedule (DoctorScheduleID, DoctorID, BranchID);

INSERT INTO master_doctorschedule
(
	DoctorScheduleID,
	BranchID,
	DoctorID,
	DayOfWeek,
	BusinessHour,
	IsAdmin,
	CreatedDate,
	CreatedBy
)
VALUES
(
	0,
	1,
	2,
	9,
	10,
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	16,
	17,
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	9,
	17,
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	5,
	16,
	17,
	0,
	'Admin',
	NOW()
),
(
	0,
	2,
	1,
	17,
	19,
	0,
	'Admin',
	NOW()
),
(
	0,
	2,
	2,
	17,
	19,
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	2,
	9,
	10,
	1,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	16,
	17,
	1,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	9,
	17,
	1,
	'Admin',
	NOW()
),
(
	0,
	1,
	5,
	16,
	17,
	1,
	'Admin',
	NOW()
),
(
	0,
	2,
	1,
	17,
	19,
	1,
	'Admin',
	NOW()
),
(
	0,
	2,
	2,
	17,
	19,
	1,
	'Admin',
	NOW()
);
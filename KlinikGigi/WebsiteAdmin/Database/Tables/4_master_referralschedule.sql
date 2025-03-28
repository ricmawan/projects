DROP TABLE IF EXISTS master_referralschedule;

CREATE TABLE master_referralschedule
(
	ReferralScheduleID	INT PRIMARY KEY AUTO_INCREMENT,
	DoctorID			BIGINT,
	BranchID			SMALLINT,
	DayOfWeek			SMALLINT,
	BusinessHour		VARCHAR(10),
	IsAdmin				SMALLINT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY (DoctorID) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX REFERRALSCHEDULE_INDEX
ON master_referralschedule (ReferralScheduleID, DoctorID, BranchID);

/*
1	System Administrator
3	drg. Christiani P.
4	drg. Dinawati P.
5	Resepsionis
6	drg. Indriati Martosaputra, Sp. Ort
7	Super Administrator
8	drg. Lika Yoda, Sp. KG
9	drg. Dimar P.
10	drg. Cyrilla, Sp.KG

*/

INSERT INTO master_referralschedule
(
	ReferralScheduleID,
	BranchID,
	DoctorID,
	DayOfWeek,
	BusinessHour,
	IsAdmin,
	CreatedBy,
	CreatedDate
)
VALUES
(
	0,
	1,
	6,
	2,
	'09:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	2,
	'09:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	2,
	'09:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	2,
	'09:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	2,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	3,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	4,
	'09:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	4,
	'09:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	4,
	'09:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	4,
	'09:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	4,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	6,
	5,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'10:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'10:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'10:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'11:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'11:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'11:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'11:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'12:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'12:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'12:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'12:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'13:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'13:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'13:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'13:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'14:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'14:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'14:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'14:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'15:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'15:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'15:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'15:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'17:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'18:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'18:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'18:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'18:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	8,
	5,
	'19:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'11:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'11:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'11:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'11:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'12:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'12:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'12:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'12:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'13:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'13:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'13:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'13:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'14:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'14:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'14:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'14:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'15:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'15:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'15:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'15:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'17:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	3,
	2,
	'18:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'09:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'09:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'09:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'09:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'10:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'10:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'10:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	1,
	'11:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'10:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'10:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'10:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'11:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'11:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'11:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'11:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'12:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'12:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'12:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'12:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'13:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'13:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'13:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'13:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'14:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'14:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'14:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'14:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	4,
	3,
	'15:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'15:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'15:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'15:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'15:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'17:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'18:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'18:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'18:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'18:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'19:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'19:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'19:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'19:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	3,
	'20:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'10:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'10:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'10:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'11:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'11:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'11:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'11:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'12:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'12:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'12:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'12:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'13:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'13:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'13:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'13:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'14:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'14:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'14:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'14:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'15:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'15:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'15:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'15:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	10,
	4,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'17:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'18:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'18:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'18:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'18:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'19:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'19:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'19:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'19:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	4,
	'20:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'10:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'10:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'10:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'10:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'11:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'11:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'11:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'11:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'12:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'12:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'12:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'12:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'13:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'13:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'13:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'13:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'14:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'14:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'14:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'14:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'15:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'15:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'15:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'15:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'16:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'16:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'16:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'16:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'17:00',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'17:15',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'17:30',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'17:45',
	0,
	'Admin',
	NOW()
),
(
	0,
	1,
	9,
	6,
	'18:00',
	0,
	'Admin',
	NOW()
);
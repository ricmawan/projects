DROP TABLE IF EXISTS master_schedule;

CREATE TABLE master_schedule
(
	ScheduleID			INT PRIMARY KEY AUTO_INCREMENT,
	BranchID			SMALLINT,
	DayOfWeek			SMALLINT,
	StartHour			SMALLINT,
	EndHour				SMALLINT,
	IsAdmin				SMALLINT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SCHEDULE_INDEX
ON master_schedule (ScheduleID);

INSERT INTO master_schedule
(
	ScheduleID,
	BranchID,
	DayOfWeek,
	StartHour,
	EndHour,
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
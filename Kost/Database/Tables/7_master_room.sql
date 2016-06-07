DROP TABLE IF EXISTS master_room;

CREATE TABLE master_room
(
	RoomID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	RoomNumber 		VARCHAR(255) NOT NULL,
	StatusID	 	INT NOT NULL,
	DailyRate		DOUBLE,
	HourlyRate		DOUBLE,
	RoomInfo		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(StatusID) REFERENCES master_status(StatusID)
)ENGINE=InnoDB;


CREATE UNIQUE INDEX ROOM_INDEX
ON master_room (RoomID);

INSERT INTO master_room
VALUES
(
	1,
	101,
	1,
	150000,
	15000,
	'test',
	NOW(),
	'Admin',
	null,
	null
),
(
	2,
	102,
	2,
	150000,
	15000,
	'test',
	NOW(),
	'Admin',
	null,
	null
),(
	3,
	103,
	3,
	150000,
	15000,
	'test',
	NOW(),
	'Admin',
	null,
	null
);
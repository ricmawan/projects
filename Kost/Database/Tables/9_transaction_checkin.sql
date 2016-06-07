DROP TABLE IF EXISTS transaction_checkin;

CREATE TABLE transaction_checkin
(
	CheckInID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	RoomID				BIGINT,
	TransactionDate		DATETIME NOT NULL,
	RateType			VARCHAR(100) NOT NULL,
	StartDate			DATETIME,
	EndDate				DATETIME,
	CustomerName		VARCHAR(255),
	Phone				VARCHAR(100),
	Address				TEXT,
	BirthDate			DATE,
	Remarks				TEXT,
	DownPaymentAmount	DOUBLE,
	DownPaymentDate		DATE,
	PaymentAmount 		DOUBLE,
	PaymentDate			DOUBLE,
	BookingFlag			BIT,
	CheckOutFlag		BIT,
	DailyRate			DOUBLE,
	HourlyRate			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(RoomID) REFERENCES master_room(RoomID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX CHECKIN_INDEX
ON transaction_checkin (CheckInID);
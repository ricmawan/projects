DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID	 		BIGINT PRIMARY KEY AUTO_INCREMENT,
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
	CheckInFlag			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX BOOKING_INDEX
ON transaction_booking (BookingID, RoomID);
DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID	 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	RoomID			BIGINT NOT NULL,
	TransactionDate	DATETIME,
	CheckIn			DATETIME,
	CheckOut		DATETIME,
	RateType		VARCHAR(100) NOT NULL,
	CustomerName	VARCHAR(255),
	Phone			VARCHAR(100),
	Address			TEXT,
	BirthDate		DATE,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX BOOKING_INDEX
ON transaction_booking (BookingID, RoomID);
DROP TABLE IF EXISTS transaction_bookingdetails;

CREATE TABLE transaction_bookingdetails
(
	BookingDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingID			BIGINT,
	TypeID 				BIGINT NOT NULL,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	SalePrice			DOUBLE,
	Discount			DOUBLE,
	IsPercentage		BIT,
	BatchNumber			VARCHAR(100) NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(BookingID) REFERENCES transaction_booking(BookingID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(TypeID) REFERENCES master_type(TypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BOOKING_INDEX
ON transaction_bookingdetails (BookingDetailsID, BookingID, TypeID);
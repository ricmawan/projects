DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingNumber		VARCHAR(100) NULL,
	CustomerID			BIGINT,
	SalesID				BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	DueDate				DATETIME NULL,
	Remarks				TEXT,
	BookingStatusID		TINYINT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(SalesID) REFERENCES master_sales(SalesID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BOOKING_INDEX
ON transaction_booking (BookingID);
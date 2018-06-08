DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingNumber		VARCHAR(100),
	RetailFlag			BIT(1) NOT NULL,
	CustomerID 			BIGINT,
	TransactionDate		DATETIME NOT NULL,
	Payment				DOUBLE,
	PrintCount			SMALLINT,
	PrintedDate			DATETIME,
    FinishFlag 			BIT,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_booking (BookingID, CustomerID);
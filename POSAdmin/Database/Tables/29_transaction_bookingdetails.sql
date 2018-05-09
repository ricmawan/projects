DROP TABLE IF EXISTS transaction_bookingdetails;

CREATE TABLE transaction_bookingdetails
(
	BookingDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	ItemDetailsID		BIGINT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	BookingPrice		DOUBLE,
	Discount			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(BookingID) REFERENCES transaction_booking(BookingID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_bookingdetails (BookingDetailsID, BookingID, ItemID, BranchID);
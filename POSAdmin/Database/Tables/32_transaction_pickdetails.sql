DROP TABLE IF EXISTS transaction_pickdetails;

CREATE TABLE transaction_pickdetails
(
	PickDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PickID				BIGINT,
	BookingDetailsID 	BIGINT,
	ItemID 				BIGINT NOT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	SalePrice			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(PickID) REFERENCES transaction_pick(PickID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BookingDetailsID) REFERENCES transaction_bookingdetails(BookingDetailsID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PICKDETAILS_INDEX
ON transaction_pickdetails (PickDetailsID, PickID, BookingDetailsID, ItemID, BranchID);
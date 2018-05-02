DROP TABLE IF EXISTS transaction_firststockdetails;

CREATE TABLE transaction_purchasedetails
(
	FirstStockDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	FirstStockID			BIGINT,
	ItemID 					BIGINT NOT NULL,
	BranchID				INT,
	Quantity				DOUBLE,
	BuyPrice				DOUBLE,
	RetailPrice				DOUBLE,
	Price1					DOUBLE,
	Price2					DOUBLE,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL,
	FOREIGN KEY(FirstStockID) REFERENCES transaction_firststock(FirstStockID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FIRSTSTOCKDETAILS_INDEX
ON transaction_firststockdetails (FirstStockDetailsID, FirstStockID, ItemID, BranchID);
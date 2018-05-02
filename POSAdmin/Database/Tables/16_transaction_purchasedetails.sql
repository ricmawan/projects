DROP TABLE IF EXISTS transaction_purchasedetails;

CREATE TABLE transaction_purchasedetails
(
	PurchaseDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	RetailPrice			DOUBLE,
	Price1				DOUBLE,
	Price2				DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(PurchaseID) REFERENCES transaction_purchase(PurchaseID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASEDETAILS_INDEX
ON transaction_purchasedetails (PurchaseDetailsID, PurchaseID, ItemID, BranchID);
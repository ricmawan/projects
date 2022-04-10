DROP TABLE IF EXISTS transaction_purchasereturndetails;

CREATE TABLE transaction_purchasereturndetails
(
	PurchaseReturnDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseReturnID			BIGINT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	BranchID					INT,
	Quantity					DOUBLE,
	BuyPrice					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(PurchaseReturnID) REFERENCES transaction_purchasereturn(PurchaseReturnID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASERETURNDETAILS_INDEX
ON transaction_purchasereturndetails (PurchaseReturnDetailsID, PurchaseReturnID, ItemID, BranchID);
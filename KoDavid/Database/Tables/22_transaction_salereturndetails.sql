DROP TABLE IF EXISTS transaction_salereturndetails;

CREATE TABLE transaction_salereturndetails
(
	SaleReturnDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleReturnID				BIGINT,
	SaleDetailsID 				BIGINT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	BranchID					INT,
	Quantity					DOUBLE,
	BuyPrice					DOUBLE,
	SalePrice					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(SaleReturnID) REFERENCES transaction_salereturn(SaleReturnID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(SaleDetailsID) REFERENCES transaction_saledetails(SaleDetailsID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALERETURNDETAILS_INDEX
ON transaction_salereturndetails (SaleReturnDetailsID, SaleReturnID, SaleDetailsID, ItemID, BranchID);
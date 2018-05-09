DROP TABLE IF EXISTS transaction_stockadjustdetails;

CREATE TABLE transaction_stockadjustdetails
(
	StockAdjustDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	StockAdjustID				BIGINT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	BranchID					INT,
	Quantity					DOUBLE,
	AdjustedQuantity			DOUBLE,
	BuyPrice					DOUBLE,
	SalePrice					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(StockAdjustID) REFERENCES transaction_stockadjust(StockAdjustID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKADJUSTDETAILS_INDEX
ON transaction_stockadjustdetails (StockAdjustDetailsID, StockAdjustID, ItemID, BranchID);
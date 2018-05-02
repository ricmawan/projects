DROP TABLE IF EXISTS transaction_stockmutationdetails;

CREATE TABLE transaction_stockmutationdetails
(
	StockMutationDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	StockMutationID				BIGINT,
	SourceID					INT,
	DestinationID				INT,
	ItemID 						BIGINT NOT NULL,
	Quantity					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(SourceID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(DestinationID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKMUTATIONDETAILS_INDEX
ON transaction_stockmutationdetails (StockMutationDetailsID, StockMutationID, ItemID, SourceID, DestinationID);
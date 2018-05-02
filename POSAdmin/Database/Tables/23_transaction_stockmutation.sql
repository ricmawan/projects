DROP TABLE IF EXISTS transaction_stockmutation;

CREATE TABLE transaction_stockmutation
(
	StockMutationID		 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate				DATETIME,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKMUTATION_INDEX
ON transaction_stockmutation (StockMutationID);
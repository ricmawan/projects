DROP TABLE IF EXISTS transaction_stockadjust;

CREATE TABLE transaction_stockadjust
(
	StockAdjustID		 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate				DATETIME,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKADJUST_INDEX
ON transaction_stockadjust (StockAdjustID);
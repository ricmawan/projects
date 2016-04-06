DROP TABLE IF EXISTS transaction_stockopname;

CREATE TABLE transaction_stockopname
(
	StockOpnameID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKOPNAME_INDEX
ON transaction_stockopname (StockOpnameID);
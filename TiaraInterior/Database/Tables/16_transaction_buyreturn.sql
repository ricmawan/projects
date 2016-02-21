DROP TABLE IF EXISTS transaction_buyreturn;

CREATE TABLE transaction_buyreturn
(
	BuyReturnID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	BuyReturnNumber	VARCHAR(100) NULL,
	SupplierID 		BIGINT,
	TransactionDate DATETIME NOT NULL,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BUYRETURN_INDEX
ON transaction_buyreturn (BuyReturnID, SupplierID);
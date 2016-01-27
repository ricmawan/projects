DROP TABLE IF EXISTS transaction_buyreturn;

CREATE TABLE transaction_buyreturn
(
	BuyReturnID BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierID 	BIGINT,
	TransactionDate DATETIME NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BUYRETURN_INDEX
ON transaction_buyreturn (BuyReturnID, SupplierID);
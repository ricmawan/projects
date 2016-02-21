DROP TABLE IF EXISTS transaction_salereturn;

CREATE TABLE transaction_salereturn
(
	SaleReturnID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	CustomerID			BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALERETURN_INDEX
ON transaction_salereturn (SaleReturnID, CustomerID);

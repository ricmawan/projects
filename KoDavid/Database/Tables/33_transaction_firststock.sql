DROP TABLE IF EXISTS transaction_firststock;

CREATE TABLE transaction_firststock
(
	FirstStockID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	FirstStockNumber	VARCHAR(100) NULL,
	SupplierID 			BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(SupplierID) REFERENCES master_supplier(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FIRSTSTOCK_INDEX
ON transaction_firststock (FirstStockID, SupplierID);
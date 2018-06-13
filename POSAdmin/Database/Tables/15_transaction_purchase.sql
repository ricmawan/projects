DROP TABLE IF EXISTS transaction_purchase;

CREATE TABLE transaction_purchase
(
	PurchaseID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseNumber	VARCHAR(100) NULL,
	SupplierID 		BIGINT,
	TransactionDate DATETIME NOT NULL,
	PaymentTypeID	SMALLINT,
	Payment			DOUBLE,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(SupplierID) REFERENCES master_supplier(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASE_INDEX
ON transaction_purchase (PurchaseID, SupplierID);
DROP TABLE IF EXISTS transaction_purchasereturn;

CREATE TABLE transaction_purchasereturn
(
	PurchaseReturnID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierID 			BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASERETURN_INDEX
ON transaction_purchasereturn (PurchaseReturnID, SupplierID);
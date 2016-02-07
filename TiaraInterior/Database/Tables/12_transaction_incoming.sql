DROP TABLE IF EXISTS transaction_incoming;

CREATE TABLE transaction_incoming
(
	IncomingID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingNumber	VARCHAR(100) NULL,
	SupplierID 		BIGINT,
	TransactionDate DATETIME NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX INCOMING_INDEX
ON transaction_incoming (IncomingID, SupplierID);
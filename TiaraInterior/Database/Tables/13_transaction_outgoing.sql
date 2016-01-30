DROP TABLE IF EXISTS transaction_outgoing;

CREATE TABLE transaction_outgoing
(
	OutgoingID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingNumber		VARCHAR(100) NULL,
	CustomerID			BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	DeliveryCost		DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOING_INDEX
ON transaction_outgoing (OutgoingID, CustomerID);

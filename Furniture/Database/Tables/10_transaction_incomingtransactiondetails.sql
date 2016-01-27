DROP TABLE IF EXISTS transaction_incomingtransactiondetails;

CREATE TABLE transaction_incomingtransactiondetails
(
	IncomingTransactionDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingTransactionID		BIGINT,
	ItemID 			BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(IncomingTransactionID) REFERENCES transaction_incomingtransaction(IncomingTransactionID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX INCOMINGTRANSACTIONDETAILS_INDEX
ON transaction_incomingtransactiondetails (IncomingTransactionDetailsID, IncomingTransactionID);

DROP TABLE IF EXISTS transaction_outgoingtransactiondetails;

CREATE TABLE transaction_outgoingtransactiondetails
(
	OutgoingTransactionDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingTransactionID	BIGINT,
	ItemID 			BIGINT NOT NULL,
	Name			VARCHAR(255),
	Quantity		DOUBLE,
	Price			DOUBLE,
	Remarks			TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingTransactionID) REFERENCES transaction_outgoingtransaction(OutgoingTransactionID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TRANSACTIONOUTDETAILS_INDEX
ON transaction_outgoingtransactiondetails (OutgoingTransactionDetailsID, OutgoingTransactionID);
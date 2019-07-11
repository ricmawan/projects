DROP TABLE IF EXISTS transaction_outgoingdummy;

CREATE TABLE transaction_outgoingdummy
(
	OutgoingDummyID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate		DATETIME,
	ReceiptNumber		VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOINGDUMMY_INDEX
ON transaction_outgoingdummy (OutgoingDummyID);
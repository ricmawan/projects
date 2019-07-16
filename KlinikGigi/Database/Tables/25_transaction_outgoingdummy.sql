DROP TABLE IF EXISTS transaction_outgoingmodel;

CREATE TABLE transaction_outgoingmodel
(
	OutgoingModelID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate		DATETIME,
	ReceiptNumber		VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOINGMODEL_INDEX
ON transaction_outgoingmodel (OutgoingModelID);
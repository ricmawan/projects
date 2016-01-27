DROP TABLE IF EXISTS transaction_outgoingtransaction;

CREATE TABLE transaction_outgoingtransaction
(
	OutgoingTransactionID BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID	BIGINT,
	TransactionDate DATETIME NOT NULL,
	Discount	INT,
	Tax		INT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TRANSACTIONOUT_INDEX
ON transaction_outgoingtransaction (OutgoingTransactionID, ProjectID);

DROP TABLE IF EXISTS transaction_cancellation;

CREATE TABLE transaction_cancellation
(
	CancellationID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	DeletedBy			BIGINT,
	OutgoingID			BIGINT,
	IncomingID			BIGINT,
	SaleReturnID		BIGINT,
	BuyReturnID			BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(DeletedBy) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CANCELLATION_INDEX
ON transaction_cancellation (CancellationID);
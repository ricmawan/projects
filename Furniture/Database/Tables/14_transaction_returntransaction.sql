DROP TABLE IF EXISTS transaction_returntransaction;

CREATE TABLE transaction_returntransaction
(
	ReturnTransactionID BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID	BIGINT,
	TransactionDate DATETIME NOT NULL,
	ItemID 			BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX RETURNTRANSACTION_INDEX
ON transaction_returntransaction (ReturnTransactionID, ProjectID);

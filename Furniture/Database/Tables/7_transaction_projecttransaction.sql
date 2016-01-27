DROP TABLE IF EXISTS transaction_projecttransaction;

CREATE TABLE transaction_projecttransaction
(
	ProjectTransactionID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID		BIGINT,
	ProjectTransactionDate	DATETIME,
	Remarks 		TEXT,
	Amount 			DOUBLE NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX PROJECTTRANSACTION_INDEX
ON transaction_projecttransaction (ProjectTransactionID, ProjectID);

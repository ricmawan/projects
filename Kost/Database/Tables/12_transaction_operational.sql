DROP TABLE IF EXISTS transaction_operational;

CREATE TABLE transaction_operational
(
	OperationalID 	BIGINT PRIMARY KEY AUTO_INCREMENT,	
	TransactionDate	DATETIME,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OPERATIONAL_INDEX
ON transaction_operational (OperationalID);
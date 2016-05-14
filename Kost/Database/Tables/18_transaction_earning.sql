DROP TABLE IF EXISTS transaction_earning;

CREATE TABLE transaction_earning
(
	EarningID	 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate	DATETIME NOT NULL,
	Name			VARCHAR(100),
	Amount			DOUBLE,
	Remarks			VARCHAR(255),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX EARNING_INDEX
ON transaction_earning (EarningID);
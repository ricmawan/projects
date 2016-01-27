DROP TABLE IF EXISTS transaction_commonoperational;

CREATE TABLE transaction_commonoperational
(
	CommonOperationalID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CommonOperationalDate	DATETIME,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX COMMONOPERATIONAL_INDEX
ON transaction_commonoperational (CommonOperationalID);
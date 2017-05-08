DROP TABLE IF EXISTS transaction_titpan;

CREATE TABLE transaction_titipan
(
	TitipanID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate			DATETIME,
	Remarks					TEXT,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TITIPAN_INDEX
ON transaction_titipan(TitipanID);
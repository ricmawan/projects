DROP TABLE IF EXISTS transaction_tokencode;

CREATE TABLE transaction_tokencode
(
	TokenCodeID				INT PRIMARY KEY AUTO_INCREMENT,
	TokenCode				VARCHAR(10),
	IsValid					BIT,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TOKENCODE_INDEX
ON transaction_tokencode (TokenCodeID);
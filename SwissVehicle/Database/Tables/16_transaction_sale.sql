DROP TABLE IF EXISTS transaction_sale;

CREATE TABLE transaction_sale
(
	SaleID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	CustomerName	VARCHAR(255),
	TransactionDate	DATE NOT NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID);
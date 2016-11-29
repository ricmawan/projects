DROP TABLE IF EXISTS transaction_sale;

CREATE TABLE transaction_sale
(
	SaleID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	TableID				BIGINT,
	TransactionDate		DATE,
	Discount			DOUBLE,
	IsPercentage		BIT,
	Remarks				TEXT,
	Payment				DOUBLE,
	IsDone				BIT,
	IsCancelled			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(TableID) REFERENCES master_table(TableID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID, TableID);
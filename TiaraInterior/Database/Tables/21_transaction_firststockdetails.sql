DROP TABLE IF EXISTS transaction_firststockdetails;

CREATE TABLE transaction_firststockdetails
(
	FirstStockDetailsID BIGINT PRIMARY KEY AUTO_INCREMENT,
	FirstStockID		BIGINT,
	TypeID 				BIGINT NOT NULL,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	SalePrice			DOUBLE,
	Discount			INT,
	BatchNumber			VARCHAR(100) NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(FirstStockID) REFERENCES transaction_firststock(FirstStockID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(TypeID) REFERENCES master_type(TypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FIRSTSTOCKDETAILS_INDEX
ON transaction_firststockdetails (FirstStockDetailsID, FirstStockID, TypeID);
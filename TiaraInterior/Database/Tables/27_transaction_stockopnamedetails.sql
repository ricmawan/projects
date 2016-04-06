DROP TABLE IF EXISTS transaction_stockopnamedetails;

CREATE TABLE transaction_stockopnamedetails
(
	StockOpnameDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	StockOpnameID			BIGINT,
	TypeID 					BIGINT NOT NULL,
	Quantity				DOUBLE,
	BatchNumber				VARCHAR(100) NULL,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL,
	FOREIGN KEY(StockOpnameID) REFERENCES transaction_stockopname(StockOpnameID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(TypeID) REFERENCES master_type(TypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKOPNAMEDETAILS_INDEX
ON transaction_stockopnamedetails (StockOpnameDetailsID, StockOpnameID);
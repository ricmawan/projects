DROP TABLE IF EXISTS transaction_salereturndetails;

CREATE TABLE transaction_salereturndetails
(
	SaleReturnDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleReturnID		BIGINT,
	ItemID 				BIGINT NOT NULL,
	Name				VARCHAR(255),
	Quantity			DOUBLE,
	Price				DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(SaleReturnID) REFERENCES transaction_salereturn(SaleReturnID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALERETURNDETAILS_INDEX
ON transaction_salereturndetails(SaleReturnDetailsID, SaleReturnID);
DROP TABLE IF EXISTS transaction_salereturndetails;

CREATE TABLE transaction_salereturndetails
(
	SaleReturnDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleReturnID		BIGINT,
	TypeID 				BIGINT NOT NULL,
	Quantity			DOUBLE,
	SalePrice			DOUBLE,
	BatchNumber			VARCHAR(100) NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(SaleReturnID) REFERENCES transaction_salereturn(SaleReturnID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(TypeID) REFERENCES master_type(TypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALERETURNDETAILS_INDEX
ON transaction_salereturndetails(SaleReturnDetailsID, SaleReturnID);
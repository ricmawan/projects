DROP TABLE IF EXISTS transaction_saledetails;

CREATE TABLE transaction_saledetails
(
	SaleDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleID			BIGINT,
	MenuListID 		BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	Discount		DOUBLE,
	IsPercentage	BIT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(SaleID) REFERENCES transaction_sale(SaleID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(MenuListID) REFERENCES master_menulist(MenuListID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_saledetails (SaleDetailsID, SaleID, MenuListID);
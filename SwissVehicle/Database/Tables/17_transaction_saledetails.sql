DROP TABLE IF EXISTS transaction_saledetails;

CREATE TABLE transaction_saledetails
(
    SaleDetailsID		BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    SaleID          	BIGINT,
    ItemID		    	BIGINT NOT NULL,
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	Remarks 		    TEXT,
    CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ItemID) REFERENCES master_item(ItemID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (SaleID) REFERENCES transaction_sale(SaleID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_saledetails (SaleDetailsID, SaleID, ItemID);
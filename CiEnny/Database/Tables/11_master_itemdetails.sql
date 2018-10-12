DROP TABLE IF EXISTS master_itemdetails;

CREATE TABLE master_itemdetails
(
	ItemDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemID 				BIGINT,
	ItemDetailsCode		VARCHAR(100),
	UnitID				SMALLINT,
	ConversionQuantity	DOUBLE,
	BuyPrice			DOUBLE,
	RetailPrice			DOUBLE,
    Price1				DOUBLE,
    Qty1				DOUBLE,
    Price2				DOUBLE,
    Qty2				DOUBLE,
	Weight				DOUBLE,
	MinimumStock		DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
    FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(UnitID) REFERENCES master_unit(UnitID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEMDETAILS_INDEX
ON master_itemdetails (ItemDetailsID, ItemID, UnitID);
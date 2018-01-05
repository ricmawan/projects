DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
    CategoryID			BIGINT,
	ItemName			VARCHAR(255) NOT NULL,
	ItemCode			VARCHAR(100),
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
    FOREIGN KEY(CategoryID) REFERENCES master_category(CategoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID, CategoryID);
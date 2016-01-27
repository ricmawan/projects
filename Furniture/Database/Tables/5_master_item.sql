DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemName	VARCHAR(255) NOT NULL,
	UnitID		BIGINT,
	CategoryID 	BIGINT NOT NULL,
	ReminderCount 	INT,
	Price		DOUBLE,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(CategoryID) REFERENCES master_category(CategoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(UnitID) REFERENCES master_unit(UnitID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

/*INSERT INTO master_item
(
	ItemID,
	ItemName,
	CategoryID,
	ReminderCount,
	Price,
	CreatedDate,
	CreatedBy
)
VALUES
(
	0,
	'Triplek 3mm',
	1,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Triplek 5mm',
	1,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Triplek 8mm',
	1,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Paku 3cm',
	2,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Paku 5cm',
	2,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Paku 8cm',
	2,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Cat kayu',
	3,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Cat besi',
	3,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Cat triplek',
	3,
	10,
	0,
	NOW(),
	'Admin'
);*/

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID, CategoryID, UnitID);
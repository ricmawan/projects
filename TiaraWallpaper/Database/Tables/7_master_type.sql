DROP TABLE IF EXISTS master_type;

CREATE TABLE master_type
(
	TypeID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	TypeName		VARCHAR(255) NOT NULL,
	UnitID			BIGINT,
	BrandID 		BIGINT NOT NULL,
	ReminderCount 	INT,
	BuyPrice		DOUBLE,
	SalePrice		DOUBLE,
	Quantity		INT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(BrandID) REFERENCES master_brand(BrandID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(UnitID) REFERENCES master_unit(UnitID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

INSERT INTO `master_type` (`TypeID`, `TypeName`, `UnitID`, `BrandID`, `ReminderCount`, `BuyPrice`, `SalePrice`, `Quantity`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, '001', 1, 1, 0, 17100, 27000, NULL, '2016-03-13 01:49:16', 'Admin', '2016-03-12 18:51:54', 'Admin'),
(2, '002', 1, 1, 0, 25000, 35000, NULL, '2016-03-13 01:49:31', 'Admin', '2016-03-12 18:50:29', 'Admin'),
(3, '003', 2, 2, 0, 22500, 35000, NULL, '2016-03-17 23:42:58', 'Admin', '2016-03-18 16:12:24', 'Admin');

CREATE UNIQUE INDEX TYPE_INDEX
ON master_type (TypeID, BrandID, UnitID);
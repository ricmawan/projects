DROP TABLE IF EXISTS master_material;

CREATE TABLE master_material
(
	MaterialID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	MaterialName	VARCHAR(255) NOT NULL,
	SalePrice		DOUBLE NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

/*INSERT INTO `master_material` (`MaterialID`, `MaterialName`, `Price`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Cabut Gigi', 200000, '2016-03-12 17:02:18', 'Admin', NULL, NULL);*/

CREATE UNIQUE INDEX MATERIAL_INDEX
ON master_material (MaterialID);
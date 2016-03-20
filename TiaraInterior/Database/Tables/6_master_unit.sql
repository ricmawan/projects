DROP TABLE IF EXISTS master_unit;

CREATE TABLE master_unit
(
	UnitID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	UnitName		VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_unit` (`UnitID`, `UnitName`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Roll', '2016-03-12 17:09:55', 'Admin', '2016-03-12 19:28:06', 'Admin'),
(2, 'm', '2016-03-12 17:10:54', 'Admin', '2016-03-20 07:43:38', 'Admin'),
(3, 'm lari', '2016-03-12 17:11:14', 'Admin', NULL, NULL),
(4, 'Box', '2016-03-13 02:28:21', 'Admin', NULL, NULL);

CREATE UNIQUE INDEX UNIT_INDEX
ON master_unit (UnitID);
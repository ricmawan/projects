DROP TABLE IF EXISTS master_supplier;

CREATE TABLE master_supplier
(
	SupplierID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierName	VARCHAR(255) NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address 		VARCHAR(255),
	City			VARCHAR(100),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_supplier` (`SupplierID`, `SupplierName`, `Telephone`, `Address`, `City`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'CV. WallPaper', '081286098', '  Pekunden Tengah', 'Semarang', '2016-03-13 01:51:07', 'Admin', '2016-03-12 19:28:51', 'Admin'),
(2, 'CV. Interior', '0811928121', '  Pekunden Barat', 'Semarang', '2016-03-13 01:51:07', 'Admin', '2016-03-12 19:28:51', 'Admin');

CREATE UNIQUE INDEX SUPPLIER_INDEX
ON master_supplier (SupplierID);
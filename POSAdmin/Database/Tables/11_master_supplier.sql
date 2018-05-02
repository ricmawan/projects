DROP TABLE IF EXISTS master_supplier;

CREATE TABLE master_supplier
(
	SupplierID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
    SupplierCode		VARCHAR(100),
	SupplierName		VARCHAR(255) NOT NULL,
    Telephone			VARCHAR(100),
	Address				TEXT,
    City				VARCHAR(100),
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SUPPLIER_INDEX
ON master_supplier (SupplierID);
DROP TABLE IF EXISTS master_supplier;

CREATE TABLE master_supplier
(
	SupplierID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierName	VARCHAR(255) NOT NULL,
	Telephone 	VARCHAR(100) NOT NULL,
	Address 	VARCHAR(255),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;

/*INSERT INTO master_supplier
(
	SupplierID,
	SupplierName,
	Telephone,
	Address,
	CreatedDate,
	CreatedBy
)
VALUES
(
	0,
	'PT. Avian',
	'021345',
	'Jakarta Barat',
	NOW(),
	'Admin'
),
(
	0,
	'PT. Paku Payung',
	'01353',
	'Semarang Tengah',
	NOW(),
	'Admin'
);*/

CREATE UNIQUE INDEX SUPPLIER_INDEX
ON master_supplier (SupplierID);

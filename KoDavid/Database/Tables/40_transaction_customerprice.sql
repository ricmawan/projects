DROP TABLE IF master_customerprice;

CREATE TABLE master_customerprice
(
	CustomerPriceID			SMALLINT PRIMARY KEY AUTO_INCREMENT,
	CustomerPriceName		VARCHAR(50),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CUSTOMERPRICE_INDEX
ON master_customerprice (CustomerPriceID);

INSERT INTO master_customerprice
VALUES
(
	1,
	'Harga Ecer',
	NOW(),
	'Admin',
	NULL,
	NULL,
),
(
	2,
	'Grosir 1',
	NOW(),
	'Admin',
	NULL,
	NULL,
),
(
	3,
	'Grosir 2',
	NOW(),
	'Admin',
	NULL,
	NULL,
),

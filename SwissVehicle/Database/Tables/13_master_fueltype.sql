DROP TABLE IF EXISTS master_fueltype;

CREATE TABLE master_fueltype
(
	FuelTypeID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	FuelTypeName	VARCHAR(255),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO master_fueltype
(
	FuelTypeName,
	CreatedDate,
	CreatedBy
)
VALUES
(
	'Premium',
	NOW(),
	'Admin'
),
(
	'Pertamax',
	NOW(),
	'Admin'
),
(
	'Pertamax Plus',
	NOW(),
	'Admin'
),
(
	'Pertalite',
	NOW(),
	'Admin'
),
(
	'Solar',
	NOW(),
	'Admin'
),
(
	'Bio Solar',
	NOW(),
	'Admin'
),
(
	'Pertamina Dex',
	NOW(),
	'Admin'
);

CREATE UNIQUE INDEX FUELTYPE_INDEX
ON master_fueltype (FuelTypeID);
DROP TABLE IF EXISTS master_category;

CREATE TABLE master_category
(
	CategoryID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CategoryName	VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;

/*INSERT INTO master_category
(
	CategoryID,
	CategoryName,
	CreatedDate,
	CreatedBy
)
VALUES
(
	0,
	'Triplek',
	NOW(),
	'Admin'
),
(
	0,
	'Paku',
	NOW(),
	'Admin'
),
(
	0,
	'Cat',
	NOW(),
	'Admin'
);*/

CREATE UNIQUE INDEX CATEGORY_INDEX
ON master_category (CategoryID);

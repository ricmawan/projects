DROP TABLE IF EXISTS master_menulistcategory;

CREATE TABLE master_menulistcategory
(
	MenuListCategoryID		INT PRIMARY KEY,
	MenuListCategoryName	VARCHAR(100),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MENULISTCATEGORY_INDEX
ON master_menulistcategory (MenuListCategoryID);

INSERT INTO master_menulistcategory
VALUES
(
	1,
	'Makanan',
	NOW(),
	'Admin',
	NULL,
	NULL
),
(
	2,
	'Minuman',
	NOW(),
	'Admin',
	NULL,
	NULL
);
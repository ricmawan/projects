DROP TABLE IF EXISTS master_menulist;

CREATE TABLE master_menulist
(
	MenuListID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	MenuListCategoryID	INT,
	MenuName			VARCHAR(100),
	Price				DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(MenuListCategoryID) REFERENCES master_menulistcategory(MenuListCategoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MENULIST_INDEX
ON master_menulist (MenuListID, MenuListCategoryID);
DROP TABLE IF EXISTS master_category;

CREATE TABLE master_category
(
	CategoryID 			INT PRIMARY KEY AUTO_INCREMENT,
	CategoryCode		VARCHAR(100),
	CategoryName		VARCHAR(255) NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CATEGORY_INDEX
ON master_category (CategoryID);
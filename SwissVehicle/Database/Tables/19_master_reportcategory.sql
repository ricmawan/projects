DROP TABLE IF EXISTS master_reportcategory;

CREATE TABLE master_reportcategory
(
	ReportCategoryID	INT PRIMARY KEY AUTO_INCREMENT,
	ReportCategoryName	VARCHAR(255) NOT NULL,
	ReportCategoryType	VARCHAR(100) NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX REPORTCATEGORY_INDEX
ON master_reportcategory (ReportCategoryID);
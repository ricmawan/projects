DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	ReportCategoryID	INT,
	ItemCode			VARCHAR(255),
	ItemName 			VARCHAR(255) NOT NULL,
	Price		 		DOUBLE NOT NULL,
	Remarks 			TEXT,
	IsSecond			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ReportCategoryID) master_reportcategory(ReportCategoryID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID, ReportCategoryID);
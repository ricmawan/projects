DROP TABLE IF EXISTS master_groupmenu;

CREATE TABLE master_groupmenu
(
	GroupMenuID		INT PRIMARY KEY,
	GroupMenuName 	VARCHAR(255),
	Icon			VARCHAR(255),
	Url				VARCHAR(255),
	OrderNo			INT
)ENGINE=InnoDB;

INSERT INTO master_groupmenu
VALUES
(
	1,
	'Home',
	'fa fa-home fa-2x',
	'./Home.php',
	1
),
(
	2,
	'Master Data',
	'fa fa-book fa-2x',
	NULL,
	2
),
(
	3,
	'Transaksi',
	'fa fa-cart-plus fa-2x',
	NULL,
	3
),
(
	4,
	'Laporan',
	'fa fa-line-chart fa-2x',
	NULL,
	4
);

CREATE UNIQUE INDEX GROUPMENU_INDEX
ON master_groupmenu (GroupMenuID);
DROP TABLE IF EXISTS master_menu;

CREATE TABLE master_menu
(
	MenuID 		BIGINT PRIMARY KEY,
	GroupMenuID	INT,
	MenuName 	VARCHAR(255),
	Url			VARCHAR(255),
	Icon		VARCHAR(255),
	IsReport	BIT,
	OrderNo		INT,
	FOREIGN KEY(GroupMenuID) REFERENCES master_groupmenu(GroupMenuID)
)ENGINE=InnoDB;

INSERT INTO master_menu
(
	MenuID,
	GroupMenuID,
	MenuName,
	URL,
	Icon,
	IsReport,
	OrderNo    
)
VALUES
(
	1,
	2,
	'User',
	'Master/User/',
	NULL,
	0,
	1
),
(
	2,
	2,
	'Meja',
	'Master/Table/',
	NULL,
	0,
	2
),
(
	3,
	3,
	'Pemesanan',
	'Transaction/Order/',
	NULL,
	0,
	1
),
(
	4,
	3,
	'Penjualan',
	'Transaction/Sale/',
	NULL,
	0,
	2
),
(
	5,
	4,
	'Penjualan',
	'Report/Sale/',
	NULL,
	1,
	1
),
(
	6,
	2,
	'Makanan & Minuman',
	'Master/MenuList/',
	NULL,
	0,
	3
);
CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);DROP TABLE IF EXISTS master_usertype;

CREATE TABLE master_usertype
(
	UserTypeID		SMALLINT PRIMARY KEY NOT NULL,
	UserTypeName	VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

INSERT INTO master_usertype
VALUES
(
	1,
	'Admin'
);DROP TABLE IF EXISTS master_user;

CREATE TABLE master_user
(
	UserID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserTypeID		SMALLINT,
	UserName		VARCHAR(255) NOT NULL,
	UserLogin 		VARCHAR(100) NOT NULL,
	UserPassword 	VARCHAR(255) NOT NULL,
	IsActive		BOOLEAN,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(UserTypeID) REFERENCES master_usertype(UserTypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

INSERT INTO master_user
VALUES
(
	0,
	1,
	'System Administrator',
	'Admin',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
);

CREATE UNIQUE INDEX USER_INDEX
ON master_user (UserID);
DROP TABLE IF EXISTS master_role;

CREATE TABLE master_role
(
	RoleID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserID 		BIGINT,
	MenuID 		BIGINT,
	EditFlag 	BOOLEAN,
	DeleteFlag 	BOOLEAN,
	FOREIGN KEY(UserID) REFERENCES master_user(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(MenuID) REFERENCES master_menu(MenuID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

INSERT INTO master_role
VALUES
(
	0,
	1,
	1,
	1,
	1
),
(
	0,
	1,
	2,
	1,
	1
),
(
	0,
	1,
	3,
	1,
	1
),
(
	0,
	1,
	4,
	1,
	1
),
(
	0,
	1,
	5,
	1,
	1
),
(
	0,
	1,
	6,
	1,
	1
);

CREATE UNIQUE INDEX ROLE_INDEX
ON master_role (RoleID, UserID, MenuID);DROP TABLE IF EXISTS master_parameter;

CREATE TABLE master_parameter
(
	ParameterID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ParameterName 	VARCHAR(255) NOT NULL,
	ParameterValue 	VARCHAR(255) NOT NULL,
	Remarks 		TEXT,
	IsNumber		INT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_parameter` (`ParameterID`, `ParameterName`, `ParameterValue`, `Remarks`, `IsNumber`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'APPLICATION_PATH', '/Projects/Melken/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\Melken\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\Melken\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\Melken\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL);DROP TABLE IF EXISTS master_tablestatus;

CREATE TABLE master_tablestatus
(
	TableStatusID 		SMALLINT PRIMARY KEY AUTO_INCREMENT,
	TableStatusName		VARCHAR(100)
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TABLESTATUS_INDEX
ON master_tablestatus (TableStatusID);

INSERT INTO master_tablestatus
VALUES
(
	1,
	'available'
),
(
	2,
	'used'
);DROP TABLE IF EXISTS master_table;

CREATE TABLE master_table
(
	TableID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TableNumber		VARCHAR(100),
	TableStatusID	SMALLINT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(TableStatusID) REFERENCES master_tablestatus(TableStatusID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TABLE_INDEX
ON master_table (TableID, TableStatusID);
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
);DROP TABLE IF EXISTS master_menulist;

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
ON master_menulist (MenuListID, MenuListCategoryID);DROP TABLE IF EXISTS transaction_sale;

CREATE TABLE transaction_sale
(
	SaleID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	TableID				BIGINT,
	TransactionDate		DATE,
	Discount			DOUBLE,
	IsPercentage		BIT,
	Remarks				TEXT,
	Payment				DOUBLE,
	IsDone				BIT,
	IsCancelled			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(TableID) REFERENCES master_table(TableID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID, TableID);DROP TABLE IF EXISTS transaction_saledetails;

CREATE TABLE transaction_saledetails
(
	SaleDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleID			BIGINT,
	MenuListID 		BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	Discount		DOUBLE,
	IsPercentage	BIT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(SaleID) REFERENCES transaction_sale(SaleID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(MenuListID) REFERENCES master_menulist(MenuListID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_saledetails (SaleDetailsID, SaleID, MenuListID);
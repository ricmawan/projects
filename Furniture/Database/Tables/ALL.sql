DROP TABLE IF EXISTS master_groupmenu;

CREATE TABLE master_groupmenu
(
	GroupMenuID	INT PRIMARY KEY AUTO_INCREMENT,
	GroupMenuName 	VARCHAR(255),
	Icon		VARCHAR(255),
	Url		VARCHAR(255),
	OrderNo		INT
);

INSERT INTO master_groupmenu
VALUES
(
	0,
	'Home',
	'fa fa-home fa-3x',
	'./Home.php',
	1
),
(
	0,
	'Master Data',
	'fa fa-book fa-3x',
	NULL,
	2
),
(
	0,
	'Transaksi',
	'fa fa-cart-plus fa-3x',
	NULL,
	3
),
(
	0,
	'Laporan',
	'fa fa-line-chart fa-3x',
	NULL,
	4
);

CREATE UNIQUE INDEX GROUPMENU_INDEX
ON master_groupmenu (GroupMenuID);
DROP TABLE IF EXISTS master_user;

CREATE TABLE master_user
(
	UserID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserName	VARCHAR(255) NOT NULL,
	UserLogin 	VARCHAR(100) NOT NULL,
	UserPassword 	VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy	VARCHAR(255) NULL
);

INSERT INTO master_user
VALUES
(
	0,
	'System Administrator',
	'Admin',
	MD5('abcdef'),
	NOW(),
	'System',
	NULL,
	NULL
);

CREATE UNIQUE INDEX USER_INDEX
ON master_user (UserID);
DROP TABLE IF EXISTS master_menu;

CREATE TABLE master_menu
(
	MenuID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	GroupMenuID	INT,
	MenuName 	VARCHAR(255),
	Url		VARCHAR(255),
	Icon		VARCHAR(255),
	OrderNo		INT,
	FOREIGN KEY(GroupMenuID) REFERENCES master_groupmenu(GroupMenuID)
);

INSERT INTO master_menu
VALUES
(
	0,
	2,
	'User',
	'Master/User/',
	NULL,
	1
),
(
	0,
	2,
	'Kategori Barang',
	'Master/Category/',
	NULL,
	2
),
(
	0,
	2,
	'Barang',
	'Master/Item/',
	NULL,
	3
),
(
	0,
	2,
	'Proyek',
	'Master/Project/',
	NULL,
	4
),
(
	0,
	2,
	'Supplier',
	'Master/Supplier/',
	NULL,
	5
),
(
	0,
	3,
	'Transaksi Masuk',
	'Transaction/IncomingTransaction/',
	NULL,
	1
),
(
	0,
	3,
	'Transaksi Keluar',
	'Transaction/OutgoingTransaction/',
	NULL,
	2
),
(
	0,
	3,
	'Retur Barang',
	'Transaction/ReturnTransaction/',
	NULL,
	3
),
(
	0,
	3,
	'Pembayaran Proyek',
	'Transaction/ProjectPayment/',
	NULL,
	4
),
(
	0,
	4,
	'Laporan Proyek',
	'Report/ProjectReport/',
	NULL,
	1
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
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
);

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
),
(
	0,
	1,
	7,
	1,
	1
),
(
	0,
	1,
	8,
	1,
	1
),
(
	0,
	1,
	9,
	1,
	1
),
(
	0,
	1,
	10,
	1,
	1
);


CREATE UNIQUE INDEX ROLE_INDEX
ON master_role (RoleID, UserID, MenuID);
DROP TABLE IF EXISTS master_category;

CREATE TABLE master_category
(
	CategoryID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CategoryName	VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
);

CREATE UNIQUE INDEX CATEGORY_INDEX
ON master_category (CategoryID);
DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemName	VARCHAR(255) NOT NULL,
	CategoryID 	BIGINT NOT NULL,
	ReminderCount 	INT,
	Price		DOUBLE,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(CategoryID) REFERENCES master_category(CategoryID) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID);
DROP TABLE IF EXISTS master_project;

CREATE TABLE master_project
(
	ProjectID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectName	VARCHAR(255) NOT NULL,
	Amount 		DOUBLE NOT NULL,
	Remarks 	TEXT,
	IsDone		BIT NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
);


CREATE UNIQUE INDEX PROJECT_INDEX
ON master_project (ProjectID);
DROP TABLE IF EXISTS transaction_projecttransaction;

CREATE TABLE transaction_projecttransaction
(
	ProjectTransactionID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID		BIGINT,
	ProjectTransactionDate	DATETIME,
	Remarks 		TEXT,
	Amount 			DOUBLE NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE UNIQUE INDEX PROJECTTRANSACTION_INDEX
ON transaction_projecttransaction (ProjectTransactionID);
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
);

CREATE UNIQUE INDEX SUPPLIER_INDEX
ON master_supplier (SupplierID);
DROP TABLE IF EXISTS transaction_incomingtransaction;

CREATE TABLE transaction_incomingtransaction
(
	IncomingTransactionID BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierID 	BIGINT,
	TransactionDate DATETIME NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(SupplierID) REFERENCES master_supplier(SupplierID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX INCOMINGTRANSACTION_INDEX
ON transaction_incomingtransaction (IncomingTransactionID, SupplierID);DROP TABLE IF EXISTS transaction_incomingtransactiondetails;

CREATE TABLE transaction_incomingtransactiondetails
(
	IncomingTransactionDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingTransactionID		BIGINT,
	ItemID 			BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(IncomingTransactionID) REFERENCES transaction_incomingtransaction(IncomingTransactionID) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE UNIQUE INDEX INCOMINGTRANSACTIONDETAILS_INDEX
ON transaction_incomingtransactiondetails (IncomingTransactionDetailsID, IncomingTransactionID);
DROP TABLE IF EXISTS transaction_outgoingtransaction;

CREATE TABLE transaction_outgoingtransaction
(
	OutgoingTransactionID BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID	BIGINT,
	TransactionDate DATETIME NOT NULL,
	Discount	INT,
	Tax		INT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX TRANSACTIONOUT_INDEX
ON transaction_outgoingtransaction (OutgoingTransactionID, ProjectID);
DROP TABLE IF EXISTS transaction_outgoingtransactiondetails;

CREATE TABLE transaction_outgoingtransactiondetails
(
	OutgoingTransactionDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingTransactionID	BIGINT,
	ItemID 			BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	Remarks			TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingTransactionID) REFERENCES transaction_outgoingtransaction(OutgoingTransactionID) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE UNIQUE INDEX TRANSACTIONOUTDETAILS_INDEX
ON transaction_outgoingtransactiondetails (OutgoingTransactionDetailsID, OutgoingTransactionID);DROP TABLE IF EXISTS master_parameter;

CREATE TABLE master_parameter
(
	ParameterID BIGINT PRIMARY KEY AUTO_INCREMENT,
	ParameterName VARCHAR(255) NOT NULL,
	ParameterValue VARCHAR(255) NOT NULL,
	Remarks TEXT,
	IsNumber INT,
	CreatedDate DATETIME NOT NULL,
	CreatedBy VARCHAR(255) NOT NULL,
	ModifiedDate TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy VARCHAR(255) NULL
);

INSERT INTO master_parameter
VALUES
(
	0,
	'APPLICATION_PATH',
	'/Project/Furniture/Source/',
	'Location of the application',
	0,
	NOW(),
	'System',
	NULL,
	NULL
);

DROP TABLE IF EXISTS transaction_returntransaction;

CREATE TABLE transaction_returntransaction
(
	ReturnTransactionID BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID	BIGINT,
	TransactionDate DATETIME NOT NULL,
	Discount	INT,
	Tax		INT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX RETURNTRANSACTION_INDEX
ON transaction_returntransaction (ReturnTransactionID, ProjectID);
DROP TABLE IF EXISTS transaction_returntransactiondetails;

CREATE TABLE transaction_returntransactiondetails
(
	ReturnTransactionDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ReturnTransactionID	BIGINT,
	ItemID 			BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	Remarks			TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(ReturnTransactionID) REFERENCES transaction_returntransaction(ReturnTransactionID) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE UNIQUE INDEX RETURNTRANSACTIONDETAILS_INDEX
ON transaction_returntransactiondetails (ReturnTransactionDetailsID, ReturnTransactionID);
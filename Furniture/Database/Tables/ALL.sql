DROP TABLE IF EXISTS master_groupmenu;

CREATE TABLE master_groupmenu
(
	GroupMenuID	INT PRIMARY KEY AUTO_INCREMENT,
	GroupMenuName 	VARCHAR(255),
	Icon		VARCHAR(255),
	Url		VARCHAR(255),
	OrderNo		INT
)ENGINE=InnoDB;

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
)ENGINE=InnoDB;

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
)ENGINE=InnoDB;

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
	'Satuan',
	'Master/Unit/',
	NULL,
	3
),
(
	0,
	2,
	'Barang',
	'Master/Item/',
	NULL,
	4
),
(
	0,
	2,
	'Proyek',
	'Master/Project/',
	NULL,
	5
),
(
	0,
	2,
	'Supplier',
	'Master/Supplier/',
	NULL,
	6
),
(
	0,
	3,
	'Barang Masuk',
	'Transaction/IncomingTransaction/',
	NULL,
	1
),
(
	0,
	3,
	'Barang Keluar',
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
	'Operasional Proyek',
	'Transaction/ProjectOperational/',
	NULL,
	4
),
(
	0,
	3,
	'Pembayaran Proyek',
	'Transaction/ProjectPayment/',
	NULL,
	5
),
(
	0,
	4,
	'Laporan Proyek',
	'Report/ProjectReport/',
	NULL,
	1
),
(
	0,
	4,
	'Mutasi Stok',
	'Report/StockMutation/',
	NULL,
	2
),
(
	0,
	4,
	'Arus Kas',
	'Report/CashFlow/',
	NULL,
	3
),
(
	0,
	3,
	'Operasional',
	'Transaction/CommonOperational/',
	NULL,
	6
),
(
	0,
	2,
	'Periode Gaji',
	'Master/Period/',
	NULL,
	7
),
(
	0,
	2,
	'Karyawan',
	'Master/Employee/',
	NULL,
	8
),
(
	0,
	3,
	'Gaji Karyawan',
	'Transaction/Salary/',
	NULL,
	7
),
(
	0,
	4,
	'Gaji Karyawan',
	'Report/SalaryReport/',
	NULL,
	4
),
(
	0,
	4,
	'Laporan Aset',
	'Report/Assets/',
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
),
(
	0,
	1,
	11,
	1,
	1
),
(
	0,
	1,
	12,
	1,
	1
),
(
	0,
	1,
	13,
	1,
	1
),
(
	0,
	1,
	14,
	1,
	1
),
(
	0,
	1,
	15,
	1,
	1
),
(
	0,
	1,
	16,
	1,
	1
),
(
	0,
	1,
	17,
	1,
	1
),
(
	0,
	1,
	18,
	1,
	1
),
(
	0,
	1,
	19,
	1,
	1
);


CREATE UNIQUE INDEX ROLE_INDEX
ON master_role (RoleID, UserID, MenuID);
DROP TABLE IF EXISTS master_unit;

CREATE TABLE master_unit
(
	UnitID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	UnitName	VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX UNIT_INDEX
ON master_unit (UnitID);
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
DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemName	VARCHAR(255) NOT NULL,
	UnitID		BIGINT,
	CategoryID 	BIGINT NOT NULL,
	ReminderCount 	INT,
	Price		DOUBLE,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(CategoryID) REFERENCES master_category(CategoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(UnitID) REFERENCES master_unit(UnitID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

/*INSERT INTO master_item
(
	ItemID,
	ItemName,
	CategoryID,
	ReminderCount,
	Price,
	CreatedDate,
	CreatedBy
)
VALUES
(
	0,
	'Triplek 3mm',
	1,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Triplek 5mm',
	1,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Triplek 8mm',
	1,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Paku 3cm',
	2,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Paku 5cm',
	2,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Paku 8cm',
	2,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Cat kayu',
	3,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Cat besi',
	3,
	10,
	0,
	NOW(),
	'Admin'
),
(
	0,
	'Cat triplek',
	3,
	10,
	0,
	NOW(),
	'Admin'
);*/

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID, CategoryID, UnitID);DROP TABLE IF EXISTS master_project;

CREATE TABLE master_project
(
	ProjectID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectName	VARCHAR(255) NOT NULL,
	IsDone		BIT NOT NULL,
	Remarks 	TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;


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
)ENGINE=InnoDB;


CREATE UNIQUE INDEX PROJECTTRANSACTION_INDEX
ON transaction_projecttransaction (ProjectTransactionID, ProjectID);
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
)ENGINE=InnoDB;

/*INSERT INTO master_supplier
(
	SupplierID,
	SupplierName,
	Telephone,
	Address,
	CreatedDate,
	CreatedBy
)
VALUES
(
	0,
	'PT. Avian',
	'021345',
	'Jakarta Barat',
	NOW(),
	'Admin'
),
(
	0,
	'PT. Paku Payung',
	'01353',
	'Semarang Tengah',
	NOW(),
	'Admin'
);*/

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
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;

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
)ENGINE=InnoDB;

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
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TRANSACTIONOUT_INDEX
ON transaction_outgoingtransaction (OutgoingTransactionID, ProjectID);
DROP TABLE IF EXISTS transaction_outgoingtransactiondetails;

CREATE TABLE transaction_outgoingtransactiondetails
(
	OutgoingTransactionDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingTransactionID	BIGINT,
	ItemID 			BIGINT NOT NULL,
	Name			VARCHAR(255),
	Quantity		DOUBLE,
	Price			DOUBLE,
	Remarks			TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingTransactionID) REFERENCES transaction_outgoingtransaction(OutgoingTransactionID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

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
)ENGINE=InnoDB;

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
	ItemID 			BIGINT NOT NULL,
	Quantity		DOUBLE,
	Price			DOUBLE,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX RETURNTRANSACTION_INDEX
ON transaction_returntransaction (ReturnTransactionID, ProjectID);
DROP TABLE IF EXISTS master_itemnotification;

CREATE TABLE master_itemnotification
(
	ItemNotificationID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemID 		BIGINT,
	Remarks		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEMNOTIFICATION_INDEX
ON master_itemnotification (ItemNotificationID, ItemID);DROP TABLE IF EXISTS transaction_projectpayment;

CREATE TABLE transaction_projectpayment
(
	ProjectPaymentID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ProjectID		BIGINT,
	ProjectTransactionDate	DATETIME,
	Remarks 		TEXT,
	Amount 			DOUBLE NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX PROJECTPAYMENT_INDEX
ON transaction_projectpayment (ProjectPaymentID, ProjectID);
DROP TABLE IF EXISTS transaction_commonoperational;

CREATE TABLE transaction_commonoperational
(
	CommonOperationalID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CommonOperationalDate	DATETIME,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX COMMONOPERATIONAL_INDEX
ON transaction_commonoperational (CommonOperationalID);DROP TABLE IF EXISTS transaction_commonoperationaldetails;

CREATE TABLE transaction_commonoperationaldetails
(
	CommonOperationalDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CommonOperationalID			BIGINT,
	Remarks 					TEXT,
	Amount 						DOUBLE NOT NULL,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(CommonOperationalID) REFERENCES transaction_commonoperational(CommonOperationalID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX COMMONOPERATIONALDETAILS_INDEX
ON transaction_commonoperationaldetails (CommonOperationalDetailsID, CommonOperationalID);
DROP TABLE IF EXISTS transaction_asset;

CREATE TABLE transaction_asset
(
	AssetID 					BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate				DATETIME,
	Remarks 					TEXT,
	Amount 						DOUBLE NOT NULL,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ASSET_INDEX
ON transaction_asset (AssetID);DROP TABLE IF EXISTS master_employee;

CREATE TABLE master_employee
(
	EmployeeID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	EmployeeName	VARCHAR(255) NOT NULL,
	StartDate		DATE NULL,
	EndDate			DATE NULL,
	DailySalary		DOUBLE NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,	
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX EMPLOYEE_INDEX
ON master_employee (EmployeeID);DROP TABLE IF EXISTS master_period;

CREATE TABLE master_period
(
	PeriodID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	StartDate	DATE NOT NULL,
	EndDate		DATE NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 	VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX PERIOD_INDEX
ON master_period (PeriodID);
DROP TABLE IF EXISTS transaction_salary;

CREATE TABLE transaction_salary
(
	SalaryID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	PeriodID				BIGINT,
	SalaryDate				DATETIME,	
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL,
	FOREIGN KEY (PeriodID) REFERENCES master_period(PeriodID)
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALARY_INDEX
ON transaction_salary (SalaryID, PeriodID);DROP TABLE IF EXISTS transaction_salarydetails;

CREATE TABLE transaction_salarydetails
(
	SalaryDetailsID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	SalaryID					BIGINT,
	ProjectID					BIGINT,
	EmployeeID					BIGINT,
	Remarks 					TEXT,
	DailySalary					DOUBLE NOT NULL,
	Days 						INT NOT NULL,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(SalaryID) REFERENCES transaction_salary(SalaryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(EmployeeID) REFERENCES master_employee(EmployeeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALARYDETAILS_INDEX
ON transaction_salarydetails (SalaryDetailsID, SalaryID, ProjectID, EmployeeID);
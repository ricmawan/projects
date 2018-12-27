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
),
(
	5,
	'Tools',
	'fa fa-gear fa-2x',
	NULL,
	5
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
	'Kategori',
	'Master/Category/',
	NULL,
	0,
	2
),
(
	3,
	2,
	'Satuan',
	'Master/Unit/',
	NULL,
	0,
	3
),
(
	4,
	2,
	'Barang',
	'Master/Item/',
	NULL,
	0,
	4
),
(
	5,
	2,
	'Supplier',
	'Master/Supplier/',
	NULL,
	0,
	5
),
(
	6,
	2,
	'Pelanggan',
	'Master/Customer/',
	NULL,
	0,
	6
),
(
	7,
	2,
	'Upload Barang',
	'Master/ItemUpload/',
	NULL,
	0,
	7
),
(
	8,
	3,
	'Stok Awal',
	'Transaction/FirstStock/',
	NULL,
	0,
	1
),
(
	9,
	3,
	'Pembelian',
	'Transaction/Purchase/',
	NULL,
	0,
	2
),
(
	10,
	3,
	'Retur Pembelian',
	'Transaction/PurchaseReturn/',
	NULL,
	0,
	3
),
(
	11,
	3,
	'Penjualan',
	'Transaction/Sale/',
	NULL,
	0,
	4
),
(
	12,
	3,
	'Retur Penjualan',
	'Transaction/SaleReturn/',
	NULL,
	0,
	5
),
(
	13,
	3,
	'Mutasi Stok',
	'Transaction/StockMutation/',
	NULL,
	0,
	6
),
(
	14,
	3,
	'Penyesuaian Stok',
	'Transaction/StockAdjust/',
	NULL,
	0,
	7
),
(
	15,
	3,
	'D.O',
	'Transaction/Booking/',
	NULL,
	0,
	8
),
(
	16,
	3,
	'Pembayaran Piutang',
	'Transaction/Payment/',
	NULL,
	0,
	9
),
(
	17,
	3,
	'Pengambilan',
	'Transaction/PickUp/',
	NULL,
	0,
	10
),
(
	18,
	3,
	'Cetak Nota',
	'Transaction/Print/',
	NULL,
	0,
	11
),
(
	19,
	4,
	'Stok',
	'Report/Stock/',
	NULL,
	1,
	1
),
(
	20,
	4,
	'Detail Stok',
	'Report/StockDetails/',
	NULL,
	1,
	2
),
(
	21,
	4,
	'Penjualan',
	'Report/Sale/',
	NULL,
	1,
	3
),
(
	22,
	4,
	'Pembelian',
	'Report/Purchase/',
	NULL,
	1,
	4
),
(
	23,
	4,
	'Pendapatan',
	'Report/Income/',
	NULL,
	1,
	5
),
(
	24,
	4,
	'Omset Pelanggan',
	'Report/CustomerPurchase/',
	NULL,
	1,
	6
),
(
	25,
	4,
	'Harian',
	'Report/Daily/',
	NULL,
	1,
	7
),
(
	26,
	4,
	'Piutang',
	'Report/Credit/',
	NULL,
	1,
	8
),
(
	27,
	5,
	'Backup Data',
	'Tools/Backup/',
	NULL,
	0,
	1
),
(
	28,
	5,
	'Restore Data',
	'Tools/Restore/',
	NULL,
	0,
	2
),
(
	29,
	5,
	'Reset Data',
	'Tools/Reset/',
	NULL,
	0,
	3
),
(
	30,
	3,
	'Pembayaran Hutang',
	'Transaction/DebtPayment/',
	NULL,
	0,
	12
),
(
	31,
	4,
	'Hutang',
	'Report/Debt/',
	NULL,
	1,
	9
),
(
	32,
	4,
	'Barang Terlaris',
	'Report/TopSelling/',
	NULL,
	1,
	10
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
),
(
	2,
	'Kasir'
);DROP TABLE IF EXISTS master_user;

CREATE TABLE master_user
(
	UserID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserTypeID		SMALLINT NOT NULL,
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
	'Admin1',
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
),
(
	0,
	1,
	20,
	1,
	1
),
(
	0,
	1,
	21,
	1,
	1
),
(
	0,
	1,
	22,
	1,
	1
),
(
	0,
	1,
	23,
	1,
	1
),
(
	0,
	1,
	24,
	1,
	1
),
(
	0,
	1,
	25,
	1,
	1
),
(
	0,
	1,
	26,
	1,
	1
),
(
	0,
	1,
	27,
	1,
	1
),
(
	0,
	1,
	28,
	1,
	1
),
(
	0,
	1,
	29,
	1,
	1
),
(
	0,
	1,
	30,
	1,
	1
),
(
	0,
	1,
	31,
	1,
	1
),
(
	0,
	1,
	32,
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
(1, 'APPLICATION_PATH', '/Projects/POSAdmin/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\POSAdmin\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\POSAdmin\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\POSAdmin\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL),
(9, 'MOBILE_PATH', '/Projects/POSAdmin/Mobile/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(10, 'DESKTOP_PATH', '/Projects/POSAdmin/Desktop/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(11, 'MOBILE_HOME', 'http://192.168.1.21/Projects/POSAdmin/Mobile/Home.php', 'Location of the home for mobile view', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(12, 'FINISH_DEFAULT', '0', 'Default value for finish flag', 0, '2016-03-12 15:01:05', 'System', NULL, NULL);DROP TABLE IF EXISTS master_eventlog;

CREATE TABLE master_eventlog
(
	EventLogID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	EventLogDate 	DATETIME,
	Description 	TEXT,
	Source			VARCHAR(100),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL
)ENGINE=InnoDB;DROP TABLE IF EXISTS master_category;

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
ON master_category (CategoryID);DROP TABLE IF EXISTS master_unit;

CREATE TABLE master_unit
(
	UnitID 			SMALLINT PRIMARY KEY AUTO_INCREMENT,
	UnitName		VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX UNIT_INDEX
ON master_unit (UnitID);DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	SessionID			VARCHAR(100),
    CategoryID			INT,
	UnitID				SMALLINT,
	ItemName			VARCHAR(255) NOT NULL,
	ItemCode			VARCHAR(100),
	BuyPrice			DOUBLE,
	RetailPrice			DOUBLE,
    Price1				DOUBLE,
    Qty1				DOUBLE,
    Price2				DOUBLE,
    Qty2				DOUBLE,
	Weight				DOUBLE,
	MinimumStock		DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
    FOREIGN KEY(CategoryID) REFERENCES master_category(CategoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(UnitID) REFERENCES master_unit(UnitID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID, UnitID, CategoryID);DROP TABLE IF EXISTS master_itemdetails;

CREATE TABLE master_itemdetails
(
	ItemDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemID 				BIGINT,
	ItemDetailsCode		VARCHAR(100),
	UnitID				SMALLINT,
	ConversionQuantity	DOUBLE,
	BuyPrice			DOUBLE,
	RetailPrice			DOUBLE,
    Price1				DOUBLE,
    Qty1				DOUBLE,
    Price2				DOUBLE,
    Qty2				DOUBLE,
	Weight				DOUBLE,
	MinimumStock		DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
    FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(UnitID) REFERENCES master_unit(UnitID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEMDETAILS_INDEX
ON master_itemdetails (ItemDetailsID, ItemID, UnitID);DROP TABLE IF EXISTS master_supplier;

CREATE TABLE master_supplier
(
	SupplierID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
    SupplierCode		VARCHAR(100),
	SupplierName		VARCHAR(255) NOT NULL,
    Telephone			VARCHAR(100),
	Address				TEXT,
    City				VARCHAR(100),
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SUPPLIER_INDEX
ON master_supplier (SupplierID);DROP TABLE IF EXISTS master_customer;

CREATE TABLE master_customer
(
	CustomerID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
    CustomerCode		VARCHAR(100),
	CustomerName		VARCHAR(255) NOT NULL,
	Address				TEXT,
    Telephone			VARCHAR(100),
    City				VARCHAR(100),
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CUSTOMER_INDEX
ON master_customer (CustomerID);DROP TABLE IF EXISTS master_branch;

CREATE TABLE master_branch
(
	BranchID 			INT PRIMARY KEY AUTO_INCREMENT,
    BranchCode			VARCHAR(100),
	BranchName			VARCHAR(255) NOT NULL,
	Address				TEXT,
    Telephone			VARCHAR(100),
    City				VARCHAR(100),
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BRANCH_INDEX
ON master_branch (BranchID);

INSERT INTO master_branch
VALUES
(
	0,
	'TK',
	'Toko',
	'',
	'',
	'',
	'',
	NOW(),
	'Admin',
	NULL,
	NULL
),
(
	0,
	'GDG',
	'Gudang',
	'',
	'',
	'',
	'',
	NOW(),
	'Admin',
	NULL,
	NULL
);DROP TABLE IF EXISTS master_paymenttype;

CREATE TABLE master_paymenttype
(
	PaymentTypeID 		SMALLINT PRIMARY KEY AUTO_INCREMENT,
	PaymentTypeName		VARCHAR(100),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PAYMENTTYPE_INDEX
ON master_paymenttype (PaymentTypeID);

INSERT INTO master_paymenttype
VALUES
(
	1,
	'Tunai',
	NOW(),
	'Admin'
),
(
	2,
	'Tempo',
	NOW(),
	'Admin'
);DROP TABLE IF EXISTS transaction_purchase;

CREATE TABLE transaction_purchase
(
	PurchaseID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseNumber	VARCHAR(100) NULL,
	SupplierID 		BIGINT,
	TransactionDate DATETIME NOT NULL,
	PaymentTypeID	SMALLINT,
	Deadline		DATETIME,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(SupplierID) REFERENCES master_supplier(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASE_INDEX
ON transaction_purchase (PurchaseID, SupplierID);DROP TABLE IF EXISTS transaction_purchasedetails;

CREATE TABLE transaction_purchasedetails
(
	PurchaseDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	ItemDetailsID		BIGINT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	RetailPrice			DOUBLE,
	Price1				DOUBLE,
	Price2				DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(PurchaseID) REFERENCES transaction_purchase(PurchaseID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASEDETAILS_INDEX
ON transaction_purchasedetails (PurchaseDetailsID, PurchaseID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_purchasereturn;

CREATE TABLE transaction_purchasereturn
(
	PurchaseReturnID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseReturnNumber	VARCHAR(100),
	SupplierID 				BIGINT,
	TransactionDate 		DATETIME NOT NULL,
	Remarks					TEXT,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASERETURN_INDEX
ON transaction_purchasereturn (PurchaseReturnID, SupplierID);DROP TABLE IF EXISTS transaction_purchasereturndetails;

CREATE TABLE transaction_purchasereturndetails
(
	PurchaseReturnDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseReturnID			BIGINT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	BranchID					INT,
	Quantity					DOUBLE,
	BuyPrice					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(PurchaseReturnID) REFERENCES transaction_purchasereturn(PurchaseReturnID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASERETURNDETAILS_INDEX
ON transaction_purchasereturndetails (PurchaseReturnDetailsID, PurchaseReturnID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_sale;

CREATE TABLE transaction_sale
(
	SaleID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleNumber		VARCHAR(100),
	RetailFlag		BIT(1) NOT NULL,
	CustomerID 		BIGINT,
	TransactionDate DATETIME NOT NULL,
	PaymentTypeID	SMALLINT,
	Payment			DOUBLE,
	PrintCount		SMALLINT,
	PrintedDate		DATETIME,
    FinishFlag		BIT,
	Remarks			TEXT,
	Discount		DOUBLE,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(PaymentTypeID) REFERENCES master_paymenttype(PaymentTypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID, CustomerID, PaymentTypeID);DROP TABLE IF EXISTS transaction_saledetails;

CREATE TABLE transaction_saledetails
(
	SaleDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleID			BIGINT,
	ItemID 			BIGINT NOT NULL,
	ItemDetailsID	BIGINT NULL,
	BranchID		INT,
	Quantity		DOUBLE,
	BuyPrice		DOUBLE,
	SalePrice		DOUBLE,
	Discount		DOUBLE,
	PrintCount		SMALLINT,
	PrintedDate		DATETIME,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(SaleID) REFERENCES transaction_sale(SaleID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_saledetails (SaleDetailsID, SaleID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_salereturn;

CREATE TABLE transaction_salereturn
(
	SaleReturnID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleID	 		BIGINT,
	TransactionDate DATETIME NOT NULL,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALERETURN_INDEX
ON transaction_salereturn (SaleReturnID, SaleID);DROP TABLE IF EXISTS transaction_salereturndetails;

CREATE TABLE transaction_salereturndetails
(
	SaleReturnDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleReturnID				BIGINT,
	SaleDetailsID 				BIGINT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	BranchID					INT,
	Quantity					DOUBLE,
	BuyPrice					DOUBLE,
	SalePrice					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(SaleReturnID) REFERENCES transaction_salereturn(SaleReturnID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(SaleDetailsID) REFERENCES transaction_saledetails(SaleDetailsID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALERETURNDETAILS_INDEX
ON transaction_salereturndetails (SaleReturnDetailsID, SaleReturnID, SaleDetailsID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_stockmutation;

CREATE TABLE transaction_stockmutation
(
	StockMutationID		 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate				DATETIME,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKMUTATION_INDEX
ON transaction_stockmutation (StockMutationID);DROP TABLE IF EXISTS transaction_stockmutationdetails;

CREATE TABLE transaction_stockmutationdetails
(
	StockMutationDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	StockMutationID				BIGINT,
	SourceID					INT,
	DestinationID				INT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	Quantity					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(SourceID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(DestinationID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKMUTATIONDETAILS_INDEX
ON transaction_stockmutationdetails (StockMutationDetailsID, StockMutationID, ItemID, SourceID, DestinationID);DROP TABLE IF EXISTS transaction_stockadjust;

CREATE TABLE transaction_stockadjust
(
	StockAdjustID		 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate				DATETIME,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKADJUST_INDEX
ON transaction_stockadjust (StockAdjustID);DROP TABLE IF EXISTS transaction_stockadjustdetails;

CREATE TABLE transaction_stockadjustdetails
(
	StockAdjustDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	StockAdjustID				BIGINT,
	ItemID 						BIGINT NOT NULL,
	ItemDetailsID				BIGINT NULL,
	BranchID					INT,
	Quantity					DOUBLE,
	AdjustedQuantity			DOUBLE,
	BuyPrice					DOUBLE,
	SalePrice					DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(StockAdjustID) REFERENCES transaction_stockadjust(StockAdjustID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX STOCKADJUSTDETAILS_INDEX
ON transaction_stockadjustdetails (StockAdjustDetailsID, StockAdjustID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_firstbalance;

CREATE TABLE transaction_firstbalance
(
	FirstBalanceID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserID				BIGINT,
	TransactionDate		DATETIME NOT NULL,
	FirstBalanceAmount	DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(UserID) REFERENCES master_user(UserID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FIRSTBALANCE_INDEX
ON transaction_firstbalance (FirstBalanceID, UserID);DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingNumber		VARCHAR(100),
	RetailFlag			BIT(1) NOT NULL,
	CustomerID 			BIGINT,
	TransactionDate		DATETIME NOT NULL,
	PaymentTypeID		SMALLINT,
	Payment				DOUBLE,
	PrintCount			SMALLINT,
	PrintedDate			DATETIME,
    FinishFlag 			BIT,
	Remarks				TEXT,
	Discount			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(PaymentTypeID) REFERENCES master_paymenttype(PaymentTypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_booking (BookingID, CustomerID);DROP TABLE IF EXISTS transaction_bookingdetails;

CREATE TABLE transaction_bookingdetails
(
	BookingDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	ItemDetailsID		BIGINT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	BookingPrice		DOUBLE,
	Discount			DOUBLE,
	PrintCount			SMALLINT,
	PrintedDate			DATETIME,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(BookingID) REFERENCES transaction_booking(BookingID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BOOKINGDETAILS_INDEX
ON transaction_bookingdetails (BookingDetailsID, BookingID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_paymentdetails;

CREATE TABLE transaction_paymentdetails
(
	PaymentDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionID		BIGINT,
	TransactionType 	VARCHAR(1), /* S=Sale B=Booking P=Purchase */
    PaymentDate			DATETIME,
	Amount				DOUBLE,
    Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PAYMENTDETAILS_INDEX
ON transaction_paymentdetails (PaymentDetailsID, TransactionID);DROP TABLE IF EXISTS transaction_pick;

CREATE TABLE transaction_pick
(
	PickID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingID	 	BIGINT,
	TransactionDate DATETIME NOT NULL,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PICK_INDEX
ON transaction_pick (PickID, BookingID);DROP TABLE IF EXISTS transaction_pickdetails;

CREATE TABLE transaction_pickdetails
(
	PickDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PickID				BIGINT,
	BookingDetailsID 	BIGINT,
	ItemID 				BIGINT NOT NULL,
	ItemDetailsID		BIGINT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	SalePrice			DOUBLE,
	Discount			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(PickID) REFERENCES transaction_pick(PickID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BookingDetailsID) REFERENCES transaction_bookingdetails(BookingDetailsID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PICKDETAILS_INDEX
ON transaction_pickdetails (PickDetailsID, PickID, BookingDetailsID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_firststock;

CREATE TABLE transaction_firststock
(
	FirstStockID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	FirstStockNumber	VARCHAR(100) NULL,
	SupplierID 			BIGINT,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(SupplierID) REFERENCES master_supplier(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FIRSTSTOCK_INDEX
ON transaction_firststock (FirstStockID, SupplierID);DROP TABLE IF EXISTS transaction_firststockdetails;

CREATE TABLE transaction_firststockdetails
(
	FirstStockDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	FirstStockID			BIGINT,
	ItemID 					BIGINT NOT NULL,
	ItemDetailsID			BIGINT NULL,
	BranchID				INT,
	Quantity				DOUBLE,
	BuyPrice				DOUBLE,
	RetailPrice				DOUBLE,
	Price1					DOUBLE,
	Price2					DOUBLE,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL,
	FOREIGN KEY(FirstStockID) REFERENCES transaction_firststock(FirstStockID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FIRSTSTOCKDETAILS_INDEX
ON transaction_firststockdetails (FirstStockDetailsID, FirstStockID, ItemID, BranchID);DROP TABLE IF EXISTS backup_history;

CREATE TABLE backup_history
(
	BackupHistoryID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	BackupDate			DATE,
	FilePath			VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BACKUPHISTORY_INDEX
ON backup_history (BackupHistoryID);DROP TABLE IF EXISTS reset_history;

CREATE TABLE reset_history
(
	ResetHistoryID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	ResetDate			DATE,	
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX RESETHISTORY_INDEX
ON reset_history (ResetHistoryID);DROP TABLE IF EXISTS restore_history;

CREATE TABLE restore_history
(
	RestoreHistoryID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	RestoreDate			DATE,
	FilePath			VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX RESTOREHISTORY_INDEX
ON restore_history (RestoreHistoryID);DROP TABLE IF EXISTS transaction_printerlist;

CREATE TABLE transaction_printerlist
(
	PrinterListID			INT PRIMARY KEY AUTO_INCREMENT,
	IPAddress				VARCHAR(100),
	SharedPrinterName		VARCHAR(100),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PRINTERLIST_INDEX
ON transaction_printerlist (PrinterListID);

INSERT INTO transaction_printerlist
(
	IPAddress,
	SharedPrinterName,
	CreatedDate,
	CreatedBy
)
VALUES
(
	'192.168.1.100',
	'//192.168.1.100/EPSON2',
	'2018-12-08',
	'Admin1'
),
(
	'::1',
	'//192.168.1.2/EPSON',
	'2018-12-08',
	'Admin1'
);DROP TABLE IF EXISTS transaction_tokencode;

CREATE TABLE transaction_tokencode
(
	TokenCodeID				INT PRIMARY KEY AUTO_INCREMENT,
	TokenCode				VARCHAR(10),
	IsValid					BIT,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TOKENCODE_INDEX
ON transaction_tokencode (TokenCodeID);
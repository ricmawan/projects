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
	'fa fa-home fa-3x',
	'./Home.php',
	1
),
(
	2,
	'Master Data',
	'fa fa-book fa-3x',
	NULL,
	2
),
(
	3,
	'Transaksi',
	'fa fa-cart-plus fa-3x',
	NULL,
	3
),
(
	4,
	'Laporan',
	'fa fa-line-chart fa-3x',
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
	'Kategori',
	'Master/Category/',
	NULL,
	0,
	2
),
(
	3,
	2,
	'Barang',
	'Master/Item/',
	NULL,
	0,
	3
),
(
	4,
	2,
	'Supplier',
	'Master/Supplier/',
	NULL,
	0,
	4
),
(
	5,
	2,
	'Pelanggan',
	'Master/Customer/',
	NULL,
	0,
	5
),
(
	6,
	3,
	'Pembelian',
	'Transaction/Purchase/',
	NULL,
	0,
	1
),
(
	7,
	3,
	'Retur Pembelian',
	'Transaction/PurchaseReturn/',
	NULL,
	0,
	2
),
(
	8,
	3,
	'Penjualan',
	'Transaction/Sale/',
	NULL,
	0,
	3
),
(
	9,
	3,
	'Retur Penjualan',
	'Transaction/SaleReturn/',
	NULL,
	0,
	4
),
/*(
	10,
	3,
	'Mutasi Stok',
	'Transaction/StockMutation/',
	NULL,
	0,
	5
),*/
(
	11,
	3,
	'Adjust Stok',
	'Transaction/StockAdjust/',
	NULL,
	0,
	6
),
/*(
	12,
	3,
	'Pemesanan',
	'Transaction/Booking/',
	NULL,
	0,
	7
),*/
(
	13,
	4,
	'Stok',
	'Report/Stock/',
	NULL,
	1,
	1
),
(
	14,
	4,
	'Detail Stok',
	'Report/StockDetails/',
	NULL,
	1,
	2
),
(
	15,
	4,
	'Penjualan',
	'Report/Sale/',
	NULL,
	1,
	3
),
(
	16,
	4,
	'Pembelian',
	'Report/Purchase/',
	NULL,
	1,
	4
),
(
	17,
	4,
	'Pendapatan',
	'Report/Income/',
	NULL,
	1,
	5
),
(
	18,
	4,
	'Omset Pelanggan',
	'Report/CustomerPurchase/',
	NULL,
	1,
	6
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
/*(
	0,
	1,
	10,
	1,
	1
),*/
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
(1, 'APPLICATION_PATH', '/Projects/CiEnny/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\CiEnny\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\CiEnny\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\CiEnny\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL);DROP TABLE IF EXISTS master_eventlog;

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
	CategoryID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	CategoryCode		VARCHAR(100),
	CategoryName		VARCHAR(255) NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CATEGORY_INDEX
ON master_category (CategoryID);DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
    CategoryID			BIGINT,
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
    FOREIGN KEY(CategoryID) REFERENCES master_category(CategoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID, CategoryID);DROP TABLE IF EXISTS master_supplier;

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
);DROP TABLE IF EXISTS transaction_purchase;

CREATE TABLE transaction_purchase
(
	PurchaseID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PurchaseNumber	VARCHAR(100) NULL,
	SupplierID 		BIGINT,
	TransactionDate DATETIME NOT NULL,
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
	Payment			DOUBLE,
	Remarks			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID, CustomerID);DROP TABLE IF EXISTS transaction_saledetails;

CREATE TABLE transaction_saledetails
(
	SaleDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	SaleID			BIGINT,
	ItemID 			BIGINT NOT NULL,
	BranchID		INT,
	Quantity		DOUBLE,
	BuyPrice		DOUBLE,
	SalePrice		DOUBLE,
	Discount		DOUBLE,
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
ON transaction_salereturndetails (SaleReturnDetailsID, SaleReturnID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_stockmutation;

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
ON transaction_stockadjustdetails (StockAdjustDetailsID, StockAdjustID, ItemID, BranchID);DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingNumber		VARCHAR(100),
	RetailFlag			BIT(1) NOT NULL,
	CustomerID 			BIGINT,
	TransactionDate		DATETIME NOT NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(CustomerID) REFERENCES master_customer(CustomerID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_booking (BookingID, CustomerID);DROP TABLE IF EXISTS transaction_bookingdetails;

CREATE TABLE transaction_bookingdetails
(
	BookingDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	BookingID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	BranchID			INT,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	BookingPrice		DOUBLE,
	Discount			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(BookingID) REFERENCES transaction_booking(BookingID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ItemID) REFERENCES master_item(ItemID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(BranchID) REFERENCES master_branch(BranchID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_bookingdetails (BookingDetailsID, BookingID, ItemID, BranchID);
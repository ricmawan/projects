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
	'Barang',
	'Master/Item/',
	NULL,
	0,
	2
),
(
	3,
	2,
	'Mobil/Mesin',
	'Master/Machine/',
	NULL,
	0,
	4
),
(
	4,
	3,
	'Pembelian',
	'Transaction/Purchase/',
	NULL,
	0,
	1
),
(
	5,
	3,
	'Servis',
	'Transaction/Service/',
	NULL,
	0,
	2
),
(
	6,
	3,
	'BBM',
	'Transaction/Fuel/',
	NULL,
	0,
	4
),
(
	7,
	3,
	'Perpanjang STNK',
	'Transaction/LicenseExtension/',
	NULL,
	0,
	5
),
(
	8,
	4,
	'Stok',
	'Report/Stock/',
	NULL,
	1,
	1
),
(
	9,
	4,
	'Pemakaian Barang',
	'Report/ItemUsage/',
	NULL,
	1,
	3
),
(
	10,
	4,
	'Pembelian',
	'Report/Purchase/',
	NULL,
	1,
	4
),
(
	11,
	4,
	'Riwayat Servis',
	'Report/ServiceHistory/',
	NULL,
	1,
	6
),
(
	12,
	4,
	'Rasio BBM',
	'Report/FuelRatio/',
	NULL,
	1,
	7
),
(
	13,
	2,
	'Supplier',
	'Master/Supplier/',
	NULL,
	0,
	5
),
(
	14,
	3,
	'Penjualan',
	'Transaction/Sale/',
	NULL,
	0,
	3
),
(
	15,
	4,
	'Stok Barang Bekas',
	'Report/SecondStock/',
	NULL,
	1,
	2
),
(
	16,
	4,
	'Penjualan',
	'Report/Sale/',
	NULL,
	1,
	5
),
(
	17,
	4,
	'Rasio Barang',
	'Report/ItemRatio/',
	NULL,
	1,
	8
),
(
	18,
	2,
	'Stok Barang',
	'Master/ItemStock/',
	NULL,
	0,
	3
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);DROP TABLE IF EXISTS master_user;

CREATE TABLE master_user
(
	UserID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserName		VARCHAR(255) NOT NULL,
	UserLogin 		VARCHAR(100) NOT NULL,
	UserPassword 	VARCHAR(255) NOT NULL,
	IsActive		BOOLEAN,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO master_user
VALUES
(
	0,
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
(1, 'APPLICATION_PATH', '/Projects/SwissVehicle/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\SwissVehicle\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\SwissVehicle\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\SwissVehicle\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL);DROP TABLE IF EXISTS master_item;

DROP TABLE IF EXISTS master_item;

CREATE TABLE master_item
(
	ItemID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	ItemCode		VARCHAR(255),
	ItemName 		VARCHAR(255) NOT NULL,
	Price		 	DOUBLE NOT NULL,
	Remarks 		TEXT,
	IsSecond		BIT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ITEM_INDEX
ON master_item (ItemID);

CREATE TABLE master_machine
(
	MachineID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	MachineKind		VARCHAR(100) NOT NULL,
	MachineType 	VARCHAR(255) NOT NULL,
	MachineYear		INT NULL,
	MachineCode		VARCHAR(255) NULL,
	BrandName		VARCHAR(255) NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MACHINE_INDEX
ON master_machine (MachineID);DROP TABLE IF EXISTS master_supplier;

CREATE TABLE master_supplier
(
	SupplierID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierName	VARCHAR(255) NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address 		VARCHAR(255),
	City			VARCHAR(100),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SUPPLIER_INDEX
ON master_supplier (SupplierID);DROP TABLE IF EXISTS transaction_purchase;

CREATE TABLE transaction_purchase
(
	PurchaseID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	SupplierID		BIGINT,
	TransactionDate	DATE NOT NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY (SupplierID) REFERENCES master_supplier(SupplierID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASE_INDEX
ON transaction_purchase (PurchaseID, SupplierID);DROP TABLE IF EXISTS transaction_purchasedetails;

CREATE TABLE transaction_purchasedetails
(
    PurchaseDetailsID   BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    PurchaseID          BIGINT,
    ItemID		    	BIGINT NOT NULL,
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	Remarks 		    TEXT,
    CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ItemID) REFERENCES master_item(ItemID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (PurchaseID) REFERENCES transaction_purchase(PurchaseID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASEDETAILS_INDEX
ON transaction_purchasedetails (PurchaseDetailsID, PurchaseID, ItemID);DROP TABLE IF EXISTS transaction_service;

CREATE TABLE transaction_service
(
	ServiceID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate	DATE,
	MachineID		BIGINT NOT NULL,
	IsSelfWorkshop	BIT NOT NULL,
	WorkshopName	VARCHAR(255),
	Kilometer		DOUBLE,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY (MachineID) REFERENCES master_machine(MachineID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SERVICE_INDEX
ON transaction_service (ServiceID, MachineID);DROP TABLE IF EXISTS transaction_servicedetails;

CREATE TABLE transaction_servicedetails
(
    ServiceDetailsID    BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	ServiceID		    BIGINT NOT NULL,
	ItemID		 	    BIGINT NULL,
    ItemName            VARCHAR(255),
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	IsSecond			BIT,
	Remarks 		    TEXT,
	CreatedDate 	    DATETIME NOT NULL,
	CreatedBy 		    VARCHAR(255) NOT NULL,
	ModifiedDate 	    TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		    VARCHAR(255) NULL,
	FOREIGN KEY (ServiceID) REFERENCES transaction_service(ServiceID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SERVICEDETAILS_INDEX
ON transaction_servicedetails (ServiceDetailsID, ServiceID, ItemID);DROP TABLE IF EXISTS master_fueltype;

CREATE TABLE master_fueltype
(
	FuelTypeID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	FuelTypeName	VARCHAR(255),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO master_fueltype
(
	FuelTypeName,
	CreatedDate,
	CreatedBy
)
VALUES
(
	'Premium',
	NOW(),
	'Admin'
),
(
	'Pertamax',
	NOW(),
	'Admin'
),
(
	'Pertamax Plus',
	NOW(),
	'Admin'
),
(
	'Pertalite',
	NOW(),
	'Admin'
),
(
	'Solar',
	NOW(),
	'Admin'
),
(
	'Bio Solar',
	NOW(),
	'Admin'
),
(
	'Pertamina Dex',
	NOW(),
	'Admin'
);

CREATE UNIQUE INDEX FUELTYPE_INDEX
ON master_fueltype (FuelTypeID);DROP TABLE IF EXISTS transaction_licenseextension;

CREATE TABLE transaction_licenseextension
(
	LicenseExtensionID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate		DATE,
	MachineID			BIGINT NOT NULL,
	DueDate				DATE NOT NULL,
	IsExtended			BIT,
	Remarks 			TEXT,
	ExtensionDate		DATE,
	ExtensionCost		DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (MachineID) REFERENCES master_machine(MachineID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX LICENSEEXTENSION_INDEX
ON transaction_licenseextension (LicenseExtensionID, MachineID);DROP TABLE IF EXISTS transaction_fuel;

CREATE TABLE transaction_fuel
(
	FuelID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	FuelTypeID		BIGINT,
	TransactionDate	DATE,
	MachineID		BIGINT NOT NULL,
	Kilometer		DOUBLE NULL,
	Quantity		DOUBLE NOT NULL,
	Price			DOUBLE NOT NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY (MachineID) REFERENCES master_machine(MachineID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (FuelTypeID) REFERENCES master_fueltype(FuelTypeID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FUEL_INDEX
ON transaction_fuel (FuelID, FuelTypeID, MachineID);

DROP TABLE IF EXISTS transaction_sale;

CREATE TABLE transaction_sale
(
	SaleID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	CustomerName	VARCHAR(255),
	TransactionDate	DATE NOT NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID);

DROP TABLE IF EXISTS transaction_sale;

CREATE TABLE transaction_sale
(
	SaleID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	CustomerName	VARCHAR(255),
	TransactionDate	DATE NOT NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALE_INDEX
ON transaction_sale (SaleID);

DROP TABLE IF EXISTS transaction_saledetails;

CREATE TABLE transaction_saledetails
(
    SaleDetailsID		BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    SaleID          	BIGINT,
    ItemID		    	BIGINT NOT NULL,
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	Remarks 		    TEXT,
    CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ItemID) REFERENCES master_item(ItemID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (SaleID) REFERENCES transaction_sale(SaleID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALEDETAILS_INDEX
ON transaction_saledetails (SaleDetailsID, SaleID, ItemID);

DROP TABLE IF EXISTS transaction_seconditem;

CREATE TABLE transaction_seconditem
(
    SecondItemID		BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    ServiceID          	BIGINT,
    ItemID		    	BIGINT NOT NULL,
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	Remarks 		    TEXT,
    CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ItemID) REFERENCES master_item(ItemID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (ServiceID) REFERENCES transaction_service(ServiceID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SECONDITEM_INDEX
ON transaction_seconditem (SecondItemID, ServiceID, ItemID);

INSERT INTO `master_item` (`ItemID`, `ItemCode`, `ItemName`, `Price`, `Remarks`, `IsSecond`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, '42010001', 'BAN LUAR RING 15 BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(2, '42010002', 'BAN LUAR RING 16 BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(3, '42010003', 'BAN LUAR RING 13', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(4, '42010004', 'BAN LUAR RING 14', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(5, '42010005', 'BAN DALAM R-12', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(6, '42010006', 'BAN DALAM R-13', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(7, '42010007', 'BAN DALAM R-14', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(8, '42010008', 'BAN DALAM R-15', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(9, '42010009', 'BAN DALAM R-16', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(10, '42010010', 'BAN VULKANISIR R-12', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(11, '42010011', 'BAN VULKANISIR R-13', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(12, '42010012', 'BAN VULKANISIR R-14', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(13, '42010013', 'BAN VULKANISIR R-15', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(14, '42010014', 'BAN VULKANISIR R-16', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(15, '45010001', 'BAUT RODA KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(16, '45010002', 'BIS PEN SAKLAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(17, '45010003', 'BOX SEKERING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(18, '45010004', 'BUSHING PEDAL KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(19, '45010005', 'COPEL STIR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(20, '45010006', 'COPEL STIR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(21, '45010007', 'CROSS COPEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(22, '45010008', 'DRUG LAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(23, '45010009', 'FILTER OLI INTERCOOLER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(24, '45010010', 'FILTER OLI LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(25, '45010011', 'FILTER SOLAR ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(26, '45010012', 'FILTER SOLAR ATAS INTERCOOLER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(27, '45010013', 'FILTER SOLAR BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(28, '45010014', 'FILTER SOLAR CD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(29, '45010015', 'FILTER UDARA INTERCOOLER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(30, '45010016', 'FLASSER RITTING L/B', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(31, '45010017', 'GAGANG WIPER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(32, '45010018', 'HANDLE PINTU DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(33, '45010019', 'HANDLE PINTU LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(34, '45010020', 'KABEL GAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(35, '45010021', 'KABEL GAS LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(36, '45010022', 'KABEL PARAREL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(37, '45010023', 'KABEL SPEDOMETER PANJANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(38, '45010024', 'KABEL SPEDOMETER PANJANG LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(39, '45010025', 'KABEL SPEDOMETER PENDEK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(40, '45010026', 'KACA SPION', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(41, '45010027', 'KAMPAS KOPLING LAMA/ BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(42, '45010028', 'KAMPAS REM BELAKANG ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(43, '45010029', 'KAMPAS REM DEPAN ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(44, '45010030', 'KAMPAS REM DOBEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(45, '45010031', 'KARET BRIKE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(46, '45010032', 'KARET PEDAL KOPLING+ REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(47, '45010033', 'KARET PEDAL REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(48, '45010034', 'KARET PER + KOPLING REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(49, '45010035', 'KARET PER BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(50, '45010036', 'KARET PER GAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(51, '45010037', 'KARET PER PENDEK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(52, '45010038', 'KARET SHOCK BREAKER BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(53, '45010039', 'KARET SHOCK BREAKER DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(54, '45010040', 'KARET SHOCK BREAKER DEPAN ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(55, '45010041', 'KARET SHOCK BREAKER DEPAN BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(56, '45010042', 'KARPET', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(57, '45010043', 'KEPET RODA BELAKANG DOBEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(58, '45010044', 'KEPET RODA BELAKANG ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(59, '45010045', 'KEPET RODA DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(60, '45010046', 'KING PEN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(61, '45010047', 'KIP CENTRAL REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(62, '45010048', 'KIP REM BELAKANG D/E', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(63, '45010049', 'KIP REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(64, '45010050', 'KIP REM DEPAN ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(65, '45010051', 'KLAKSON 24 V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(66, '45010052', 'KUNCI PINTU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(67, '45010053', 'LAKER GANDUL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(68, '45010054', 'LAKER RODA BELAKANG DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(69, '45010055', 'LAKER RODA BELAKANG LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(70, '45010056', 'LAKER RODA DEPAN DALAM ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(71, '45010057', 'LAKER RODA DEPAN LUAR DOBEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(72, '45010058', 'LAKER RODA DEPAN LUAR ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(73, '45010059', 'LAKER RODA GILA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(74, '45010060', 'LAMPU SEAL BEAM DOUBLE KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(75, '45010061', 'MASTER KOPLING ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(76, '45010062', 'MATAHARI BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(77, '45010063', 'MATAHARI LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(78, '45010064', 'MIKA LAMPU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(79, '45010065', 'MUR BAUT KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(80, '45010066', 'MUR RODA KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(81, '45010067', 'PER BELAKANG 1 ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(82, '45010068', 'PER DEPAN 2 LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(83, '45010069', 'PER DEPAN 3 LAMA / BELAKANG ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(84, '45010070', 'PER DEPAN 4 LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(85, '45010071', 'PER DEPAN 5 LAMA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(86, '45010072', 'PER KAMPAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(87, '45010073', 'PER PEDAL KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(88, '45010074', 'PLAT KOPLING NEW', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(89, '45010075', 'PUTARAN KACA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(90, '45010076', 'REGULATOR PINTU KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(91, '45010077', 'REGULATOR PINTU KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(92, '45010078', 'REP KOPLING ATAS LAMA/ BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(93, '45010079', 'REP KOPLING BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(94, '45010080', 'REP KOPLING BAWAH LAMA / BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(95, '45010081', 'RING VELG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(96, '45010082', 'RING VELG 15', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(97, '45010083', 'SEAL BEAM 24 V  E/D', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(98, '45010084', 'SEAL GARDAN/ PINION', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(99, '45010085', 'SEAL KER AS BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(100, '45010086', 'SEAL KER AS DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(101, '45010087', 'SEAL RODA BELAKANG DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(102, '45010088', 'SEAL RODA BELAKANG LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(103, '45010089', 'SEAL RODA DEPAN DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(104, '45010090', 'SEAL RODA DEPAN DOBEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(105, '45010091', 'SELANG BLOWER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(106, '45010092', 'SELANG BLOWER BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(107, '45010093', 'SELANG GAJAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(108, '45010094', 'SELANG RADIATOR ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(109, '45010095', 'SELANG RADIATOR BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(110, '45010096', 'SHOCK BREAKER BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(111, '45010097', 'SHOCK BREAKER DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(112, '45010098', 'SLIWER R15', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(113, '45010099', 'SPEDOMETER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(114, '45010100', 'STOP LAMPU TANPA MIKA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(115, '45010101', 'SWITCH KLAKSON', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(116, '45010102', 'SWITCH REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(117, '45010103', 'TIE ROD 555', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(118, '45010104', 'TORN BAUT BARU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(119, '45010105', 'TORN BAUT BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(120, '45010106', 'VENTBELT', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(121, '45010107', 'VENTBELT ALTENATOR INTERCOOLER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(122, '45010108', 'WATER PUMP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(123, '45010109', 'WIPER BOSH (1SET=2PCS)', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(124, '45010110', 'WIPER BOSH 18', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(125, '45010111', 'YOKE PALAGE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(126, '45020001', 'AS TEMPLAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(127, '45020002', 'BALLJOINT ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(128, '45020003', 'BALLJOINT BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(129, '45020004', 'BAUT RODA KECIL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(130, '45020005', 'BAUT TAP BESAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(131, '45020006', 'BAUT TAP OLI K', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(132, '45020007', 'BELL CRANK ASSY', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(133, '45020008', 'BISS PEDAL KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(134, '45020009', 'BISS STIR KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(135, '45020010', 'BISS STIRR KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(136, '45020011', 'BOX SEKERING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(137, '45020012', 'BOX SEKERING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(138, '45020013', 'BUSI PEMANAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(139, '45020014', 'BUSI PEMANAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(140, '45020015', 'CAKEPAN NOKEN AS NO 5', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(141, '45020016', 'CAP NO 3 / 1', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(142, '45020017', 'CAPIT URANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(143, '45020018', 'COM LAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(144, '45020019', 'CROSSCOUPLE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(145, '45020020', 'DIS BRIKE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(146, '45020021', 'DOOR LOCK KANAN KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(147, '45020022', 'DRUP LAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(148, '45020023', 'FILTER OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(149, '45020024', 'FILTER SOLAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(150, '45020025', 'FILTER UDARA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(151, '45020026', 'FLANGE YUKE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(152, '45020027', 'FLASSER RITTING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(153, '45020028', 'GANTUNGAN BAN SEREP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(154, '45020029', 'GIGI SPEDOMETER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(155, '45020030', 'HANDLE PINTU DALAM L300', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(156, '45020031', 'HANDREM PANJANG KANAN / KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(157, '45020032', 'IDDLE ARM ASSY / TANGAN GARENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(158, '45020033', 'IDLE ARM ASSY', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(159, '45020034', 'KABEL GAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(160, '45020035', 'KABEL HAND REM KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(161, '45020036', 'KABEL HAND REM KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(162, '45020037', 'KABEL HAND REM PANJANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(163, '45020038', 'KABEL HAND REM PENDEK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(164, '45020039', 'KABEL HAND REM PENDEK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(165, '45020040', 'KABEL KUNCI KONTAK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(166, '45020041', 'KABEL SPEDO', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(167, '45020042', 'KABEL VERSNELENG PANJANG / PENDEK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(168, '45020043', 'KACA SPION KANAN/ KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(169, '45020044', 'KAMPAS KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(170, '45020045', 'KAMPAS REM BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(171, '45020046', 'KAMPAS REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(172, '45020047', 'KANCINGAN KAMPAS REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(173, '45020048', 'KARET BISS JEMBER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(174, '45020049', 'KARET BRIKE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(175, '45020050', 'KARET PANGKON KNALPOT', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(176, '45020051', 'KARET PEDAL REM + KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(177, '45020052', 'KARET PER BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(178, '45020053', 'KARET PER PANJANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(179, '45020054', 'KARET PER PENDEK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(180, '45020055', 'KARET SPIRAL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(181, '45020056', 'KARET STABILIZER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(182, '45020057', 'KARET SUSU BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(183, '45020058', 'KARET SUSU DEPAN KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(184, '45020059', 'KARET SUSU DPN KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(185, '45020060', 'KEPET RODA BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(186, '45020061', 'KIP KOPLING ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(187, '45020062', 'KIP KOPLING BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(188, '45020063', 'KIP REM BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(189, '45020064', 'KIP REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(190, '45020065', 'KIPAS RADIATOR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(191, '45020066', 'KLAKSON 12V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(192, '45020067', 'KLEM ACCU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(193, '45020068', 'KLEM TUTUP FILTER ANGIN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(194, '45020069', 'LAKER AS DIRECT', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(195, '45020070', 'LAKER AS PRISS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(196, '45020071', 'LAKER RODA BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(197, '45020072', 'LAKER RODA BELAKANG DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(198, '45020073', 'LAKER RODA BELAKANG LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(199, '45020074', 'LAKER RODA DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(200, '45020075', 'LAKER RODA DEPAN DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(201, '45020076', 'LAKER RODA GILA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(202, '45020077', 'LAKER SPEDOMETER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(203, '45020078', 'LAKER VERSNELENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(204, '45020079', 'LAMPU BEMPER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(205, '45020080', 'LAMPU BOX KODOK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(206, '45020081', 'LAMPU BOX KOTAK', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(207, '45020082', 'LAMPU KOTA KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(208, '45020083', 'LAMPU KOTA KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(209, '45020084', 'LAMPU RITTING BEMPER KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(210, '45020085', 'LAMPU SEAL BEAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(211, '45020086', 'LAMPU STOP BELAKANG LENGKAP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(212, '45020087', 'LASER RITTING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(213, '45020088', 'MASTER KOPLING BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(214, '45020089', 'MATAHARI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(215, '45020090', 'MIKA BEMPER KANAN/KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(216, '45020091', 'MIKA LAMPU BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(217, '45020092', 'MIKA LAMPU RITTING KOTA KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(218, '45020093', 'MIKA LAMPU RITTING KOTA KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(219, '45020094', 'MUR RODA K', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(220, '45020095', 'PACKING DIESEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(221, '45020096', 'PACKING MANIPOLD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(222, '45020097', 'PACKING VERSNELENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(223, '45020098', 'PANGKON MESIN / VERSNELENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(224, '45020099', 'PUTARAN KACA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(225, '45020100', 'REGULATOR WINDOW KANAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(226, '45020101', 'REGULATOR WINDOW KIRI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(227, '45020102', 'RELAY 24V KAKI 5CD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(228, '45020103', 'REP KIP REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(229, '45020104', 'RING SELANG BLOWER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(230, '45020105', 'RING SHOCK BELAKANG DEPAN TEBAL/TIPIS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(231, '45020106', 'RING SOLAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(232, '45020107', 'SEAL 1/2 BLN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(233, '45020108', 'SEAL BALANCE BESAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(234, '45020109', 'SEAL BALANCE KECIL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(235, '45020110', 'SEAL BEAM 12 V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(236, '45020111', 'SEAL KEUR AS BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(237, '45020112', 'SEAL KEUR AS DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(238, '45020113', 'SEAL NOKEN AS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(239, '45020114', 'SEAL PINION/ GARDAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(240, '45020115', 'SEAL RODA BELAKANG DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(241, '45020116', 'SEAL RODA BELAKANG LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(242, '45020117', 'SEAL RODA DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(243, '45020118', 'SEAL SAMPING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(244, '45020119', 'SEAL VERSNELENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(245, '45020120', 'SEAL VERSNELENG BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(246, '45020121', 'SEAL VERSNELENG DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(247, '45020122', 'SEAL VERSNELENG SAMPING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(248, '45020123', 'SELANG BLOWER BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(249, '45020124', 'SELANG HAWA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(250, '45020125', 'SELANG OLI COOLER PENDEK/PANJANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(251, '45020126', 'SELANG RADIATOR ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(252, '45020127', 'SELANG RADIATOR BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(253, '45020128', 'SHOCK BREAKER BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(254, '45020129', 'SHOCK BREAKER DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(255, '45020130', 'SLAVE YUKE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(256, '45020131', 'SLEAVE BALANCE BESAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(257, '45020132', 'SLEAVE BALANCE KECIL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(258, '45020133', 'STANG GARENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(259, '45020134', 'STANG WIPER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(260, '45020135', 'STOP LAMPU ASSY', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(261, '45020136', 'STOP LAMPU BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(262, '45020137', 'SWITCH KLAKSON', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(263, '45020138', 'SWITCH OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(264, '45020139', 'SWITCH REM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(265, '45020140', 'SWITCH TEMPERATUR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(266, '45020141', 'TALANG AIR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(267, '45020142', 'TANKI CADANGAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(268, '45020143', 'TENSIONER BESAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(269, '45020144', 'TENSIONER KECIL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(270, '45020145', 'TIE ROD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(271, '45020146', 'TIMING BELT BESAR NEW', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(272, '45020147', 'TIMING BELT BESAR OLD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(273, '45020148', 'TIMING BELT KECIL  OLD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(274, '45020149', 'TIMING BELT KECIL NEW', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(275, '45020150', 'TUTUP RADIATOR TABUNG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(276, '45020151', 'TUTUP TANKI SOLAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(277, '45020152', 'UTILUX', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(278, '45020153', 'VELG RING 14', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(279, '45020154', 'VENT BELT POWER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(280, '45020155', 'VENTBELT', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(281, '45020156', 'VENTBELT ALTENATOR NEW', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(282, '45020157', 'WATER PUMP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(283, '45020158', 'WIPER ARM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(284, '45020159', 'WIPER BOSH L300', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(285, '45020160', 'WIPER L300 SET', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(286, '45020161', 'WIPER LINK ASSY', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(287, '45030001', 'BALLJOINT BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(288, '45030002', 'BUSHING JEMB ATAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(289, '45030003', 'DRUP LAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(290, '45030004', 'FILTER OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(291, '45030005', 'FILTER UDARA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(292, '45030006', 'FILTER UDARA KUDA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(293, '45030007', 'KABEL SPEDOMETER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(294, '45030008', 'KAMPAS REM BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(295, '45030009', 'KAMPAS REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(296, '45030010', 'LAKER RODA DEPAN DALAM', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(297, '45030011', 'LAKER RODA DEPAN LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(298, '45030012', 'MATAHARI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(299, '45030013', 'PLAT KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(300, '45030014', 'REPARKIT KOPLING BAWAH', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(301, '45030015', 'SEAL RODA DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(302, '45030016', 'SWITCH HANDLE UP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(303, '45030017', 'SWITCH OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(304, '45030018', 'TUTUP RADIATOR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(305, '45040001', 'CONDENSOR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(306, '45040002', 'FILTER OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(307, '45040003', 'FILTER SOLAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(308, '45040004', 'FILTER UDARA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(309, '45040005', 'KLEP EX', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(310, '45040006', 'LAKER RODA DEPAN LUAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(311, '45040007', 'PLATINA ROTAX', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(312, '45040008', 'SHOCK BREAKER DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(313, '45040009', 'TIE ROD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(314, '45050001', 'BOLP CUMI 24V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(315, '45050002', 'BUSI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(316, '45050003', 'FILTER OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(317, '45050004', 'FILTER UDARA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(318, '45050005', 'KAMPAS REM BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(319, '45050006', 'KAMPAS REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(320, '45050007', 'SHOCK BREAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(321, '45060001', 'BOX SEKERING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(322, '45060002', 'BUSHING STIR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(323, '45060003', 'BUSI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(324, '45060004', 'COMLAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(325, '45060005', 'CROSS COPEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(326, '45060006', 'DRUP LAKER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(327, '45060007', 'FILTER BENSIN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(328, '45060008', 'FILTER OLI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(329, '45060009', 'FILTER UDARA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(330, '45060010', 'KABEL BUSI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(331, '45060011', 'KABEL GAS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(332, '45060012', 'KABEL KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(333, '45060013', 'KABEL SPEDO', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(334, '45060014', 'KAMPAS KOPLING', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(335, '45060015', 'KAMPAS REM BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(336, '45060016', 'KAMPAS REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(337, '45060017', 'KARET BISS STIR T120', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(338, '45060018', 'KARET PER BESAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(339, '45060019', 'KARET PER KECIL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(340, '45060020', 'KEEP REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(341, '45060021', 'KEPET RODA BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(342, '45060022', 'LAKER RODA BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(343, '45060023', 'LAKER RODA DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(344, '45060024', 'LAMPU DEPAN 12V NETRAL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(345, '45060025', 'LAMPU HOLOGEN TUSUK T120', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(346, '45060026', 'MATAHARI', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(347, '45060027', 'PLATINA ROTAX', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(348, '45060028', 'PUTARAN KACA', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(349, '45060029', 'RELLAY 12V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(350, '45060030', 'REPARKIT REM DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(351, '45060031', 'ROTAX', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(352, '45060032', 'SAKLAR LAMPU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(353, '45060033', 'SEAL KER AS DEPAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(354, '45060034', 'SEAL NOKEN AS', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(355, '45060035', 'SEAL RODA BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(356, '45060036', 'SEAL RODA BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(357, '45060037', 'SELANG RADIATOR ATAS ''6''', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(358, '45060038', 'SELANG RADIATOR BAWAH ''6''', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(359, '45060039', 'SHOCK BREAKER BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(360, '45060040', 'SHOCK BREAKER DEPAN ''6'' BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(361, '45060041', 'STOP LAMPU BELAKANG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(362, '45060042', 'TENSIONAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(363, '45060043', 'TIE ROAD', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(364, '45060044', 'TIMING BELT', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(365, '45060045', 'VAN BELT ALTERNATOR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(366, '45060046', 'WATER PUMP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(367, '45070001', 'AIR ACCU', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(368, '45070002', 'AIR ACCU POWER', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(369, '45070003', 'BAN DALAM R-13', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(370, '45070004', 'BAN DALAM R-14', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(371, '45070005', 'BAN DALAM R-15', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(372, '45070006', 'BAN VULKANISIR R-13', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(373, '45070007', 'BAN VULKANISIR R-14', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(374, '45070008', 'BAN VULKANISIR R-15', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(375, '45070009', 'BAN VULKANISIR R-16', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(376, '45070010', 'BAUT K', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(377, '45070011', 'BOLP 12 V BESAR DOUBLE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(378, '45070012', 'BOLP 12 V BESAR ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(379, '45070013', 'BOLP 12 V KECIL DOUBLE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(380, '45070014', 'BOLP 12 V KECIL ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(381, '45070015', 'BOLP 24 V BESAR DOUBLE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(382, '45070016', 'BOLP 24 V BESAR ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(383, '45070017', 'BOLP 24 V KECIL DOUBLE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(384, '45070018', 'BOLP 24 V KECIL ENGKEL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(385, '45070019', 'BOLP CUMI 12V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(386, '45070020', 'BOLP CUMI 24V', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(387, '45070021', 'KANEBO', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(388, '45070022', 'MINYAK REM STP BESAT', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(389, '45070023', 'MUR BAUT+RING UK.10', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(390, '45070024', 'MUR BAUT+RING UK.12', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(391, '45070025', 'OLI MIRACLE', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(392, '45070026', 'OLI POWER STERING B', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(393, '45070027', 'OLI POWER STERING K', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(394, '45070028', 'OLI REM BESAR', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(395, '45070029', 'OLI REM KECIL', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(396, '45070030', 'OLI SILICON', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(397, '45070031', 'OLI SILICON RED', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(398, '45070032', 'RILLAY KAKI 4', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(399, '45070033', 'RILLAY KAKI 5', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(400, '45070034', 'SEGITIGA PENGAMAN', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(401, '45070035', 'SEKERING GEPENG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(402, '45070036', 'SEKERING TABUNG', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(403, '45070037', 'TRUST M 843 (OLI GENSET)', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(404, '45070038', 'TRUST M 898', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', ''),
(405, '45070039', 'TRUST M RCP', 0, '', b'0', '2016-09-01 00:00:00', 'Admin', '2016-09-29 15:06:49', '');

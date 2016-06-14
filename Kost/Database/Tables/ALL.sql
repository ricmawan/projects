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
	'fa fa-home fa-2',
	'./Home.php',
	1
),
(
	2,
	'Master',
	'fa fa-book fa-2',
	NULL,
	2
),
(
	3,
	'Operasional',
	'fa fa-cart-plus fa-2',
	NULL,
	3
),
(
	4,
	'Laporan',
	'fa fa-line-chart fa-2',
	NULL,
	4
);

CREATE UNIQUE INDEX GROUPMENU_INDEX
ON master_groupmenu (GroupMenuID);
DROP TABLE IF EXISTS master_menu;

CREATE TABLE master_menu
(
	MenuID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
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
	'Kamar',
	'Master/Room/',
	NULL,
	0,
	2
),
(
	3,
	2,
	'Inventaris',
	'Master/Inventory/',
	NULL,
	0,
	3
),
(
	4,
	3,
	'Biaya',
	'Transaction/Cost/',
	NULL,
	0,
	1
),
/*(
	5,
	3,
	'Daftar Inventaris',
	'Transaction/Inventory/',
	NULL,
	0,
	2
),*/
(
	6,
	3,
	'Pembelian Inventaris',
	'Transaction/IncomingInventory/',
	NULL,
	0,
	3
),
(
	7,
	3,
	'Pemakaian Inventaris',
	'Transaction/OutgoingInventory/',
	NULL,
	0,
	4
),
(
	8,
	4,
	'Laba Rugi',
	'Report/ProfitAndLoss/',
	NULL,
	0,
	1
),
(
	9,
	4,
	'Inventaris',
	'Report/Inventory/',
	NULL,
	0,
	2
),
(
	10,
	4,
	'Pemakaian Kamar',
	'Report/RoomUsage/',
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
/*(
	0,
	1,
	5,
	1,
	1
),*/
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
(1, 'APPLICATION_PATH', '/Projects/Kost/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\Kost\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL);DROP TABLE IF EXISTS master_status;

CREATE TABLE master_status
(
	StatusID 		INT PRIMARY KEY AUTO_INCREMENT,
	StatusName 		VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

INSERT INTO `master_status` (StatusID, StatusName)
VALUES
	(1, 'available'),
	(2, 'booked'),
	(3, 'occupied');DROP TABLE IF EXISTS master_room;

CREATE TABLE master_room
(
	RoomID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	RoomNumber 		VARCHAR(255) NOT NULL,
	StatusID	 	INT NOT NULL,
	DailyRate		DOUBLE,
	HourlyRate		DOUBLE,
	RoomInfo		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(StatusID) REFERENCES master_status(StatusID)
)ENGINE=InnoDB;


CREATE UNIQUE INDEX ROOM_INDEX
ON master_room (RoomID);

INSERT INTO master_room
VALUES
(
	1,
	101,
	1,
	150000,
	15000,
	'test',
	NOW(),
	'Admin',
	null,
	null
),
(
	2,
	102,
	1,
	150000,
	15000,
	'test',
	NOW(),
	'Admin',
	null,
	null
),(
	3,
	103,
	1,
	150000,
	15000,
	'test',
	NOW(),
	'Admin',
	null,
	null
);DROP TABLE IF EXISTS master_inventory;

CREATE TABLE master_inventory
(
	InventoryID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	InventoryName	VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX INVENTORY_INDEX
ON master_inventory (InventoryID);DROP TABLE IF EXISTS transaction_checkin;

CREATE TABLE transaction_checkin
(
	CheckInID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	RoomID				BIGINT,
	TransactionDate		DATETIME NOT NULL,
	RateType			VARCHAR(100) NOT NULL,
	StartDate			DATETIME,
	EndDate				DATETIME,
	CustomerName		VARCHAR(255),
	Phone				VARCHAR(100),
	Address				TEXT,
	BirthDate			DATE,
	Remarks				TEXT,
	DownPaymentAmount	DOUBLE,
	DownPaymentDate		DATE,
	PaymentAmount 		DOUBLE,
	PaymentDate			DOUBLE,
	BookingFlag			BIT,
	CheckOutFlag		BIT,
	DailyRate			DOUBLE,
	HourlyRate			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(RoomID) REFERENCES master_room(RoomID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX CHECKIN_INDEX
ON transaction_checkin (CheckInID);DROP TABLE IF EXISTS transaction_checkout;

CREATE TABLE transaction_checkout
(
	CheckOutID	 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CheckInID		BIGINT,
	TransactionDate	DATETIME NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(CheckInID) REFERENCES transaction_checkin(CheckInID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX CHECKOUTID_INDEX
ON transaction_checkout (CheckOutID, CheckInID);DROP TABLE IF EXISTS transaction_booking;

CREATE TABLE transaction_booking
(
	BookingID	 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	RoomID				BIGINT,
	TransactionDate		DATETIME NOT NULL,
	RateType			VARCHAR(100) NOT NULL,
	StartDate			DATETIME,
	EndDate				DATETIME,
	CustomerName		VARCHAR(255),
	Phone				VARCHAR(100),
	Address				TEXT,
	BirthDate			DATE,
	Remarks				TEXT,
	DownPaymentAmount	DOUBLE,
	DownPaymentDate		DATE,
	CheckInFlag			BIT,
	DailyRate			DOUBLE,
	HourlyRate			DOUBLE,
	IsCancelled			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX BOOKING_INDEX
ON transaction_booking (BookingID, RoomID);DROP TABLE IF EXISTS transaction_operational;

CREATE TABLE transaction_operational
(
	OperationalID 	BIGINT PRIMARY KEY AUTO_INCREMENT,	
	TransactionDate	DATETIME,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OPERATIONAL_INDEX
ON transaction_operational (OperationalID);DROP TABLE IF EXISTS transaction_operationaldetails;

CREATE TABLE transaction_operationaldetails
(
	OperationalDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OperationalID			BIGINT,
	OperationalType			VARCHAR(100),
	Amount					DOUBLE,
	Remarks					VARCHAR(255),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL,
	FOREIGN KEY(OperationalID) REFERENCES transaction_operational(OperationalID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OPERATIONALDETAILS_INDEX
ON transaction_operationaldetails (OperationalDetailsID, OperationalID);DROP TABLE IF EXISTS transaction_incominginventory;

CREATE TABLE transaction_incominginventory
(
	IncomingInventoryID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate			DATETIME NOT NULL,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX INCOMINGINVENTORY_INDEX
ON transaction_incominginventory (IncomingInventoryID);DROP TABLE IF EXISTS transaction_incominginventorydetails;

CREATE TABLE transaction_incominginventorydetails
(
	IncomingInventoryDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingInventoryID			BIGINT,
	InventoryID					BIGINT,
	Quantity					DOUBLE,
	Price						DOUBLE,
	Remarks						VARCHAR(255),
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 					VARCHAR(255) NULL,
	FOREIGN KEY(IncomingInventoryID) REFERENCES transaction_incominginventory(IncomingInventoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(InventoryID) REFERENCES master_inventory(InventoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX INCOMINGINVENTORYDETAILS_INDEX
ON transaction_incominginventorydetails (IncomingInventoryDetailsID, IncomingInventoryID, InventoryID);DROP TABLE IF EXISTS transaction_outgoinginventory;

CREATE TABLE transaction_outgoinginventory
(
	OutgoingInventoryID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate			DATETIME NOT NULL,
	Remarks					VARCHAR(255),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OUTGOINGINVENTORY_INDEX
ON transaction_outgoinginventory (OutgoingInventoryID);DROP TABLE IF EXISTS transaction_outgoinginventorydetails;

CREATE TABLE transaction_outgoinginventorydetails
(
	OutgoingInventoryDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingInventoryID			BIGINT,
	InventoryID					BIGINT,
	Quantity					DOUBLE,
	Remarks						VARCHAR(255),
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 					VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingInventoryID) REFERENCES transaction_outgoinginventory(OutgoingInventoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(InventoryID) REFERENCES master_inventory(InventoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OUTGOINGINVENTORYDETAILS_INDEX
ON transaction_outgoinginventorydetails (OutgoingInventoryDetailsID, OutgoingInventoryID, InventoryID);
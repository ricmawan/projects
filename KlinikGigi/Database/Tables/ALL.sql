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
	'Jenis Periksa',
	'Master/Examination/',
	NULL,
	0,
	2
),
(
	3,
	2,
	'Pasien',
	'Master/Patient/',
	NULL,
	0,
	3
),
(
	4,
	3,
	'Pendaftaran',
	'Transaction/Registration/',
	NULL,
	0,
	1
),
(
	5,
	3,
	'Tindakan',
	'Transaction/Medication/',
	NULL,
	0,
	2
),
(
	6,
	3,
	'Pembayaran',
	'Transaction/Payment/',
	NULL,
	0,
	3
),
(
	7,
	4,
	'Gaji Dokter',
	'Report/Salary/',
	NULL,
	1,
	1
),
(
	8,
	4,
	'Transaksi Bulanan',
	'Report/MonthlyIncome/',
	NULL,
	1,
	2
),
(
	9,
	4,
	'Rekam Medis',
	'Report/MedicalRecord/',
	NULL,
	1,
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
),
(
	2,
	'Dokter'
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
);

CREATE UNIQUE INDEX ROLE_INDEX
ON master_role (RoleID, UserID, MenuID);DROP TABLE IF EXISTS master_examination;

CREATE TABLE master_examination
(
	ExaminationID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ExaminationName	VARCHAR(255) NOT NULL,
	Price			DOUBLE NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_examination` (`ExaminationID`, `ExaminationName`, `Price`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Cabut Gigi', 200000, '2016-03-12 17:02:18', 'Admin', NULL, NULL),
(2, 'Tambal Gigi', 150000, '2016-03-12 17:02:36', 'Admin', NULL, NULL),
(3, 'Operasi Gigi', 1000000, '2016-03-12 17:02:44', 'Admin', NULL, NULL),
(4, 'Kontrol Rutin', 50000, '2016-03-12 17:02:53', 'Admin', NULL, NULL),
(5, 'Pasang Kawat', 3500000, '2016-03-12 17:03:06', 'Admin', NULL, NULL);

CREATE UNIQUE INDEX EXAMINATION_INDEX
ON master_examination (ExaminationID);DROP TABLE IF EXISTS master_patient;

CREATE TABLE master_patient
(
	PatientID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PatientNumber	VARCHAR(100) NOT NULL,
	PatientName		VARCHAR(255) NOT NULL,
	BirthDate		DATE NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address 		TEXT,
	City			VARCHAR(100),
	Allergy			TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PATIENT_INDEX
ON master_patient (PatientID);DROP TABLE IF EXISTS transaction_medication;

CREATE TABLE transaction_medication
(
	MedicationID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	PatientID			BIGINT NOT NULL,
	OrderNumber			VARCHAR(100) NULL,
	TransactionDate 	DATETIME NOT NULL,
	Remarks				TEXT,
	IsDone				BIT,
	IsCancelled			BIT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(PatientID) REFERENCES master_patient(PatientID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MEDICATION_INDEX
ON transaction_medication (MedicationID, PatientID);DROP TABLE IF EXISTS transaction_medicationdetails;

CREATE TABLE transaction_medicationdetails
(
	MedicationDetailsID BIGINT PRIMARY KEY AUTO_INCREMENT,
	DoctorID			BIGINT NOT NULL,
	ExaminationID		BIGINT NOT NULL,
	MedicationID		BIGINT NOT NULL,
	Remarks				TEXT,
	Price				DOUBLE,
	Quantity			DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(DoctorID) REFERENCES master_user(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(ExaminationID) REFERENCES master_examination(ExaminationID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(MedicationID) REFERENCES transaction_medication(MedicationID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MEDICATIONDETAILS_INDEX
ON transaction_medicationdetails (MedicationDetailsID, DoctorID, ExaminationID, MedicationID);DROP TABLE IF EXISTS master_parameter;

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
(1, 'APPLICATION_PATH', '/Projects/KlinikGigi/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\KlinikGigi\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\KlinikGigi\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\KlinikGigi\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL);DROP TABLE IF EXISTS transaction_invoicenumber;

CREATE TABLE transaction_invoicenumber
(
	InvoiceNumberID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate		DATE,
	OrderNumber			VARCHAR(20),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX INVOICENUMBER_INDEX
ON transaction_invoicenumber (InvoiceNumberID, TransactionDate);
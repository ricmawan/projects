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

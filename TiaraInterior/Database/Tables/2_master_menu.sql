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
	'Merek',
	'Master/Brand/',
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
	'Tipe',
	'Master/Type/',
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
	6
),
(
	6,
	2,
	'Supplier',
	'Master/Supplier/',
	NULL,
	0,
	7
),
(
	7,
	3,
	'Stok Awal',
	'Transaction/FirstStock/',
	NULL,
	0,
	1
),
(
	8,
	3,
	'Barang Masuk',
	'Transaction/Incoming/',
	NULL,
	0,
	2
),
(
	9,
	3,
	'Barang Keluar',
	'Transaction/Outgoing/',
	NULL,
	0,
	3
),
(
	10,
	3,
	'Retur Beli',
	'Transaction/BuyReturn/',
	NULL,
	0,
	4
),
(
	11,
	3,
	'Retur Jual',
	'Transaction/SaleReturn/',
	NULL,
	0,
	5
),
(
	12,
	4,
	'Pembelian',
	'Report/Purchase/',
	NULL,
	1,
	1
),
(
	13,
	4,
	'Penjualan',
	'Report/Selling/',
	NULL,
	1,
	2
),
(
	14,
	4,
	'Penjualan Per Barang',
	'Report/SaleByItem/',
	NULL,
	1,
	3
),
(
	15,
	2,
	'Barang',
	'Master/Item/',
	NULL,
	0,
	5
),
(
	16,
	2,
	'Sales',
	'Master/Sales/',
	NULL,
	0,
	8
),
(
	17,
	5,
	'Bakcup Database',
	'Tools/BackupDB/',
	NULL,
	0,
	1
),
(
	18,
	5,
	'Restore Database',
	'Tools/RestoreDB/',
	NULL,
	0,
	2
),
(
	19,
	5,
	'Reset',
	'Tools/Reset/',
	NULL,
	0,
	3
),
(
	20,
	4,
	'Penjualan Per Pelanggan',
	'Report/SaleByCustomer/',
	NULL,
	1,
	3
),
(
	21,
	3,
	'Penyesuaian Stok',
	'Transaction/StockOpname/',
	NULL,
	0,
	6
),
(
	22,
	3,
	'Booking',
	'Transaction/Booking/',
	NULL,
	0,
	7
),
(
	23,
	3,
	'Pembatalan',
	'Transaction/Cancellation/',
	NULL,
	0,
	8
),
(
	24,
	4,
	'Stok',
	'Report/Stock/',
	NULL,
	1,
	5
),
(
	25,
	4,
	'Rinci Penjualan',
	'Report/SaleDetails/',
	NULL,
	1,
	6
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
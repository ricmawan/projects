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
	'Barang Masuk',
	'Transaction/Incoming/',
	NULL,
	0,
	1
),
(
	8,
	3,
	'Barang Keluar',
	'Transaction/Outgoing/',
	NULL,
	0,
	2
),
(
	9,
	3,
	'Retur Beli',
	'Transaction/BuyReturn/',
	NULL,
	0,
	3
),
(
	10,
	3,
	'Retur Jual',
	'Transaction/SaleReturn/',
	NULL,
	0,
	4
),
(
	11,
	4,
	'Pembelian',
	'Report/Purchase/',
	NULL,
	1,
	1
),
(
	12,
	4,
	'Penjualan Per Sales',
	'Report/SaleBySales/',
	NULL,
	1,
	2
),
(
	13,
	4,
	'Penjualan Per Barang',
	'Report/SaleByItem/',
	NULL,
	1,
	3
),
(
	14,
	4,
	'Penjualan Per Customer',
	'Report/SaleByCustomer/',
	NULL,
	1,
	4
),
(
	15,
	2,
	'Barang',
	'Master/Item/',
	NULL,
	1,
	5
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);

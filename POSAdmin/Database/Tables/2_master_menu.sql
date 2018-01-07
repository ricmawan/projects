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
(
	10,
	3,
	'Mutasi Stok',
	'Transaction/StockMutation/',
	NULL,
	0,
	5
),
(
	11,
	3,
	'Adjust Stok',
	'Transaction/StockAdjust/',
	NULL,
	0,
	6
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
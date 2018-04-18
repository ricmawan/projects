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
),
(
	12,
	3,
	'Pemesanan',
	'Transaction/Booking/',
	NULL,
	0,
	7
),
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
),
(
	19,
	2,
	'Update Harga',
	'Master/PriceUpdate/',
	NULL,
	0,
	6
),
(
	20,
	5,
	'Backup Data',
	'Tools/Backup/',
	NULL,
	0,
	1
),
(
	21,
	5,
	'Restore Data',
	'Tools/Restore/',
	NULL,
	0,
	2
),
(
	22,
	4,
	'Cetak Nota & Surat Jalan',
	'Transaction/Print/',
	NULL,
	0,
	9
),
(
	23,
	4,
	'Pembayaran & Pengambilan',
	'Transaction/Payment/',
	NULL,
	0,
	8
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
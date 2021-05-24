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
),
(
	33,
	4,
	'Stok Kosong',
	'Report/NoStock/',
	NULL,
	1,
	11
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
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
ON master_menu (MenuID);
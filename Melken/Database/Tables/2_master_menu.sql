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
	'Meja',
	'Master/Table/',
	NULL,
	0,
	2
),
(
	3,
	3,
	'Pemesanan',
	'Transaction/Order/',
	NULL,
	0,
	1
),
(
	4,
	3,
	'Penjualan',
	'Transaction/Sale/',
	NULL,
	0,
	2
),
(
	5,
	4,
	'Penjualan',
	'Report/Sale/',
	NULL,
	1,
	1
),
(
	6,
	2,
	'Makanan & Minuman',
	'Master/MenuList/',
	NULL,
	0,
	3
);
CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
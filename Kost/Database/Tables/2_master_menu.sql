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
(
	5,
	5,
	'Booking',
	'Transaction/Booking/',
	NULL,
	0,
	1
),
(
	6,
	3,
	'Pembelian Inventaris',
	'Transaction/IncomingInventory/',
	NULL,
	0,
	2
),
(
	7,
	3,
	'Pemakaian Inventaris',
	'Transaction/OutgoingInventory/',
	NULL,
	0,
	3
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
),
(
	11,
	5,
	'Check In',
	'Transaction/CheckIn/',
	NULL,
	0,
	3
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
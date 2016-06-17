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
	'fa fa-home fa-2',
	'./Home.php',
	1
),
(
	2,
	'Master',
	'fa fa-book fa-2',
	NULL,
	2
),
(
	3,
	'Operasional',
	'fa fa-cart-plus fa-2',
	NULL,
	3
),
(
	4,
	'Laporan',
	'fa fa-line-chart fa-2',
	NULL,
	4
),
(
	5,
	'Transaksi',
	'fa fa-bed fa-2',
	NULL,
	5
);

CREATE UNIQUE INDEX GROUPMENU_INDEX
ON master_groupmenu (GroupMenuID);

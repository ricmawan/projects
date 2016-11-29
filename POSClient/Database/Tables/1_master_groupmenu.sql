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
	'fa fa-home fa-3x',
	'./Home.php',
	1
),
(
	2,
	'Master Data',
	'fa fa-book fa-3x',
	NULL,
	2
),
(
	3,
	'Transaksi',
	'fa fa-cart-plus fa-3x',
	NULL,
	3
),
(
	4,
	'Laporan',
	'fa fa-line-chart fa-3x',
	NULL,
	4
);

CREATE UNIQUE INDEX GROUPMENU_INDEX
ON master_groupmenu (GroupMenuID);

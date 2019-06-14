DROP TABLE IF EXISTS master_groupmenu_web;

CREATE TABLE master_groupmenu_web
(
	GroupMenuID		INT PRIMARY KEY,
	GroupMenuName 	VARCHAR(255),
	Icon			VARCHAR(255),
	Url				VARCHAR(255),
	OrderNo			INT
)ENGINE=InnoDB;

INSERT INTO master_groupmenu_web
VALUES
(
	1,
	'Home',
	'fa fa-home',
	'./Home.php',
	1
),
(
	2,
	'Master Data',
	'fa fa-database',
	NULL,
	2
),
(
	3,
	'Transaksi',
	'fa fa-cart-plus',
	NULL,
	3
),
(
	4,
	'Konten',
	'fa fa-th-large',
	NULL,
	4
);

CREATE UNIQUE INDEX GROUPMENUWEB_INDEX
ON master_groupmenu_web (GroupMenuID);

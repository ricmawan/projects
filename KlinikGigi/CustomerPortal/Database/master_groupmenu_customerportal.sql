DROP TABLE IF EXISTS master_groupmenu_customerportal;

CREATE TABLE master_groupmenu_customerportal
(
	GroupMenuID		INT PRIMARY KEY,
	GroupMenuName 	VARCHAR(255),
	Icon			VARCHAR(255),
	Url				VARCHAR(255),
	OrderNo			INT
)ENGINE=InnoDB;

INSERT INTO master_groupmenu_customerportal
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
	'Penjadwalan',
	'fa fa-calendar',
	'./Scheduling/',
	2
);

CREATE UNIQUE INDEX GROUPMENUCUSTOMERPORTAL_INDEX
ON master_groupmenu_customerportal (GroupMenuID);

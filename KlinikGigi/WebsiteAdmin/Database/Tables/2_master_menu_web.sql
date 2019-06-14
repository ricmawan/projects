DROP TABLE IF EXISTS master_menu_web;

CREATE TABLE master_menu_web
(
	MenuID 		BIGINT PRIMARY KEY,
	GroupMenuID	INT,
	MenuName 	VARCHAR(255),
	Url			VARCHAR(255),
	Icon		VARCHAR(255),
	IsReport	BIT,
	OrderNo		INT,
	FOREIGN KEY(GroupMenuID) REFERENCES master_groupmenu_web(GroupMenuID)
)ENGINE=InnoDB;

INSERT INTO master_menu_web
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
	3,
	'Jadwal',
	'Transaction/Schedule/',
	NULL,
	0,
	1
);

CREATE UNIQUE INDEX MENUWEB_INDEX
ON master_menu_web (MenuID);
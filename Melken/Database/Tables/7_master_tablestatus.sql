DROP TABLE IF EXISTS master_tablestatus;

CREATE TABLE master_tablestatus
(
	TableStatusID 		SMALLINT PRIMARY KEY AUTO_INCREMENT,
	TableStatusName		VARCHAR(100)
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TABLESTATUS_INDEX
ON master_tablestatus (TableStatusID);

INSERT INTO master_tablestatus
VALUES
(
	1,
	'available'
),
(
	2,
	'used'
);
DROP TABLE IF EXISTS master_usertype;

CREATE TABLE master_usertype
(
	UserTypeID		SMALLINT PRIMARY KEY NOT NULL,
	UserTypeName	VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

INSERT INTO master_usertype
VALUES
(
	1,
	'Admin'
),
(
	2,
	'Dokter'
);
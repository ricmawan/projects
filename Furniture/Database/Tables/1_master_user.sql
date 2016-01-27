DROP TABLE IF EXISTS master_user;

CREATE TABLE master_user
(
	UserID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserName	VARCHAR(255) NOT NULL,
	UserLogin 	VARCHAR(100) NOT NULL,
	UserPassword 	VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 	VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy	VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO master_user
VALUES
(
	0,
	'System Administrator',
	'Admin',
	MD5('abcdef'),
	NOW(),
	'System',
	NULL,
	NULL
);

CREATE UNIQUE INDEX USER_INDEX
ON master_user (UserID);

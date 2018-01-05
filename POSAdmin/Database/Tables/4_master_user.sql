DROP TABLE IF EXISTS master_user;

CREATE TABLE master_user
(
	UserID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	UserTypeID		SMALLINT NOT NULL,
	UserName		VARCHAR(255) NOT NULL,
	UserLogin 		VARCHAR(100) NOT NULL,
	UserPassword 	VARCHAR(255) NOT NULL,
	IsActive		BOOLEAN,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy		VARCHAR(255) NULL,
	FOREIGN KEY(UserTypeID) REFERENCES master_usertype(UserTypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

INSERT INTO master_user
VALUES
(
	0,
	1,
	'System Administrator',
	'Admin1',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin2',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin3',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin4',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin5',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin6',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin7',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin8',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin9',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin10',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
),
(
	0,
	1,
	'System Administrator',
	'Admin11',
	MD5('abcdef'),
	1,
	NOW(),
	'System',
	NULL,
	NULL
);

CREATE UNIQUE INDEX USER_INDEX
ON master_user (UserID);

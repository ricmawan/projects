DROP TABLE IF EXISTS master_parameter;

CREATE TABLE master_parameter
(
	ParameterID BIGINT PRIMARY KEY AUTO_INCREMENT,
	ParameterName VARCHAR(255) NOT NULL,
	ParameterValue VARCHAR(255) NOT NULL,
	Remarks TEXT,
	IsNumber INT,
	CreatedDate DATETIME NOT NULL,
	CreatedBy VARCHAR(255) NOT NULL,
	ModifiedDate TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO master_parameter
VALUES
(
	0,
	'APPLICATION_PATH',
	'/Project/Furniture/Source/',
	'Location of the application',
	0,
	NOW(),
	'System',
	NULL,
	NULL
);


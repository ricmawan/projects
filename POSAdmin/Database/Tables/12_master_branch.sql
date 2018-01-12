DROP TABLE IF EXISTS master_branch;

CREATE TABLE master_branch
(
	BranchID 			INT PRIMARY KEY AUTO_INCREMENT,
    BranchCode			VARCHAR(100),
	BranchName			VARCHAR(255) NOT NULL,
	Address				TEXT,
    Telephone			VARCHAR(100),
    City				VARCHAR(100),
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BRANCH_INDEX
ON master_branch (BranchID);

INSERT INTO master_branch
VALUES
(
	0,
	'TK',
	'Toko',
	'',
	'',
	'',
	'',
	NOW(),
	'Admin',
	NULL,
	NULL
),
(
	0,
	'GDG',
	'Gudang',
	'',
	'',
	'',
	'',
	NOW(),
	'Admin',
	NULL,
	NULL
);
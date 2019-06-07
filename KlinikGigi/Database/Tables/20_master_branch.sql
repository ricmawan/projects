DROP TABLE IF EXISTS master_branch;

CREATE TABLE master_branch
(
	BranchID		SMALLINT PRIMARY KEY AUTO_INCREMENT,
	BranchName		VARCHAR(255) NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address 		TEXT,
	City			VARCHAR(100),
	DailyLimit		INT,
	StartHour		SMALLINT,
	EndHour			SMALLINT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BRANCH_INDEX
ON master_branch (BranchID);

INSERT INTO master_branch
(
	BranchName,
	Telephone,
	Address,
	City,
	DailyLimit,
	StartHour,
	EndHour
)
VALUES
(
	'Kawi',
	'024-00000',
	'Jl. Kawi',
	'Semarang',
	30,
	8,
	12
),
(
	'Indraprasta',
	'024-00000',
	'Jl. Indraprasta',
	'Semarang',
	40,
	17,
	21
);
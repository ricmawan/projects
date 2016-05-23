DROP TABLE IF EXISTS transaction_operationaldetails;

CREATE TABLE transaction_operationaldetails
(
	OperationalDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OperationalID			BIGINT,
	OperationalType			VARCHAR(100),
	Amount					DOUBLE,
	Remarks					VARCHAR(255),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL,
	FOREIGN KEY(OperationalID) REFERENCES transaction_operational(OperationalID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OPERATIONALDETAILS_INDEX
ON transaction_operationaldetails (OperationalDetailsID, OperationalID);
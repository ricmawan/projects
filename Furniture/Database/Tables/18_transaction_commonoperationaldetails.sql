DROP TABLE IF EXISTS transaction_commonoperationaldetails;

CREATE TABLE transaction_commonoperationaldetails
(
	CommonOperationalDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	CommonOperationalID			BIGINT,
	Remarks 					TEXT,
	Amount 						DOUBLE NOT NULL,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(CommonOperationalID) REFERENCES transaction_commonoperational(CommonOperationalID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX COMMONOPERATIONALDETAILS_INDEX
ON transaction_commonoperationaldetails (CommonOperationalDetailsID, CommonOperationalID);

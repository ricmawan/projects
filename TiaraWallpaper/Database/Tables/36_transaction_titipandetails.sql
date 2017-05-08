DROP TABLE IF EXISTS transaction_titipandetails;

CREATE TABLE transaction_titipandetails
(
	TitipanDetailsID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	TitipanID					BIGINT,
	Remarks 					TEXT,
	Quantity					DOUBLE,
	Price						DOUBLE,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(TitipanID) REFERENCES transaction_titipan(TitipanID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX TITIPANDETAILS_INDEX
ON transaction_titipandetails (TitipanDetailsID, TitipanID);
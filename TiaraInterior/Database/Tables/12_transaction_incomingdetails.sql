DROP TABLE IF EXISTS transaction_incomingdetails;

CREATE TABLE transaction_incomingdetails
(
	IncomingDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	Quantity			DOUBLE,
	Price				DOUBLE,
	BatchNumber			VARCHAR(100) NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(IncomingID) REFERENCES transaction_incoming(IncomingID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX INCOMINGDETAILS_INDEX
ON transaction_incomingdetails (IncomingDetailsID, IncomingID);
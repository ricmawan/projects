DROP TABLE IF EXISTS transaction_outgoingdetails;

CREATE TABLE transaction_outgoingdetails
(
	OutgoingDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	Name				VARCHAR(255),
	Quantity			DOUBLE,
	Price				DOUBLE,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingID) REFERENCES transaction_outgoing(OutgoingID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOINGDETAILS_INDEX
ON transaction_outgoingdetails(OutgoingDetailsID, OutgoingID);
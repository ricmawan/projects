DROP TABLE IF EXISTS transaction_outgoingdetails;

CREATE TABLE transaction_outgoingdetails
(
	OutgoingDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingID			BIGINT,
	TypeID 				BIGINT NOT NULL,
	Quantity			DOUBLE,
	BuyPrice			DOUBLE,
	SalePrice			DOUBLE,
	Discount			INT,
	IsPercentage		BIT,
	BatchNumber			VARCHAR(100) NULL,
	Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingID) REFERENCES transaction_outgoing(OutgoingID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(TypeID) REFERENCES master_type(TypeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX OUTGOINGDETAILS_INDEX
ON transaction_outgoingdetails(OutgoingDetailsID, OutgoingID, TypeID);
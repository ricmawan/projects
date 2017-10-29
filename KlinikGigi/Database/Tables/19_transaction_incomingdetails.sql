DROP TABLE IF EXISTS transaction_incomingdetails;

CREATE TABLE transaction_incomingdetails
(
	IncomingDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingID			BIGINT,
	MaterialID 			BIGINT NOT NULL,
	SupplierName		VARCHAR(255),
	Quantity			DOUBLE,
	Remarks				VARCHAR(255),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(IncomingID) REFERENCES transaction_incoming(IncomingID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(MaterialID) REFERENCES master_material(MaterialID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX INCOMINGDETAILS_INDEX
ON transaction_incomingdetails (IncomingDetailsID, IncomingID, MaterialID);
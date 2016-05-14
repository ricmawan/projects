DROP TABLE IF EXISTS transaction_outgoinginventorydetails;

CREATE TABLE transaction_outgoinginventorydetails
(
	OutgoingInventoryDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingInventoryID			BIGINT,
	InventoryID					BIGINT,
	Quantity					DOUBLE,
	Price						DOUBLE,
	Remarks						VARCHAR(255),
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 					VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingInventoryID) REFERENCES transaction_outgoinginventory(OutgoingInventoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(InventoryID) REFERENCES master_inventory(InventoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OUTGOINGINVENTORYDETAILS_INDEX
ON transaction_outgoinginventorydetails (OutgoingInventoryDetailsID, OutgoingInventoryID, InventoryID);
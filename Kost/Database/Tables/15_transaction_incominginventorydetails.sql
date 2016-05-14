DROP TABLE IF EXISTS transaction_incominginventorydetails;

CREATE TABLE transaction_incominginventorydetails
(
	IncomingInventoryDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	IncomingInventoryID			BIGINT,
	InventoryID					BIGINT,
	Quantity					DOUBLE,
	Price						DOUBLE,
	Remarks						VARCHAR(255),
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 					VARCHAR(255) NULL,
	FOREIGN KEY(IncomingInventoryID) REFERENCES transaction_incominginventory(IncomingInventoryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(InventoryID) REFERENCES master_inventory(InventoryID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX INCOMINGINVENTORYDETAILS_INDEX
ON transaction_incominginventorydetails (IncomingInventoryDetailsID, IncomingInventoryID, InventoryID);
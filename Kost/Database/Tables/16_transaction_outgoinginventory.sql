DROP TABLE IF EXISTS transaction_outgoinginventory;

CREATE TABLE transaction_outgoinginventory
(
	OutgoingInventoryID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate			DATETIME NOT NULL,
	Remarks					VARCHAR(255),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX OUTGOINGINVENTORY_INDEX
ON transaction_outgoinginventory (OutgoingInventoryID);
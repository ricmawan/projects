DROP TABLE IF EXISTS transaction_incominginventory;

CREATE TABLE transaction_incominginventory
(
	IncomingInventoryID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate			DATETIME NOT NULL,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX INCOMINGINVENTORY_INDEX
ON transaction_incominginventory (IncomingInventoryID);
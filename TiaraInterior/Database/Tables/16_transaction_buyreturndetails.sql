DROP TABLE IF EXISTS transaction_buyreturndetails;

CREATE TABLE transaction_buyreturndetails
(
	BuyReturnDetailsID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	BuyReturnID			BIGINT,
	ItemID 				BIGINT NOT NULL,
	Quantity			DOUBLE,
	Price				DOUBLE,
	BatchNumber			VARCHAR(100) NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(BuyReturnID) REFERENCES transaction_buyreturn(BuyReturnID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX BUYRETURNDETAILS_INDEX
ON transaction_buyreturndetails (BuyReturnDetailsID, BuyReturnID);
DROP TABLE IF EXISTS transaction_purchasedetails;

CREATE TABLE transaction_purchasedetails
(
    PurchaseDetailsID   BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    PurchaseID          BIGINT,
    ItemID		    	BIGINT NOT NULL,
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	Remarks 		    TEXT,
    CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ItemID) REFERENCES master_item(ItemID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (PurchaseID) REFERENCES transaction_purchase(PurchaseID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PURCHASEDETAILS_INDEX
ON transaction_purchasedetails (PurchaseDetailsID, PurchaseID, ItemID);
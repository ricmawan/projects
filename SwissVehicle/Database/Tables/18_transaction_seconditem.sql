DROP TABLE IF EXISTS transaction_seconditem;

CREATE TABLE transaction_seconditem
(
    SecondItemID		BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    ServiceID          	BIGINT,
    ItemID		    	BIGINT NOT NULL,
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	Remarks 		    TEXT,
    CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (ItemID) REFERENCES master_item(ItemID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (ServiceID) REFERENCES transaction_service(ServiceID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SECONDITEM_INDEX
ON transaction_seconditem (SecondItemID, ServiceID, ItemID);
DROP TABLE IF EXISTS transaction_servicedetails;

CREATE TABLE transaction_servicedetails
(
    ServiceDetailsID    BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	ServiceID		    BIGINT NOT NULL,
	ItemID		 	    BIGINT NULL,
    ItemName            VARCHAR(255),
	Quantity		    DOUBLE NOT NULL,
	Price			    DOUBLE NOT NULL,
	IsSecond			BIT,
	Remarks 		    TEXT,
	CreatedDate 	    DATETIME NOT NULL,
	CreatedBy 		    VARCHAR(255) NOT NULL,
	ModifiedDate 	    TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		    VARCHAR(255) NULL,
	FOREIGN KEY (ServiceID) REFERENCES transaction_service(ServiceID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SERVICEDETAILS_INDEX
ON transaction_servicedetails (ServiceDetailsID, ServiceID, ItemID);
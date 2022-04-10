DROP TABLE IF EXISTS master_customer;

CREATE TABLE master_customer
(
	CustomerID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
    CustomerCode		VARCHAR(100),
	CustomerName		VARCHAR(255) NOT NULL,
	Address				TEXT,
    Telephone			VARCHAR(100),
    City				VARCHAR(100),
	Remarks				TEXT,
	CustomerPriceID		SMALLINT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX CUSTOMER_INDEX
ON master_customer (CustomerID);

/*PriceID
1 : RetailPrice
2 : Price 1
3 : Price 3
*/
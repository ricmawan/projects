DROP TABLE IF EXISTS master_customer;

CREATE TABLE master_customer
(
	CustomerID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	SalesID			BIGINT,
	CustomerName	VARCHAR(255) NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address1 		VARCHAR(255),
	Address2 		VARCHAR(255),
	City			VARCHAR(100),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY(SalesID) REFERENCES master_sales(SalesID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

INSERT INTO `master_customer` (`CustomerID`, `SalesID`, `CustomerName`, `Telephone`, `Address`, `City`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 1, 'Bejo', '20983091', 'Jalan Pekunden Tengah No 1108\r\n(Wingko Babat Pak Moel)', 'Semarang', '2016-03-13 01:52:39', 'Admin', '2016-03-20 16:02:31', 'Admin'),
(2, 2, 'Slamet', '0812182655', 'Thamrin Square C14\r\nJalan MH. Thamrin No 5\r\n(Salon Slamet Saleh)', 'Semarang', '2016-03-13 01:52:39', 'Admin', '2016-03-20 16:02:31', 'Admin');

CREATE UNIQUE INDEX CUSTOMER_INDEX
ON master_Customer (CustomerID);
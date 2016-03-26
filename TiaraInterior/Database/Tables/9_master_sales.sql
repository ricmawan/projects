DROP TABLE IF EXISTS master_sales;

CREATE TABLE master_sales
(
	SalesID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	SalesName		VARCHAR(255) NOT NULL,
	Alias			VARCHAR(4) NOT NULL,
	Telephone 		VARCHAR(100) NOT NULL,
	Address 		VARCHAR(255),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_sales` (`SalesID`, `SalesName`, `Alias`, `Telephone`, `Address`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Budi', 'Budi', '08128716', '  Pekunden Barat ', '2016-03-13 01:51:22', 'Admin', '2016-03-19 19:44:38', 'Admin'),
(2, 'Ibu Budi', 'IBBD', '08112121', '  Pekunden Tengah ', '2016-03-13 01:51:22', 'Admin', '2016-03-19 19:44:38', 'Admin');

CREATE UNIQUE INDEX SALES_INDEX
ON master_sales (SalesID);
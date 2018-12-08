DROP TABLE IF EXISTS transaction_printerlist;

CREATE TABLE transaction_printerlist
(
	PrinterListID			INT PRIMARY KEY AUTO_INCREMENT,
	IPAddress				VARCHAR(100),
	SharedPrinterName		VARCHAR(100),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
	
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PRINTERLIST_INDEX
ON transaction_printerlist (PrinterListID);

INSERT INTO transaction_printerlist
(
	IPAddress,
	SharedPrinterName,
	CreatedDate,
	CreatedBy
)
VALUES
(
	'192.168.1.100',
	'//192.168.1.100/EPSON2',
	'2018-12-08',
	'Admin1'
),
(
	'::1',
	'//192.168.1.2/EPSON',
	'2018-12-08',
	'Admin1'
);
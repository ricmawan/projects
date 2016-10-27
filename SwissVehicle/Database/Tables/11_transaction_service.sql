DROP TABLE IF EXISTS transaction_service;

CREATE TABLE transaction_service
(
	ServiceID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate	DATE,
	MachineID		BIGINT NOT NULL,
	IsSelfWorkshop	BIT NOT NULL,
	WorkshopName	VARCHAR(255),
	Kilometer		DOUBLE,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY (MachineID) REFERENCES master_machine(MachineID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SERVICE_INDEX
ON transaction_service (ServiceID, MachineID);
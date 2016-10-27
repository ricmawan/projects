DROP TABLE IF EXISTS transaction_licenseextension;

CREATE TABLE transaction_licenseextension
(
	LicenseExtensionID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate		DATE,
	MachineID			BIGINT NOT NULL,
	DueDate				DATE NOT NULL,
	IsExtended			BIT,
	Remarks 			TEXT,
	ExtensionDate		DATE,
	ExtensionCost		DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY (MachineID) REFERENCES master_machine(MachineID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX LICENSEEXTENSION_INDEX
ON transaction_licenseextension (LicenseExtensionID, MachineID);
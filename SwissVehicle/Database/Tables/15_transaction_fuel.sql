DROP TABLE IF EXISTS transaction_fuel;

CREATE TABLE transaction_fuel
(
	FuelID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	FuelTypeID		BIGINT,
	TransactionDate	DATE,
	MachineID		BIGINT NOT NULL,
	Kilometer		DOUBLE NULL,
	Quantity		DOUBLE NOT NULL,
	Price			DOUBLE NOT NULL,
	Remarks 		TEXT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL,
	FOREIGN KEY (MachineID) REFERENCES master_machine(MachineID) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (FuelTypeID) REFERENCES master_fueltype(FuelTypeID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX FUEL_INDEX
ON transaction_fuel (FuelID, FuelTypeID, MachineID);
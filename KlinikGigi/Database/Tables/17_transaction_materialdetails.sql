DROP TABLE IF EXISTS transaction_materialdetails;

CREATE TABLE transaction_materialdetails
(
	MaterialDetailsID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	MedicationDetailsID		BIGINT NOT NULL,
	MaterialID				BIGINT NOT NULL,
	Remarks					TEXT,
	SalePrice				DOUBLE,
	Quantity				DOUBLE,
	SessionID				VARCHAR(100),
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy				VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX MATERIALDETAILS_INDEX
ON transaction_materialdetails (MaterialDetailsID, MaterialID);
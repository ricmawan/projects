DROP TABLE IF EXISTS transaction_asset;

CREATE TABLE transaction_asset
(
	AssetID 					BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate				DATETIME,
	Remarks 					TEXT,
	Amount 						DOUBLE NOT NULL,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX ASSET_INDEX
ON transaction_asset (AssetID);
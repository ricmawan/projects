DROP TABLE IF EXISTS transaction_debtpayment;

CREATE TABLE transaction_debtpayment
(
	DebtPaymentID		BIGINT PRIMARY KEY AUTO_INCREMENT,
	MedicationID		BIGINT NOT NULL,
	Cash				DOUBLE,
	Debit				DOUBLE,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL,
	FOREIGN KEY(MedicationID) REFERENCES transaction_medication(MedicationID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX DEBTPAYMENT_INDEX
ON transaction_debtpayment (DebtPaymentID, MedicationID);
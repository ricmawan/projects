DROP TABLE IF EXISTS transaction_outgoingpayment;

CREATE TABLE transaction_outgoingpayment
(
	OutgoingPaymentID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	OutgoingID			BIGINT,
	TransactionDate		DATETIME,
	Remarks 			TEXT,
	Amount 				DOUBLE NOT NULL,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 			VARCHAR(255) NULL,
	FOREIGN KEY(OutgoingID) REFERENCES transaction_outgoing(OutgoingID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;


CREATE UNIQUE INDEX PROJECTPAYMENT_INDEX
ON transaction_outgoingpayment (OutgoingPaymentID, OutgoingID);

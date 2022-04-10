DROP TABLE IF EXISTS transaction_paymentdetails;

CREATE TABLE transaction_paymentdetails
(
	PaymentDetailsID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionID		BIGINT,
	TransactionType 	VARCHAR(1), /* S=Sale B=Booking P=Purchase */
    PaymentDate			DATETIME,
	Amount				DOUBLE,
    Remarks				TEXT,
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL,
	ModifiedDate 		TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy			VARCHAR(255) NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PAYMENTDETAILS_INDEX
ON transaction_paymentdetails (PaymentDetailsID, TransactionID);
DROP TABLE IF EXISTS master_paymenttype;

CREATE TABLE master_paymenttype
(
	PaymentTypeID 		SMALLINT PRIMARY KEY AUTO_INCREMENT,
	PaymentTypeName		VARCHAR(100),
	CreatedDate 		DATETIME NOT NULL,
	CreatedBy 			VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

CREATE UNIQUE INDEX PAYMENTTYPE_INDEX
ON master_paymenttype (PaymentTypeID);

INSERT INTO master_paymenttype
VALUES
(
	1,
	'Tunai',
	NOW(),
	'Admin'
),
(
	2,
	'Tempo',
	NOW(),
	'Admin'
);
DROP TABLE IF EXISTS transaction_expense;

CREATE TABLE transaction_expense
(
	ExpenseID	 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	TransactionDate	DATETIME NOT NULL,
	Name			VARCHAR(100),
	Amount			DOUBLE,
	Remarks			VARCHAR(255),
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;


CREATE UNIQUE INDEX EXPENSE_INDEX
ON transaction_expense (ExpenseID);
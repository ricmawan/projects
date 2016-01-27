DROP TABLE IF EXISTS transaction_salary;

CREATE TABLE transaction_salary
(
	SalaryID 				BIGINT PRIMARY KEY AUTO_INCREMENT,
	PeriodID				BIGINT,
	SalaryDate				DATETIME,	
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL,
	FOREIGN KEY (PeriodID) REFERENCES master_period(PeriodID)
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALARY_INDEX
ON transaction_salary (SalaryID, PeriodID);
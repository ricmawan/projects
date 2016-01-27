DROP TABLE IF EXISTS transaction_salarydetails;

CREATE TABLE transaction_salarydetails
(
	SalaryDetailsID 			BIGINT PRIMARY KEY AUTO_INCREMENT,
	SalaryID					BIGINT,
	ProjectID					BIGINT,
	EmployeeID					BIGINT,
	Remarks 					TEXT,
	DailySalary					DOUBLE NOT NULL,
	Days 						INT NOT NULL,
	CreatedDate 				DATETIME NOT NULL,
	CreatedBy 					VARCHAR(255) NOT NULL,
	ModifiedDate 				TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy					VARCHAR(255) NULL,
	FOREIGN KEY(SalaryID) REFERENCES transaction_salary(SalaryID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(ProjectID) REFERENCES master_project(ProjectID) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(EmployeeID) REFERENCES master_employee(EmployeeID) ON UPDATE CASCADE ON DELETE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX SALARYDETAILS_INDEX
ON transaction_salarydetails (SalaryDetailsID, SalaryID, ProjectID, EmployeeID);
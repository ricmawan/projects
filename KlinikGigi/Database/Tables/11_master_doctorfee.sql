DROP TABLE IF EXISTS transaction_doctorcommision;

CREATE TABLE transaction_doctorcommision
(
	DoctorCommisionID 		BIGINT PRIMARY KEY AUTO_INCREMENT,
	BusinessMonth 			SMALLINT,
	BusinessYear 			INT,
	DoctorID				BIGINT,
	CommisionPercentage		SMALLINT,
	ToolsFee				DOUBLE,
	CreatedDate 			DATETIME NOT NULL,
	CreatedBy 				VARCHAR(255) NOT NULL,
	ModifiedDate 			TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 				VARCHAR(255) NULL,
	FOREIGN KEY (DoctorID) REFERENCES master_user(UserID) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

CREATE UNIQUE INDEX DOCTORCOMMISION_INDEX
ON transaction_doctorcommision (DoctorCommisionID, DoctorID);
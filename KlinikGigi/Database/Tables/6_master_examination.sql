DROP TABLE IF EXISTS master_examination;

CREATE TABLE master_examination
(
	ExaminationID	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ExaminationName	VARCHAR(255) NOT NULL,
	Price			DOUBLE NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_examination` (`ExaminationID`, `ExaminationName`, `Price`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Cabut Gigi', 200000, '2016-03-12 17:02:18', 'Admin', NULL, NULL),
(2, 'Tambal Gigi', 150000, '2016-03-12 17:02:36', 'Admin', NULL, NULL),
(3, 'Operasi Gigi', 1000000, '2016-03-12 17:02:44', 'Admin', NULL, NULL),
(4, 'Kontrol Rutin', 50000, '2016-03-12 17:02:53', 'Admin', NULL, NULL),
(5, 'Pasang Kawat', 3500000, '2016-03-12 17:03:06', 'Admin', NULL, NULL);

CREATE UNIQUE INDEX EXAMINATION_INDEX
ON master_examination (ExaminationID);
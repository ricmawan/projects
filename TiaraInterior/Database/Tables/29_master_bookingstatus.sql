DROP TABLE IF EXISTS master_bookingstatus;

CREATE TABLE master_bookingstatus
(
	BookingStatusID		TINYINT PRIMARY KEY AUTO_INCREMENT,
	BookingStatusName	VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

INSERT INTO `master_bookingstatus` 
VALUES
(1, 'Dalam Proses'),
(2, 'Sudah Selesai'),
(3, 'Sudah Jatuh Tempo');

CREATE UNIQUE INDEX BOOKINGSTATUS_INDEX
ON master_bookingstatus (BookingStatusID);
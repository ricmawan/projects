DROP TABLE IF EXISTS master_status;

CREATE TABLE master_status
(
	StatusID 		INT PRIMARY KEY AUTO_INCREMENT,
	StatusName 		VARCHAR(255) NOT NULL
)ENGINE=InnoDB;

INSERT INTO `master_status` (StatusID, StatusName)
VALUES
	(1, 'available'),
	(2, 'booked'),
	(3, 'occupied');
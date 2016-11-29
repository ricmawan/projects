DROP TABLE IF EXISTS master_parameter;

CREATE TABLE master_parameter
(
	ParameterID 	BIGINT PRIMARY KEY AUTO_INCREMENT,
	ParameterName 	VARCHAR(255) NOT NULL,
	ParameterValue 	VARCHAR(255) NOT NULL,
	Remarks 		TEXT,
	IsNumber		INT,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_parameter` (`ParameterID`, `ParameterName`, `ParameterValue`, `Remarks`, `IsNumber`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'APPLICATION_PATH', '/Projects/Melken/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\Melken\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\Melken\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\Melken\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL);
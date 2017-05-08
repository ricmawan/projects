DROP TABLE IF EXISTS master_brand;

CREATE TABLE master_brand
(
	BrandID			BIGINT PRIMARY KEY AUTO_INCREMENT,
	BrandName		VARCHAR(255) NOT NULL,
	CreatedDate 	DATETIME NOT NULL,
	CreatedBy 		VARCHAR(255) NOT NULL,
	ModifiedDate 	TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL,
	ModifiedBy 		VARCHAR(255) NULL
)ENGINE=InnoDB;

INSERT INTO `master_brand` (`BrandID`, `BrandName`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Eurowall', '2016-03-12 17:02:18', 'Admin', NULL, NULL),
(2, 'MAESTRO', '2016-03-12 17:02:36', 'Admin', NULL, NULL),
(3, 'KING', '2016-03-12 17:02:44', 'Admin', NULL, NULL),
(4, 'Queen', '2016-03-12 17:02:53', 'Admin', NULL, NULL),
(5, 'MONCHERI', '2016-03-12 17:03:06', 'Admin', NULL, NULL),
(6, 'WOW', '2016-03-12 17:03:12', 'Admin', NULL, NULL),
(7, 'Econia', '2016-03-12 17:03:33', 'Admin', NULL, NULL),
(8, 'Borneo', '2016-03-12 17:03:49', 'Admin', NULL, NULL),
(9, 'Bacan', '2016-03-12 17:03:56', 'Admin', NULL, NULL),
(10, 'Crown', '2016-03-12 17:06:03', 'Admin', NULL, NULL),
(11, 'Star', '2016-03-12 17:09:20', 'Admin', NULL, NULL),
(12, 'Bravo', '2016-03-12 17:12:21', 'Admin', NULL, NULL),
(13, 'Empire', '2016-03-12 17:12:30', 'Admin', NULL, NULL),
(14, 'Excellent', '2016-03-12 17:12:41', 'Admin', NULL, NULL),
(15, 'Sky Line', '2016-03-12 17:12:53', 'Admin', NULL, NULL),
(16, 'Supra', '2016-03-12 17:13:04', 'Admin', NULL, NULL),
(17, 'Delta', '2016-03-12 17:13:15', 'Admin', NULL, NULL),
(18, 'Ion', '2016-03-12 17:13:19', 'Admin', NULL, NULL),
(19, 'Focus', '2016-03-12 17:13:26', 'Admin', NULL, NULL),
(20, 'Renova', '2016-03-12 17:13:35', 'Admin', '2016-03-12 19:27:52', 'Admin');

CREATE UNIQUE INDEX BRAND_INDEX
ON master_brand (BrandID);
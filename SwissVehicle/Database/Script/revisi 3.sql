ALTER TABLE `master_fueltype` ADD `Price` DOUBLE NOT NULL AFTER `FuelTypeName`;


INSERT INTO master_menu
VALUES
(
	19,
	2,
	'Jenis BBM',
	'Master/FuelType/',
	NULL,
	0,
	6
),
(
	20,
	2,
	'Kategori Laporan',
	'Master/ReportCategory/',
	NULL,
	0,
	7
),
(
	21,
	4,
	'Saldo Spare Part',
	'Report/SparePartBalance/',
	NULL,
	1,
	9
);

INSERT INTO master_role
VALUES
(
	0,
	1,
	19,
	1,
	1
),
(
	0,
	1,
	20,
	1,
	1
),
(
	0,
	1,
	21,
	1,
	1
);

ALTER TABLE `master_item` ADD `ReportCategoryID` INT NOT NULL AFTER `ItemID`;
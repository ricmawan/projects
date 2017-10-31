INSERT INTO master_menu
(
	MenuID,
	GroupMenuID,
	MenuName,
	URL,
	Icon,
	IsReport,
	OrderNo    
)
VALUES
(
	15,
	2,
	'Material',
	'Master/Material/',
	NULL,
	0,
	5
),
(
	16,
	3,
	'Pembelian Material',
	'Transaction/IncomingMaterial/',
	NULL,
	0,
	4
),
(
	17,
	4,
	'Pembelian Material',
	'Report/IncomingMaterial/',
	NULL,
	0,
	4
);

INSERT INTO master_role
VALUES
(
	0,
	1,
	15,
	1,
	1
),
(
	0,
	1,
	16,
	1,
	1
),
(
	0,
	1,
	17,
	1,
	1
);

ALTER TABLE `transaction_medicationdetails` ADD `MaterialID` BIGINT NOT NULL AFTER `MedicationID`;
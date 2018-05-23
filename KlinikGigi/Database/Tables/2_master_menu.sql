DROP TABLE IF EXISTS master_menu;

CREATE TABLE master_menu
(
	MenuID 		BIGINT PRIMARY KEY,
	GroupMenuID	INT,
	MenuName 	VARCHAR(255),
	Url			VARCHAR(255),
	Icon		VARCHAR(255),
	IsReport	BIT,
	OrderNo		INT,
	FOREIGN KEY(GroupMenuID) REFERENCES master_groupmenu(GroupMenuID)
)ENGINE=InnoDB;

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
	1,
	2,
	'User',
	'Master/User/',
	NULL,
	0,
	1
),
(
	2,
	2,
	'Jenis Periksa',
	'Master/Examination/',
	NULL,
	0,
	3
),
(
	3,
	2,
	'Pasien',
	'Master/Patient/',
	NULL,
	0,
	4
),
(
	4,
	3,
	'Pendaftaran',
	'Transaction/Registration/',
	NULL,
	0,
	1
),
(
	5,
	3,
	'Tindakan',
	'Transaction/Medication/',
	NULL,
	0,
	2
),
(
	6,
	3,
	'Pembayaran',
	'Transaction/Payment/',
	NULL,
	0,
	3
),
(
	7,
	4,
	'Gaji Dokter',
	'Report/Salary/',
	NULL,
	1,
	1
),
(
	8,
	4,
	'Transaksi Bulanan',
	'Report/MonthlyIncome/',
	NULL,
	1,
	2
),
(
	9,
	4,
	'Rekam Medis',
	'Report/MedicalRecord/',
	NULL,
	1,
	3
),
(
	10,
	2,
	'Dokter',
	'Master/Doctor/',
	NULL,
	0,
	2
),
(
	11,
	3,
	'Pembayaran Kekurangan',
	'Transaction/DebtPayment/',
	NULL,
	0,
	4
),
(
	12,
	3,
	'Jadwal Periksa',
	'Transaction/CheckSchedule/',
	NULL,
	0,
	4
),
(
	13,
	4,
	'Foto Pemeriksaan',
	'Report/Photos/',
	NULL,
	0,
	4
),
(
	14,
	4,
	'Email',
	'Report/Email/',
	NULL,
	0,
	5
),
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
	2,
	'Jadwal Praktek',
	'Master/Schedule/',
	NULL,
	0,
	6
),
(
	18,
	2,
	'Pengecualian Praktek',
	'Master/ExceptionSchedule/',
	NULL,
	0,
	7
);

CREATE UNIQUE INDEX MENU_INDEX
ON master_menu (MenuID);
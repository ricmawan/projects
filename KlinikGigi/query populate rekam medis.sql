INSERT INTO transaction_medicalrecord
(
	PatientID,
    BranchID,
    MedicationID,
    MedicationDetailsID,
    ExaminationName,
    TransactionDate,
    Remarks,
    CreatedDate,
    CreatedBy
)
SELECT
	MP.PatientID,
	1,
	TM.MedicationID,
	TMD.MedicationDetailsID,
	ME.ExaminationName,
	TM.TransactionDate,
	TMD.Remarks,
	NOW(),
	'superadmin'
FROM
	transaction_medication TM
	JOIN transaction_medicationdetails TMD
		ON TM.MedicationID = TMD.MedicationID
	JOIN master_patient MP
		ON TM.PatientID = MP.PatientID
	JOIN master_examination ME
		ON TMD.ExaminationID = ME.ExaminationID
WHERE
	TMD.Synchronized = 0
	AND TM.IsDone = 1
	AND TM.IsCancelled = 0
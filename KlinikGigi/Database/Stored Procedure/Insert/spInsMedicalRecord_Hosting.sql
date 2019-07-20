DROP PROCEDURE IF EXISTS spInsMedicalRecord;

DELIMITER $$
CREATE PROCEDURE spInsMedicalRecord (
	pPatientID				BIGINT,
	pBranchID	 			SMALLINT,
	pMedicationID			BIGINT,
	pMedicationDetailsID 	BIGINT,
	pExaminationName 		VARCHAR(255),
	pTransactionDate		DATETIME,
	pRemarks				TEXT,
	pCurrentUser			VARCHAR(255)
)

StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	
	
	SET PassValidate = 1;
	
	START TRANSACTION;	

SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			transaction_medicalrecord
		WHERE
			pMedicationDetailsID = MedicationDetailsID
			AND pBranchID = pBranchID
		LIMIT 1;
			
		IF PassValidate = 0 THEN 

SET State = 2;

			SELECT
				pMedicationDetailsID AS 'ID',
				'Rekam medis sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;

			LEAVE StoredProcedure;
			
		ELSE 

SET State = 3;

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
			VALUES
			(
				pPatientID,
				pBranchID,
				pMedicationID,
				pMedicationDetailsID,
				pExaminationName,
				pTransactionDate,
				pRemarks,
				NOW(),
				pCurrentUser
			);

			SELECT
				pMedicationDetailsID AS 'ID',
				'Rekam medis Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';

		END IF;
	COMMIT;
END
$$
DELIMITER ;

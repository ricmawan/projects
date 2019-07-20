DROP PROCEDURE IF EXISTS spInsPatient;

DELIMITER $$
CREATE PROCEDURE spInsPatient (
	pID 			BIGINT,
	pPatientNumber	VARCHAR(100),
	pPatientName 	VARCHAR(255),
	pBirthDate		DATE,
	pAddress 		TEXT,
	pAllergy 		TEXT,
	pCity			VARCHAR(100),
	pTelephone		VARCHAR(255),
	pEmail			VARCHAR(255),
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255),
	pInfo			VARCHAR(255)
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
			master_patient
		WHERE
			(TRIM(PatientName) = TRIM(pPatientName)
			AND TRIM(Address) = TRIM(pAddress)
			AND TRIM(City) = TRIM(pCity)
			AND PatientID <> pID)
			OR (TRIM(PatientNumber) = TRIM(pPatientNumber)
			OR PatientID = pID)
		LIMIT 1;
			
		IF PassValidate = 0 THEN 
SET State = 2;
			SELECT
				pID AS 'ID',
				'Pasien sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE 
SET State = 3;
			IF(pIsEdit = 0)	THEN 
				INSERT INTO master_patient
				(
					PatientNumber,
					PatientName,
					BirthDate,
					Address,
					Allergy,
					City,
					Telephone,
					Email,
					CreatedDate,
					CreatedBy,
                    Info
				)
				VALUES (
					pPatientNumber,
					pPatientName,
					pBirthDate,
					pAddress,
					pAllergy,
					pCity,
					pTelephone,
					pEmail,
					NOW(),
					pCurrentUser,
                    pInfo
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Pasien Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_patient
				SET
					PatientNumber = pPatientNumber,
					PatientName = pPatientName,
					BirthDate = pBirthDate,
					Address = pAddress,
					Allergy = pAllergy,
					City = pCity,
					Telephone = pTelephone,
					Email = pEmail,
					ModifiedBy = pCurrentUser,
                    Info = pInfo
				WHERE
					PatientID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Pasien Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END
$$
DELIMITER ;

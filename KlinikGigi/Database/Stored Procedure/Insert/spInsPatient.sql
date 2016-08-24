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
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	/*DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR ", IFNULL(@ErrNo, ''), " (", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		SELECT 
			pId AS 'ID', 
			'Terjadi Kesalahan Sistem' AS 'Message', 
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag', 
			State AS 'State';
	END;*/
	
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
			AND PatientID <> pID)
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Pasien sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_patient
				(
					PatientNumber,
					PatientName,
					BirthDate,
					Address,
					Allergy,
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pPatientNumber,
					pPatientName,
					pBirthDate,
					pAddress,
					pAllergy,
					pCity,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					MAX(PatientID) AS 'ID',
					'Pasien Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State'
				FROM
					master_patient;
					
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
					ModifiedBy = pCurrentUser
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
END;
$$
DELIMITER ;

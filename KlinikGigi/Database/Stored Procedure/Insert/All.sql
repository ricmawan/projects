DROP PROCEDURE IF EXISTS spInsExamination;

DELIMITER $$
CREATE PROCEDURE spInsExamination (
	pID 				BIGINT, 
	pExaminationName 	VARCHAR(255),
	pPrice				DOUBLE,
	pIsEdit				INT,
    pCurrentUser		VARCHAR(255)
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
			master_examination
		WHERE
			TRIM(ExaminationName) = TRIM(pExaminationName)
			AND ExaminationID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Jenis Periksa sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_examination
				(
					ExaminationName,
					Price,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pExaminationName,
					pPrice,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Jenis Periksa Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_examination
				SET
					ExaminationName = pExaminationName,
					Price = pPrice,
					ModifiedBy = pCurrentUser
				WHERE
					ExaminationID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Jenis Periksa Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
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
DROP PROCEDURE IF EXISTS spInsUser;

DELIMITER $$
CREATE PROCEDURE spInsUser (
	pID 			BIGINT, 
	pUserName 		VARCHAR(255),
	pUserTypeID		SMALLINT,
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
	pMenuID 		VARCHAR(255),
	pEditMenuID 	VARCHAR(255),
	pDeleteMenuID	VARCHAR(255),
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
			pID AS 'ID', 
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
			master_user 
		WHERE
			UserName = pUserName
			AND UserLogin = pUserLogin
			AND UserID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Username sudah dipakai' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
		
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_user
				(
					UserName,
					UserLogin,
					UserTypeID,
					UserPassword,
					IsActive,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
					pUserTypeID,
					pPassword,
					pIsActive,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					MAX(UserID)
				INTO 
					pID
				FROM
					master_user;
					
			ELSE
SET State = 5;
				UPDATE
					master_user
				SET
					UserName = pUserName,
					UserLogin = pUserLogin,
					UserTypeID = pUserTypeID,
					UserPassword = pPassword,
					IsActive = pIsActive,
					ModifiedBy = pCurrentUser
				WHERE
					UserID = pID;
		
			END IF;
	
SET State = 6;

			DELETE 
			FROM 
				master_role
			WHERE
				UserID = pID;
				
SET State = 7;
			loopdata : WHILE pMenuID <> "" DO
				INSERT INTO master_role
				(
					UserID,
					MenuID,
					EditFlag,
					DeleteFlag
				)
				VALUES
				(
					pID,
					SUBSTRING(pMenuID, 1, IF((INSTR(pMenuID, ',') - 1) = -1, LENGTH(pMenuID), (INSTR(pMenuID, ',') - 1))),
					SUBSTRING(pEditMenuID, 1, IF((INSTR(pEditMenuID, ',') - 1) = -1, LENGTH(pEditMenuID), (INSTR(pEditMenuID, ',') - 1))),
					SUBSTRING(pDeleteMenuID, 1, IF((INSTR(pDeleteMenuID, ',') - 1) = -1, LENGTH(pDeleteMenuID), (INSTR(pDeleteMenuID, ',') - 1)))
				);
				
				IF(INSTR(pMenuID, ',') = 0) THEN SET pMenuID = "";
				ELSE
					SET pMenuID = SUBSTRING(pMenuID, INSTR(pMenuID, ',') + 1, LENGTH(pMenuID));
					SET pEditMenuID = SUBSTRING(pEditMenuID, INSTR(pEditMenuID, ',') + 1, LENGTH(pEditMenuID));
					SET pDeleteMenuID = SUBSTRING(pDeleteMenuID, INSTR(pDeleteMenuID, ',') + 1, LENGTH(pDeleteMenuID));
				END IF;
				
			END WHILE loopdata; 
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
		SELECT
			pID AS 'ID',
			'User Berhasil Ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
SET State = 9;
		SELECT
			pID AS 'ID',
			'User Berhasil Diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
    COMMIT;
END;
$$
DELIMITER ;

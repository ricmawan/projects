DROP PROCEDURE IF EXISTS spInsProject;

DELIMITER $$
CREATE PROCEDURE spInsProject (
	pID 			BIGINT, 
	pProjectName 		VARCHAR(255),
	pIsDone 	INT,
	pRemarks	TEXT,
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
			master_project
		WHERE
			TRIM(ProjectName) = TRIM(pProjectName)
			AND ProjectID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Proyek sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_project
				(
					ProjectName,
					IsDone,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pProjectName,
					pIsDone,
					pRemarks,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Proyek Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_project
				SET
					ProjectName = pProjectName,
					IsDone = pIsDone,
					Remarks = pRemarks,
					ModifiedDate = NOW(),
					ModifiedBy = pCurrentUser
				WHERE
					ProjectID = pID;
		
SET State = 6;
				SELECT
					pID AS 'ID',
					'Proyek Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;

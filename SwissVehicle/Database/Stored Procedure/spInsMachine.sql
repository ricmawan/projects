DROP PROCEDURE IF EXISTS spInsMachine;

DELIMITER $$
CREATE PROCEDURE spInsMachine (
	pID 			BIGINT, 
	pMachineKind 	VARCHAR(100),
	pMachineType 	VARCHAR(255),
	pMachineYear	INT,
	pMachineCode	VARCHAR(255),
	pBrandName		VARCHAR(255),
	pRemarks		TEXT,
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
			master_machine
		WHERE
			MachineType = pMachineType
			AND MachineCode = pMachineCode
			AND MachineID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				CONCAT(pMachineKind, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
		
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_machine
				(
					MachineKind,
					MachineType,
					MachineYear,
					MachineCode,
					BrandName,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pMachineKind,
					pMachineType,
					pMachineYear,
					pMachineCode,
					pBrandName,
					pRemarks,
					NOW(),
					pCurrentUser
				);
					
			ELSE
SET State = 5;
				UPDATE
					master_machine
				SET
					MachineKind = pMachineKind,
					MachineType = pMachineType,
					MachineYear = pMachineYear,
					MachineCode = pMachineCode,
					BrandName = pBrandName,
					Remarks = pRemarks,
					ModifiedBy = pCurrentUser
				WHERE
					MachineID = pID;
		
			END IF;
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
		SELECT
			pID AS 'ID',
			CONCAT(pMachineKind, ' berhasil ditambahkan') AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
SET State = 9;
		SELECT
			pID AS 'ID',
			CONCAT(pMachineKind, ' berhasil diubah') AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
    COMMIT;
END;
$$
DELIMITER ;

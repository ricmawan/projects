DROP PROCEDURE IF EXISTS spInsEmployee;

DELIMITER $$
CREATE PROCEDURE spInsEmployee (
	pID				BIGINT, 
	pEmployeeName 	VARCHAR(255),
	pStartDate		VARCHAR(10),
	pEndDate		VARCHAR(10),
	pDailySalary	DOUBLE,
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
			master_employee
		WHERE
			TRIM(EmployeeName) = TRIM(pEmployeeName)
			AND EmployeeID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Karyawan sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_employee
				(
					EmployeeName,
					StartDate,
					EndDate,
					DailySalary,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pEmployeeName,
					pStartDate,
					pEndDate,
					pDailySalary,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Karyawan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_employee
				SET
					EmployeeName = pEmployeeName,
					StartDate = pStartDate,
					EndDate = pEndDate,
					DailySalary = pDailySalary,
					ModifiedBy = pCurrentUser,
					ModifiedDate = NOW()
				WHERE
					EmployeeID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Karyawan Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
				
			END IF;
		END IF;			
	COMMIT;
END;
$$
DELIMITER ;

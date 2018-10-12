DROP PROCEDURE IF EXISTS spInsCategory;

DELIMITER $$
CREATE PROCEDURE spInsCategory (
	pID				BIGINT, 
	pCategoryName 	VARCHAR(255), 
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
			master_category
		WHERE
			TRIM(CategoryName) = TRIM(pCategoryName)
			AND CategoryID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Kategori sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_category
				(
					CategoryName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCategoryName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Kategori Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_category
				SET
					CategoryName = pCategoryName,
					ModifiedDate = NOW(),
					ModifiedBy = pCurrentUser
				WHERE
					CategoryID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Kategori Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
				
			END IF;
		END IF;			
	COMMIT;
END;
$$
DELIMITER ;
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
DROP PROCEDURE IF EXISTS spInsItem;

DELIMITER $$
CREATE PROCEDURE spInsItem (
	pID 			BIGINT, 
	pItemName 		VARCHAR(255),
	pCategoryID 	BIGINT,
	pUnitID			BIGINT,
	pReminderCount	INT,
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
			master_item
		WHERE
			TRIM(ItemName) = TRIM(pItemName)
			AND CategoryID = pCategoryID
			AND ItemID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Barang sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_item
				(
					ItemName,
					CategoryID,
					UnitID,
					ReminderCount,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemName,
					pCategoryID,
					pUnitID,
					pReminderCount,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_item
				SET
					ItemName = pItemName,
					ReminderCount = pReminderCount,
					CategoryID = pCategoryID,
					UnitID = pUnitID,
					ModifiedBy = pCurrentUser,
					ModifiedDate = NOW()
				WHERE
					ItemID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsPeriod;

DELIMITER $$
CREATE PROCEDURE spInsPeriod (
	pID				BIGINT, 
	pStartDate	 	DATE, 
	pEndDate	 	DATE, 
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
	
		IF(pIsEdit = 0)	THEN /*Tambah baru*/
			INSERT INTO master_period
			(
				StartDate,
				EndDate,
				CreatedDate,
				CreatedBy
			)
			VALUES (
				pStartDate,
				pEndDate,
				NOW(),
				pCurrentUser
			);
		
SET State = 4;			               
			SELECT
				pID AS 'ID',
				'Periode Gaji Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
				
		ELSE
SET State = 5;
			UPDATE
				master_period
			SET
				StartDate = pStartDate,
				EndDate = pEndDate,
				ModifiedDate = NOW(),
				ModifiedBy = pCurrentUser
			WHERE
				PeriodID = pID;
				
SET State = 6;
			SELECT
				pID AS 'ID',
				'Periode Gaji Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
			
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
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
DROP PROCEDURE IF EXISTS spInsSupplier;

DELIMITER $$
CREATE PROCEDURE spInsSupplier (
	pID 			BIGINT, 
	pSupplierName 		VARCHAR(255),
	pAddress 	TEXT,
	pTelephone	VARCHAR(255),
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
			master_supplier
		WHERE
			TRIM(SupplierName) = TRIM(pSupplierName)
			AND SupplierID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Supplier sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_supplier
				(
					SupplierName,
					Address,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSupplierName,
					pAddress,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Supplier Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';		
			ELSE
SET State = 5;
				UPDATE
					master_supplier
				SET
					SupplierName = pSupplierName,
					Address = pAddress,
					Telephone = pTelephone,
					ModifiedDate = NOW(),
					ModifiedBy = pCurrentUser
				WHERE
					SupplierID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Supplier Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsUnit;

DELIMITER $$
CREATE PROCEDURE spInsUnit (
	pID				BIGINT, 
	pUnitName 	VARCHAR(255), 
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
			master_unit
		WHERE
			TRIM(UnitName) = TRIM(pUnitName)
			AND UnitID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Satuan sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_unit
				(
					UnitName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUnitName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Satuan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_unit
				SET
					UnitName = pUnitName,
					ModifiedDate = NOW(),
					ModifiedBy = pCurrentUser
				WHERE
					UnitID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Satuan Berhasil Diubah' AS 'Message',
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
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
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
					UserPassword,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
					pPassword,
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
					UserPassword = pPassword,
					ModifiedDate = NOW(),
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

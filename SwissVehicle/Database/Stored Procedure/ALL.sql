DROP PROCEDURE IF EXISTS spInsItem;

DELIMITER $$
CREATE PROCEDURE spInsItem (
	pID 			BIGINT, 
	pItemName 		VARCHAR(255),
	pItemCode		VARCHAR(255),
	pIsSecond		BIT,
	pPrice			DOUBLE,
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
			master_item
		WHERE
			(ItemName = pItemName
			OR ItemCode = pItemCode)
			AND ItemID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Nama/Kode barang sudah ada' AS 'Message',
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
					ItemCode,
					IsSecond,
					Price,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemName,
					pItemCode,
					pIsSecond,
					pPrice,
					NOW(),
					pCurrentUser
				);
					
			ELSE
SET State = 5;
				UPDATE
					master_item
				SET
					ItemName = pItemName,
					ItemCode = pItemCode,
					IsSecond = pIsSecond,
					Price = pPrice,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;
		
			END IF;
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
		SELECT
			pID AS 'ID',
			'Barang berhasil ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
SET State = 9;
		SELECT
			pID AS 'ID',
			'Barang berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
    COMMIT;
END;
$$
DELIMITER ;

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

DROP PROCEDURE IF EXISTS spInsSupplier;

DELIMITER $$
CREATE PROCEDURE spInsSupplier (
	pID 			BIGINT, 
	pSupplierName 	VARCHAR(255),
	pAddress		TEXT,
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
			master_supplier
		WHERE
			TRIM(SupplierName) = TRIM(pSupplierName)
			AND TRIM(Address) = TRIM(pAddress)
			AND TRIM(City) = TRIM(pCity)
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
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSupplierName,
					pAddress,
					pCity,
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
					City = pCity,
					Telephone = pTelephone,
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

DROP PROCEDURE IF EXISTS spInsUser;

DELIMITER $$
CREATE PROCEDURE spInsUser (
	pID 			BIGINT, 
	pUserName 		VARCHAR(255),
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
			(UserName = pUserName
			OR UserLogin = pUserLogin)
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
					IsActive,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
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

DROP PROCEDURE IF EXISTS spUpdUserPassword;

DELIMITER $$
CREATE PROCEDURE spUpdUserPassword (
	pID 			BIGINT, 
	pPassword 		VARCHAR(255),
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
			UPDATE
				master_user
			SET
				UserPassword = pPassword,
				ModifiedBy = pCurrentUser
			WHERE
				UserID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Password berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;

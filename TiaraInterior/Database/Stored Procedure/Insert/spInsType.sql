DROP PROCEDURE IF EXISTS spInsType;

DELIMITER $$
CREATE PROCEDURE spInsType (
	pID 			BIGINT, 
	pTypeName 		VARCHAR(255),
	pBrandID 		BIGINT,
	pUnitID			BIGINT,
	pReminderCount	INT,
	pBuyPrice		DOUBLE,
	pSalePrice		DOUBLE,
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
			master_type
		WHERE
			TRIM(TypeName) = TRIM(pTypeName)
			AND BrandID = pBrandID
			AND TypeID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Tipe sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_type
				(
					TypeName,
					BrandID,
					UnitID,
					ReminderCount,
					BuyPrice,
					SalePrice,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pTypeName,
					pBrandID,
					pUnitID,
					pReminderCount,
					pBuyPrice,
					pSalePrice,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Tipe Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_type
				SET
					TypeName = pTypeName,
					ReminderCount = pReminderCount,
					BrandID = pBrandID,
					UnitID = pUnitID,
					BuyPrice = pBuyPrice,
					SalePrice = pSalePrice,
					ModifiedBy = pCurrentUser
				WHERE
					TypeID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Tipe Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;

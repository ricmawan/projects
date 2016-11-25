DROP PROCEDURE IF EXISTS spInsFuelType;

DELIMITER $$
CREATE PROCEDURE spInsFuelType (
	pID 			BIGINT, 
	pFuelTypeName	VARCHAR(255),
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
			master_fueltype
		WHERE
			FuelTypeName = pFuelTypeName
			AND FuelTypeID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Jenis BBM sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
		
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_fueltype
				(
					FuelTypeName,
					Price,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pFuelTypeName,
					pPrice,
					NOW(),
					pCurrentUser
				);
					
			ELSE
SET State = 5;
				UPDATE
					master_fueltype
				SET
					FuelTypeName = pFuelTypeName,
					Price = pPrice,
					ModifiedBy = pCurrentUser
				WHERE
					FuelTypeID = pID;
		
			END IF;
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
		SELECT
			pID AS 'ID',
			'Jenis BBM berhasil ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
SET State = 9;
		SELECT
			pID AS 'ID',
			'Jenis BBM berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
    COMMIT;
END;
$$
DELIMITER ;

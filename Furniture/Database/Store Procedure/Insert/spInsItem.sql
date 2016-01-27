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

DROP PROCEDURE IF EXISTS spInsMenuList;

DELIMITER $$
CREATE PROCEDURE spInsMenuList (
	pID 				BIGINT, 
	pMenuListCategoryID	SMALLINT,
	pMenuName			VARCHAR(100),
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
			master_menulist
		WHERE
			MenuName = pMenuName
			AND MenuListID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Nama menu sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
		
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_menulist
				(
					MenuName,
					MenuListCategoryID,
					Price,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pMenuName,
					pMenuListCategoryID,
					pPrice,
					NOW(),
					pCurrentUser
				);
					
			ELSE
SET State = 5;
				UPDATE
					master_menulist
				SET
					MenuName = pMenuName,
					MenuListCategoryID = pMenuListCategoryID,
					Price = pPrice,
					ModifiedBy = pCurrentUser
				WHERE
					MenuListID = pID;
		
			END IF;
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
		SELECT
			pID AS 'ID',
			'Menu berhasil ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
SET State = 9;
		SELECT
			pID AS 'ID',
			'Menu berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
    COMMIT;
END;
$$
DELIMITER ;

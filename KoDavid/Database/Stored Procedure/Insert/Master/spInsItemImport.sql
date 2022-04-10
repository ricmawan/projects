/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the item
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsItemImport;

DELIMITER $$
CREATE PROCEDURE spInsItemImport (
	pID 				BIGINT, 
    pItemCode			VARCHAR(100),
	pItemName 			VARCHAR(255),
    pCategoryID			BIGINT,
    pUnitID				SMALLINT,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pQty1				DOUBLE,
    pPrice2				DOUBLE,
    pQty2				DOUBLE,
    pWeight				DOUBLE,
	pMinimumStock		DOUBLE,
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
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsItemImport', pCurrentUser);
        DELETE FROM temp_master_itemdetails;
		SELECT
			pID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
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
			TRIM(ItemCode) = TRIM(pItemCode)
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 2;
        
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
        
SET State = 3;

        SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_itemdetails
		WHERE
			TRIM(ItemDetailsCode) = TRIM(pItemCode)
		LIMIT 1;
        
SET State = 4;
		
        IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
		        
SET State = 5;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item
		WHERE
			TRIM(ItemName) = TRIM(pItemName)
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 6;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Nama Barang ', pItemName, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
        
SET State = 7;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_item
				(
                    ItemCode,
                    ItemName,
					CategoryID,
                    UnitID,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemCode,
					pItemName,
					pCategoryID,
                    pUnitID,
					pBuyPrice,
					pRetailPrice,
					pPrice1,
					pQty1,
					pPrice2,
					pQty2,
					pWeight,
					pMinimumStock,
					NOW(),
					pCurrentUser
				);
			
SET State = 8;

				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;                

SET State = 9;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			ELSE
            
SET State = 10;

				UPDATE
					master_item
				SET
					ItemCode = pItemCode,
                    ItemName = pItemName,
					CategoryID = pCategoryID,
                    UnitID = pUnitID,
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Qty1 = pQty1,
					Price2 = pPrice2,
					Qty2 = pQty2,
					Weight = pWeight,
					MinimumStock = pMinimumStock,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;

SET State = 11;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			END IF;
		END IF;

	COMMIT;
END;
$$
DELIMITER ;

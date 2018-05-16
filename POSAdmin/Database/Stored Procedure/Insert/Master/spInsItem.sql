/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the item
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsItem;

DELIMITER $$
CREATE PROCEDURE spInsItem (
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
    pItemDetails		TEXT,
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
		CALL spInsEventLog(@full_error, 'spInsItem', pCurrentUser);
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
		
        CREATE TEMPORARY TABLE IF NOT EXISTS temp_master_itemdetails
		(
			ItemDetailsID 		BIGINT,
			ItemID 				BIGINT,
			ItemDetailsCode		VARCHAR(100),
			UnitID				SMALLINT,
			ConversionQuantity	DOUBLE,
			BuyPrice			DOUBLE,
			RetailPrice			DOUBLE,
			Price1				DOUBLE,
			Qty1				DOUBLE,
			Price2				DOUBLE,
			Qty2				DOUBLE,
			Weight				DOUBLE,
			MinimumStock		DOUBLE
		);
        
SET State = 2;

		IF(pItemDetails <> "" ) THEN
			SET @query = CONCAT("INSERT INTO temp_master_itemdetails
								(
									ItemDetailsID,
									ItemID,
									ItemDetailsCode,
									UnitID,
									ConversionQuantity,
									BuyPrice,
									RetailPrice,
									Price1,
									Qty1,
									Price2,
									Qty2,
									Weight,
									MinimumStock
								)
								VALUES", pItemDetails);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;
       
SET State = 3;

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
			master_itemdetails
		WHERE
			TRIM(ItemDetailsCode) = TRIM(pItemCode)
		LIMIT 1;
        
SET State = 6;
		
        IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
		
SET State = 7;
		
        SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_itemdetails MID
            JOIN temp_master_itemdetails TMID
				ON TRIM(MID.ItemDetailsCode) = TRIM(TMID.ItemDetailsCode)
                AND MID.ItemDetailsID <> TMID.ItemDetailsID
		LIMIT 1;
        
SET State = 8;
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', GC.ItemDetailsCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State'
			FROM
				(
					SELECT
						TMID.ItemID,
						GROUP_CONCAT(TRIM(TMID.ItemDetailsCode) SEPARATOR ', ') ItemDetailsCode
					FROM
						master_itemdetails MID
						JOIN temp_master_itemdetails TMID
							ON TRIM(MID.ItemDetailsCode) = TRIM(TMID.ItemDetailsCode)
							AND MID.ItemDetailsID <> TMID.ItemDetailsID
					GROUP BY
						TMID.ItemID
				)GC;
		
			LEAVE StoredProcedure;
		END IF;
			
SET State = 9;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item MI
            JOIN temp_master_itemdetails TMID
				ON TRIM(MI.ItemCode) = TRIM(TMID.ItemDetailsCode)
		LIMIT 1;

SET State = 10;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', GC.ItemDetailsCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State'
			FROM
				(
					SELECT
						TMID.ItemID,
						GROUP_CONCAT(TRIM(TMID.ItemDetailsCode) SEPARATOR ', ') ItemDetailsCode
					FROM
						master_item MI
						JOIN temp_master_itemdetails TMID
							ON TRIM(MI.ItemCode) = TRIM(TMID.ItemDetailsCode)
					GROUP BY
						TMID.ItemID
				)GC;
		
			LEAVE StoredProcedure;
            
		END IF;
        
SET State = 11;

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
        
SET State = 12;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Nama Barang ', pItemName, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
        
SET State = 13;

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
			
SET State = 14;

				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;

SET State = 15;
				SET SQL_SAFE_UPDATES = 0;
                
				UPDATE temp_master_itemdetails
                SET ItemID = pID
                WHERE
					ItemDetailsID = 0;
				
                SET SQL_SAFE_UPDATES = 1;
                
SET State = 16;
				INSERT INTO master_itemdetails
                (
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
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
                SELECT
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
                    NOW(),
                    'Admin'
				FROM
					temp_master_itemdetails;
                
SET State = 17;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			ELSE
            
SET State = 18;

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

SET State = 19;

				UPDATE master_itemdetails MID
                JOIN temp_master_itemdetails TMID
					ON TMID.ItemDetailsID = MID.ItemDetailsID
				SET
					MID.ItemDetailsCode = TMID.ItemDetailsCode,
					MID.UnitID = TMID.UnitID,
					MID.ConversionQuantity = TMID.ConversionQuantity,
					MID.BuyPrice = TMID.BuyPrice,
					MID.RetailPrice = TMID.RetailPrice,
					MID.Price1 = TMID.Price1,
					MID.Qty1 = TMID.Qty1,
					MID.Price2 = TMID.Price2,
					MID.Qty2 = TMID.Qty2,
					MID.Weight = TMID.Weight,
					ModifiedBy = pCurrentUser;
                    
SET State = 20;
				
				DELETE FROM master_itemdetails
				WHERE 
					ItemDetailsID NOT IN(
											SELECT 
												TMID.ItemDetailsID
											FROM 
												temp_master_itemdetails TMID
										)
					AND ItemID = pID;
                                
SET State = 21;

				INSERT INTO master_itemdetails
                (
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
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
                SELECT
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
                    NOW(),
                    'Admin'
				FROM
					temp_master_itemdetails
				WHERE
					ItemDetailsID = 0;
                    
SET State = 22;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			END IF;
		END IF;
        
        DROP TEMPORARY TABLE temp_master_itemdetails;
        
	COMMIT;
END;
$$
DELIMITER ;

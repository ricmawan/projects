DROP PROCEDURE IF EXISTS spInsFirstStock;

DELIMITER $$
CREATE PROCEDURE spInsFirstStock (
	pID 					BIGINT,
    pFirstStockNumber		VARCHAR(100),
    pTransactionDate 		DATETIME,
	pFirstStockDetailsID	BIGINT,
    pBranchID				INT,
    pItemID					BIGINT,
    pItemDetailsID			BIGINT,
	pQuantity				DOUBLE,
    pBuyPrice				DOUBLE,
    pRetailPrice			DOUBLE,
    pPrice1					DOUBLE,
    pPrice2					DOUBLE,
    pCurrentUser			VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsFirstStock', pCurrentUser);
		SELECT
			pID AS 'ID',
            pFirstStockDetailsID AS 'FirstStockDetailsID',
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
			transaction_firststock
		WHERE
			TRIM(FirstStockNumber) = TRIM(pFirstStockNumber)
			AND FirstStockID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
                pFirstStockDetailsID AS 'FirstStockDetailsID',
				CONCAT('No. Invoice ', pFirstStockNumber, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pID = 0)	THEN /*Tambah baru*/
				INSERT INTO transaction_firststock
				(
                    FirstStockNumber,
                    TransactionDate,
					CreatedDate,
					CreatedBy
				)
				VALUES 
                (
					pFirstStockNumber,
					pTransactionDate,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;	               
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
                    
			ELSE
SET State = 5;
				UPDATE
					transaction_firststock
				SET
					FirstStockNumber = pFirstStockNumber,
                    TransactionDate = pTransactionDate,
					ModifiedBy = pCurrentUser
				WHERE
					FirstStockID = pID;
                    
			END IF;
            
SET State = 6;
			
			IF(pFirstStockDetailsID = 0) THEN
				INSERT INTO transaction_firststockdetails
                (
					FirstStockID,
                    ItemID,
                    ItemDetailsID,
                    BranchID,
                    Quantity,
                    BuyPrice,
                    RetailPrice,
                    Price1,
                    Price2,
                    CreatedDate,
                    CreatedBy
                )
                VALUES
                (
					pID,
                    pItemID,
                    pItemDetailsID,
                    pBranchID,
                    pQuantity,
                    pBuyPrice,
                    pRetailPrice,
                    pPrice1,
                    pPrice2,
                    NOW(),
                    pCurrentUser
                );
                
SET State = 7;
				
				SELECT
					LAST_INSERT_ID()
				INTO 
					pFirstStockDetailsID;
			
			ELSE
					
SET State = 8;
				
				UPDATE 
					transaction_firststockdetails
				SET
					ItemID = pItemID,
                    ItemDetailsID = pItemDetailsID,
                    BranchID = pBranchID,
                    Quantity = pQuantity,
                    BuyPrice = pBuyPrice,
                    RetailPrice = pRetailPrice,
                    Price1 = pPrice1,
                    Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					FirstStockDetailsID = pFirstStockDetailsID;
				
			END IF;
			
SET State = 9;

				UPDATE 
					master_item
				SET
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pItemID;
                    
SET State = 10;
			
				SELECT
					pID AS 'ID',
                    pFirstStockDetailsID AS 'FirstStockDetailsID',
					'Transaksi Berhasil Disimpan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
		END IF;
	COMMIT;
END;
$$
DELIMITER ;

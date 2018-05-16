DROP PROCEDURE IF EXISTS spInsPurchase;

DELIMITER $$
CREATE PROCEDURE spInsPurchase (
	pID 				BIGINT,
    pPurchaseNumber		VARCHAR(100),
    pSupplierID			BIGINT,
	pTransactionDate 	DATETIME,
	pPurchaseDetailsID	BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
    pItemDetailsID		BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pPrice2				DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsPurchase', pCurrentUser);
		SELECT
			pID AS 'ID',
            pPurchaseDetailsID AS 'PurchaseDetailsID',
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
			transaction_purchase
		WHERE
			TRIM(PurchaseNumber) = TRIM(pPurchaseNumber)
			AND PurchaseID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
                pPurchaseDetailsID AS 'PurchaseDetailsID',
				CONCAT('No. Invoice ', pPurchaseNumber, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pID = 0)	THEN /*Tambah baru*/
				INSERT INTO transaction_purchase
				(
                    PurchaseNumber,
                    SupplierID,
					TransactionDate,
					CreatedDate,
					CreatedBy
				)
				VALUES 
                (
					pPurchaseNumber,
					pSupplierID,
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
					transaction_purchase
				SET
					PurchaseNumber = pPurchaseNumber,
                    SupplierID = pSupplierID,
					TransactionDate = pTransactionDate,
					ModifiedBy = pCurrentUser
				WHERE
					PurchaseID = pID;
                    
			END IF;
            
SET State = 6;
			
			IF(pPurchaseDetailsID = 0) THEN
				INSERT INTO transaction_purchasedetails
                (
					PurchaseID,
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
					pPurchaseDetailsID;
			
			ELSE
					
SET State = 8;
				
				UPDATE 
					transaction_purchasedetails
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
					PurchaseDetailsID = pPurchaseDetailsID;
				
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
					ItemID = pItemID
                    AND pItemDetailsID IS NULL;
                    
SET State = 10;

				UPDATE 
					master_itemdetails
				SET
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					ItemDetailsID = pItemDetailsID
                    AND pItemDetailsID IS NOT NULL;
SET State = 11;
				
				SELECT
					pID AS 'ID',
                    pPurchaseDetailsID AS 'PurchaseDetailsID',
					'Transaksi Berhasil Disimpan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
		END IF;
	COMMIT;
END;
$$
DELIMITER ;

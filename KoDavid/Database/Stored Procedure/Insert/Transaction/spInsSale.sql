DROP PROCEDURE IF EXISTS spInsSale;

DELIMITER $$
CREATE PROCEDURE spInsSale (
	pID 				BIGINT,
	pSaleNumber			VARCHAR(100),
	pRetailFlag			BIT,
    pFinishFlag			BIT,
    pCustomerID			BIGINT,
	pTransactionDate 	DATETIME,
	pSaleDetailsID		BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pItemDetailsID		BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pSalePrice			DOUBLE,
	pDiscount			DOUBLE,
	pUserID				BIGINT,
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
		CALL spInsEventLog(@full_error, 'spInsSale', pCurrentUser);
		SELECT
			pID AS 'ID',
            pSaleDetailsID AS 'SaleDetailsID',
			pSaleNumber AS 'SaleNumber',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pID = 0)	THEN /*Tambah baru*/
			SELECT
				CONCAT(RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(SaleNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
			FROM
				transaction_sale TS
			WHERE
				MONTH(TS.TransactionDate) = MONTH(pTransactionDate)
				AND YEAR(TS.TransactionDate) = YEAR(pTransactionDate)
			INTO 
				pSaleNumber;
				
SET State = 2;
			INSERT INTO transaction_sale
			(
				SaleNumber,
				RetailFlag,
                FinishFlag,
				CustomerID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pSaleNumber,
				pRetailFlag,
                pFinishFlag,
				pCustomerID,
				pTransactionDate,
				NOW(),
				pCurrentUser
			);
		
SET State = 3;	               
			SELECT
				LAST_INSERT_ID()
			INTO 
				pID;
				
		ELSE
		
SET State = 4;
			UPDATE
				transaction_sale
			SET
				customerID = pCustomerID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pSaleDetailsID = 0) THEN
			INSERT INTO transaction_saledetails
			(
				SaleID,
				ItemID,
                ItemDetailsID,
				BranchID,
				Quantity,
				BuyPrice,
				SalePrice,
				Discount,
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
				pSalePrice,
				pDiscount,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pSaleDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_saledetails
			SET
				ItemID = pItemID,
                ItemDetailsID = pItemDetailsID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				BuyPrice = pBuyPrice,
				SalePrice = pSalePrice,
				Discount = pDiscount,
				ModifiedBy = pCurrentUser
			WHERE
				SaleDetailsID = pSaleDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pSaleDetailsID AS 'SaleDetailsID',
			pSaleNumber AS 'SaleNumber',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;

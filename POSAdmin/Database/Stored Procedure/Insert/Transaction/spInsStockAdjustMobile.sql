DROP PROCEDURE IF EXISTS spInsStockAdjustMobile;

DELIMITER $$
CREATE PROCEDURE spInsStockAdjustMobile (
	pBranchID					INT,
	pTransactionDate 			DATETIME,
	pStockAdjustData			TEXT,
    pCurrentUser				VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	DECLARE pStockAdjustID BIGINT;
	DECLARE pStockAdjustDetailsID BIGINT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsStockAdjustMobile', pCurrentUser);
		SELECT
			pStockAdjustID AS 'ID',
            pStockAdjustDetailsID AS 'StockAdjustDetailsID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF EXISTS(
					SELECT
						1 
					FROM
						transaction_stockadjust
					WHERE
						DATE_FORMAT(TransactionDate, '%Y-%m-%d') = pTransactionDate
				)
		THEN /*Tambah baru*/
			SELECT
				StockAdjustID
			FROM
				transaction_stockadjust
			WHERE
				DATE_FORMAT(TransactionDate, '%Y-%m-%d') = pTransactionDate
			INTO 
				pStockAdjustID;
		ELSE
		
SET State = 2;
			INSERT INTO transaction_stockadjust
			(
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pTransactionDate,
				NOW(),
				pCurrentUser
			);
		
SET State = 3;	               
			SELECT
				LAST_INSERT_ID()
			INTO 
				pStockAdjustID;

		END IF;
		
SET State = 4;

		IF(pStockAdjustData <> "" ) THEN
			SET @query = CONCAT("INSERT INTO transaction_stockadjustdetails
								(
									StockAdjustID,
									ItemID,
									BranchID,
									Quantity,
									AdjustedQuantity,
									BuyPrice,
									SalePrice,
									CreatedDate,
									CreatedBy
								)
								VALUES", REPLACE(REPLACE(pStockAdjustData, ', UserLogin)', CONCAT(', "', pCurrentUser, '")')), '(StockAdjustID,', CONCAT('(', pStockAdjustID, ','))
								);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
			
		END IF;

SET State = 5;
			
		SELECT
			LAST_INSERT_ID()
		INTO 
			pStockAdjustDetailsID;
	
SET State = 6;

		SELECT
			pStockAdjustID AS 'ID',
			pStockAdjustDetailsID AS 'StockAdjustDetailsID',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
			
	COMMIT;
END;
$$
DELIMITER ;

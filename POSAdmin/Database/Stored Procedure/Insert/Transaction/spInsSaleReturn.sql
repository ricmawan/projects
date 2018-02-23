/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert sale return
Created Date: 23 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsSaleReturn;

DELIMITER $$
CREATE PROCEDURE spInsSaleReturn (
	pID 				BIGINT, 
	pSaleID 			BIGINT,
	pTransactionDate 	DATETIME,
	pSaleReturnData 	TEXT,
	pIsEdit				INT,
    pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE PassValidate INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsSaleReturn', pCurrentUser);
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
		IF(pIsEdit = 0)	THEN /*Tambah baru*/
			INSERT INTO transaction_salereturn
			(
				SaleID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES (
				pSaleID,
				pTransactionDate,
				NOW(),
				pCurrentUser
			);
			
SET State = 2;			               
			SELECT
				LAST_INSERT_ID()
			INTO 
				pID;
			
		ELSE
			
SET State = 3;
			UPDATE
				transaction_salereturn
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleReturnID = pID;
	
		END IF;
	
SET State = 4;

		DELETE 
		FROM 
			transaction_salereturndetails
		WHERE
			SaleReturnID = pID;
					
SET State = 5;
		IF(pSaleReturnData <> "" ) THEN
			SET @query = CONCAT("INSERT INTO transaction_salereturndetails
								(
									SaleReturnID,
									ItemID,
									BranchID,
									Quantity,
									BuyPrice,
									SalePrice,
									CreatedDate,
									CreatedBy
								)
								VALUES", REPLACE(REPLACE(pSaleReturnData, ', UserLogin)', CONCAT(', "', pCurrentUser, '")')), '(0,', CONCAT('(', pID, ','))
								);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
			
		END IF;

SET State = 6;

		IF(pIsEdit = 0) THEN
			SELECT
				pID AS 'ID',
				'Retur Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		ELSE
	
SET State = 7;

			SELECT
				pID AS 'ID',
				'Retur Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		END IF;
    COMMIT;
END;
$$
DELIMITER ;

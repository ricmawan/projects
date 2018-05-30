/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select firststock transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelFirstStock;

DELIMITER $$
CREATE PROCEDURE spSelFirstStock (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelFirstStock', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_firststock FS
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						FS.FirstStockID,
                        FS.FirstStockNumber,
                        DATE_FORMAT(FS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        FS.TransactionDate PlainTransactionDate,
                        IFNULL(FSD.Total, 0) Total
					FROM
						transaction_firststock FS
						LEFT JOIN
                        (
							SELECT
								FS.FirstStockID,
                                SUM(FSD.Quantity * FSD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
							FROM
								transaction_firststock FS
                                LEFT JOIN transaction_firststockdetails FSD
									ON FS.FirstStockID = FSD.FirstStockID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = FSD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								FS.FirstStockID
                        )FSD
							ON FSD.FirstStockID = FS.FirstStockID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

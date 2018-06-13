/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPickUp;

DELIMITER $$
CREATE PROCEDURE spSelPickUp (
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
		CALL spInsEventLog(@full_error, 'spSelPickUp', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_pick TSR
						JOIN transaction_booking TS
							ON TS.BookingID = TSR.BookingID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TSR.PickID,
                        TS.BookingNumber,
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TSR.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSRD.Total, 0) Total
					FROM
						transaction_pick TSR
						JOIN transaction_booking TS
							ON TS.BookingID = TSR.BookingID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN
                        (
							SELECT
								TSR.PickID,
                                SUM(TSRD.Quantity * (TSRD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSRD.Discount)) Total
							FROM
								transaction_booking TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								JOIN transaction_pick TSR
									ON TS.BookingID = TSR.BookingID
                                LEFT JOIN transaction_pickdetails TSRD
									ON TSR.PickID = TSRD.PickID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSRD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TSRD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TSR.PickID
                        )TSRD
							ON TSRD.PickID = TSR.PickID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

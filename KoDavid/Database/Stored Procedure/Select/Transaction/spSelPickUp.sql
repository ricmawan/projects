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
						TP.PickID,
                        TB.BookingNumber,
                        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TP.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TPD.Total, 0) Total
					FROM
						transaction_pick TP
						JOIN transaction_booking TB
							ON TB.BookingID = TP.BookingID
                        JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN
                        (
							SELECT
								TP.PickID,
                                SUM(TPD.Quantity * (TPD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - IFNULL(TPD.Discount, 0))) Total
							FROM
								transaction_booking TB
                                JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
								JOIN transaction_pick TP
									ON TB.BookingID = TP.BookingID
                                LEFT JOIN transaction_pickdetails TPD
									ON TP.PickID = TPD.PickID
								LEFT JOIN master_item MI
									ON MI.ItemID = TPD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TPD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TP.PickID
                        )TPD
							ON TPD.PickID = TP.PickID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

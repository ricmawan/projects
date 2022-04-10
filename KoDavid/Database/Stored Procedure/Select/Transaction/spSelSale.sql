/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSale;

DELIMITER $$
CREATE PROCEDURE spSelSale (
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
		CALL spInsEventLog(@full_error, 'spSelSale', pCurrentUser);
	END;
	
SET State = 1;
	
    DELETE FROM 
		transaction_sale
    WHERE
		FinishFlag = 0
        AND DATE_FORMAT(TransactionDate, '%Y-%m-%d') <> DATE_FORMAT(NOW(), '%Y-%m-%d');
    
SET State = 2;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TS.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSD.Total, 0) Total,
						IFNULL(TSD.Weight, 0) Weight,
						TS.RetailFlag,
						IFNULL(TS.Payment, 0) Payment,
                        IFNULL(PT.PaymentTypeName, '') PaymentTypeName,
                        CASE
							WHEN FinishFlag = 0
                            THEN 'Belum Selesai'
                            ELSE 'Selesai'
						END Status,
                        IFNULL(PT.PaymentTypeID, 1) PaymentTypeID,
						TS.Discount
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_paymenttype PT
							ON PT.PaymentTypeID = TS.PaymentTypeID
						LEFT JOIN
                        (
							SELECT
								TS.SaleID,
                                SUM(TSD.Quantity * (TSD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSD.Discount)) Total,
								SUM(TSD.Quantity * MI.Weight * IFNULL(MID.ConversionQuantity, 1)) Weight
							FROM
								transaction_sale TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
                                LEFT JOIN transaction_saledetails TSD
									ON TS.SaleID = TSD.SaleID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TSD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TS.SaleID
                        )TSD
							ON TSD.SaleID = TS.SaleID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCustomerPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelCustomerPurchaseReport (
	pCustomerID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
						SUM(Total) GrandTotal
					FROM
						(
							SELECT
								SUM(SD.Quantity * SD.SalePrice - SD.Discount) Total
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
							WHERE 
								TS.CustomerID = ", pCustomerID ,"
								AND CAST(TS.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TS.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
		                    SELECT
								-SUM(SRD.Quantity * SRD.SalePrice) Total
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
							WHERE 
								TS.CustomerID = ", pCustomerID ,"
								AND CAST(TSR.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TSR.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SUM(SD.Quantity * SD.SalePrice - SD.Discount) Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
					WHERE 
						TS.CustomerID = ", pCustomerID ,"
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
						-SUM(SRD.Quantity * SRD.SalePrice) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
					WHERE 
						TS.CustomerID = ", pCustomerID ,"
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

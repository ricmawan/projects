/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select purchase transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchase;

DELIMITER $$
CREATE PROCEDURE spSelPurchase (
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
		CALL spInsEventLog(@full_error, 'spSelPurchase', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_purchase TP
                        JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
                        TP.PurchaseNumber,
                        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TransactionDate PlainTransactionDate,
                        MS.SupplierID,
                        MS.SupplierName,
						IFNULL(TPD.Total, 0) Total
					FROM
						transaction_purchase TP
                        JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN
                        (
							SELECT
								TP.PurchaseID,
                                SUM(TPD.Quantity * TPD.BuyPrice) Total
							FROM
								transaction_purchase TP
                                JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
                                LEFT JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TP.PurchaseID
                        )TPD
							ON TPD.PurchaseID = TP.PurchaseID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

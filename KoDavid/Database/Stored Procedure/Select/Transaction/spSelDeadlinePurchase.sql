/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDeadlinePurchase;

DELIMITER $$
CREATE PROCEDURE spSelDeadlinePurchase (
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
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDeadlinePurchase', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								PD.TotalPayment
							FROM
								transaction_purchase TP
								JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
								LEFT JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TPD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) TotalPayment
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'P'
									GROUP BY
										TransactionID
								)PD
									ON PD.TransactionID = TP.PurchaseID
							WHERE 
								", pWhere, "
								AND TP.PaymentTypeID = 2
								AND DATE_FORMAT(TP.Deadline, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')
							GROUP BY
								TP.PurchaseID
							HAVING
								SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(PD.TotalPayment, 0) > 0
						)TP"
				);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseNumber,
                        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
                        DATE_FORMAT(TP.Deadline, '%d-%m-%Y') Deadline,
                        MS.SupplierName,
                        SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total,
						IFNULL(PD.TotalPayment, 0) TotalPay,
                        PD.TotalPayment,
                        SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(PD.TotalPayment, 0) Debt
					FROM
						transaction_purchase TP
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN transaction_purchasedetails TPD
							ON TP.PurchaseID = TPD.PurchaseID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = TPD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) TotalPayment
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'P'
							GROUP BY
								TransactionID
						)PD
							ON PD.TransactionID = TP.PurchaseID
					WHERE 
						", pWhere, "
						AND TP.PaymentTypeID = 2
						AND DATE_FORMAT(TP.Deadline, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')
					GROUP BY
						TP.PurchaseID
					HAVING
						SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(PD.TotalPayment, 0) > 0 
					  ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END;
$$
DELIMITER ;

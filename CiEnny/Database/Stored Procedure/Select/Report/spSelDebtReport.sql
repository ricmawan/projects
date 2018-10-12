/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDebtReport;

DELIMITER $$
CREATE PROCEDURE spSelDebtReport (
	pFromDate		DATE,
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
		CALL spInsEventLog(@full_error, 'spSelDebtReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
                        SUM(Debt) GrandTotal
					FROM
						(
							SELECT
								SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) Debt
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails PD
									ON PD.PurchaseID = TP.PurchaseID
								JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = PD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'P'
                                        AND PD.PaymentDate <= '", pFromDate, "'
									GROUP BY
										TransactionID
								)TPD
									ON TPD.TransactionID = TP.PurchaseID
							WHERE 
								TP.PaymentTypeID = 2
                                AND TP.TransactionDate <= '", pFromDate, "'
								AND ", pWhere, "
							GROUP BY
								TP.PurchaseID,
								TP.PurchaseNumber,
								TP.TransactionDate,
								MS.SupplierID,
								MS.SupplierName,
								TPD.Amount
							HAVING
								SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) > 0
						) TP"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
						TP.PurchaseNumber,
						DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
						TP.TransactionDate PlainTransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalPurchase,
						IFNULL(TPD.Amount, 0) TotalPayment,
					    SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) Debt,
                        'Pembelian' TransactionType,
					    TPD.Amount
					FROM
						transaction_purchase TP
						JOIN transaction_purchasedetails PD
							ON PD.PurchaseID = TP.PurchaseID
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = PD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'P'
								AND PD.PaymentDate <= '", pFromDate, "'
							GROUP BY
								TransactionID
						)TPD
							ON TPD.TransactionID = TP.PurchaseID
					WHERE 
						TP.PaymentTypeID = 2
						AND TP.TransactionDate <= '", pFromDate, "'
						AND ", pWhere, "
					GROUP BY
						TP.PurchaseID,
						TP.PurchaseNumber,
						TP.TransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						TPD.Amount
					HAVING
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) > 0
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

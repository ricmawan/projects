/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDebtPayment;

DELIMITER $$
CREATE PROCEDURE spSelDebtPayment (
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
		CALL spInsEventLog(@full_error, 'spSelDebtPayment', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(

							SELECT
								1 AS nRows
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails PD
									ON TP.PurchaseID = PD.PurchaseID
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
									GROUP BY
										TransactionID
								)TPM
									ON TPM.TransactionID = TP.PurchaseID
							WHERE 
								TP.PaymentTypeID = 2
								AND ", pWhere, "
							GROUP BY
								TP.PurchaseID,
								TPM.Amount
							HAVING
								SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(TPM.Amount, 0) > 0
						) PD"
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
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total,
						IFNULL(TPM.Amount, 0) TotalPayment,
					    SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(TPM.Amount, 0) Debit,
                        'P' TransactionType,
                        TPM.Amount AmountDoang
					FROM
						transaction_purchase TP
						JOIN transaction_purchasedetails PD
							ON TP.PurchaseID = PD.PurchaseID
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
							GROUP BY
								TransactionID
						)TPM
							ON TPM.TransactionID = TP.PurchaseID
					WHERE 
						TP.PaymentTypeID = 2
						AND ", pWhere, "
					GROUP BY
						TP.PurchaseID,
						TP.PurchaseNumber,
						TP.TransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						IFNULL(TPM.Amount, 0),
						TPM.Amount
					HAVING
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(TPM.Amount, 0) > 0
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

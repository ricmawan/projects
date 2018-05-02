/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPayment;

DELIMITER $$
CREATE PROCEDURE spSelPayment (
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
		CALL spInsEventLog(@full_error, 'spSelPayment', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								TS.SaleID,
							    IFNULL(TS.Payment, 0) Payment,
							    IFNULL(TP.Amount, 0) Amount,
							    0 PickQuantity
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'S'
									GROUP BY
										TransactionID
								)TP
									ON TP.TransactionID = TS.SaleID
							WHERE 
								TS.PaymentTypeID = 2
								AND ", pWhere, "
							GROUP BY
								TS.SaleID,
								TS.Payment,
								TP.Amount
							HAVING
								SUM(SD.Quantity * SD.SalePrice  - SD.Discount) > (Amount + Payment)
							UNION ALL
		                    SELECT
								TB.BookingID,
								IFNULL(TB.Payment, 0) Payment,
								IFNULL(TP.Amount, 0) Amount,
							    IFNULL(PK.Quantity, 0) PickQuantity
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON BD.BookingID = TB.BookingID
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'B'
									GROUP BY
										TransactionID
								)TP
									ON TP.TransactionID = TB.BookingID
								LEFT JOIN
							    (
									SELECT
										TB.BookingID,
							            SUM(PD.Quantity) Quantity
									FROM
										transaction_pickdetails PD
							            JOIN transaction_bookingdetails BD
											ON PD.BookingDetailsID = BD.BookingDetailsID
										JOIN transaction_booking TB
											ON TB.BookingID = BD.BookingID
									GROUP BY
										TB.BookingID
								)PK
									ON TB.BookingID = PK.BookingID
							WHERE
								", pWhere2, "
							GROUP BY
								TB.BookingID,
								TB.Payment,
								TP.Amount,
								PK.Quantity
							HAVING
								SUM(BD.Quantity * BD.BookingPrice  - BD.Discount) > (Amount + Payment)
							    OR SUM(BD.Quantity) > PickQuantity
						) TS"
					);
                       
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
						SUM(SD.Quantity * SD.SalePrice  - SD.Discount) Total,
						IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
					    IFNULL(TS.Payment, 0) Payment,
					    IFNULL(TP.Amount, 0) Amount,
					    0 PickQuantity
					FROM
						transaction_sale TS
						JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'S'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TS.SaleID
					WHERE 
						TS.PaymentTypeID = 2
						AND ", pWhere, "
					GROUP BY
						TS.SaleID,
						TS.SaleID,
						TS.SaleNumber,
						TS.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						IFNULL(TS.Payment, 0),
					    IFNULL(TP.Amount, 0)
					HAVING
						SUM(SD.Quantity * SD.SalePrice  - SD.Discount) > (Amount + Payment)
					UNION ALL
                    SELECT
						TB.BookingID,
						TB.BookingNumber,
						DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
						TB.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(BD.Quantity * BD.BookingPrice  - BD.Discount) Total,
						IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
						IFNULL(TB.Payment, 0) Payment,
						IFNULL(TP.Amount, 0) Amount,
					    IFNULL(PK.Quantity, 0) PickQuantity
					FROM
						transaction_booking TB
						JOIN transaction_bookingdetails BD
							ON BD.BookingID = TB.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'B'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TB.BookingID
						LEFT JOIN
					    (
							SELECT
								TB.BookingID,
					            SUM(PD.Quantity) Quantity
							FROM
								transaction_pickdetails PD
					            JOIN transaction_bookingdetails BD
									ON PD.BookingDetailsID = BD.BookingDetailsID
								JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID
							GROUP BY
								TB.BookingID
						)PK
							ON TB.BookingID = PK.BookingID
					WHERE
						", pWhere2, "
					GROUP BY
						TB.BookingID,
						TB.BookingNumber,
						TB.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						IFNULL(TB.Payment, 0),
						IFNULL(TP.Amount, 0),
					    IFNULL(PK.Quantity, 0)
					HAVING
						SUM(BD.Quantity * BD.BookingPrice  - BD.Discount) > (Amount + Payment)
					    OR SUM(BD.Quantity) > PickQuantity
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

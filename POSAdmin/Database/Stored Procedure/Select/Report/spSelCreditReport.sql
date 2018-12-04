/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCreditReport;

DELIMITER $$
CREATE PROCEDURE spSelCreditReport (
	pFromDate		DATE,
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
		CALL spInsEventLog(@full_error, 'spSelCreditReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
                        SUM(Credit) GrandTotal
					FROM
						(
							SELECT
								SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TS.Discount, 0)) Credit
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'S'
                                        AND PD.PaymentDate <= '", pFromDate, "'
									GROUP BY
										TransactionID
								)TP
									ON TP.TransactionID = TS.SaleID
							WHERE 
								TS.PaymentTypeID = 2
                                AND TS.TransactionDate <= '", pFromDate, "'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID,
								TS.SaleNumber,
								TS.TransactionDate,
								MC.CustomerID,
								MC.CustomerName,
								TS.Payment,
								TP.Amount,
								TS.Discount
							HAVING
								SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TS.Discount, 0)) > 0
							UNION ALL
		                    SELECT
								SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TB.Discount, 0))
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON BD.BookingID = TB.BookingID
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'B'
                                        AND PD.PaymentDate <= '", pFromDate, "'
									GROUP BY
										TransactionID
								)TP
									ON TP.TransactionID = TB.BookingID
							WHERE
								TB.PaymentTypeID = 2
                                AND TB.TransactionDate <= '", pFromDate, "'
								AND ", pWhere2, "
							GROUP BY
								TB.BookingID,
								TB.BookingNumber,
								TB.TransactionDate,
								MC.CustomerID,
								MC.CustomerName,
								TB.Payment,
								TP.Amount,
								TB.Discount
							HAVING
								SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TB.Discount, 0)) > 0
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
						SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  - SD.Discount)) TotalSale,
						IFNULL(TS.Discount, 0) Discount,
						IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
					    SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TS.Discount, 0)) Credit,
                        'Penjualan' TransactionType,
                        TS.Payment,
					    TP.Amount
					FROM
						transaction_sale TS
						JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'S'
                                AND PD.PaymentDate <= '", pFromDate, "'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TS.SaleID
					WHERE 
						TS.PaymentTypeID = 2
                        AND TS.TransactionDate <= '", pFromDate, "'
						AND ", pWhere, "
					GROUP BY
						TS.SaleID,
						TS.SaleNumber,
						TS.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						TS.Payment,
					    TP.Amount,
						TS.Discount
					HAVING
						SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TS.Discount, 0)) > 0
					UNION ALL
                    SELECT
						TB.BookingID,
						TB.BookingNumber,
						DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
						TB.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) TotalSale,
						IFNULL(TB.Discount, 0) Discount,
						IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TS.Discount, 0)),
                        'Pemesanan' TransactionType,
						TB.Payment,
					    TP.Amount
					FROM
						transaction_booking TB
						JOIN transaction_bookingdetails BD
							ON BD.BookingID = TB.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = BD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'B'
                                AND PD.PaymentDate <= '", pFromDate, "'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TB.BookingID
					WHERE
						TB.PaymentTypeID = 2
                        AND TB.TransactionDate <= '", pFromDate, "'
						AND ", pWhere2, "
					GROUP BY
						TB.BookingID,
						TB.BookingNumber,
						TB.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						TB.Payment,
					    TP.Amount,
						TB.Discount
					HAVING
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) + IFNULL(TB.Discount, 0)) > 0
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

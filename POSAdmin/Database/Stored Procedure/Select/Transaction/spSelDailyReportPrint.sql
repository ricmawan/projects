/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDailyReportPrint;

DELIMITER $$
CREATE PROCEDURE spSelDailyReportPrint (
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
		CALL spInsEventLog(@full_error, 'spSelDailyReportPrint', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		1 UnionLevel,
		IFNULL(SFB.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(FB.FirstBalanceAmount) Amount
			FROM
				transaction_firstbalance FB
				JOIN master_user MUS
					ON MUS.UserID = FB.UserID
			WHERE
				CAST(FB.TransactionDate AS DATE) = CAST(NOW() AS DATE)
				AND FB.CreatedBy = pCurrentUser
		)SFB
	UNION ALL
	SELECT
		2 UnionLevel,
		IFNULL(S.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(STS.Amount) Amount
			FROM
				(
					SELECT
						SUM(TSD.Quantity * (TSD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSD.Discount)) - TS.Discount Amount
					FROM
						transaction_sale TS
						LEFT JOIN transaction_saledetails TSD
							ON TS.SaleID = TSD.SaleID
						LEFT JOIN master_item MI
							ON MI.ItemID = TSD.ItemID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = TSD.ItemDetailsID
					WHERE
						DATE_FORMAT(TS.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						AND TS.PaymentTypeID = 1
						AND TS.CreatedBy = pCurrentUser
					GROUP BY
						TS.SaleID,
						TS.Discount
				)STS
		)S
	UNION ALL
    SELECT
		3 UnionLevel,
		IFNULL(B.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(STB.Amount) Amount
			FROM
				(
					SELECT
						SUM(TBD.Quantity * (TBD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - TBD.Discount)) - TB.Discount Amount
					FROM
						transaction_booking TB
						LEFT JOIN transaction_bookingdetails TBD
							ON TB.BookingID = TBD.BookingID
						LEFT JOIN master_item MI
							ON MI.ItemID = TBD.ItemID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = TBD.ItemDetailsID
					WHERE
						DATE_FORMAT(TB.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						AND TB.PaymentTypeID = 1
						AND TB.CreatedBy = pCurrentUser
					GROUP BY
						TB.BookingID,
						TB.Discount
				)STB
		)B
	UNION ALL
    SELECT
		4 UnionLevel,
		-IFNULL(SR.Amount, 0)
	FROM
		(
			SELECT
				SUM(TSRD.Quantity * TSRD.SalePrice) Amount
			FROM
				transaction_sale TS
				JOIN transaction_salereturn TSR
					ON TS.SaleID = TSR.SaleID
				LEFT JOIN transaction_salereturndetails TSRD
					ON TSR.SaleReturnID = TSRD.SaleReturnID
				LEFT JOIN master_item MI
					ON MI.ItemID = TSRD.ItemID
			WHERE
				DATE_FORMAT(TSR.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND TS.CreatedBy = pCurrentUser
		)SR
	UNION ALL
    SELECT
		5 UnionLevel,
		IFNULL(DP.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(T.Amount) Amount
			FROM
				(
					SELECT
						SUM(TS.Payment) Amount
					FROM
						transaction_sale TS
					WHERE
						DATE_FORMAT(TS.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						AND TS.PaymentTypeID = 2
                        AND TS.CreatedBy = pCurrentUser
					UNION ALL
					SELECT
						SUM(TB.Payment) Amount
					FROM
						transaction_booking TB
					WHERE
						DATE_FORMAT(TB.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						AND TB.PaymentTypeID = 2
                        AND TB.CreatedBy = pCurrentUser
				)T
		)DP
	UNION ALL
    SELECT
		6 UnionLevel,
		IFNULL(PD.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(PD.Amount) Amount
			FROM
				transaction_paymentdetails PD
			WHERE
				DATE_FORMAT(PD.PaymentDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
				AND PD.TransactionType IN('S', 'B')
                AND PD.CreatedBy = pCurrentUser
		)PD;

END;
$$
DELIMITER ;

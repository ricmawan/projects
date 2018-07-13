/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelTopSellingReport;

DELIMITER $$
CREATE PROCEDURE spSelTopSellingReport (
	pCategoryID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelTopSellingReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemName,
        IFNULL(S.Quantity, 0) + IFNULL(B.Quantity, 0) - IFNULL(SR.Quantity, 0) SellingCount
	FROM
		master_item MI
        LEFT JOIN
        (
			SELECT
				MI.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_sale TS
                JOIN transaction_saledetails SD
					ON SD.SaleID = TS.SaleID
				JOIN master_item MI
					ON MI.ItemID = SD.ItemID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = SD.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
                AND CAST(TS.TransactionDate AS DATE) >= pFromDate
				AND CAST(TS.TransactionDate AS DATE) <= pToDate
			GROUP BY
				MI.ItemID
		)S
			ON MI.ItemID = S.ItemID
		LEFT join
        (
            SELECT
				MI.ItemID,
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_booking TB
                JOIN transaction_bookingdetails BD
					ON TB.BookingID = BD.BookingID
				JOIN master_item MI
					ON MI.ItemID = BD.ItemID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = BD.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
                AND CAST(TB.TransactionDate AS DATE) >= pFromDate
				AND CAST(TB.TransactionDate AS DATE) <= pToDate
			GROUP BY
				MI.ItemID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
        (
            SELECT
				MI.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CAST(SR.TransactionDate AS DATE) >= pFromDate
				AND CAST(SR.TransactionDate AS DATE) <= pToDate
			GROUP BY
				MI.ItemID
        )SR
			ON SR.ItemID = MI.ItemID
	WHERE
		CASE
			WHEN pCategoryID = 0
			THEN MI.CategoryID
			ELSE pCategoryID
		END = MI.CategoryID
	ORDER BY
		SellingCount DESC
	LIMIT
		0, 10;
    
    
END;
$$
DELIMITER ;

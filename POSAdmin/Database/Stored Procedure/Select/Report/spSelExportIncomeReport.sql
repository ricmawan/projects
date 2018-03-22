/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportIncomeReport;

DELIMITER $$
CREATE PROCEDURE spSelExportIncomeReport (
	pBranchID		INT,
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
		CALL spInsEventLog(@full_error, 'spSelExportIncomeReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        SD.BuyPrice,
        (SD.Quantity * SD.BuyPrice) TotalBuy,
        SD.SalePrice,
        SD.Discount,
        ((SD.Quantity * SD.SalePrice) - SD.Discount) TotalSale,
		(SD.Quantity * SD.SalePrice) - (SD.Quantity * SD.BuyPrice) Income
    FROM
		transaction_sale TS
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
	WHERE
		SD.BranchID = pBranchID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber) SaleNumber,
        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        SRD.BuyPrice,
		(SRD.Quantity * SRD.BuyPrice) TotalBuy,
        SRD.SalePrice,
        0 Discount,
        (SRD.Quantity * SRD.SalePrice) TotalSale,
        -((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)) Income
    FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
	WHERE
		SRD.BranchID = pBranchID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;

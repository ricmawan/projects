/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleNumber
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetailsByNumber;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetailsByNumber (
	pSaleNumber		VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetailsByNumber', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.SaleID,
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        (SD.Quantity * IFNULL(MID.ConversionQuantity, 1) - IFNULL(TSR.Quantity, 0)) Quantity,
        (SD.BuyPrice / IFNULL(MID.ConversionQuantity, 1)) BuyPrice,
        (SD.SalePrice / IFNULL(MID.ConversionQuantity, 1)) SalePrice,
        MC.CustomerName,
        MU.UnitName
	FROM
		transaction_sale TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		JOIN master_unit MU
			ON MI.UnitID = MU.UnitID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN
		(
			SELECT
				SR.SaleID,
				SRD.ItemID,
				SRD.SaleDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
			GROUP BY
				SR.SaleID,
				SRD.ItemID,
				SRD.SaleDetailsID
		)TSR
			ON TSR.SaleID = TS.SaleID
			AND MI.ItemID = TSR.ItemID
			AND TSR.SaleDetailsID = SD.SaleDetailsID
	WHERE
		TRIM(TS.SaleNumber) = TRIM(pSaleNumber)
	ORDER BY
		SD.SaleDetailsID;
        
END;
$$
DELIMITER ;

/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetails;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetails (
	pSaleID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        SD.Quantity,
        SD.BuyPrice,
        SD.SalePrice,
		SD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight
	FROM
		transaction_saledetails SD
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
	WHERE
		SD.SaleID = pSaleID
	ORDER BY
		SD.SaleDetailsID;
        
END;
$$
DELIMITER ;

/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockAdjustDetails;

DELIMITER $$
CREATE PROCEDURE spSelStockAdjustDetails (
	pStockAdjustID		BIGINT,
    pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelStockAdjustDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SA.StockAdjustID,
		SAD.StockAdjustDetailsID,
		MI.ItemID,
		SAD.BranchID,
		CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
		MI.ItemCode,
        MI.ItemName,
        SAD.Quantity,
        SAD.AdjustedQuantity
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
        JOIN master_branch MB
			ON MB.BranchID = SAD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
	WHERE
		SA.StockAdjustID = pStockAdjustID
	ORDER BY
		SAD.StockAdjustDetailsID;
        
END;
$$
DELIMITER ;

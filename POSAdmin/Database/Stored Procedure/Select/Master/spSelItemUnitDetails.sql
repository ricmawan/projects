/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemUnitDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemUnitDetails (
	pItemID			BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelItemUnitDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		ItemDetailsID,
		ItemID,
		ItemDetailsCode,
		UnitID,
		ConversionQuantity
		/*BuyPrice,
		RetailPrice,
		Price1,
		Qty1,
		Price2,
		Qty2,
		Weight,
		MinimumStock*/
	FROM
		master_itemdetails MID
	WHERE
		MID.ItemID = pItemID
	ORDER BY
		MID.ItemDetailsID;
        
END;
$$
DELIMITER ;

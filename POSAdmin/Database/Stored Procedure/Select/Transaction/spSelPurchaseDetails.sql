/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select purchase details by PurchaseID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseDetails;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseDetails (
	pPurchaseID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		PD.PurchaseDetailsID,
        PD.ItemID,
        PD.BranchID,
        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
        MI.ItemCode,
        MI.ItemName,
        PD.Quantity,
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2
	FROM
		transaction_purchasedetails PD
        JOIN master_branch MB
			ON MB.BranchID = PD.BranchID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
	WHERE
		PD.PurchaseID = pPurchaseID;
        
END;
$$
DELIMITER ;

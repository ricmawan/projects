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
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        PD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        PD.ItemDetailsID
	FROM
		transaction_purchasedetails PD
        JOIN master_branch MB
			ON MB.BranchID = PD.BranchID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '", ', MI.BuyPrice, ', ', MI.RetailPrice, ', ', MI.Price1, ', ', MI.Price2 ,']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '", ', MID.BuyPrice, ', ', MID.RetailPrice, ', ', MID.Price1, ', ', MID.Price2 ,']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = PD.ItemID
	WHERE
		PD.PurchaseID = pPurchaseID
	GROUP BY
		PD.PurchaseDetailsID,
        PD.ItemID,
        PD.BranchID,
		MB.BranchCode,
		MB.BranchName,
		IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        IFNULL(MID.UnitID, MI.UnitID),
        PD.Quantity,
        MU.UnitName,
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2,
        PD.ItemDetailsID
	ORDER BY
		PD.PurchaseDetailsID;
        
END;
$$
DELIMITER ;

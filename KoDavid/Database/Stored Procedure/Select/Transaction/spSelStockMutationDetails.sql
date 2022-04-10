/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockMutationDetails;

DELIMITER $$
CREATE PROCEDURE spSelStockMutationDetails (
	pStockMutationID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelStockMutationDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SM.StockMutationID,
		SMD.StockMutationDetailsID,
		MI.ItemID,
		SMD.SourceID,
		SMD.DestinationID,
		CONCAT(SB.BranchCode, ' - ', SB.BranchName) SourceBranchName,
		CONCAT(DB.BranchCode, ' - ', DB.BranchName) DestinationBranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        SMD.Quantity,
        MU.UnitName,
        MU.UnitID,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        SMD.ItemDetailsID
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
        JOIN master_branch SB
			ON SB.BranchID = SMD.SourceID
		JOIN master_branch DB
			ON DB.BranchID = SMD.DestinationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SMD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '"]') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '"]') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = SMD.ItemID
	WHERE
		SM.StockMutationID = pStockMutationID
	GROUP BY
		SM.StockMutationID,
		SMD.StockMutationDetailsID,
		MI.ItemID,
		SMD.SourceID,
		SMD.DestinationID,
		CONCAT(SB.BranchCode, ' - ', SB.BranchName),
		CONCAT(DB.BranchCode, ' - ', DB.BranchName),
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SMD.Quantity,
        MU.UnitName,
        MU.UnitID,
        SMD.ItemDetailsID
	ORDER BY
		SMD.StockMutationDetailsID;

END;
$$
DELIMITER ;

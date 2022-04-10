/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockMutation;

DELIMITER $$
CREATE PROCEDURE spSelStockMutation (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockMutation', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
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
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						SM.StockMutationID,
						SMD.StockMutationDetailsID,
                        DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SM.TransactionDate PlainTransactionDate,
                        CONCAT(SB.BranchCode, ' - ', SB.BranchName) SourceBranchName,
                        CONCAT(DB.BranchCode, ' - ', DB.BranchName) DestinationBranchName,
                        MI.ItemID,
                        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
                        MI.ItemName,
                        MU.UnitID,
                        SMD.Quantity,
                        SMD.SourceID,
                        SMD.DestinationID,
                        MU.UnitName,
                        MU.UnitID
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
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;

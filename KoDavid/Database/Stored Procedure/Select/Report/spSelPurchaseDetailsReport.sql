/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseDetailsReport (
	pPurchaseID			BIGINT,
	pBranchID			INT,
	pTransactionType 	VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Pembelian')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        PD.Quantity,
            MU.UnitName,
	        PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
			(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) SubTotal
		FROM
			transaction_purchasedetails PD
	        JOIN master_item MI
				ON MI.ItemID = PD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = PD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			PD.PurchaseID = pPurchaseID
            AND CASE
					WHEN pBranchID = 0
					THEN PD.BranchID
					ELSE pBranchID
				END = PD.BranchID
		ORDER BY
			PD.PurchaseDetailsID;
	ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        PRD.Quantity,
            MU.UnitName,
	        PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
            -(PRD.Quantity * PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) SubTotal
		FROM
			transaction_purchasereturndetails PRD
	        JOIN master_item MI
				ON MI.ItemID = PRD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = PRD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			PRD.PurchaseReturnID = pPurchaseID
            AND CASE
					WHEN pBranchID = 0
					THEN PRD.BranchID
					ELSE pBranchID
				END = PRD.BranchID
		ORDER BY
			PRD.PurchaseReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;

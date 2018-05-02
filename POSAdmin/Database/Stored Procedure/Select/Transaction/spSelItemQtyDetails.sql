/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemQtyDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemQtyDetails (
	pItemCode		VARCHAR(100),
	pBranchID		INT,
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
		CALL spInsEventLog(@full_error, 'spSelItemQtyDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemID,
		MI.ItemCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) Stock
	FROM
		master_item MI
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(TPD.Quantity) Quantity
			FROM
				transaction_purchasedetails TPD
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND TPD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturndetails SRD
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SRD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_saledetails SD
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(PRD.Quantity) Quantity
			FROM
				transaction_purchasereturndetails PRD
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND PRD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SMD.Quantity) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SMD.SourceID = pBranchID
			GROUP BY
				MI.ItemID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
			FROM
				transaction_stockadjustdetails SAD
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SAD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				BD.BranchID,
				SUM(BD.Quantity) Quantity
			FROM
				transaction_bookingdetails BD
				JOIN master_item MI
					ON MI.ItemID = BD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND pBranchID = BD.BranchID
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity) Quantity
			FROM
				transaction_pickdetails PD
				JOIN master_item MI
					ON MI.ItemID = PD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND pBranchID = PD.BranchID
			GROUP BY
				PD.ItemID,
				PD.BranchID
		)P
			ON P.ItemID = MI.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode);
        
END;
$$
DELIMITER ;

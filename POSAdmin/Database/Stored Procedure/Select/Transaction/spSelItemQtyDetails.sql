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
        NULL ItemDetailsID,
		MI.ItemCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MI.UnitID,
        1 ConversionQty,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) Stock,
        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) StockNoConversion
	FROM
		master_item MI
        LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = FSD.BranchID
			GROUP BY
				FSD.ItemID
		)FS
			ON FS.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = TPD.BranchID
			GROUP BY
				TPD.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SRD.BranchID
			GROUP BY
				SRD.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SD.BranchID
			GROUP BY
				SD.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PRD.BranchID
			GROUP BY
				PRD.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.DestinationID
			GROUP BY
				SMD.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
        (
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.SourceID
			GROUP BY
				SMD.ItemID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SAD.BranchID
			GROUP BY
				SAD.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = BD.BranchID
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PD.BranchID
			GROUP BY
				PD.ItemID
		)P
			ON P.ItemID = MI.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MI.ItemID,
        MID.ItemDetailsID,
		MID.ItemDetailsCode,
		MI.ItemName,
		MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight,
        MID.UnitID,
        MID.ConversionQuantity,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) StockNoConversion
	FROM
		master_itemdetails MID
        JOIN master_item MI
			ON MI.ItemID = MID.ItemID
		LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = FSD.BranchID
			GROUP BY
				FSD.ItemID
		)FS
			ON FS.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = TPD.BranchID
			GROUP BY
				TPD.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SRD.BranchID
			GROUP BY
				SRD.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SD.BranchID
			GROUP BY
				SD.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PRD.BranchID
			GROUP BY
				PRD.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.DestinationID
			GROUP BY
				SMD.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
        (
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.SourceID
			GROUP BY
				SMD.ItemID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SAD.BranchID
			GROUP BY
				SAD.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = BD.BranchID
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PD.BranchID
			GROUP BY
				PD.ItemID
		)P
			ON P.ItemID = MI.ItemID
	WHERE
		TRIM(MID.ItemDetailsCode) = TRIM(pItemCode);

SET State = 2;
	SELECT
		NULL ItemDetailsID,
        MI.ItemCode,
		MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight
	FROM
		master_unit MU
		JOIN master_item MI
			ON MI.UnitID = MU.UnitID
	WHERE 
        TRIM(MI.ItemCode) = TRIM(pItemCode)
    UNION ALL
    SELECT
		MID.ItemDetailsID,
        MID.ItemDetailsCode,
    	MU.UnitID,
		MU.UnitName,
        MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
        TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		NULL ItemDetailsID,
        MI.ItemCode,
		MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight
	FROM
		master_unit MU
		JOIN master_item MI
			ON MI.UnitID = MU.UnitID
		JOIN master_itemdetails MID
			ON MID.ItemID = MI.ItemID
	WHERE 
        TRIM(MID.ItemDetailsCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MID.ItemDetailsID,
        MID.ItemDetailsCode,
    	MU.UnitID,
		MU.UnitName,
        MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
        TRIM(MID.ItemDetailsCode) = TRIM(pItemCode);
        
END;
$$
DELIMITER ;

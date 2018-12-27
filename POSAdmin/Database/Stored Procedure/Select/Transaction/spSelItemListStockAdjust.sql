/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemListStockAdjust;

DELIMITER $$
CREATE PROCEDURE spSelItemListStockAdjust (
	pBranchID		INT,
	pCategoryID		INT,
    pItemName		VARCHAR(50),
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
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItemListBranch', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		COUNT(1) nRows
	FROM
		master_item MI
	WHERE
		CASE
			WHEN pCategoryID = 0
			THEN MI.CategoryID 
			ELSE pCategoryID 
		END = MI.CategoryID
		AND MI.ItemName LIKE CONCAT('%', pItemName, '%');
		
SET State = 2;

	SELECT
		MI.ItemID,
		0 ItemDetailsID,
		MI.ItemCode,
		MI.ItemName,
		MC.CategoryID,
		MC.CategoryName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
		MI.MinimumStock,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) Stock,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
		MU.UnitName
	FROM
		master_item MI
		CROSS JOIN master_branch MB
		JOIN master_category MC
			ON MC.CategoryID = MI.CategoryID
		JOIN master_unit MU
			ON MU.UnitID = MI.UnitID
		LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				FSD.BranchID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN FSD.BranchID
					ELSE pBranchID
				END = FSD.BranchID
			GROUP BY
				FSD.ItemID,
				FSD.BranchID
		)FS
			ON FS.ItemID = MI.ItemID
			AND MB.BranchID = FS.BranchID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				TPD.BranchID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN TPD.BranchID
					ELSE pBranchID
				END = TPD.BranchID
			GROUP BY
				TPD.ItemID,
				TPD.BranchID
		)TP
			ON TP.ItemID = MI.ItemID
			AND MB.BranchID = TP.BranchID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SRD.BranchID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SRD.BranchID
					ELSE pBranchID
				END = SRD.BranchID
			GROUP BY
				SRD.ItemID,
				SRD.BranchID
		)SR
			ON SR.ItemID = MI.ItemID
			AND SR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SD.BranchID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				/*JOIN transaction_sale TS
					ON TS.SaleID = SD.SaleID*/
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SD.BranchID
					ELSE pBranchID
				END = SD.BranchID
				/*AND TS.FinishFlag = 1*/
			GROUP BY
				SD.ItemID,
				SD.BranchID
		)S
			ON S.ItemID = MI.ItemID
			AND S.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				PRD.BranchID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN PRD.BranchID
					ELSE pBranchID
				END = PRD.BranchID
			GROUP BY
				PRD.ItemID,
				PRD.BranchID
		)PR
			ON MI.ItemID = PR.ItemID
			AND PR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.DestinationID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SMD.DestinationID
					ELSE pBranchID
				END = SMD.DestinationID
			GROUP BY
				SMD.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
			AND SM.DestinationID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.SourceID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SMD.SourceID
					ELSE pBranchID
				END = SMD.SourceID
			GROUP BY
				SMD.ItemID,
				SMD.SourceID
		)SMM
			ON MI.ItemID = SMM.ItemID
			AND SMM.SourceID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SAD.BranchID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SAD.BranchID
					ELSE pBranchID
				END = SAD.BranchID
			GROUP BY
				SAD.ItemID,
				SAD.BranchID
		)SA
			ON MI.ItemID = SA.ItemID
			AND SA.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				BD.BranchID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1) ) Quantity
			FROM
				transaction_bookingdetails BD
				/*JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID*/
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN BD.BranchID
					ELSE pBranchID
				END = BD.BranchID
				/*AND TB.FinishFlag = 1*/
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
			AND B.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN PD.BranchID
					ELSE pBranchID
				END = PD.BranchID
			GROUP BY
				PD.ItemID,
				PD.BranchID
		)P
			ON P.ItemID = MI.ItemID
			AND P.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN PD.BranchID
					ELSE pBranchID
				END = PD.BranchID
				/*AND TB.FinishFlag = 1*/
			GROUP BY
				BD.ItemID,
				PD.BranchID
		)BN
			ON BN.ItemID = MI.ItemID
			AND BN.BranchID = MB.BranchID
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN MB.BranchID
			ELSE pBranchID
		END = MB.BranchID
		AND CASE
				WHEN pCategoryID = 0
                THEN MC.CategoryID 
				ELSE pCategoryID 
			END = MC.CategoryID
		AND MI.ItemName LIKE CONCAT('%', pItemName, '%')
        
	ORDER BY
		MI.ItemName ASC
	LIMIT
		pLimit_s, pLimit_l;
		
	/* Unit selain pcs
	UNION ALL
	SELECT
		MI.ItemID,
		MID.ItemDetailsID,
		MID.ItemDetailsCode,
		MI.ItemName,
		MC.CategoryID,
		MC.CategoryName,
		IFNULL(MID.ConversionQuantity, 1) * MI.BuyPrice BuyPrice,
		CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
			ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
		END RetailPrice,
		CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
			ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
		END Price1,
		MI.Qty1,
		CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
			ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
		END Price2,
		MI.Qty2,
		MID.Weight,
		MID.MinimumStock,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
		MU.UnitName
	FROM
		master_itemdetails MID
		CROSS JOIN master_branch MB
		JOIN master_unit MU
			ON MU.UnitID = MID.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
		JOIN master_category MC
			ON MC.CategoryID = MI.CategoryID
		LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				FSD.BranchID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN FSD.BranchID
					ELSE pBranchID
				END = FSD.BranchID
			GROUP BY
				FSD.ItemID,
				FSD.BranchID
		)FS
			ON FS.ItemID = MI.ItemID
			AND MB.BranchID = FS.BranchID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				TPD.BranchID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN TPD.BranchID
					ELSE pBranchID
				END = TPD.BranchID
			GROUP BY
				TPD.ItemID,
				TPD.BranchID
		)TP
			ON TP.ItemID = MI.ItemID
			AND MB.BranchID = TP.BranchID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SRD.BranchID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SRD.BranchID
					ELSE pBranchID
				END = SRD.BranchID
			GROUP BY
				SRD.ItemID,
				SRD.BranchID
		)SR
			ON SR.ItemID = MI.ItemID
			AND SR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SD.BranchID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				/*JOIN transaction_sale TS
					ON TS.SaleID = SD.SaleID*/
				/*LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SD.BranchID
					ELSE pBranchID
				END = SD.BranchID
				/*AND TS.FinishFlag = 1*/
			/*GROUP BY
				SD.ItemID,
				SD.BranchID
		)S
			ON S.ItemID = MI.ItemID
			AND S.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				PRD.BranchID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN PRD.BranchID
					ELSE pBranchID
				END = PRD.BranchID
			GROUP BY
				PRD.ItemID,
				PRD.BranchID
		)PR
			ON MI.ItemID = PR.ItemID
			AND PR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.DestinationID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SMD.DestinationID
					ELSE pBranchID
				END = SMD.DestinationID
			GROUP BY
				SMD.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
			AND SM.DestinationID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.SourceID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SMD.SourceID
					ELSE pBranchID
				END = SMD.SourceID
			GROUP BY
				SMD.ItemID,
				SMD.SourceID
		)SMM
			ON MI.ItemID = SMM.ItemID
			AND SMM.SourceID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SAD.BranchID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN SAD.BranchID
					ELSE pBranchID
				END = SAD.BranchID
			GROUP BY
				SAD.ItemID,
				SAD.BranchID
		)SA
			ON MI.ItemID = SA.ItemID
			AND SA.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				BD.BranchID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				/*JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID*/
				/*LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN BD.BranchID
					ELSE pBranchID
				END = BD.BranchID
				/*AND TB.FinishFlag = 1*/
			/*GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
			AND B.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN PD.BranchID
					ELSE pBranchID
				END = PD.BranchID
			GROUP BY
				PD.ItemID,
				PD.BranchID
		)P
			ON P.ItemID = MI.ItemID
			AND P.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				CASE
					WHEN pBranchID = 0
					THEN PD.BranchID
					ELSE pBranchID
				END = PD.BranchID
				/*AND TB.FinishFlag = 1*/
			/*GROUP BY
				BD.ItemID,
				PD.BranchID
		)BN
			ON BN.ItemID = MI.ItemID
			AND BN.BranchID = MB.BranchID
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN MB.BranchID
			ELSE pBranchID
		END = MB.BranchID
		AND MC.CategoryID = pCategoryID*/
        
END;
$$
DELIMITER ;

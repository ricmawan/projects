/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for reset stock by year
Created Date: 12 March 2021
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsResetStock;

DELIMITER $$
CREATE PROCEDURE spInsResetStock (
	pYear				INT,
	pRemarks			VARCHAR(255),
	pBranchID			INT,
	pFirstStockNumber 	VARCHAR(100),
    pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State 	INT;
	DECLARE pID 	BIGINT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsResetStock', pCurrentUser);
	END;
	
SET State = 1;

	INSERT INTO transaction_firststock
	(
		FirstStockNumber,
		TransactionDate,
		CreatedDate,
		CreatedBy
	)
	VALUES 
	(
		pFirstStockNumber,
		NOW(),
		NOW(),
		pCurrentUser
	);
			
SET State = 2;	               
	SELECT
		LAST_INSERT_ID()
	INTO 
		pID;
		
SET State = 3;

	INSERT INTO transaction_firststockdetails
	(
		FirstStockID,
		ItemID,
		ItemDetailsID,
		BranchID,
		Quantity,
		BuyPrice,
		RetailPrice,
		Price1,
		Price2,
		CreatedDate,
		CreatedBy
	)
	SELECT
		pID,
		MI.ItemID,
		0,
		pBranchID,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Price2,
		NOW(),
		pCurrentUser
	FROM
		master_item MI
		JOIN master_unit MU
			ON MI.UnitID = MU.UnitID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				FSD.BranchID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				JOIN master_item MI
					ON FSD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(FSD.CreatedDate) = pYear
				AND FSD.BranchID = pBranchID
			GROUP BY
				MI.ItemID,
				FSD.BranchID
		)FS
			ON FS.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				TPD.BranchID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(TPD.CreatedDate) = pYear
				AND TPD.BranchID = pBranchID
			GROUP BY
				MI.ItemID,
				TPD.BranchID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SRD.BranchID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(SRD.CreatedDate) = pYear
				AND SRD.BranchID = pBranchID
			GROUP BY
				MI.ItemID,
				SRD.BranchID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SD.BranchID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				JOIN transaction_sale TS
					ON TS.SaleID = SD.SaleID
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(SD.CreatedDate) = pYear
				AND SD.BranchID = pBranchID
				AND TS.FinishFlag = 1
			GROUP BY
				MI.ItemID,
				SD.BranchID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				PRD.BranchID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(PRD.CreatedDate) = pYear
				AND PRD.BranchID = pBranchID
			GROUP BY
				MI.ItemID,
				PRD.BranchID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SMD.DestinationID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(SMD.CreatedDate) = pYear
				AND SMD.DestinationID = pBranchID
			GROUP BY
				MI.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.SourceID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(SMD.CreatedDate) = pYear
				AND SMD.SourceID = pBranchID
			GROUP BY
				SMD.ItemID,
				SMD.SourceID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SAD.BranchID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(SAD.CreatedDate) = pYear
				AND SAD.BranchID = pBranchID
			GROUP BY
				MI.ItemID,
				SAD.BranchID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				BD.BranchID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID
				JOIN master_item MI
					ON MI.ItemID = BD.ItemID
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				YEAR(BD.CreatedDate) = pYear
				AND BD.BranchID = pBranchID
				AND TB.FinishFlag = 1
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
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				JOIN master_item MI
					ON MI.ItemID = PD.ItemID
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				YEAR(PD.CreatedDate) = pYear
				AND PD.BranchID = pBranchID
			GROUP BY
				PD.ItemID,
				PD.BranchID
		)P
			ON P.ItemID = MI.ItemID
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
				YEAR(BD.CreatedDate) = pYear
				AND BD.BranchID = pBranchID
			GROUP BY
				BD.ItemID,
				PD.BranchID
		)BN
			ON BN.ItemID = MI.ItemID;
			
SET State = 4;

	DELETE FSD
	FROM	
		transaction_firststockdetails FSD
	WHERE
		YEAR(FSD.CreatedDate) = pYear
		AND FSD.BranchID = pBranchID;

SET State = 5;

	DELETE TPD
	FROM
		transaction_purchasedetails TPD
	WHERE
		YEAR(TPD.CreatedDate) = pYear
		AND TPD.BranchID = pBranchID;
		
SET State = 6;
		
	DELETE SRD
	FROM
		transaction_salereturndetails SRD
	WHERE
		YEAR(SRD.CreatedDate) = pYear
		AND SRD.BranchID = pBranchID;
			
SET State = 7;

	DELETE SD
	FROM
		transaction_saledetails SD
		JOIN transaction_sale TS
			ON TS.SaleID = SD.SaleID
	WHERE
		YEAR(SD.CreatedDate) = pYear
		AND SD.BranchID = pBranchID
		AND TS.FinishFlag = 1;
	
SET State = 8;

	DELETE PRD
	FROM
		transaction_purchasereturndetails PRD
	WHERE
		YEAR(PRD.CreatedDate) = pYear
		AND PRD.BranchID = pBranchID;
		
SET State = 9;

	DELETE SMD
	FROM
		transaction_stockmutationdetails SMD
	WHERE
		YEAR(SMD.CreatedDate) = pYear
		AND SMD.DestinationID = pBranchID;
		
SET State = 10;

	DELETE SMD
	FROM
		transaction_stockmutationdetails SMD
	WHERE
		YEAR(SMD.CreatedDate) = pYear
		AND SMD.SourceID = pBranchID;
	
SET State = 11;

	DELETE SAD
	FROM
		transaction_stockadjustdetails SAD
	WHERE
		YEAR(SAD.CreatedDate) = pYear
		AND SAD.BranchID = pBranchID;
	
SET State = 12;

	DELETE BD
	FROM
		transaction_bookingdetails BD
		JOIN transaction_booking TB
			ON TB.BookingID = BD.BookingID
	WHERE
		YEAR(BD.CreatedDate) = pYear
		AND BD.BranchID = pBranchID
		AND TB.FinishFlag = 1;

SET State = 13;

	DELETE PD
	FROM
		transaction_pickdetails PD
	WHERE
		YEAR(PD.CreatedDate) = pYear
		AND PD.BranchID = pBranchID;
		
SET State = 14;

	DELETE BD
	FROM
		transaction_bookingdetails BD
	WHERE
		YEAR(BD.CreatedDate) = pYear
		AND BD.BranchID = pBranchID;
		
	COMMIT;
END;
$$
DELIMITER ;

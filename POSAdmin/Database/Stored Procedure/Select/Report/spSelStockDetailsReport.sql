/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelStockDetailsReport (
	pItemID					BIGINT,
	pBranchID 				INT,
	pFromDate				DATE,
	pToDate					DATE,
    pConversionQuantity		DOUBLE,
    pCurrentUser			VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelStockDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		'Mutasi Sebelumnya' TransactionType,
		'-' TransactionDate,
		pFromDate DateNoFormat,
		'' CustomerName,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / pConversionQuantity, 2) Quantity,
		'0000-00-00' CreatedDate
	FROM
		master_item MI
        JOIN master_itemdetails MID
			ON MID.ItemID = MI.ItemID
        LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststock FS
				JOIN transaction_firststockdetails FSD
					ON FS.FirstStockID = FSD.FirstStockID
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				FSD.BranchID = pBranchID
				AND CAST(FS.TransactionDate AS DATE) < pFromDate
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
				transaction_purchase TP
				JOIN transaction_purchasedetails TPD
					ON TP.PurchaseID = TPD.PurchaseID
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				TPD.BranchID = pBranchID
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
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
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SRD.SaleReturnID = SR.SaleReturnID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SRD.BranchID = pBranchID
				AND CAST(SR.TransactionDate AS DATE) < pFromDate
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
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SD.BranchID = pBranchID
				AND CAST(TS.TransactionDate AS DATE) < pFromDate
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
				transaction_purchasereturn TPR
				JOIN transaction_purchasereturndetails PRD
					ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				PRD.BranchID = pBranchID
				AND CAST(TPR.TransactionDate AS DATE) < pFromDate
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
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SMD.DestinationID = pBranchID
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SMD.ItemID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
        (
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SMD.SourceID = pBranchID
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SMD.ItemID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity)  * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjust SA
				JOIN transaction_stockadjustdetails SAD
					ON SA.StockAdjustID = SAD.StockAdjustID
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SAD.BranchID = pBranchID
				AND CAST(SA.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SAD.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
                JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID
                LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				BD.BranchID = pBranchID
				AND CAST(TB.TransactionDate AS DATE) < pFromDate
			GROUP BY
				BD.ItemID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pick TP
				JOIN transaction_pickdetails PD
					ON TP.PickID = PD.PickID
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PD.BranchID
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
			GROUP BY
				PD.ItemID
		)P
			ON P.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_booking B
				JOIN transaction_bookingdetails BD
					ON B.BookingID = BD.BookingID
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				pBranchID = PD.BranchID
				AND CAST(B.TransactionDate AS DATE) < pFromDate
			GROUP BY
				BD.ItemID
		)BN
			ON BN.ItemID = MI.ItemID
	WHERE
		MI.ItemID = pItemID
	UNION ALL
    SELECT
		'Stok Awal',
		DATE_FORMAT(FS.TransactionDate, '%d-%m-%Y') TransactionDate,
		FS.TransactionDate DateNoFormat,
		'-',
		ROUND((FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		FS.CreatedDate
	FROM
		transaction_firststock FS
		JOIN transaction_firststockdetails FSD
			ON FS.FirstStockID = FSD.FirstStockID
		JOIN master_item MI
			ON FSD.ItemID = MI.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = FSD.ItemDetailsID
	WHERE
		FSD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(FS.TransactionDate AS DATE) >= pFromDate
		AND CAST(FS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Pembelian',
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MS.SupplierName,
		ROUND((TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2) ,
		TP.CreatedDate
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails TPD
			ON TP.PurchaseID = TPD.PurchaseID
		JOIN master_item MI
			ON TPD.ItemID = MI.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = TPD.ItemDetailsID
	WHERE
		TPD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Penjualan',
		DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') TransactionDate,
		SR.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		SR.CreatedDate
	FROM
		transaction_salereturn SR
		JOIN transaction_sale TS
			ON TS.SaleID = SR.SaleID
		JOIN transaction_salereturndetails SRD
			ON SRD.SaleReturnID = SR.SaleReturnID
		JOIN master_item MI
			ON SRD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SRD.ItemDetailsID
	WHERE
		SRD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SR.TransactionDate AS DATE) >= pFromDate
		AND CAST(SR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Penjualan',
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((-SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		TS.CreatedDate
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_item MI
			ON SD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
	WHERE
		SD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Pembelian',
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		TPR.TransactionDate DateNoFormat,
		MS.SupplierName,
		ROUND((-PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) /pConversionQuantity, 2),
		TPR.CreatedDate
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PRD.ItemDetailsID
	WHERE
		PRD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		ROUND((SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) /pConversionQuantity, 2),
		SM.CreatedDate
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SMD.ItemDetailsID
	WHERE
		SMD.DestinationID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		ROUND((-SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		SM.CreatedDate
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SMD.ItemDetailsID
	WHERE
		SMD.SourceID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Adjust Stok',
		DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
		SA.TransactionDate DateNoFormat,
		'',
		ROUND(((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		SA.CreatedDate
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SAD.ItemDetailsID
	WHERE
		SAD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SA.TransactionDate AS DATE) >= pFromDate
		AND CAST(SA.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		'Pemesanan',
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
		TB.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((-(BD.Quantity  - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		TB.CreatedDate
	FROM
		transaction_booking TB
		JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_item MI
			ON BD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN transaction_pickdetails PD
			ON PD.BookingDetailsID = BD.BookingDetailsID
			AND PD.BranchID <> BD.BranchID
	WHERE
		BD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TB.TransactionDate AS DATE) >= pFromDate
		AND CAST(TB.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		'Pengambilan',
        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((-PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		TP.CreatedDate
	FROM
		transaction_bookingdetails BD
        JOIN transaction_pickdetails PD
			ON PD.BookingDetailsID = BD.BookingDetailsID
			AND PD.BranchID <> BD.BranchID
		JOIN transaction_pick TP
			ON TP.PickID = PD.PickID
		JOIN transaction_booking TB
			ON TB.BookingID = TP.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		LEFT JOIN master_itemdetails MID
			ON BD.ItemDetailsID = MID.ItemDetailsID
	WHERE
		PD.BranchID = pBranchID
		AND PD.ItemID = pItemID
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	ORDER BY
		DateNoFormat,
		CreatedDate;

        
END;
$$
DELIMITER ;

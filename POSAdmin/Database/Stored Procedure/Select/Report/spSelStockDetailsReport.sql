/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelStockDetailsReport (
	pItemCode		VARCHAR(100),
	pBranchID 		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelStockDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		'Stok Awal' TransactionType,
		DATE_FORMAT(pFromDate, '%d-%m-%Y') TransactionDate,
		pFromDate DateNoFormat,
		'' CustomerName,
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Quantity,
		'0000-00-00' CreatedDate
	FROM
		master_item MI
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(TPD.Quantity) Quantity
			FROM
				transaction_purchase TP
				JOIN transaction_purchasedetails TPD
					ON TP.PurchaseID = TPD.PurchaseID
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
			WHERE
				TPD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
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
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SRD.SaleReturnID = SR.SaleReturnID
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
			WHERE
				SRD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SR.TransactionDate AS DATE) < pFromDate
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
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
			WHERE
				SD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TS.TransactionDate AS DATE) < pFromDate
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
				transaction_purchasereturn TPR
				JOIN transaction_purchasereturndetails PRD
					ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
			WHERE
				PRD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TPR.TransactionDate AS DATE) < pFromDate
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
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
			WHERE
				SMD.DestinationID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
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
				transaction_stockadjust SA
				JOIN transaction_stockadjustdetails SAD
					ON SA.StockAdjustID = SAD.StockAdjustID
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
			WHERE
				SAD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SA.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
	SELECT
		'Pembelian',
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MS.SupplierName,
		TPD.Quantity,
		TP.CreatedDate
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails TPD
			ON TP.PurchaseID = TPD.PurchaseID
		JOIN master_item MI
			ON TPD.ItemID = MI.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
	WHERE
		TPD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Penjualan',
		DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') TransactionDate,
		SR.TransactionDate DateNoFormat,
		MC.CustomerName,
		SRD.Quantity,
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
	WHERE
		SRD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SR.TransactionDate AS DATE) >= pFromDate
		AND CAST(SR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Penjualan',
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate DateNoFormat,
		MC.CustomerName,
		-SD.Quantity,
		TS.CreatedDate
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_item MI
			ON SD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
	WHERE
		SD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Pembelian',
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		TPR.TransactionDate DateNoFormat,
		MS.SupplierName,
		-PRD.Quantity,
		TPR.CreatedDate
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
	WHERE
		PRD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		SMD.Quantity,
		SM.CreatedDate
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
	WHERE
		SMD.DestinationID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Adjust Stok',
		DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
		SA.TransactionDate DateNoFormat,
		'',
		(SAD.AdjustedQuantity - SAD.Quantity),
		SA.CreatedDate
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
	WHERE
		SAD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SA.TransactionDate AS DATE) >= pFromDate
		AND CAST(SA.TransactionDate AS DATE) <= pToDate
	ORDER BY
		DateNoFormat,
		CreatedDate;

        
END;
$$
DELIMITER ;

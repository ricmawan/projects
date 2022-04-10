/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDailyReport;

DELIMITER $$
CREATE PROCEDURE spSelDailyReport (
	pUserID				BIGINT,
	pTransactionDate	DATE,
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
		CALL spInsEventLog(@full_error, 'spSelDailyReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		0 UnionLevel,
        MUS.UserName,
        'Saldo Awal' TransactionName,
		'' TransactionNumber,
        '' CustomerName,
        '' ItemName,
        '' ItemCode,
        0 Quantity,
        '' UnitName,
        0  SalePrice,
        0 Discount,
		0 DiscountTotal,
        FB.FirstBalanceAmount SubTotal,
        0 Payment
	FROM
		transaction_firstbalance FB
		JOIN master_user MUS
			ON MUS.UserID = FB.UserID
	WHERE
		CAST(FB.TransactionDate AS DATE) = pTransactionDate
        AND CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
	UNION ALL
	SELECT
		1 UnionLevel,
		MUS.UserName,
        'Penjualan Tunai' TransactionName,
		TS.SaleNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        MU.UnitName,
        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        SD.Discount,
		IFNULL(TS.Discount, 0) DiscountTotal,
        SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount) SubTotal,
        0 Payment
    FROM
		transaction_sale TS
        JOIN master_user MUS
			ON TS.CreatedBy = MUS.UserLogin
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TS.TransactionDate AS DATE) = pTransactionDate
        AND IFNULL(TS.PaymentTypeID, 0) = 1
	UNION ALL
    SELECT
		2 UnionLevel,
		MUS.UserName,
        'Pemesanan Tunai' TransactionName,
		TB.BookingNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        BD.Quantity,
        MU.UnitName,
        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        BD.Discount,
		IFNULL(TB.Discount, 0) DiscountTotal,
        BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount) SubTotal,
        0 Payment
    FROM
		transaction_booking TB
		JOIN master_user MUS
			ON TB.CreatedBy = MUS.UserLogin
        JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TB.TransactionDate AS DATE) = pTransactionDate
        AND IFNULL(TB.PaymentTypeID, 0) = 1
	UNION ALL
    SELECT
		3 UnionLevel,
		MUS.UserName,
        'Retur Penjualan',
		CONCAT('R', TS.SaleNumber) SaleNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        MU.UnitName,
        SRD.SalePrice,
        0 Discount,
		0 DiscountTotal,
        -(SRD.Quantity * SRD.SalePrice) SubTotal,
        0 Payment
    FROM
		transaction_salereturn TSR
        JOIN master_user MUS
			ON TSR.CreatedBy = MUS.UserLogin
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SRD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TSR.TransactionDate AS DATE) = pTransactionDate
	UNION ALL
    SELECT
		4 UnionLevel,
		MUS.UserName,
        'DP Penjualan' TransactionName,
		TS.SaleNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        MU.UnitName,
        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        SD.Discount,
		TS.Discount DiscountTotal,
        SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount) SubTotal,
        IFNULL(TS.Payment, 0) Payment
    FROM
		transaction_sale TS
        JOIN master_user MUS
			ON TS.CreatedBy = MUS.UserLogin
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TS.TransactionDate AS DATE) = pTransactionDate
        AND IFNULL(TS.PaymentTypeID, 0) = 2
        AND IFNULL(TS.Payment, 0) > 0
	UNION ALL
    SELECT
		5 UnionLevel,
		MUS.UserName,
        'DP Pemesanan' TransactionName,
		TB.BookingNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        BD.Quantity,
        MU.UnitName,
        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        BD.Discount,
		TB.Discount DiscountTotal,
        BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount) SubTotal,
        IFNULL(TB.Payment, 0) Payment
    FROM
		transaction_booking TB
		JOIN master_user MUS
			ON TB.CreatedBy = MUS.UserLogin
        JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TB.TransactionDate AS DATE) = pTransactionDate
		AND IFNULL(TB.PaymentTypeID, 0) = 2
        AND IFNULL(TB.Payment, 0) > 0
	UNION ALL
    SELECT
		6 UnionLevel,
		MUS.UserName,
        'Pembayaran Piutang' TransactionName,
		IFNULL(TS.SaleNumber, TB.BookingNumber) TransactionNumber,
        MC.CustomerName,
		'',
        '',
        0,
        '',
        0,
        0,
		0 DiscountTotal,
        PD.Amount,
        0 Payment
	FROM
		transaction_paymentdetails PD
        JOIN master_user MUS
			ON MUS.UserLogin = PD.CreatedBy
		LEFT JOIN transaction_sale TS
			ON TS.SaleID = PD.TransactionID
            AND PD.TransactionType = 'S'
		LEFT JOIN transaction_booking TB
			ON TB.BookingID = PD.TransactionID
            AND PD.TransactionType = 'B'
		JOIN master_customer MC
			ON MC.CustomerID = IFNULL(TS.CustomerID, TB.CustomerID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND PD.TransactionType IN ('S', 'B')
        AND CAST(PD.PaymentDate AS DATE) = pTransactionDate
	ORDER BY
		UserName,
        UnionLevel,
		TransactionNumber;

END;
$$
DELIMITER ;

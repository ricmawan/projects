/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelIncomeDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelIncomeDetailsReport (
	pSaleID				BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelIncomeDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Penjualan')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SD.Quantity,
            MU.UnitName,
            SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
            (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalBuy,
	        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
			SD.Discount,
			(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) TotalSale,
            (SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount))  - (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Income
		FROM
			transaction_saledetails SD
	        JOIN master_item MI
				ON MI.ItemID = SD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = SD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			SD.SaleID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN SD.BranchID
					ELSE pBranchID
				END = SD.BranchID
		ORDER BY
			SD.SaleDetailsID;
	ELSEIF(pTransactionType = 'Pemesanan')
    THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        BD.Quantity,
            MU.UnitName,
            BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
            (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalBuy,
	        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
			BD.Discount,
			(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) TotalSale,
            (BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount))  - (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Income
		FROM
			transaction_bookingdetails BD
	        JOIN master_item MI
				ON MI.ItemID = BD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = BD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			BD.BookingID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN BD.BranchID
					ELSE pBranchID
				END = BD.BranchID
		ORDER BY
			BD.BookingDetailsID;
    ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SRD.Quantity,
            MU.UnitName,
            SRD.BuyPrice,
            (SRD.Quantity * SRD.BuyPrice) TotalBuy,
	        SRD.SalePrice,
            0 Discount,
			(SRD.Quantity * SRD.SalePrice) TotalSale,
            -((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)) Income
		FROM
			transaction_salereturndetails SRD
	        JOIN master_item MI
				ON MI.ItemID = SRD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = SRD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			SRD.SaleReturnID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN SRD.BranchID
					ELSE pBranchID
				END = SRD.BranchID
		ORDER BY
			SRD.SaleReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;

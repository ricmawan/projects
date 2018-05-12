/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking details by BookingID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetails;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetails (
	pBookingID			BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetails', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		SD.BookingDetailsID,
        SD.ItemID,
        SD.BranchID,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        SD.BuyPrice,
        SD.BookingPrice,
		SD.Discount,
		IFNULL(MID.RetailPrice, MI.RetailPrice) RetailPrice,
        IFNULL(MID.Price1, MI.Price1) Price1,
        IFNULL(MID.Qty1, MI.Qty1) Qty1,
        IFNULL(MID.Price2, MI.Price2) Price2,
        IFNULL(MID.Qty2, MI.Qty2) Qty2,
		IFNULL(MID.Weight, MI.Weight) Weight,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1) ConversionQty
	FROM
		transaction_bookingdetails SD
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '", ', MI.BuyPrice, ', ', MI.RetailPrice, ', ', MI.Price1, ', ', MI.Price2, ', ', MI.Qty1, ', ', MI.Qty2, ']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '", ', MID.BuyPrice, ', ', MID.RetailPrice, ', ', MID.Price1, ', ', MID.Price2, ', ', MID.Qty1, ', ', MID.Qty2, ']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = SD.ItemID
	WHERE
		SD.BookingID = pBookingID
	GROUP BY
		SD.BookingDetailsID,
        SD.ItemID,
        SD.BranchID,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID),
        SD.BuyPrice,
        SD.BookingPrice,
		SD.Discount,
		IFNULL(MID.RetailPrice, MI.RetailPrice),
        IFNULL(MID.Price1, MI.Price1),
        IFNULL(MID.Qty1, MI.Qty1),
        IFNULL(MID.Price2, MI.Price2),
        IFNULL(MID.Qty2, MI.Qty2),
		IFNULL(MID.Weight, MI.Weight),
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		SD.BookingDetailsID;
        
END;
$$
DELIMITER ;

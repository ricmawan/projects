/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking return details
Created Date: 24 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPickUpDetails;

DELIMITER $$
CREATE PROCEDURE spSelPickUpDetails (
	pPickID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPickUpDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TSR.BookingID,
		TSR.PickID,
		TSRD.PickDetailsID,
		TSRD.BookingDetailsID,
        TSRD.ItemID,
        TSRD.ItemDetailsID,
        TSRD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        TSRD.Quantity,
        TSRD.BuyPrice,
        TSRD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSRD.Discount SalePrice,
        (IFNULL(TS.Quantity, 0) - IFNULL(SR.Quantity, 0) + IFNULL(TSRD.Quantity, 0)) Maksimum,
        MU.UnitName,
        TSRD.Discount,
        IFNULL(MID.ConversionQuantity, 1) ConversionQuantity
	FROM
		transaction_pick TSR
		JOIN transaction_pickdetails TSRD
			ON TSRD.PickID = TSR.PickID
		JOIN master_item MI
			ON MI.ItemID = TSRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = TSRD.ItemDetailsID
		JOIN master_unit MU
			ON IFNULL(MID.UnitID, MI.UnitID) = MU.UnitID
        LEFT JOIN
		(
			SELECT
				TS.BookingID,
				SD.ItemID,
				SD.BookingDetailsID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_booking TS
				JOIN transaction_bookingdetails SD
					ON TS.BookingID = SD.BookingID
			GROUP BY
				TS.BookingID,
				SD.ItemID,
				SD.BookingDetailsID
		)TS
			ON TSR.BookingID = TS.BookingID
			AND MI.ItemID = TS.ItemID
			AND TSRD.BookingDetailsID = TS.BookingDetailsID
		LEFT JOIN
		(
			SELECT
				SR.BookingID,
				SRD.ItemID,
				SRD.BookingDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_pick SR
				JOIN transaction_pickdetails SRD
					ON SR.PickID = SRD.PickID
			GROUP BY
				SR.BookingID,
				SRD.ItemID,
				SRD.BookingDetailsID
		)SR
			ON TSR.BookingID = SR.BookingID
			AND MI.ItemID = SR.ItemID
			AND TSRD.BookingDetailsID = SR.BookingDetailsID

	WHERE
		TSR.PickID = pPickID
	ORDER BY
		TSRD.PickDetailsID;
        
END;
$$
DELIMITER ;

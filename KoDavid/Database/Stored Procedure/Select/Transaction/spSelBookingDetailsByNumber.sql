/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking details by BookingNumber
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetailsByNumber;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetailsByNumber (
	pBookingNumber	VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetailsByNumber', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.BookingID,
		SD.BookingDetailsID,
        SD.ItemID,
        IFNULL(MID.ItemDetailsID, '') ItemDetailsID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        SD.Quantity - IFNULL(TSR.Quantity, 0) Quantity,
        SD.BuyPrice,
        SD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount BookingPrice,
        SD.Discount,
        MC.CustomerName,
        MU.UnitName,
        IFNULL(MID.ConversionQuantity, 1) ConversionQuantity
	FROM
		transaction_booking TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN transaction_bookingdetails SD
			ON TS.BookingID = SD.BookingID
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON IFNULL(MID.UnitID, MI.UnitID) = MU.UnitID
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
		)TSR
			ON TSR.BookingID = TS.BookingID
			AND MI.ItemID = TSR.ItemID
			AND TSR.BookingDetailsID = SD.BookingDetailsID
	WHERE
		TRIM(TS.BookingNumber) = TRIM(pBookingNumber)
	ORDER BY
		SD.BookingDetailsID;
        
END;
$$
DELIMITER ;

/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking details by BookingID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetails;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetails (
	pBookingID		BIGINT,
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
		BD.BookingDetailsID,
        BD.ItemID,
        BD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        BD.Quantity,
        BD.BuyPrice,
        BD.BookingPrice,
		BD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight
	FROM
		transaction_bookingdetails SD
        JOIN master_branch MB
			ON MB.BranchID = BD.BranchID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
	WHERE
		BD.BookingID = pBookingID
	ORDER BY
		BD.BookingDetailsID;
        
END;
$$
DELIMITER ;

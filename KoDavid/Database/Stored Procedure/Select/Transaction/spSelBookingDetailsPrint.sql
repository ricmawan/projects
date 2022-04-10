/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetailsPrint;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetailsPrint (
	pBookingDetailsID	TEXT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetailsPrint', pCurrentUser);
	END;
	
SET State = 1;

	IF(pBookingDetailsID <> "" ) THEN
		SET @query = CONCAT("SELECT
								BD.Quantity,
								MI.ItemName,
                                MU.UnitName
							 FROM
								transaction_bookingdetails BD
								JOIN master_item MI
									ON MI.ItemID = BD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
								JOIN master_unit MU
									ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
							WHERE
								BD.BookingDetailsID IN ", pBookingDetailsID );
							
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
SET State = 2;
		SET @query = CONCAT("UPDATE transaction_bookingdetails
							SET
								PrintCount = IFNULL(PrintCount, 0) + 1,
								PrintedDate = NOW()
							WHERE
								BookingDetailsID IN ", pBookingDetailsID );
							
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
	END IF;
        
END;
$$
DELIMITER ;
